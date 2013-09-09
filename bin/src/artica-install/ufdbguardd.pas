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
     binpath:string;
     procedure ufdb_admin_events(textlog:string;sfunction:string;sline:string);
public
    procedure   Free;
    constructor Create(const zSYS:Tsystem);

    function    VERSION():string;
    function    BIN_PATH():string;


END;

implementation

constructor tufdbguardd.Create(const zSYS:Tsystem);
begin

       LOGS:=tlogs.Create();
       SYS:=zSYS;
       binpath:=BIN_PATH();


end;
//##############################################################################
procedure tufdbguardd.free();
begin
    logs.Free;
end;
//##############################################################################
function tufdbguardd.BIN_PATH():string;
begin
result:=SYS.LOCATE_GENERIC_BIN('ufdbguardd');
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
