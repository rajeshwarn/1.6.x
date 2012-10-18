unit ufdbguardd;

{$MODE DELPHI}
{$LONGSTRINGS ON}

interface

uses
    Classes, SysUtils,variants,strutils,Process,logs,unix,RegExpr in 'RegExpr.pas',zsystem,IniFiles;



  type
  tufdbguardd=class


private
     LOGS:Tlogs;
     SYS:TSystem;
     artica_path:string;
     TAIL_STARTUP:string;
     TAIL_LOG_PATH:string;
     EnableUfdbGuard:integer;
     EnableWebProxyStatsAppliance:integer;
     EnableRemoteStatisticsAppliance:integer;
     UseRemoteUfdbguardService:integer;
     SQUIDEnable:integer;
     binpath:string;
     mem_installee:integer;
     procedure ufdb_admin_events(textlog:string;sfunction:string;sline:string);
public
    procedure   Free;
    constructor Create(const zSYS:Tsystem);
    procedure   START();
    procedure   STOP();
    function    STATUS():string;
    function    PID_NUM():string;
    function    VERSION():string;
    function    BIN_PATH():string;
    function    PID_PATH():string;
    function    TAIL_PID_NUM():string;
    procedure   TAIL_STOP();
    procedure   TAIL_START();
END;

implementation

constructor tufdbguardd.Create(const zSYS:Tsystem);
begin

       LOGS:=tlogs.Create();
       SYS:=zSYS;
       binpath:=BIN_PATH();
       mem_installee:=SYS.MEM_TOTAL_INSTALLEE();
       TAIL_STARTUP:=SYS.LOCATE_PHP5_BIN()+' /usr/share/artica-postfix/exec.ufdbguard-tail.php';
       TAIL_LOG_PATH:='/var/log/squid/ufdbguardd.log';
       if not TryStrToInt(SYS.GET_INFO('SQUIDEnable'),SQUIDEnable) then SQUIDEnable:=1;
       if not TryStrToInt(SYS.GET_INFO('EnableUfdbGuard'),EnableUfdbGuard) then EnableUfdbGuard:=0;
       if not TryStrToInt(SYS.GET_INFO('EnableWebProxyStatsAppliance'),EnableWebProxyStatsAppliance) then EnableWebProxyStatsAppliance:=0;
       if not TryStrToInt(SYS.GET_INFO('UseRemoteUfdbguardService'),UseRemoteUfdbguardService) then UseRemoteUfdbguardService:=0;
       if not TryStrToInt(SYS.GET_INFO('EnableRemoteStatisticsAppliance'),EnableRemoteStatisticsAppliance) then EnableRemoteStatisticsAppliance:=0;



       if not SYS.ISMemoryHiger1G() then begin
          if EnableUfdbGuard=1 then begin
             logs.NOTIFICATION('[ARTICA]: ('+SYS.HOSTNAME_g()+') Warning: your memory is under 1GB ['+IntToStr(mem_installee)+'], UfdBguard is now disabled','UfdBguard is disabled because your computer is not "memory compliance" tufdbguardd.Create() line 106','proxy');
             SYS.set_INFO('EnableUfdbGuard','0');
             EnableUfdbGuard:=0;
             SYS.THREAD_COMMAND_SET(SYS.LOCATE_PHP5_BIN()+' /usr/share/artica-postfix/exec.squid.php --build');
          end;
       end;

       if FileExists('/etc/artica-postfix/OPENVPN_APPLIANCE') then SQUIDEnable:=0;
       if SQUIDEnable=0 then EnableUfdbGuard:=0;
       if EnableWebProxyStatsAppliance=1 then  EnableUfdbGuard:=1;
       if UseRemoteUfdbguardService=1 then  EnableUfdbGuard:=0;

end;
//##############################################################################
procedure tufdbguardd.free();
begin
    logs.Free;
end;
//##############################################################################

procedure tufdbguardd.STOP();
var
   count:integer;
   RegExpr:TRegExpr;
   cmd:string;
   pids:Tstringlist;
   pidstring:string;
   fpid,i:integer;
begin
if not FileExists(binpath) then begin

   writeln('Stopping ufdbguardd..........: Not installed');
   exit;
end;

if not SYS.PROCESS_EXIST(PID_NUM()) then begin
        writeln('Stopping ufdbguardd..........: already Stopped');
        exit;
end;
   pidstring:=PID_NUM();
   writeln('Stopping ufdbguardd..........: ' + pidstring + ' PID..');

   if FileExists('/etc/init.d/ufdb') then cmd:='/etc/init.d/ufdb stop';
   fpsystem(cmd);

 if not SYS.PROCESS_EXIST(PID_NUM()) then begin
        writeln('Stopping ufdbguardd..........: Stopped');
        TAIL_STOP();
        exit;
 end;

   cmd:=SYS.LOCATE_GENERIC_BIN('kill')+' '+pidstring+' >/dev/null 2>&1';
   fpsystem(cmd);

   count:=0;
   ufdb_admin_events('receive command to stop UfdbGuard daemon','STOP','100');
   while SYS.PROCESS_EXIST(pidstring) do begin
        sleep(200);
        count:=count+1;
        if count>50 then begin
            if length(pidstring)>0 then begin
               if SYS.PROCESS_EXIST(pidstring) then begin
                  writeln('Stopping ufdbguardd..........: kill pid '+ pidstring+' after timeout');
                  fpsystem('/bin/kill -9 ' + pidstring);
               end;
            end;
            break;
        end;
        pidstring:=PID_NUM();
  end;

  if not SYS.PROCESS_EXIST(PID_NUM()) then begin
     writeln('Stopping ufdbguardd..........: Stopped');
     TAIL_STOP();
  end;
end;

//##############################################################################
function tufdbguardd.BIN_PATH():string;
begin
result:=SYS.LOCATE_GENERIC_BIN('ufdbguardd');
end;
//##############################################################################
procedure tufdbguardd.START();
var
   count:integer;
   cmd:string;
   su,nohup:string;
   conf:TiniFile;
   enabled:integer;
   RegExpr:TRegExpr;
   servername:string;
   tmpfile:string;
   cmdline:string;
begin

   if not FileExists(binpath) then begin
         logs.DebugLogs('Starting......: ufdbguardd not installed');
         exit;
   end;

if EnableUfdbGuard=0 then begin
   logs.DebugLogs('Starting......:  ufdbguardd is disabled Memory: '+IntToStr(mem_installee));
   STOP();
   exit;
end;

if EnableRemoteStatisticsAppliance=1 then begin
   logs.DebugLogs('Starting......:  ufdbguardd is disabled: using statistics appliance...');
   STOP();
   exit;
end;

if SYS.PROCESS_EXIST(PID_NUM()) then begin
   logs.DebugLogs('Starting......:  ufdbguardd Already running using PID ' +PID_NUM()+ '...');
   exit;
end;

    SYS.DirFiles('/tmp','ufdbguardd-*');
    for count:=0 to sys.DirListFiles.Count-1 do begin
        logs.DebugLogs('Starting......: ufdbguardd remove /tmp/'+sys.DirListFiles.Strings[count]);
        logs.DeleteFile('/tmp/'+sys.DirListFiles.Strings[count]);
    end;
   fpsystem(SYS.LOCATE_PHP5_BIN()+' /usr/share/artica-postfix/exec.squidguard.php --build');
   logs.DebugLogs('Starting......: ufdbguardd server...');
   ForceDirectories('/var/lib/squidguard/security/cacerts');
   ForceDirectories('/var/log/ufdbguard');
   fpsystem('/bin/chown squid:squid /var/log/ufdbguard');
   fpsystem('/bin/chown -R squid:squid /var/log/ufdbguard');

   if FileExists('/etc/init.d/ufdb') then cmd:='/etc/init.d/ufdb start';
   ufdb_admin_events('receive command to start UfdbGuard daemon','START','100');
   fpsystem(cmd);
   count:=0;
   while not SYS.PROCESS_EXIST(PID_NUM()) do begin
     sleep(300);
     inc(count);
     if count>50 then begin
       logs.DebugLogs('Starting......: ufdbguardd (timeout!!!)');
       logs.DebugLogs('Starting......: ufdbguardd "'+cmd+'"');
       break;
     end;
   end;


if SYS.PROCESS_EXIST(PID_NUM()) then begin
   logs.DebugLogs('Starting......:  ufdbguardd started with new PID ' +PID_NUM()+ '...');
   TAIL_START();
   exit;
end;


   logs.DebugLogs('Starting......:  ufdbguardd using cmdline');
   cmd:=binpath+' -d -r -w 90 -c /etc/ufdbguard/ufdbGuard.conf';
   fpsystem(cmd);
   count:=0;
   while not SYS.PROCESS_EXIST(PID_NUM()) do begin
     sleep(300);
     inc(count);
     if count>50 then begin
       logs.DebugLogs('Starting......: ufdbguardd (timeout!!!)');
       logs.DebugLogs('Starting......: ufdbguardd "'+cmd+'"');
       break;
     end;
   end;




   if not SYS.PROCESS_EXIST(PID_NUM()) then begin
       logs.DebugLogs('Starting......: ufdbguardd (failed!!!)');
       logs.DebugLogs('Starting......: ufdbguardd "'+cmd+'"');
   end else begin
       logs.DebugLogs('Starting......: ufdbguardd started with new PID '+PID_NUM());
       TAIL_START();
   end;

end;
//##############################################################################
procedure tufdbguardd.TAIL_START();
var
   count:integer;
   cmd:string;
   su,nohup:string;
   conf:TiniFile;
   enabled,pidint:integer;
   RegExpr:TRegExpr;
   servername:string;
   tmpfile:string;
   cmdline,pid:string;
   noufdbg:boolean;
   noufdbgcmdline:string;
begin
 noufdbg:=SYS.COMMANDLINE_PARAMETERS('--noufdbg');
if noufdbg then noufdbgcmdline:=' --noufdbg';

   if not FileExists(binpath) then begin
         logs.DebugLogs('Starting......: ufdbguardd not installed');
         exit;
   end;

if EnableUfdbGuard=0 then begin
   logs.DebugLogs('Starting......:  ufdbguardd is disabled');
   TAIL_STOP();
   exit;
end;

if EnableRemoteStatisticsAppliance=1 then begin
   logs.DebugLogs('Starting......:  ufdbguardd is disabled: using statistics appliance');
   TAIL_STOP();
   exit;
end;

if SYS.PROCESS_EXIST(TAIL_PID_NUM()) then begin
   logs.DebugLogs('Starting......:  ufdbguardd-tail Already running using PID ' +TAIL_PID_NUM()+ '...');
   exit;
end;

pid:=SYS.PIDOF_PATTERN('/usr/bin/tail -f -n 0 '+TAIL_LOG_PATH);
count:=0;
pidint:=0;
      while SYS.PROCESS_EXIST(pid) do begin
          if count>0 then break;
          if not TryStrToInt(pid,pidint) then continue;
          logs.DebugLogs('Starting......: ufdbguardd-tail stop tail pid '+pid);
          ufdb_admin_events('ufdbguardd-tail stop tail pid '+pid,'TAIL_START','254');
          if pidint>0 then  fpsystem('/bin/kill '+pid);
          sleep(200);
          pid:=SYS.PIDOF_PATTERN('/usr/bin/tail -f -n 0 /var/log/ufdbguard/ufdbguardd.log');
          inc(count);
      end;


cmd:='/usr/bin/tail -f -n 0 '+TAIL_LOG_PATH+'|'+TAIL_STARTUP+noufdbgcmdline+' 2>&1 &';
logs.Debuglogs(cmd);
fpsystem(cmd);
pid:=TAIL_PID_NUM();
count:=0;
   ufdb_admin_events('receive order to start ufdbguardd-tail','TAIL_START','254');
   fpsystem(cmd);
   count:=0;
   while not SYS.PROCESS_EXIST(TAIL_PID_NUM()) do begin
     sleep(300);
     inc(count);
     if count>5 then begin
       logs.DebugLogs('Starting......: ufdbguardd-tail (timeout!!!)');
       logs.DebugLogs('Starting......: ufdbguardd-tail "'+cmd+'"');
       break;
     end;
   end;

   if not SYS.PROCESS_EXIST(TAIL_PID_NUM()) then begin
       ufdb_admin_events('ufdbguardd-tail (failed!!!)','TAIL_START','281');
       logs.DebugLogs('Starting......: ufdbguardd-tail (failed!!!)');
       logs.DebugLogs('Starting......: ufdbguardd-tail "'+cmd+'"');
   end else begin
       pid:=TAIL_PID_NUM();
       ufdb_admin_events('ufdbguardd-tail started pid '+pid,'TAIL_START','286');
       logs.DebugLogs('Starting......: ufdbguardd-tail started with new PID '+pid);

       if not noufdbg  then begin
          ufdb_admin_events('Reloading UfdbGuard daemon','TAIL_START','294');
          fpsystem('/etc/init.d/ufdb reconfig');
       end;
   end;

end;
//##############################################################################
procedure tufdbguardd.TAIL_STOP();
var
   pid:string;
   pidint,i:integer;
   count:integer;
   CountTail:Tstringlist;
begin
pid:=TAIL_PID_NUM();
if not SYS.PROCESS_EXIST(pid) then begin
      writeln('Stopping ufdbguardd-tail..........: Already stopped');
      CountTail:=Tstringlist.Create;
      try
         CountTail.AddStrings(SYS.PIDOF_PATTERN_PROCESS_LIST('/usr/bin/tail -f -n 0 '+TAIL_LOG_PATH));
         writeln('Stopping ufdbguardd-tail..........: Tail processe(s) number '+IntToStr(CountTail.Count));
      except
        writeln('Stopping ufdbguardd-tail..........: fatal error on SYS.PIDOF_PATTERN_PROCESS_LIST() function');
      end;

      count:=0;
     for i:=0 to CountTail.Count-1 do begin;
          pid:=CountTail.Strings[i];
          if count>100 then break;
          if not TryStrToInt(pid,pidint) then continue;
          ufdb_admin_events('Stopping ufdbguardd-tail pid'+pid,'TAIL_STOP','316');
          writeln('Stopping ufdbguardd-tail..........: Stop tail pid '+pid);
          if pidint>0 then  fpsystem('/bin/kill '+pid);
          sleep(100);
          inc(count);
      end;
      exit;
end;

writeln('Stopping ufdbguardd-tail..........: Stopping pid '+pid);
fpsystem('/bin/kill '+pid);

pid:=TAIL_PID_NUM();
if not SYS.PROCESS_EXIST(pid) then begin
      writeln('Stopping ufdbguardd-tail..........: Stopped');
end;


CountTail:=Tstringlist.Create;
CountTail.AddStrings(SYS.PIDOF_PATTERN_PROCESS_LIST('/usr/bin/tail -f -n 0 '+TAIL_LOG_PATH));
if CountTail.Count>0 then ufdb_admin_events('Stopping Tail processe(s) number '+IntToStr(CountTail.Count),'TAIL_STOP','336');
writeln('Stopping ufdbguardd-tail..........: Tail processe(s) number '+IntToStr(CountTail.Count));
count:=0;
     for i:=0 to CountTail.Count-1 do begin;
          pid:=CountTail.Strings[i];
          if count>100 then break;
          if not TryStrToInt(pid,pidint) then continue;
          writeln('Stopping ufdbguardd-tail..........: Stop tail pid '+pid);
          if pidint>0 then  fpsystem('/bin/kill '+pid);
          sleep(100);
          inc(count);
      end;


end;
//#####################################################################################
function tufdbguardd.STATUS():string;
var
pidpath:string;
begin
    if not FileExists(binpath) then exit;
   pidpath:=logs.FILE_TEMP();
   fpsystem(SYS.LOCATE_PHP5_BIN()+' /usr/share/artica-postfix/exec.status.php --ufdbguardd >'+pidpath +' 2>&1');
   result:=logs.ReadFromFile(pidpath);
   logs.DeleteFile(pidpath);
end;
//#########################################################################################
 function tufdbguardd.TAIL_PID_NUM():string;
begin
  result:=SYS.GET_PID_FROM_PATH('/etc/artica-postfix/exec.ufdbguard-tail.php.pid');
  if sys.verbosed then logs.Debuglogs(' ->'+result);
end;
 //##############################################################################
 function tufdbguardd.PID_NUM():string;
begin
  result:=SYS.GET_PID_FROM_PATH(PID_PATH());
  if sys.verbosed then logs.Debuglogs(' ->'+result);
  if length(result)=0 then result:=SYS.PIDOF_PATTERN(binpath);
  if not SYS.PROCESS_EXIST(result) then result:=SYS.PIDOF_PATTERN(binpath);
end;
 //##############################################################################
function tufdbguardd.PID_PATH():string;
begin
     exit('/var/tmp/ufdbguardd.pid');
end;
 //##############################################################################
 function tufdbguardd.VERSION():string;
var
   l:TstringList;
   i:integer;
   RegExpr:TRegExpr;
   tmpstr:string;
begin

    if length(binpath)=0 then exit;
    if Not Fileexists(binpath) then exit;
    result:=SYS.GET_CACHE_VERSION('APP_UFDBGUARD');
     if length(result)>2 then exit;
     if not FileExists(binpath) then exit;

    tmpstr:=logs.FILE_TEMP();
    fpsystem(binpath +' -v >'+tmpstr +' 2>&1');
    if not FileExists(tmpstr) then exit;
    l:=TstringList.Create;
    l.LoadFromFile(tmpstr);
    logs.DeleteFile(tmpstr);

    RegExpr:=TRegExpr.Create;
    RegExpr.Expression:='ufdbguardd.+?\s+([0-9\.]+)';
    for i:=0 to l.Count-1 do begin
         if RegExpr.Exec(l.Strings[i]) then begin
            result:=RegExpr.Match[1];
            break;
         end;
    end;
 SYS.SET_CACHE_VERSION('APP_UFDBGUARD',result);
l.free;
RegExpr.free;
end;
//##############################################################################

procedure tufdbguardd.ufdb_admin_events(textlog:string;sfunction:string;sline:string);

var
   xdate:string;
   xtring:string;
   md5:string;
begin
   xdate:=logs.DateTimeNowSQL();
   xtring:='a:6:{s:5:"zdate";s:19:"'+xdate+'";s:4:"text";s:16:"'+textlog+'";s:8:"function";s:20:"'+sfunction+'";s:4:"file";s:19:"artica-install";s:4:"line";s:16:"'+sline+'";s:8:"category";s:20:"Daemon";}';
   md5:=logs.MD5FromString(xtring);
   logs.WriteToFile(xtring,'/var/log/artica-postfix/ufdbguard_admin_events/'+md5+'.log');
end;






// ufdbguardd
end.
