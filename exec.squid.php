<?php
$GLOBALS["DEBUG_INCLUDES"]=false;
$GLOBALS["ARGVS"]=implode(" ",$argv);
if(preg_match("#schedule-id=([0-9]+)#",implode(" ",$argv),$re)){$GLOBALS["SCHEDULE_ID"]=$re[1];}
if(posix_getuid()<>0){die("Cannot be used in web server mode\n\n");}
if(preg_match("#--includes#",implode(" ",$argv))){$GLOBALS["DEBUG_INCLUDES"]=true;}
if($GLOBALS["DEBUG_INCLUDES"]){echo basename(__FILE__)."::class.templates.inc\n";}
include_once(dirname(__FILE__).'/ressources/class.templates.inc');
include_once(dirname(__FILE__).'/ressources/class.squid.remote-stats-appliance.inc');
if($GLOBALS["DEBUG_INCLUDES"]){echo basename(__FILE__)."::class.ini.inc\n";}
include_once(dirname(__FILE__).'/ressources/class.ini.inc');
if($GLOBALS["DEBUG_INCLUDES"]){echo basename(__FILE__)."::class.squid.inc\n";}
include_once(dirname(__FILE__).'/ressources/class.squid.inc');
if($GLOBALS["DEBUG_INCLUDES"]){echo basename(__FILE__)."::framework/class.unix.inc\n";}
include_once(dirname(__FILE__).'/framework/class.unix.inc');
if($GLOBALS["DEBUG_INCLUDES"]){echo basename(__FILE__)."::frame.class.inc\n";}
include_once(dirname(__FILE__).'/framework/frame.class.inc');
include_once(dirname(__FILE__).'/ressources/class.mysql.inc');
include_once(dirname(__FILE__).'/ressources/class.squid.acls.inc');

$unix=new unix();
$sock=new sockets();
$GLOBALS["RELOAD"]=false;
$GLOBALS["VERBOSE"]=false;
$GLOBALS["NO_USE_BIN"]=false;
$GLOBALS["REBUILD"]=false;
$GLOBALS["FORCE"]=false;
$GLOBALS["OUTPUT"]=false;
$GLOBALS["AS_ROOT"]=true;
$GLOBALS["NOCACHES"]=false;
$GLOBALS["NOAPPLY"]=false;
$GLOBALS["NORELOAD"]=false;




WriteMyLogs("commands= ".implode(" ",$argv),"MAIN",__FILE__,__LINE__);
if(!is_file("/usr/share/artica-postfix/ressources/settings.inc")){shell_exec("/usr/share/artica-postfix/bin/process1 --force --verbose");}
if(preg_match("#--verbose#",implode(" ",$argv))){$GLOBALS["VERBOSE"]=true;}
if(preg_match("#--reload#",implode(" ",$argv))){$GLOBALS["NORELOAD"]=true;}
if(preg_match("#--noreload#",implode(" ",$argv))){$GLOBALS["NORELOAD"]=true;}
if(preg_match("#--rebuild#",implode(" ",$argv))){$GLOBALS["REBUILD"]=true;}
if(preg_match("#--force#",implode(" ",$argv))){$GLOBALS["FORCE"]=true;}
if(preg_match("#--output#",implode(" ",$argv))){$GLOBALS["OUTPUT"]=true;}
if(preg_match("#--withoutloading#",implode(" ",$argv))){$GLOBALS["NO_USE_BIN"]=true;$GLOBALS["NORELOAD"]=true;}
if(preg_match("#--nocaches#",implode(" ",$argv))){$GLOBALS["NOCACHES"]=true;}
if(preg_match("#--noapply#",implode(" ",$argv))){$GLOBALS["NOCACHES"]=true;$GLOBALS["NOAPPLY"]=true;$GLOBALS["FORCE"]=true;}


if($GLOBALS["VERBOSE"]){ini_set('display_errors', 1);	ini_set('html_errors',0);ini_set('display_errors', 1);ini_set('error_reporting', E_ALL);}



	$squidbin=$unix->find_program("squid3");
	$php5=$unix->LOCATE_PHP5_BIN();
	if(!is_file($squidbin)){$squidbin=$unix->find_program("squid");}
	$GLOBALS["SQUIDBIN"]=$squidbin;
	$GLOBALS["CLASS_USERS"]=new usersMenus();
	if($GLOBALS["VERBOSE"]){echo "squid binary=$squidbin\n";}
	
	
if($argv[1]=="--import-acls"){import_acls($argv[2]);return;}
if($argv[1]=="--import-webfilter"){import_webfilter($argv[2]);return;}	



if($argv[1]=="--notify-clients-proxy"){notify_remote_proxys();return;}
if($argv[1]=="--ping-clients-proxy"){notify_remote_proxys("PING");return;}
if($argv[1]=="--export-tables"){StatsApplianceExportTables();return;}	
if($argv[1]=="--reload-squid"){if($GLOBALS["VERBOSE"]){echo "reload in debug mode\n";} Reload_Squid();die();}
if($argv[1]=="--retrans"){retrans();die();}
if($argv[1]=="--certificate"){certificate_generate();die();}
if($argv[1]=="--caches"){BuildCaches();die();}
if($argv[1]=="--caches-reconstruct"){ReconstructCaches();die();}
if($argv[1]=="--compilation-params"){compilation_params();die();}
if($argv[1]=="--mysql-tpl"){DefaultTemplatesInMysql();die();}
if($argv[1]=="--tpl-save"){TemplatesInMysql();die();}
if($argv[1]=="--templates"){TemplatesInMysql();die();}
if($argv[1]=="--tpl-unique"){TemplatesUniqueInMysql($argv[2]);die();}
if($argv[1]=="--cache-infos"){caches_infos();die();}
if($argv[1]=="--writeinitd"){writeinitd();die();}
if($argv[1]=="--watchdog"){watchdog($direction);die();}
if($argv[1]=="--watchdog-config"){watchdog_config();die();}
if($argv[1]=="--build-schedules"){build_schedules();die();}
if($argv[1]=="--build-schedules-test"){build_schedules_tests();die();}
if($argv[1]=="--run-schedules"){run_schedules($argv[2]);die();}
if($argv[1]=="--schedules-extract"){extract_schedules();die();}
if($argv[1]=="--restart-squid"){restart_squid();die();}
if($argv[1]=="--restart-kav4proxy"){restart_kav4proxy();die();}
if($argv[1]=="--wrapzap"){wrapzap();die();}
if($argv[1]=="--wrapzap-compile"){wrapzap_compile();die();}
if($argv[1]=="--change-value"){change_value($argv[2],$argv[3]);die();}
if($argv[1]=="--smooth-build"){$GLOBALS["FORCE"]=true;build_smoothly();die();}
if($argv[1]=="--reconfigure-squid"){Reload_Squid();die();}
if($argv[1]=="--remove-cache"){remove_cache($argv[2]);die();}
if($argv[1]=="--rotate"){rotate_logs();die();}
if($argv[1]=="--replicate"){remote_appliance_restore_tables();die();}
if($argv[1]=="--banddebug"){bandwithdebug();die();}
if($argv[1]=="--acls"){output_acls();die();}
if($argv[1]=="--global-conf"){output_global_conf();die();}
if($argv[1]=="--remoteapp-conf"){remote_appliance_retreive_conf();die();}
if($argv[1]=="--remote-settings"){remote_appliance_getsettings();die();}
if($argv[1]=="--build-whitelists"){build_whitelist();die();}
if($argv[1]=="--check-temp"){CheckTempConfig();die();}
if($argv[1]=="--test-sarg"){test_sarg();die();}





// $EnableRemoteStatisticsAppliance -> Le proxy est un client.
// $EnableWebProxyStatsAppliance -> Le serveur est un serveur de statistiques.

$EnableKerbAuth=$sock->GET_INFO("EnableKerbAuth");
if(!is_numeric($EnableKerbAuth)){$EnableKerbAuth=0;}
$EnableWebProxyStatsAppliance=$sock->GET_INFO("EnableWebProxyStatsAppliance");
$EnableRemoteStatisticsAppliance=$sock->GET_INFO("EnableRemoteStatisticsAppliance");

if(!is_numeric($EnableRemoteStatisticsAppliance)){$EnableRemoteStatisticsAppliance=0;}
if(!is_numeric($EnableWebProxyStatsAppliance)){$EnableWebProxyStatsAppliance=0;}
$UnlockWebStats=$sock->GET_INFO("UnlockWebStats");
if(!is_numeric($UnlockWebStats)){$UnlockWebStats=0;}
$users=new usersMenus();
if($users->WEBSTATS_APPLIANCE){$EnableWebProxyStatsAppliance=1;
$sock->SET_INFO("$EnableWebProxyStatsAppliance",1);}
if($EnableWebProxyStatsAppliance==1){notify_remote_proxys();}
if($UnlockWebStats==1){$EnableRemoteStatisticsAppliance=0;}

//request_header_max_size




if($argv[1]=="--reconfigure"){
		$EXEC_PID_FILE="/etc/artica-postfix/".basename(__FILE__).".reconfigure.pid";
		$unix=new unix();
		
		$oldpid=@file_get_contents($EXEC_PID_FILE);
		if($unix->process_exists($oldpid,basename(__FILE__))){
			$timefile=$unix->file_time_min($EXEC_PID_FILE);
			if($timefile<15){print "Starting......: Checking (L.".__LINE__.") squid Already executed pid $oldpid {$timefile}Mn...\n";die();}
			
		}		
	squid_reconfigure_build_tool();	
	$q=new mysql_squid_builder();
	$q->CheckDefaultSchedules();	
	@file_put_contents($EXEC_PID_FILE, posix_getpid());
	ApplyConfig();
	BuildCaches(true);
	certificate_generate();
	echo "Starting......: Check files and security...\n";
	CheckFilesAndSecurity();
	echo "Starting......: Reloading SQUID...\n";
	Reload_Squid();
	build_sslpasswords();
	writeinitd();
	exec("/usr/share/artica-postfix/bin/artica-install --squid-reload");
	writelogs("reload Dansguardian (if enabled)",__FUNCTION__,__FILE__,__LINE__);
	echo "Starting......: Reloading Dansguardian (if enabled)\n";
	exec("/usr/share/artica-postfix/bin/artica-install --reload-dansguardian");
	writelogs("reload c-icap (if enabled)",__FUNCTION__,__FILE__,__LINE__);
	echo "Starting......: Reloading c-icap (if enabled)\n";
	exec("/usr/share/artica-postfix/bin/artica-install --c-icap-reload");
	writelogs("reload Kav4Proxy (if enabled)",__FUNCTION__,__FILE__,__LINE__);
	echo "Starting......: Reloading Kaspersky (if enabled)\n";
	exec("/usr/share/artica-postfix/bin/artica-install --reload-kav4proxy");
	die();
}

function WriteToSyslog_execsquid($text){
	if(!function_exists("syslog")){return;}
	$LOG_SEV=LOG_INFO;
	openlog("stats-appliance", LOG_PID , LOG_SYSLOG);
	syslog($LOG_SEV, $text);
	closelog();

}

if($argv[1]=="--build"){
		$unix=new unix();
		
		$squidbin=$unix->LOCATE_SQUID_BIN();
		if(!is_file($squidbin)){
			echo "Starting......: [SERV]: Unable to stat squid binary, aborting..\n";
			die();
		}
		
		
		$EXEC_TIME_FILE="/etc/artica-postfix/".basename(__FILE__).".build.time";
		if(!$GLOBALS["FORCE"]){
			$time=$unix->file_time_min($EXEC_TIME_FILE);
			if($time==0){
				echo "Starting......: [SERV]: Only one config per minute...\n";
				die();
			}
			
		}
		@unlink($EXEC_TIME_FILE);
		@file_put_contents($EXEC_TIME_FILE, time());
		
	
		$TimeStart=time();
		$EXEC_PID_FILE="/etc/artica-postfix/".basename(__FILE__).".build.pid";
		
		$kill=$unix->find_program("kill");
		$oldpid=@file_get_contents($EXEC_PID_FILE);
		if($unix->process_exists($oldpid,basename(__FILE__))){
			$TimePid=$unix->PROCCESS_TIME_MIN($oldpid);
			if($TimePid>30){
				posix_kill(intval($oldpid),9);
			}else{
				if(!$GLOBALS["FORCE"]){
					print "Starting......: Checking (L.".__LINE__.") Squid Already executed pid $oldpid since {$TimePid}mn ...\n";
					die();
				}
			}
		}

		echo "Starting......: [SERV]: is connected to remote appliance ? `$EnableRemoteStatisticsAppliance`\n";
		
		if($EnableRemoteStatisticsAppliance==1){
			squid_admin_notifs("Start to rebuilding Proxy settings","MAIN",__FILE__,__LINE__);
			$r=new squid_stats_appliance();
			echo "Starting......: [SERV]: ################################\n";
			echo "Starting......: [SERV]: # This server is connected to: #\n";
			echo "Starting......: [SERV]: # $r->URI #\n";
			echo "Starting......: [SERV]: ################################\n";
			remote_appliance_restore_tables();
		}
		
		squid_reconfigure_build_tool();
		$childpid=posix_getpid();
		$sock=new sockets();
		$squid_user=SquidUser();
		$SQUID_CONFIG_PATH=$unix->SQUID_CONFIG_PATH();	
		$PHP=LOCATE_PHP5_BIN2();	
		$NOHUP=$unix->find_program("nohup");
		writeinitd();
		@file_put_contents($EXEC_PID_FILE,$childpid);
		if(is_file("/etc/squid3/mime.conf")){shell_exec("/bin/chown squid:squid /etc/squid3/mime.conf");}
		$EnableKerbAuth=$sock->GET_INFO("EnableKerbAuth");
		if(!is_numeric("$EnableKerbAuth")){$EnableKerbAuth=0;}	
		
		if(!is_dir("/usr/share/squid-langpack")){TemplatesInMysql(true);exit;}
		echo "Starting......: Checking squid kerberos authentification is set to $EnableKerbAuth\n";
		echo "Starting......: Checking squid certificate\n";
		checkdatabase();
		certificate_generate();
		remote_appliance_restore_tables();
		echo "Starting......: Instanciate squid library..\n";
		$squid=new squidbee();
		$squidbin=$unix->find_program("squid3");
		echo "Starting......: checking squid binaries..\n";
		if(!is_file($squidbin)){$squidbin=$unix->find_program("squid");}
		echo "Starting......: Binary: $squidbin\n";
		echo "Starting......: Config: $SQUID_CONFIG_PATH\n";
		echo "Starting......: User..: $squid_user\n";
		echo "Starting......: Checking blocked sites\n";
		shell_exec("$NOHUP $PHP ".basename(__FILE__)."/exec.squid.netads.php >/dev/null 2>&1 &");
		$squid->BuildBlockedSites();
		echo "Starting......: Checking FTP ACLs\n";
		acl_clients_ftp();
		echo "Starting......: Checking Whitelisted browsers\n";
		acl_whitelisted_browsers();
		acl_allowed_browsers();
		echo "Starting......: Checking wrapzap\n";
		wrapzap();
		echo "Starting......: Building master configuration\n";
		$squid->ASROOT=true;		
		ApplyConfig();
		if($GLOBALS["NOAPPLY"]){return;}
		echo "Starting......: Checking Watchdog\n";
		watchdog_config();
		errors_details_txt();
		BuildCaches(true);
		CheckFilesAndSecurity();
		build_schedules(true);
		build_sslpasswords();
		writeinitd();
		
		Reload_Squid();
		$BuildAllTemplatesDone=$sock->GET_INFO("BuildAllTemplatesDone");
		if(!is_numeric($BuildAllTemplatesDone)){$BuildAllTemplatesDone=0;}
		if($BuildAllTemplatesDone==0){
			echo "Starting......: scheduling Building templates\n";
			sys_THREAD_COMMAND_SET("$PHP ". __FILE__." --tpl-save");
			$sock->SET_INFO("BuildAllTemplatesDone", 1);
		}
		
		echo "Starting......: Done (Took: ".$unix->distanceOfTimeInWords($TimeStart,time()).")\n";
		die();
	}
	
	
writelogs("Unable to understand:`".@implode(" ", $argv)."`","MAIN",__FILE__,__LINE__);	
	
	
function change_value($key,$val){
	$squid=new squidbee();
	$squid->global_conf_array[$key]=$val;
	$squid->SaveToLdap();
	echo "Starting......: Squid change $key to $val (squid will be restarted)\n";
	
}


function build_sslpasswords(){
	
	$q=new mysql();
	$sql="SELECT `keyPassword`,`CommonName` FROM sslcertificates WHERE LENGTH(keyPassword)>0";
	$results=$q->QUERY_SQL($sql,"artica_backup");
	while ($ligne = mysql_fetch_assoc($results)) {
		$array["/etc/squid3/{$ligne["CommonName"]}.key"]=$ligne["keyPassword"];
	}
	@file_put_contents("/etc/squid3/sslpass", serialize($array));
	
}

function output_global_conf(){
	$sock=new sockets();
	echo $sock->GET_INFO("ArticaSquidParameters");
	
}

function build_smoothly(){
		$unix=new unix();
		remote_appliance_restore_tables();
		$squid=new squidbee();
		ApplyConfig(true);
		Reload_Squid();
	
}

function remote_appliance_retreive_conf(){
	$unix=new unix();
	$sock=new sockets();
	$UnlockWebStats=$sock->GET_INFO("UnlockWebStats");
	if(!is_numeric($UnlockWebStats)){$UnlockWebStats=0;}
	if($UnlockWebStats==1){return;}
	
	
	$EnableRemoteStatisticsAppliance=$sock->GET_INFO("EnableRemoteStatisticsAppliance");
	if(!is_numeric($EnableRemoteStatisticsAppliance)){$EnableRemoteStatisticsAppliance=0;}	
	if($EnableRemoteStatisticsAppliance==0){$GLOBALS[__FUNCTION__."_EXECUTED"]=true;return;}
	echo "Starting......: [SYS]: Squid check -> squid_stats_appliance()\n";
	$s=new squid_stats_appliance();
	echo "Starting......: [SERV] Replicate settings from the remote appliance...\n";
	$s->REPLICATE_ETC_ARTICA_CONFS();
	$s->Replicate();
	echo "Starting......: Replicate all settings from the remote appliance done...\n";	
		
	
}
function remote_appliance_getsettings(){
	$sock=new sockets();
	$uuid=$sock->getFrameWork("services.php?GetMyHostId=yes");
	echo "UUID:$uuid\n";
	
	$sq=new squid_stats_appliance();
	$array=$sq->GetSquidDefinedSettings();
	
	
	if(is_array($array)){
		$sock=new sockets();
		while (list ($key, $val) = each ($array) ){
			if($key=="uuid"){continue;}
			echo "Starting......: Replicate $key = `$val`\n";
			$sock->SET_INFO($key, $val);
		}
	}
	
}

function remote_appliance_restore_tables(){
	if(isset($GLOBALS[__FUNCTION__."_EXECUTED"])){return;}
	$unix=new unix();
	$sock=new sockets();
	$EnableRemoteStatisticsAppliance=$sock->GET_INFO("EnableRemoteStatisticsAppliance");
	if(!is_numeric($EnableRemoteStatisticsAppliance)){$EnableRemoteStatisticsAppliance=0;}	
	if($EnableRemoteStatisticsAppliance==0){$GLOBALS[__FUNCTION__."_EXECUTED"]=true;return;}
	echo "Starting......: [SYS]: Squid check if configuration must be retreived\n";
	remote_appliance_retreive_conf();
	$php5=$unix->LOCATE_PHP5_BIN();
	$nohup=$unix->find_program("nohup");
	$dirname=dirname(__FILE__);
	$GLOBALS[__FUNCTION__."_EXECUTED"]=true;
}



function CheckFilesAndSecurity(){
	if(isset($GLOBALS[__FUNCTION__."_EXECUTED"])){return;}
	$GLOBALS[__FUNCTION__."_EXECUTED"]=true;
	$squid_user=SquidUser();
	$unix=new unix();
	$chown=$unix->find_program("chown");
	$chmod=$unix->find_program("chmod");
	$squid_user=SquidUser();
	$ln=$unix->find_program("ln");
	$rm=$unix->find_program("rm");
	if(!is_dir("/var/logs")){@mkdir("/var/logs",0755,true);}
	
	if(!is_dir("/home/squid/cache/cache-default/00")){
			@mkdir("/home/squid/cache/cache-default",0755,true);
			shell_exec("$chown $squid_user /home/squid/cache/cache-default >/dev/null 2>&1");
			shell_exec("$chown $squid_user /home/squid/cache/ >/dev/null 2>&1");
			shell_exec("$chown $squid_user /home/squid >/dev/null 2>&1");
			exec("{$GLOBALS["SQUIDBIN"]} -z 2>&1",$results);
	}
	@mkdir("/var/lib/squid/session",0755,true);
	@mkdir("/var/squid/cache",0755,true);
	@mkdir("/var/lib/ssl_db",0755,true);
	if(!is_dir("/var/run/squid")){@mkdir("/var/run/squid",0755,true);}
	@mkdir("/var/log/squid/squid",0755,true);
	if(!is_file("/var/logs/cache.log")){@file_put_contents("/var/logs/cache.log", "\n");}
	if(!is_dir("/usr/share/squid3/errors/lb-lu")){shell_exec("$ln -sf /usr/share/squid3/errors/en-us /usr/share/squid3/errors/lb-lu");}
	
	$unix->chown_func($squid_user, $squid_user,"/var/squid/cache");
	$unix->chown_func($squid_user, $squid_user,"/var/lib/squid/session");
	$unix->chown_func($squid_user, $squid_user,"/etc/squid3/*");
	$unix->chown_func($squid_user, $squid_user,"/var/run/squid");
	$unix->chown_func($squid_user, $squid_user,"/var/log/squid/*");
	$unix->chown_func($squid_user, $squid_user,"/var/logs");
	$unix->chown_func($squid_user, $squid_user,"/var/lib/ssl_db");
	$unix->chown_func($squid_user, $squid_user,"/var/logs/cache.log");
	$unix->chown_func($squid_user, $squid_user,"/home/squid/cache");
	$unix->chown_func($squid_user, $squid_user,"/home/squid");
	$unix->chmod_func(0755, "/var/run/squid");
	$unix->chmod_func(0755, "/home/squid");
	$unix->chmod_func(0755, "/home/squid/cache");
	$squid_locate_pinger=$unix->squid_locate_pinger();
	if(is_file($squid_locate_pinger)){
		shell_exec("$chmod 04755 $squid_locate_pinger");
		$unix->chown_func($squid_user, $squid_user,$squid_locate_pinger);
	}
	

	
	$GetCachesInsquidConf=$unix->SQUID_CACHE_FROM_SQUIDCONF();
	while (list ($CacheDirectory, $type) = each ($GetCachesInsquidConf)){
	
		if(trim($CacheDirectory)==null){continue;}
		if(!is_dir($CacheDirectory)){continue;}
		$unix->chown_func("squid","squid",$CacheDirectory);
		$unix->chown_func("squid","squid","$CacheDirectory/*");
		$unix->chmod_func(0777, "$CacheDirectory/*");
		$unix->chmod_alldirs(0755, $CacheDirectory);
		@chmod($CacheDirectory, 0755);
	}
	
		
	if(is_dir("/usr/share/squid-langpack")){$unix->chown_func($squid_user,$squid_user,"/usr/share/squid-langpack");}
	if(!is_file("/var/log/squid/squidGuard.log")){@file_put_contents("/var/log/squid/squidGuard.log","#");}
	
	
	if(!is_file("/etc/squid3/squid-block.acl")){@file_put_contents("/etc/squid3/squid-block.acl","");}
	if(!is_file("/etc/squid3/clients_ftp.acl")){@file_put_contents("/etc/squid3/clients_ftp.acl","");}
	if(!is_file("/etc/squid3/allowed-user-agents.acl")){@file_put_contents("/etc/squid3/allowed-user-agents.acl","");}	
	
	$unix->Winbindd_privileged_SQUID();
	
	$tpls["ERR_CONFLICT_HOST"]="<!DOCTYPE html PUBLIC \"-//W3C//DTD HTML 4.01//EN\" \"http://www.w3.org/TR/html4/strict.dtd\"> <html><head> <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\"> <title>ERROR: The requested URL could not be retrieved</title> <style type=\"text/css\"><!--   %l  body :lang(fa) { direction: rtl; font-size: 100%; font-family: Tahoma, Roya, sans-serif; float: right; } :lang(he) { direction: rtl; }  --></style> </head><body id=%c> <div id=\"titles\"> <h1>ERROR</h1> <h2>The requested URL could not be retrieved</h2> </div> <hr>  <div id=\"content\"> <p>The following error was encountered while trying to retrieve the URL: <a href=\"%U\">%U</a></p>  <blockquote id=\"data\"> <pre>URI Host Conflict</pre> </blockquote>  <p>This means the domain name you are trying to access apparently no longer exists on the machine you are requesting it from.</p>  <p>Some possible problems are:</p> <ul> <li>The domain may have moved very recently. Trying again will resolve that.</li> <li>The website may require you to use a local country-based version. Using your ISP provided DNS server(s) should resolve that.</li> </ul>  <p>Your cache administrator is <a href=\"mailto:%w%W\">%w</a>.</p> <br> </div>  <hr> <div id=\"footer\"> <p>Generated %T by %h (%s)</p> <!-- %c --> </div> </body></html>";
	$tpls["MGR_INDEX"]="\n";
	
	while (list ($file, $lined) = each ($tpls)){
		if(!is_file("/usr/share/squid-langpack/en/$file")){@file_put_contents("/usr/share/squid-langpack/en/$file", $lined);}
		if(!is_file("/usr/share/squid3/errors/templates/$file")){@file_put_contents("/usr/share/squid3/errors/templates/$file",$lined);}
	}
	
	$ssl_crtd=locate_ssl_crtd();
	if(!is_file("/var/lib/ssl_db/index.txt")){
		if(is_file($ssl_crtd)){
			if(is_dir("/var/lib/ssl_db")){shell_exec("$rm -rf /var/lib/ssl_db");}
			shell_exec("$ssl_crtd -c -s /var/lib/ssl_db");
			$unix->chown_func($squid_user, $squid_user,"/var/lib/ssl_db/*");
		}else{
			echo "Starting......: unable to stat ssl_crtd to fill `/var/lib/ssl_db`\n";	
		}
	}
}




function watchdog($direction){
	$EXEC_PID_FILE="/etc/artica-postfix/".basename(__FILE__).".".__FUNCTION__.".$direction.pid";
	$unix=new unix();
	if($unix->process_exists(@file_get_contents($EXEC_PID_FILE))){
		writelogs("Starting......: Checking squid $direction executed pid ". @file_get_contents($EXEC_PID_FILE)."...",__FUNCTION__,__FILE__,__LINE__);
		return;
	}
	$childpid=posix_getpid();
	@file_put_contents($EXEC_PID_FILE,$childpid);

	if($direction=="start"){
		shell_exec("/etc/init.d/artica-postfix start squid-cache");
		return;
	}
	
	if($direction=="stop"){
		shell_exec("/etc/init.d/artica-postfix stop squid-cache");
		return;
	}	
	
}


function locate_ssl_crtd(){
	return locate_generic_bin("ssl_crtd");

	
}

function locate_generic_bin($program){
	$unix=new unix();
	return $unix->squid_locate_generic_bin($program);
	
}



function remove_cache($cacheenc){
	$unix=new unix();
	$nohup=$unix->find_program("nohup");
	$sock=new sockets();
	$PidFile="/etc/artica-postfix/pids/".md5("remove-$cacheenc").".pid";
	
	
	$oldpid=$unix->get_pid_from_file($PidFile);
	if($unix->process_exists($oldpid,basename(__FILE__))){
		WriteToSyslogMail("remove_cache():: Another artica script running pid $oldpid, aborting ...", basename(__FILE__));
		return;
	}

	$directory=base64_decode($cacheenc);
	if(!is_dir($directory)){WriteToSyslogMail("remove_cache():: $directory no such directory", basename(__FILE__));return;}
	$rm=$unix->find_program("rm");
	shell_exec("$rm -rf $directory");
	ApplyConfig();
	shell_exec('/etc/init.d/artica-postfix restart squid-cache');
	caches_infos();
	
}

function Start_squid(){
	system("/usr/share/artica-postfix/bin/artica-install -watchdog squid-cache --without-config");
	system("/etc/init.d/auth-tail restart");
}


function urlrewriteaccessdeny(){
	$q=new mysql();
	$q2=new mysql_squid_builder();
	$acl=new squid_acls();
	$sql="SELECT * FROM urlrewriteaccessdeny";
	$results = $q->QUERY_SQL($sql,"artica_backup");
	if(!$q->ok){
		echo "Starting......: [ACLS]: $q->mysql_error\n";
		return;
	}
	$f=array();
	$FAMILIES=array();
	$url_rewrite_program=array();
	while ($ligne = mysql_fetch_assoc($results)) {
		if($ligne["items"]==null){continue;}
		$website=strtolower(trim($ligne["items"]));
		$website=$acl->dstdomain_parse($website);
		if($website==null){continue;}
		$familysite=$q2->GetFamilySites($website);
		echo "Starting......: [ACLS]: $website -> $familysite\n";
		$FINALARRAY[$familysite][$website]=true;
	
	}
	
	while (list ($familysite, $websites) = each ($FINALARRAY) ){
		if(isset($websites[$familysite])){
			if(substr($familysite, 0,1)<>"."){$familysite=".$familysite";}
			$f[]=$familysite;
			continue;
		}
		while (list ($sitename, $none) = each ($websites) ){
			if(substr($sitename, 0,1)<>"."){$sitename=".$sitename";}
			$f[]=$sitename;
		}
		
		
	}
	
	while (list ($index, $website) = each ($f) ){
		$website=trim(strtolower($website));
		if($website==null){continue;}
		$website=$acl->dstdomain_parse($website);
		$url_rewrite_program[]=$website;
	}
	
	echo "Starting......: [ACLS]: ".count($url_rewrite_program)." Whitelisted webistes from webfiltering\n";
	@file_put_contents("/etc/squid3/url_rewrite_program.deny.db", @implode("\n", $url_rewrite_program)."\n");
	@chown("/etc/squid3/url_rewrite_program.deny.db", "squid");
	@chgrp("/etc/squid3/url_rewrite_program.deny.db","squid");	
	
}

function build_whitelist(){
	urlrewriteaccessdeny();
	Reload_only_squid();
}

function Reload_only_squid(){
	$unix=new unix();
	if(!is_file($GLOBALS["SQUIDBIN"])){
		$GLOBALS["SQUIDBIN"]=$unix->find_program("squid");
		if(!is_file($GLOBALS["SQUIDBIN"])){$GLOBALS["SQUIDBIN"]=$unix->find_program("squid3");}
	}	
	
	if(function_exists("debug_backtrace")){
		$trace=debug_backtrace();
		if(isset($trace[1])){
			$file=basename($trace[1]["file"]);
			$function=$trace[1]["function"];
			$line=$trace[1]["line"];
			$called="Called by $function() from line $line";
		}
			
	}
	
	
	squid_watchdog_events("Reconfiguring Proxy parameters...");
	squid_admin_mysql(2, "Reconfiguring squid-cache","$called");
	exec("{$GLOBALS["SQUIDBIN"]} -k reconfigure 2>&1",$results);
	
}
function squid_watchdog_events($text){
	$unix=new unix();
	if(function_exists("debug_backtrace")){$trace=debug_backtrace();if(isset($trace[1])){$sourcefile=basename($trace[1]["file"]);$sourcefunction=$trace[1]["function"];$sourceline=$trace[1]["line"];}}
	$unix->events($text,"/var/log/squid.watchdog.log",false,$sourcefunction,$sourceline);
}

function Reload_Squid(){
	if($GLOBALS["NORELOAD"]){return;}
	echo "Starting......: Reloading proxy service...\n";
	EventsWatchdog("Reloading proxy service");
	$unix=new unix();
	$nohup=$unix->find_program("nohup");
	$php5=$unix->LOCATE_PHP5_BIN();
	$chmod=$unix->find_program("chmod");
	$sock=new sockets();
	$executed=null;
	WriteToSyslogMail("Reload_Squid():: Ask to reload squid", basename(__FILE__));
	$SquidCacheReloadTTL=$sock->GET_INFO("SquidCacheReloadTTL");
	if(!is_numeric($SquidCacheReloadTTL)){$SquidCacheReloadTTL=10;}
	$TimeFile="/etc/artica-postfix/pids/reloadsquid.time";
	$PidFile="/etc/artica-postfix/pids/reloadsquid.pid";
	$SystemInfoCache="/etc/squid3/squid_get_system_info.db";
	$TimeMin=$unix->file_time_min($TimeFile);
	$EnableWebProxyStatsAppliance=$sock->GET_INFO("EnableWebProxyStatsAppliance");
	$EnableRemoteStatisticsAppliance=$sock->GET_INFO("EnableRemoteStatisticsAppliance");
	$EnableKerbAuth=$sock->GET_INFO("EnableKerbAuth");
	
	if(!is_numeric($EnableWebProxyStatsAppliance)){$EnableWebProxyStatsAppliance=0;}
	if(!is_numeric($EnableRemoteStatisticsAppliance)){$EnableRemoteStatisticsAppliance=0;}
	if(!is_numeric($EnableKerbAuth)){$EnableKerbAuth=0;}
	
	@unlink($SystemInfoCache);

	if(!is_file("/etc/squid3/url_rewrite_program.deny.db")){
		@file_put_contents("/etc/squid3/url_rewrite_program.deny.db", "");

	}	
	
	@chown("/etc/squid3/url_rewrite_program.deny.db", "squid");
	@chgrp("/etc/squid3/url_rewrite_program.deny.db","squid");
		
	$oldpid=$unix->get_pid_from_file($PidFile);
	if($oldpid<>getmypid()){
		if($unix->process_exists($oldpid,basename(__FILE__))){
			echo "Starting......: [RELOAD]: Another artica script running pid $oldpid, aborting ...\n";
			WriteToSyslogMail("Reload_Squid():: Another artica script running pid $oldpid, aborting ...", basename(__FILE__));
			return;
		}
	}
	
	
	if(!is_file($GLOBALS["SQUIDBIN"])){
		$GLOBALS["SQUIDBIN"]=$unix->find_program("squid");
		if(!is_file($GLOBALS["SQUIDBIN"])){$GLOBALS["SQUIDBIN"]=$unix->find_program("squid3");}
	}
	
	echo "Starting......: [RELOAD]: Checking transparent mode..\n";
	shell_exec("$php5 ". dirname(__FILE__)."/exec.squid.transparent.php");
	
	if($EnableKerbAuth==1){
		echo "Starting......: [RELOAD]: Checks winbind privileges\n";
		shell_exec("$php5 /usr/share/artica-postfix/exec.winbindd.php --privs-squid");
	}	

	$unix->THREAD_COMMAND_SET("/etc/init.d/auth-tail restart");
	
	$pid=$unix->get_pid_from_file($unix->LOCATE_SQUID_PID());
	if(!$unix->process_exists($pid)){
		
		@unlink($TimeFile);
		@file_put_contents($TimeFile, time());
		@file_put_contents($PidFile, getmypid());
		echo "Starting......: [RELOAD]: Squid is not running, start it\n";
		WriteToSyslogMail("Reload_Squid():: Squid is not running, start it...", basename(__FILE__));
		if(function_exists("debug_backtrace")){$trace=debug_backtrace();if(isset($trace[1])){$sourcefunction=$trace[1]["function"];$sourceline=$trace[1]["line"];$executed="Executed by $sourcefunction() line $sourceline\nusing argv:{$GLOBALS["ARGVS"]}\n";}}
		squid_admin_notifs("Squid is not running, start it\n$executed", __FUNCTION__, __FILE__, __LINE__, "proxy");
		squid_admin_mysql(1, "Change Reload to start Squid is not running", "$executed");
		Start_squid();
		return;
	}
	
	if(!$GLOBALS["FORCE"]){
	if($TimeMin<$SquidCacheReloadTTL){
		squid_admin_mysql(2, "Reload squid PID $pid aborted", "need at least {$SquidCacheReloadTTL}mn current {$TimeMin}mn");
		echo "Starting......: [RELOAD]: Reload squid PID $pid aborted, need at least {$SquidCacheReloadTTL}mn current {$TimeMin}mn\n";
		WriteToSyslogMail("Reload_Squid():: Reload squid PID $pid aborted, need at least {$SquidCacheReloadTTL}mn current {$TimeMin}mn", basename(__FILE__));
		
		return;
		}
	}
	
	@unlink($TimeFile);
	@file_put_contents($TimeFile, time());
	@file_put_contents($PidFile, getmypid());
	
	
	if($GLOBALS["NO_USE_BIN"]){return;}
	if(!is_file($GLOBALS["SQUIDBIN"])){
		$GLOBALS["SQUIDBIN"]=$unix->find_program("squid");
		if(!is_file($GLOBALS["SQUIDBIN"])){$GLOBALS["SQUIDBIN"]=$unix->find_program("squid3");}
	}
	
	if(!is_file($GLOBALS["SQUIDBIN"])){
		WriteToSyslogMail("Reload_Squid():: Fatal,unable to find a suitable squid binary", basename(__FILE__));
		return;
	}
	
	EventsWatchdog("Reloading Squid");
	WriteToSyslogMail("Reload_Squid():: reloading Squid", basename(__FILE__));
	echo "Starting......: [RELOAD]: Reloading {$GLOBALS["SQUIDBIN"]} PID $pid\n";
	squid_watchdog_events("Reconfiguring Proxy parameters...");
	
	if(function_exists("debug_backtrace")){$trace=debug_backtrace();if(isset($trace[1])){$file=basename($trace[1]["file"]);$function=$trace[1]["function"];$line=$trace[1]["line"];$called="Called by $function() from line $line";}}
	squid_admin_mysql(2, "Reconfiguring squid-cache","$called");
	
	
	
	$results=array();
	exec("{$GLOBALS["SQUIDBIN"]} -k reconfigure 2>&1",$results);
	while (list ($num, $val) = each ($results)){
		
		if(preg_match("#ERROR: No running copy#",$val)){
			if(function_exists("debug_backtrace")){$trace=debug_backtrace();if(isset($trace[1])){$sourcefunction=$trace[1]["function"];$sourceline=$trace[1]["line"];$executed="Executed by $sourcefunction() line $sourceline\nusing argv:{$GLOBALS["ARGVS"]}\n";}}
			squid_admin_notifs("Stopping squid instances in memory\n$executed", __FUNCTION__, __FILE__, __LINE__, "proxy");			
			echo "Starting......: [RELOAD]: Stopping squid instances in memory\n";
			KillSquid();
			break;
		}
		
		if(preg_match("#WARNING:\s+#i", $val)){continue;}
		
		
		echo "Starting......: $val\n";
	}
	
	if(function_exists("debug_backtrace")){$trace=debug_backtrace();if(isset($trace[1])){$sourcefunction=$trace[1]["function"];$sourceline=$trace[1]["line"];$executed="Executed by $sourcefunction() line $sourceline\nusing argv:{$GLOBALS["ARGVS"]}\n";}}
	
	
	if($EnableWebProxyStatsAppliance==1){
		if(is_file("/etc/init.d/syslog")){
			$results=array();
			echo "Starting......: [RELOAD]: Reloading syslog engine\n";
			@chmod("/etc/init.d/syslog",0755);
			shell_exec("$nohup /etc/init.d/syslog reload >/dev/null 2>&1 &");
		}
	}
	echo "Starting......: [RELOAD]: Reloading artica-status\n";
	shell_exec("$nohup $php5 /etc/init.d/artica-postfix restart artica-status >/dev/null 2>&1 &");
	shell_exec("$nohup $php5 /usr/share/artica-postfix/exec.clean.logs.php --squid-caches --force >/dev/null 2>&1 &");
	
	if($EnableRemoteStatisticsAppliance==1){
		echo "Starting......: [RELOAD]: Sends information to the Statistics Appliance...\n";
		shell_exec("$nohup $php5 /usr/share/artica-postfix/exec.netagent.php >/dev/null 2>&1 &");
	}else{
		echo "Starting......: [RELOAD]: Reloading Proxy Watchdog events...\n";
		shell_exec("$nohup /etc/init.d/auth-tail restart >/dev/null 2>&1 &");
	}

	
	
}

function KillSquid(){
	$unix=new unix();
	$pidof=$unix->find_program("pidof");
	$kill=$unix->find_program("kill");
	if(strlen($pidof)<4){return;}
	exec("$pidof {$GLOBALS["SQUIDBIN"]}",$results);
	$f=explode(" ",@implode("",$results));
	while (list ($num, $val) = each ($f)){
		$val=trim($val);
		if(!is_numeric($val)){continue;}
		echo "Starting......: stopping pid $val\n";
		$unix->KILL_PROCESS($val,9);
		usleep(10000);
	}
	
	
}


function squidclamav(){
	$squid=new squidbee();
	$sock=new sockets();
	$unix=new unix();
	if(!isset($GLOBALS["CLASS_USERS"])){$GLOBALS["CLASS_USERS"]=new usersMenus();}
	$users=$GLOBALS["CLASS_USERS"];
	$SquidGuardIPWeb=$sock->GET_INFO("SquidGuardIPWeb");
	if($SquidGuardIPWeb==null){$SquidGuardIPWeb="http://$users->hostname:9020/exec.squidguard.php";}
	
	
	$conf[]="squid_ip 127.0.0.1";
	$conf[]="squid_port $squid->listen_port";
	$conf[]="logfile /var/log/squid/squidclamav.log";
	$conf[]="debug 0";
	$conf[]="stat 0";
	$conf[]="clamd_local ".$unix->LOCATE_CLAMDSOCKET();
	$conf[]="#clamd_ip 192.168.1.5";
	$conf[]="#clamd_port 3310";
	$conf[]="maxsize 5000000";
	$conf[]="redirect $SquidGuardIPWeb";
	if($squid->enable_squidguard==1){
		$conf[]="squidguard $users->SQUIDGUARD_BIN_PATH";
	}else{
		if($squid->enable_UfdbGuard==1){
			$conf[]="squidguard $users->ufdbgclient_path";
		}
	}
	$conf[]="maxredir 30";
	$conf[]="timeout 60";
	$conf[]="useragent Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1)";
	$conf[]="trust_cache 1";
	$conf[]="";
	$conf[]="# Do not scan standard HTTP images";
	$conf[]="abort ^.*\.(ico|gif|png|jpg)$";
	$conf[]="abortcontent ^image\/.*$";
	$conf[]="# Do not scan text and javascript files";
	$conf[]="abort ^.*\.(css|xml|xsl|js|html|jsp)$";
	$conf[]="abortcontent ^text\/.*$";
	$conf[]="abortcontent ^application\/x-javascript$";
	$conf[]="# Do not scan streaming videos";
	$conf[]="abortcontent ^video\/mp4";
	$conf[]="abortcontent ^video\/x-flv$";
	$conf[]="# Do not scan pdf and flash";
	$conf[]="#abort ^.*\.(pdf|swf)$";
	$conf[]="";
	$conf[]="# Do not scan sequence of framed Microsoft Media Server (MMS)";
	$conf[]="abortcontent ^.*application\/x-mms-framed.*$";
	$conf[]="";
	$conf[]="# White list some sites";
	$conf[]="whitelist .*\.clamav.net";	
	@file_put_contents("/etc/squidclamav.conf",@implode("\n",$conf));
	echo "Starting......: Squid building squidclamav.conf configuration done\n";
}

function GetLocalCaches(){
	$unix=new unix();	
	return $unix->SQUID_CACHE_FROM_SQUIDCONF();
}



function ReconstructCaches(){
	$squid=new squidbee();
	$unix=new unix();	
	$main_cache=$squid->CACHE_PATH;
	echo "Starting......:  reconstruct caches\n";
	$GetCachesInsquidConf=$unix->SQUID_CACHE_FROM_SQUIDCONF();
	while (list ($dir, $type) = each ($GetCachesInsquidConf)){
		if(is_dir($dir)){
			echo "Starting......: Squid removing directory $num\n";
			shell_exec("/bin/rm -rf $dir");
		}
	}
	echo "Starting......:  Building caches\n";
	BuildCaches();
	caches_infos();
}

function NudeBooster(){
	$sock=new sockets();
	$unix=new unix();	
	$umount=$unix->find_program("umount");
	$SquidNuditScanParams=unserialize(base64_decode($sock->GET_INFO("SquidNudityScanParams")));	
	if(!isset($SquidNuditScanParams["MemoryDir"])){$SquidNuditScanParams["MemoryDir"]=0;}
	$MemoryDir=$SquidNuditScanParams["MemoryDir"];
	$workdir="/var/lib/nudityScan";	
	if(!is_numeric($MemoryDir)){$MemoryDir=0;}
	echo "Starting......: Squid nudity MemBoost {$MemoryDir}M\n";
	if($MemoryDir==0){
		shell_exec("$umount -l /var/lib/nudityScan >/dev/null 2>&1");
		return;
	}
	$idbin=$unix->find_program("id");
	$rm=$unix->find_program("rm");
	$mount=$unix->find_program("mount");
	exec("$idbin squid 2>&1",$results);
	if(!preg_match("#uid=([0-9]+).*?gid=([0-9]+)#", @implode("", $results),$re)){echo "Starting......: Squid nudity squid no such user...\n";return;}
	
	shell_exec("$umount -l $workdir");
	$uid=$re[1];
	$gid=$re[2];	
	shell_exec("$rm -rf $workdir");
	@mkdir($workdir,0755);	
	echo "Starting......: Squid nudity MemBoost squid ($uid/$gid)\n";	
	shell_exec("$mount -t tmpfs -o size={$MemoryDir}M,noauto,user,exec,uid=$uid,gid=$gid tmpfs $workdir");
	$mountedM=NudeBooster_tmpfs_mounted_size();
	if($mountedM>1){echo "Starting......: Squid nudity MemBoost mounted with {$mountedM}M\n";}else{
		echo "Starting......: Squid nudity mounted failed\n";
	}			
	
}

function NudeBooster_tmpfs_mounted_size(){
	$unix=new unix();
	$mount=$unix->find_program("mount");
	exec("$mount 2>&1",$results);
	while (list ($num, $ligne) = each ($results) ){
		if(preg_match("#^tmpfs on.*?lib\/nudityScan.*?tmpfs\s+\(.*?size=([0-9]+)M#", $ligne,$re)){return $re[1];}}
	return null;
}


function BuildCaches($NOTSTART=false){
	echo "Starting......: Squid Check *** caches ***\n";
	$squid=new squidbee();
	$unix=new unix();	
	$SQUID_CONFIG_PATH=$unix->SQUID_CONFIG_PATH();
	$unix=new unix();
	$sock=new sockets();
	$su_bin=$unix->find_program("su");
	$chown=$unix->find_program("chown");
	$chmod=$unix->find_program("chmod");
	$nohup=$unix->find_program("nohup");
	$TimeFileChown="/etc/artica-postfix/pids/".basename(__FILE__).".".__FUNCTION__.".time";
	$SquidBoosterMem=$sock->GET_INFO("SquidBoosterMem");
	if(!is_numeric($SquidBoosterMem)){$SquidBoosterMem=0;}
	$DisableSquidSNMPMode=$sock->GET_INFO("DisableSquidSNMPMode");
	if(!is_numeric($DisableSquidSNMPMode)){$DisableSquidSNMPMode=1;}	
	$squid_user=SquidUser();
	writelogs("Using squid user: \"$squid_user\"",__FUNCTION__,__FILE__,__LINE__);
	writelogs("$chown cache directories...",__FUNCTION__,__FILE__,__LINE__);
	$unix->chown_func($squid_user,null, "/etc/squid3/*");
	if(is_dir("/usr/share/squid-langpack")){$unix->chown_func($squid_user,null, "/usr/share/squid-langpack");}
	
	$GetCachesInsquidConf=$unix->SQUID_CACHE_FROM_SQUIDCONF();
	
	if($GLOBALS["OUTPUT"]){echo "Starting......: Squid ".count($GetCachesInsquidConf)." caches to check\n";}
	writelogs(count($GetCachesInsquidConf)." caches to check",__FUNCTION__,__FILE__,__LINE__);
	
	$MustBuild=false;
	if($SquidBoosterMem>0){
		if($DisableSquidSNMPMode==1){
			if($GLOBALS["OUTPUT"]){echo "Starting......: Squid Cache booster set to {$SquidBoosterMem}Mb\n";}
			@mkdir("/var/squid/cache_booster",0755,true);
			@chown("/var/squid/cache_booster", "squid");
			@chgrp("/var/squid/cache_booster", "squid");
			if(!is_dir("/var/squid/cache_booster/00")){
				echo "Starting......: Squid *** /var/squid/cache_booster/00 *** No such directory ask to rebuild caches\n";
				$MustBuild=true;
			}
		}
	}		
	
	if(!$GLOBALS["NOCACHES"]){
		$TimeFileChownTime=$unix->file_time_min($TimeFileChown);
		
		while (list ($CacheDirectory, $type) = each ($GetCachesInsquidConf)){
			if(trim($CacheDirectory)==null){continue;}
			if($GLOBALS["OUTPUT"]){echo "Starting......: Squid Check *** $CacheDirectory ***\n";}
			$subdir=basename($CacheDirectory);
			$MainDir=dirname($CacheDirectory);
			
			writelogs("Directory \"$CacheDirectory\" SUBDIR=$subdir Main dir=$MainDir",__FUNCTION__,__FILE__,__LINE__);
			if(isDirInFsTab($MainDir)){
				if($GLOBALS["OUTPUT"]){echo "Starting......: Squid Check *** $MainDir -> Mounted ? ***\n";}
			}
			
			if(!is_dir($CacheDirectory)){
				echo "Starting......: Squid Check creating cache \"$CacheDirectory\" no such directory\n";
				@mkdir($CacheDirectory,0755,true);
				$MustBuild=true;
			}
			echo "Starting......: Squid Check cache \"$CacheDirectory\" owned by $squid_user\n";
			
			$unix->chown_func($squid_user,$squid_user,$CacheDirectory);
			$unix->chmod_alldirs(0755, $CacheDirectory);
			@chmod($CacheDirectory, 0755);
			$unix->chown_func($squid_user,$squid_user,"$CacheDirectory/*");
			
					
		}
	}
	if($unix->file_time_min($TimeFileChown)>120){@unlink($TimeFileChown);@file_put_contents($TimeFileChown, time());}
	
	
	if(!$GLOBALS["NOCACHES"]){$MustBuild=false;return;}
	
	
	if(!$MustBuild){
		if($GLOBALS["OUTPUT"]){caches_infos();}
		echo "Starting......: Squid all caches are OK\n";
		return;
	}
	
	
	if(preg_match("#(.+?):#",$squid_user,$re)){$squid_uid=$re[1];}else{$squid_uid="squid";}
	writelogs("Stopping squid...",__FUNCTION__,__FILE__,__LINE__);
	shell_exec("/etc/init.d/artica-postfix stop squid-cache");
	writelogs("Building caches with user: \"$squid_uid\"",__FUNCTION__,__FILE__,__LINE__);
	writelogs("$su_bin $squid_uid -c \"{$GLOBALS["SQUIDBIN"]} -z\" 2>&1",__FUNCTION__,__FILE__,__LINE__);
	exec("$su_bin $squid_uid -c \"{$GLOBALS["SQUIDBIN"]} -z\" 2>&1",$results);	
	
	while (list ($agent, $val) = each ($results) ){
			writelogs("$val",__FUNCTION__,__FILE__,__LINE__);
	}
	
	
	writelogs("Send Notifications",__FUNCTION__,__FILE__,__LINE__);
	send_email_events("Squid Cache: reconfigure caches","Here it is the results\n",@implode("\n",$results),"proxy");
	writelogs("Starting squid",__FUNCTION__,__FILE__,__LINE__);
	
	unset($results);
	if(!$NOTSTART){
		reconfigure_squid();
	}	
	
	
	
}

function kernel_values(){
	$unix=new unix();
	$nohup=$unix->find_program("nohup");
	$php5=$unix->LOCATE_PHP5_BIN();
	$cmd="$php5 /usr/share/artica-postfix/exec.kernel-tuning.php --squid";	
}

function isDirInFsTab($directory){
	$directoryRegex=$directory;
	$directoryRegex=str_replace("/", "\/", $directoryRegex);
	$directoryRegex=str_replace(".", "\.", $directoryRegex);
	$f=explode("\n", @file_get_contents("/etc/fstab"));
	while (list ($index, $val) = each ($f) ){
		if(preg_match("#^(.+)\s+$directoryRegex#", $val,$re)){
			echo "Starting......: Squid Check $directory must be mounted on {$re[1]}\n";
			return true;
		}
		
	}
}



function security_limit(){
	
	$f=file("/etc/security/limits.conf");
	$add=false;
	while (list ($index, $line) = each ($f) ){
		
		if(preg_match("#^squid.*?#", $line)){
			echo "Starting......: [SYS]: Squid /etc/security/limits.conf OK\n"; 
			return;
		}
		
	}
	echo "Starting......: [SYS]: Squid /etc/security/limits.conf adding 65535 for squid username\n"; 
	$f[]="squid - nofile 65535";
	@file_put_contents("/etc/security/limits.conf", @implode("\n", $f));
	
	
}

function CheckTempConfig(){
	$unix=new unix();
	$squidbin=$unix->find_program("squid");
	if(!is_file($squidbin)){$squidbin=$unix->find_program("squid3");}
	$cmd="$squidbin -f /tmp/squid.conf -k parse 2>&1";
	if($GLOBALS["VERBOSE"]){echo "$cmd\n";}
	exec($cmd,$results);
	
	while (list ($index, $ligne) = each ($results) ){
		if(preg_match("#(unrecognized|FATAL|Bungled)#", $ligne)){
			echo "FAILED\n";
			echo "$ligne\n";
			if(preg_match("#line ([0-9]+):#", $ligne,$ri)){
				$Buggedline=$ri[1];
				$tt=explode("\n",@file_get_contents("/tmp/squid.conf"));
				for($i=$Buggedline-2;$i<$Buggedline+2;$i++){
					$lineNumber=$i+1;
					if(trim($tt[$i])==null){continue;}
					echo "[line:$lineNumber]: {$tt[$i]}\n";}
				}	
			return;		
		}
		
		if(preg_match("#ERROR: Failed#", $ligne)){
			echo "FAILED\n";
			echo "$ligne\n";
			return;
		}

	}
	
	echo "SUCCESS\n";
	
}


function ApplyConfig($smooth=false){
	if(function_exists("WriteToSyslogMail")){WriteToSyslogMail("Invoke ApplyConfig function", basename(__FILE__));}
	$unix=new unix();
	$ulimit=$unix->find_program("ulimit");
	if(is_file($ulimit)){
		shell_exec("$ulimit -HSd unlimited");
	}else{
		echo "Starting......: [SYS]: Squid ulimit no such binary...\n"; 
	}
	
	echo "Starting......: [SYS]: Squid apply kernel settings\n"; 
	kernel_values();
	echo "Starting......: [SYS]: Squid apply Checks security limits\n"; 
	security_limit();
	echo "Starting......: [SYS]: Squid Checking Remote appliances...\n";
	remote_appliance_restore_tables();
	echo "Starting......: [SYS]: Squid Checking Remote appliances done...\n";
	$nohup=$unix->find_program("nohup");
	$php5=$unix->LOCATE_PHP5_BIN();
	$squidbin=$unix->find_program("squid");
	$SQUID_CONFIG_PATH=$unix->SQUID_CONFIG_PATH();
	
	echo "Starting......: [SYS]: Squid loading libraires...\n";
	$sock=new sockets();
	$squid=new squidbee();
	@mkdir("/var/run/squid",0755,true);
	
	if(!is_file($squidbin)){$squidbin=$unix->find_program("squid3");}
	echo "Starting......: [SYS]: Squid binary: `$squidbin`\n";
	echo "Starting......: [SYS]: Squid Conf..: `$SQUID_CONFIG_PATH`\n";
	echo "Starting......: [SYS]: Squid php...: `$php5`\n";
	echo "Starting......: [SYS]: Squid nohup.: `$nohup`\n";
	
	
	$DenySquidWriteConf=$sock->GET_INFO("DenySquidWriteConf");
	if(!is_numeric($DenySquidWriteConf)){$DenySquidWriteConf=0;}

	echo "Starting......: [SYS]: Squid Checking `DenySquidWriteConf` = $DenySquidWriteConf\n";
	@mkdir("/var/log/squid/nudity",0755,true);
	@copy("/etc/artica-postfix/settings/Daemons/SquidNudityScanParams","/etc/squid3/SquidNudityScanParams");
	$unix->chown_func("squid","squid", "/etc/squid3/SquidNudityScanParams");
	$unix->chown_func("squid","squid", "/var/log/squid/nudity");
	$unix->chown_func("squid","squid", "/var/run/squid");
	$unix->chmod_func(0755, "/var/run/squid/*");
	echo "Starting......: [SYS]: Squid Checking `NudeBooster`\n";
	NudeBooster();
	if(!is_dir("/usr/share/squid-langpack")){
		echo "Starting......: [SYS]: Squid Checking Templates from MySQL\n";
		TemplatesInMysql(true);
		return;
	}
	writelogs("->BuildBlockedSites",__FUNCTION__,__FILE__,__LINE__);
	$EnableRemoteStatisticsAppliance=0;
	echo "Starting......: [SYS]: Squid Build blocked Websites list...\n";
	$squid->BuildBlockedSites();
	acl_clients_ftp();
	acl_whitelisted_browsers();
	acl_allowed_browsers();
	echo "Starting......: [SYS]: Squid Build url_rewrite_access deny...\n";
	urlrewriteaccessdeny();
	echo "Starting......: [SYS]:Squid building main configuration done\n";
	
	
	if($DenySquidWriteConf==0){
			$conf=$squid->BuildSquidConf();
			$conf=str_replace("\n\n", "\n", $conf);
			@file_put_contents("/tmp/squid.conf", $conf);
			echo "Starting......: [SYS]: Squid Check validity of the configuration file with /tmp/squid.conf...\n";
			exec("$squidbin -f /tmp/squid.conf -k parse 2>&1",$results);
			while (list ($index, $ligne) = each ($results) ){
				if(strpos($ligne,"| WARNING:")>0){continue;}
			
				if(preg_match("#ERROR: Failed#", $ligne)){
					echo "Starting......: [SYS]: Squid `$ligne`, aborting configuration, keep the old one...\n";
					echo "<div style='font-size:16px;font-weight:bold;color:#E71010'>$ligne</div>";
					$sock->TOP_NOTIFY("$ligne","error");
					return;			
				}
				
				if(preg_match("#Segmentation fault#", $ligne)){
					echo "Starting......: [SYS]: Squid `$ligne`, aborting configuration, keep the old one...\n";
					echo "<div style='font-size:16px;font-weight:bold;color:#E71010'>$ligne</div>";
					$sock->TOP_NOTIFY("$ligne","error");
					return;	
				}				
			
			
				if(preg_match("#(unrecognized|FATAL|Bungled)#", $ligne)){
					echo "Starting......: [SYS]: Squid `$ligne`, aborting configuration, keep the old one...\n";
					echo "<div style='font-size:16px;font-weight:bold;color:#E71010'>$ligne</div>";
					if(preg_match("#line ([0-9]+):#", $ligne,$ri)){
						$Buggedline=$ri[1];
						$tt=explode("\n",@file_get_contents("/tmp/squid.conf"));
						echo "<HR>";
						for($i=$Buggedline-2;$i<$Buggedline+2;$i++){
							$lineNumber=$i+1;
							$colorbugged="black";
							if(trim($tt[$i])==null){continue;}
							if($lineNumber==$Buggedline){$colorbugged="#E71010";}
							echo "<div style='font-size:12px;font-weight:bold;color:$colorbugged'>[line:$lineNumber]: {$tt[$i]}</div>";}
						}
						echo "<HR>";
					$sock->TOP_NOTIFY("$ligne","error");
					return;
				}
			
				if(preg_match("#strtokFile:\s+(.+?)\s+not found#", $ligne,$re)){
					$filename=trim($re[1]);
					echo "Starting......: [SYS]: Squid missing $filename, create an empty one\n";
					@mkdir(dirname($filename),0755,true);
					@file_put_contents($filename ,"");
					@chown($filename, "squid");
					@chgrp($filename, "squid");
					continue;
				}
			
				if(preg_match("#Processing:\s+#", $ligne)){continue;}
				if(preg_match("#Warning: empty ACL#", $ligne)){continue;}
				if(preg_match("#searching predictable#", $ligne)){continue;}
				if(preg_match("#is a subnetwork of#", $ligne)){continue;}
				if(preg_match("#You should probably#", $ligne)){continue;}
				if(preg_match("#Startup:\s+#", $ligne)){continue;}
				echo "Starting......: [SYS]: $ligne\n";
			
			}
			
			@file_put_contents("/etc/artica-postfix/settings/Daemons/GlobalSquidConf",$conf);
			echo "Starting......: [SYS]: Squid Check validity OK...\n";
			if($GLOBALS["NOAPPLY"]){
				echo "Starting......: [SYS]: WARNING \"NOAPPLY\" Artica is denied to apply settings...\n";
				return;}
			echo "Starting......: [SYS]: Squid Writing configuration file \"$SQUID_CONFIG_PATH\" ". strlen($conf)." bytes...\n";
			@file_put_contents($SQUID_CONFIG_PATH,$conf);
			@mkdir("/etc/squid3",0755,true);
			if($SQUID_CONFIG_PATH<>"/etc/squid3/squid.conf"){@file_put_contents("/etc/squid3/squid.conf",$conf);}
			$sock->TOP_NOTIFY("{squid_parameters_was_saved}","info");
			$cmd=$unix->LOCATE_PHP5_BIN()." ".__FILE__." --templates --noreload";
			$unix->THREAD_COMMAND_SET($cmd);			
	}
	
	if(!$smooth){squidclamav();}
	if(!$smooth){wrapzap();}
	if(!$smooth){certificate_generate();}
	$cmd=$nohup." ". $unix->LOCATE_PHP5_BIN()." ".__FILE__." --cache-infos --force >/dev/null 2>&1 &";
	if($GLOBALS["VERBOSE"]){echo "$cmd\n";}
	shell_exec($cmd);
	shell_exec("$nohup $php5 /usr/share/artica-postfix/exec.syslog-engine.php --rsylogd >/dev/null 2>&1 &");
	shell_exec("$nohup $php5 /usr/share/artica-postfix/exec.squid.watchdog.php --init >/dev/null 2>&1 &");
	if(!$smooth){CheckFilesAndSecurity();}
	
}

function acl_clients_ftp(){
	$q=new mysql();
	$sql="SELECT * FROM squid_white WHERE task_type='FTP_RESTR' ORDER BY ID DESC";
	$results=$q->QUERY_SQL($sql,"artica_backup");
	if(!$q->ok){return;}
	
	while($ligne=@mysql_fetch_array($results,MYSQL_ASSOC)){
		if(!preg_match("#FTP_RESTR:(.+)#",$ligne["uri"],$re)){continue;}	
		$f[]=$re[1];
	}
	@file_put_contents("/etc/squid3/clients_ftp.acl",@implode("\n",$f));
	
}

function acl_allowed_browsers(){
	$sql="SELECT uri FROM squid_white WHERE task_type='USER_AGENT_BAN_WHITE' ORDER BY ID DESC";
	$q=new mysql();
	$results=$q->QUERY_SQL($sql,"artica_backup");	
	if(!$q->ok){
		writelogs("$q->mysql_error",__FUNCTION__,__FILE__,__LINE__);
		return;
	}	
	
	while($ligne=mysql_fetch_array($results,MYSQL_ASSOC)){
		$string=trim($ligne["uri"]);
		if($string==null){continue;}
		$string=str_replace(".","\.",$string);
		$string=str_replace("(","\(",$string);
		$string=str_replace(")","\)",$string);
		$string=str_replace("/","\/",$string);
		$f[]=$string;
	}	
	@file_put_contents("/etc/squid3/allowed-user-agents.acl",@implode("\n",$f));
}

function acl_whitelisted_browsers(){
	$sql="SELECT uri FROM squid_white WHERE task_type='AUTH_WL_USERAGENTS'";
	$q=new mysql();
	$results=$q->QUERY_SQL($sql,"artica_backup");
	if(!$q->ok){
		writelogs("$q->mysql_error",__FUNCTION__,__FILE__,__LINE__);
		return;
	}
	
	while($ligne=mysql_fetch_array($results,MYSQL_ASSOC)){
		$arrayUserAgents[$ligne["uri"]]=1;
	}
	
	if(!isset($arrayUserAgents)){
		echo "Starting......: Whitelisted User-Agents: 0\n";
		@file_put_contents("/etc/squid3/white-listed-user-agents.acl","");
		return;
	}
		
	if(!is_array($arrayUserAgents)){
		echo "Starting......: Whitelisted User-Agents: 0\n";
		@file_put_contents("/etc/squid3/white-listed-user-agents.acl","");
		return;
	}
		

	while (list ($agent, $val) = each ($arrayUserAgents) ){
		$sql="SELECT unique_key,`string` FROM `UserAgents` WHERE browser='$agent' ORDER BY string";
		$q=new mysql();
		$results=$q->QUERY_SQL($sql,"artica_backup");
		while($ligne=mysql_fetch_array($results,MYSQL_ASSOC)){
			$string=trim($ligne["string"]);
			if($string==null){continue;}
			$string=str_replace(".","\.",$string);
			$string=str_replace("(","\(",$string);
			$string=str_replace(")","\)",$string);
			$string=str_replace("/","\/",$string);
			$f[]=$string;
		}
	}
	echo "Starting......: Whitelisted User-Agents: ". count($arrayUserAgents)." (". count($f)." patterns)\n";		
	@file_put_contents("/etc/squid3/white-listed-user-agents.acl",@implode("\n",$f));		
		
	
}


function retrans(){
	$unix=new unix();
	$array=$unix->getDirectories("/tmp");
	while (list ($num, $ligne) = each ($array) ){
		if(preg_match("#(.+?)\/temporaryFolder\/bases\/av#",$ligne,$re)){
			$folder=$re[1];
		}
	}
	if(is_dir($folder)){
		$cmd=$unix->find_program("du")." -h -s $folder 2>&1";
		exec($cmd,$results);
		$text=trim(implode(" ",$results));
		if(preg_match("#^([0-9\.\,A-Z]+)#",$text,$re)){
			$dbsize=$re[1];
		}
	}else{
		$dbsize="0M";
	}
	
	echo $dbsize;
}


function certificate_conf(){
	include_once('ressources/class.ssl.certificate.inc');
	$ssl=new ssl_certificate();
	$array=$ssl->array_ssl;
	if(!isset($GLOBALS["CLASS_USERS"])){$GLOBALS["CLASS_USERS"]=new usersMenus();}
	$users=$GLOBALS["CLASS_USERS"];
	$sock=new sockets();	
	$cc=$array["artica"]["country"]."_".$array["default_ca"]["countryName_value"];
	

	
	
		$country_code="US";
		$contryname="Delaware";
		$locality="Wilmington";
		$organizationalUnitName="Artica Web Proxy Unit";
		$organizationName="Artica";
		$emailAddress="root@$users->hostname";
		$commonName=$users->hostname;
		
		
		
		if(preg_match("#(.+?)_(.+?)$#",$cc,$re)){
			$contryname=$re[1];
			$country_code=$re[2];
		}
		if($array["server_policy"]["localityName"]<>null){$locality=$array["server_policy"]["localityName"];}
		if($array["server_policy"]["organizationalUnitName"]<>null){$organizationalUnitName=$array["server_policy"]["organizationalUnitName"];}
		if($array["server_policy"]["emailAddress"]<>null){$emailAddress=$array["server_policy"]["emailAddress"];}
		if($array["server_policy"]["organizationName"]<>null){$organizationName=$array["server_policy"]["organizationName"];}
		if($array["server_policy"]["commonName"]<>null){$commonName=$array["server_policy"]["commonName"];}
	
		@mkdir("/etc/squid3/ssl/new",0666,true);
		
		$conf[]="[ca]";
		$conf[]="default_ca=default_db";
		$conf[]="unique_subject=no";
		$conf[]="";
		$conf[]="[default_db]";
		$conf[]="dir=.";
		$conf[]="certs=.";
		$conf[]="new_certs_dir=/etc/squid3/ssl/new";
		$conf[]="database= /etc/squid3/ssl/ca.index";
		$conf[]="serial = /etc/squid3/ssl/ca.serial";
		$conf[]="RANDFILE=.rnd";
		$conf[]="certificate=/etc/squid3/ssl/key.pem";
		$conf[]="private_key=/etc/squid3/ssl/ca.key";
		$conf[]="default_days= 730";
		$conf[]="default_crl_days=30";
		$conf[]="default_md=md5";
		$conf[]="preserve=no";
		$conf[]="name_opt=ca_default";
		$conf[]="cert_opt=ca_default";
		$conf[]="unique_subject=no";
		$conf[]="policy=policy_match";
		$conf[]="";
		$conf[]="[server_policy]";
		$conf[]="countryName=supplied";
		$conf[]="stateOrProvinceName=supplied";
		$conf[]="localityName=supplied";
		$conf[]="organizationName=supplied";
		$conf[]="organizationalUnitName=supplied";
		$conf[]="commonName=supplied";
		$conf[]="emailAddress=supplied";
		$conf[]="";
		$conf[]="[server_cert]";
		$conf[]="subjectKeyIdentifier=hash";
		$conf[]="authorityKeyIdentifier=keyid:always";
		$conf[]="extendedKeyUsage=serverAuth,clientAuth,msSGC,nsSGC";
		$conf[]="basicConstraints= critical,CA:false";
		$conf[]="";
		$conf[]="[user_policy]";
		$conf[]="commonName=supplied";
		$conf[]="emailAddress=supplied";
		$conf[]="";
		$conf[]="[user_cert]";
		$conf[]="subjectAltName=email:copy";
		$conf[]="basicConstraints= critical,CA:false";
		$conf[]="authorityKeyIdentifier=keyid:always";
		$conf[]="extendedKeyUsage=clientAuth,emailProtection";
		$conf[]="";
		$conf[]="[req]";
		$conf[]="default_bits=1024";
		$conf[]="default_keyfile=ca.key";
		$conf[]="distinguished_name=default_ca";
		$conf[]="x509_extensions=extensions";
		$conf[]="string_mask=nombstr";
		$conf[]="req_extensions=req_extensions";
		$conf[]="input_password=secret";
		$conf[]="output_password=secret";
		$conf[]="";
		$conf[]="[default_ca]";
		$conf[]="countryName=Country Code";
		$conf[]="countryName_value=$country_code";
		$conf[]="countryName_min=2";
		$conf[]="countryName_max=2";
		$conf[]="stateOrProvinceName=State Name";
		$conf[]="stateOrProvinceName_value=$contryname";
		$conf[]="localityName=Locality Name";
		$conf[]="localityName_value=$locality";
		$conf[]="organizationName=Organization Name";
		$conf[]="organizationName_value=$organizationName";
		$conf[]="organizationalUnitName=Organizational Unit Name";
		$conf[]="organizationalUnitName_value=$organizationalUnitName";
		$conf[]="commonName=Common Name";
		$conf[]="commonName_value=$commonName";
		$conf[]="commonName_max=64";
		$conf[]="emailAddress=Email Address";
		$conf[]="emailAddress_value=$emailAddress";
		$conf[]="emailAddress_max=40";
		$conf[]="unique_subject=no";
		$conf[]="";
		$conf[]="[extensions]";
		$conf[]="subjectKeyIdentifier=hash";
		$conf[]="authorityKeyIdentifier=keyid:always";
		$conf[]="basicConstraints=critical,CA:false";
		$conf[]="";
		$conf[]="[req_extensions]";
		$conf[]="nsCertType=objsign,email,server";
		$conf[]="";
		$conf[]="[CA_default]";
		$conf[]="policy=policy_match";
		$conf[]="";
		$conf[]="[policy_match]";
		$conf[]="countryName=match";
		$conf[]="stateOrProvinceName=match";
		$conf[]="organizationName=match";
		$conf[]="organizationalUnitName=optional";
		$conf[]="commonName=match";
		$conf[]="emailAddress=optional";
		$conf[]="";
		$conf[]="[policy_anything]";
		$conf[]="countryName=optional";
		$conf[]="stateOrProvinceName=optional";
		$conf[]="localityName=optional";
		$conf[]="organizationName=optional";
		$conf[]="organizationalUnitName=optional";
		$conf[]="commonName=optional";
		$conf[]="emailAddress=optional";
		$conf[]="";
		$conf[]="[v3_ca]";
		$conf[]="subjectKeyIdentifier=hash";
		$conf[]="authorityKeyIdentifier=keyid:always,issuer:always";
		$conf[]="basicConstraints=critical,CA:false";
		@mkdir("/etc/squid3/ssl",0666,true);
		file_put_contents("/etc/squid3/ssl/openssl.conf",@implode("\n",$conf));		
	}

function certificate_generate(){
		$ssl_path="/etc/squid3/ssl";
		
		if(is_certificate()){
			echo "Starting......: Squid SSL certificate OK\n";
			return;
		}
		
		
		@unlink("$ssl_path/privkey.cp.pem");
		@unlink("$ssl_path/cacert.pem");
		@unlink("$ssl_path/privkey.pem");
		
		
		 echo "Starting......: Squid building SSL certificate\n";
		 certificate_conf();
		 $ldap=new clladp();
		 $sock=new sockets();
		 $unix=new unix();
		$CertificateMaxDays=$sock->GET_INFO('CertificateMaxDays');
		if($CertificateMaxDays==null){$CertificateMaxDays='730';}
		 echo "Starting......: Squid Max Days are $CertificateMaxDays\n";		 
		 $password=$unix->shellEscapeChars($ldap->ldap_password);
		 
		 $openssl=$unix->find_program("openssl");
		 $config="/etc/squid3/ssl/openssl.conf";
		 
		 
		 system("$openssl genrsa -des3 -passout pass:$password -out $ssl_path/privkey.pem 2048 1024");
		 system("$openssl req -new -x509 -nodes -passin pass:$password -key $ssl_path/privkey.pem -batch -config $config -out $ssl_path/cacert.pem -days $CertificateMaxDays");
		 system("/bin/cp $ssl_path/privkey.pem $ssl_path/privkey.cp.pem");
		 system("$openssl rsa -passin pass:$password -in $ssl_path/privkey.cp.pem -out $ssl_path/privkey.pem"); 
		 
	     
	}
	
function is_certificate(){
	$ssl_path="/etc/squid3/ssl";;
	if(!is_file("$ssl_path/cacert.pem")){return false;}
	if(!is_file("$ssl_path/privkey.pem")){return false;}
	if(!is_file("$ssl_path/privkey.cp.pem")){return false;}
	return true;
	
}

function wrapzap_compile(){
	$sql="SELECT * FROM squid_adzapper WHERE enabled=1";
	$q=new mysql();
	$f=array();
	$tpl=new templates();
	$unix=new unix();
	$results=$q->QUERY_SQL($sql,"artica_backup");
	if(!$q->ok){writelogs($q->mysql_error,__FUNCTION__,__FILE__,__LINE__);return;}
	
	while($ligne=@mysql_fetch_array($results,MYSQL_ASSOC)){
		$f[]="{$ligne["uri_type"]} {$ligne["uri"]}";
	}
	
	echo "Starting......: adZapper ". count($f)." rows\n"; 
	@file_put_contents("/etc/squid3/zapper.post-database.txt",@implode("\n",$f));
	$squiduser=SquidUser();
	$unix->chown_func($squiduser,null, "/etc/squid3/zapper.pre-database.txt");
	$unix->chown_func($squiduser,null, "/etc/squid3/zapper.post-database.txt");


	if($GLOBALS["RELOAD"]){
		$unix=new unix();
		squid_watchdog_events("Reconfiguring Proxy parameters...");
		if(function_exists("debug_backtrace")){$trace=debug_backtrace();if(isset($trace[1])){$file=basename($trace[1]["file"]);$function=$trace[1]["function"];$line=$trace[1]["line"];$called="Called by $function() from line $line";}}
		squid_admin_mysql(2, "Reconfiguring squid-cache","$called");
		shell_exec("{$GLOBALS["SQUIDBIN"]} -k reconfigure");
	}
}


function wrapzap(){
	if(!isset($GLOBALS["CLASS_USERS"])){$GLOBALS["CLASS_USERS"]=new usersMenus();}
	$users=$GLOBALS["CLASS_USERS"];
	$sock=new sockets();
	$SquidGuardIPWeb=$sock->GET_INFO("SquidGuardIPWeb");
	if($SquidGuardIPWeb==null){$SquidGuardIPWeb="http://$users->hostname:9020/zaps";}
	$SquidGuardIPWeb=str_replace('.(none)',"",$SquidGuardIPWeb);
	
	if(preg_match("#http:\/\/(.+?)\/#",$SquidGuardIPWeb,$re)){
		$SquidGuardIPWeb="http://{$re[1]}/zaps";
	}
	
	if(!is_file("/etc/squid3/zapper.pre-database.txt")){@file_put_contents("/etc/squid3/zapper.pre-database.txt","#");}
	if(!is_file("/etc/squid3/zapper.post-database.txt")){@file_put_contents("/etc/squid3/zapper.post-database.txt","#");}
	
	wrapzap_compile();
	
	
	echo "Starting......: adZapper redirector to \"$SquidGuardIPWeb\"\n"; 
	
$f[]="#!/bin/sh";
$f[]="#";
$f[]="# Wrapper to set environment variables then exec the real zapper.";
$f[]="# The reasons for this are twofold:";
$f[]="#	- for some reason squid doesn't preserve the original environment";
$f[]="#	  when you do a restart (or SIGHUP)";
$f[]="#	- to avoid having to hack the squid startup script (if you have";
$f[]="#	  a presupplied one, such as ships with some linux distributions)";
$f[]="#";
$f[]="# Install in the same directory you put the zapper (just for convenience) and";
$f[]="# hack the pathnames below to suit.";
$f[]="# Note that you can skip this script and run the zapper with no environment";
$f[]="# settings at all and it will work fine; the variables are all set here merely";
$f[]="# for completeness so that customisation is easy for you.";
$f[]="#	- Cameron Simpson <cs@zip.com.au> 21apr2000";
$f[]="#";
$f[]="";
$f[]="# modify this to match your install";
$f[]="zapper=/usr/bin/squid_redirect";
$f[]="";
$f[]="ZAP_MODE=				# or \"CLEAR\"";
$f[]="ZAP_BASE=$SquidGuardIPWeb	# a local web server will be better";
$f[]="ZAP_BASE_SSL=https://adzapper.sourceforge.net/zaps # this can probably be ignored";
$f[]="";
$f[]="ZAP_PREMATCH=/etc/squid3/zapper.pre-database.txt";
$f[]="ZAP_POSTMATCH=/etc/squid3/zapper.post-database.txt";
$f[]="ZAP_MATCH=				# pathname of extra pattern file";
$f[]="					# for patterns to use instead of the";
$f[]="					# inbuilt pattern list";
$f[]="ZAP_NO_CHANGE=				# set to \"NULL\" is your proxy is Apache2 instead of Squid";
$f[]="";
$f[]="STUBURL_AD=\$ZAP_BASE/ad.gif";
$f[]="STUBURL_ADSSL=\$ZAP_BASE_SSL/ad.gif";
$f[]="STUBURL_ADBG=\$ZAP_BASE/adbg.gif";
$f[]="STUBURL_ADJS=\$ZAP_BASE/no-op.js";
$f[]="STUBURL_ADJSTEXT=";
$f[]="STUBURL_ADHTML=\$ZAP_BASE/no-op.html";
$f[]="STUBURL_ADHTMLTEXT=";
$f[]="STUBURL_ADMP3=\$ZAP_BASE/ad.mp3";
$f[]="STUBURL_ADPOPUP=\$ZAP_BASE/closepopup.html";
$f[]="STUBURL_ADSWF=\$ZAP_BASE/ad.swf";
$f[]="STUBURL_COUNTER=\$ZAP_BASE/counter.gif";
$f[]="STUBURL_COUNTERJS=\$ZAP_BASE/no-op-counter.js";
$f[]="STUBURL_COUNTERHTML=\$ZAP_BASE/no-op-counter.html";
$f[]="STUBURL_WEBBUG=\$ZAP_BASE/webbug.gif";
$f[]="STUBURL_WEBBUGJS=\$ZAP_BASE/webbug.js";
$f[]="STUBURL_WEBBUGHTML=\$ZAP_BASE/webbug.html";
$f[]="";
$f[]="STUBURL_PRINT=				# off by default, set to 1";
$f[]="";
$f[]="export ZAP_MODE ZAP_BASE ZAP_BASE_SSL ZAP_PREMATCH ZAP_POSTMATCH ZAP_MATCH ZAP_NO_CHANGE";
$f[]="export STUBURL_AD STUBURL_ADSSL STUBURL_ADJS STUBURL_ADHTML STUBURL_ADMP3 \ ";
$f[]="	STUBURL_ADPOPUP STUBURL_ADSWF STUBURL_COUNTER STUBURL_COUNTERJS \ ";
$f[]="	STUBURL_COUNTERHTML STUBURL_WEBBUG STUBURL_WEBBUGJS STUBURL_WEBBUGHTML \ ";
$f[]="	STUBURL_PRINT STUBURL_ADHTMLTEXT STUBURL_ADJSTEXT";
$f[]="";
$f[]="# Here, having arranged the environment, we exec the real zapper.";
$f[]="# If you're chaining redirectors then comment out the direct exec below and";
$f[]="# uncomment (and adjust) the exec of zapchain which takes care of running";
$f[]="# multiple redirections.";
$f[]="";
$f[]="exec \"\$zapper\"";
$f[]="# exec /path/to/zapchain \"\$zapper\" /path/to/another/eg/squirm";	
@file_put_contents("/usr/bin/wrapzap",@implode("\n",$f));
@chmod("/usr/bin/wrapzap",0755);
echo "Starting......: adZapper wrapzap done...\n"; 

}


function SquidUser(){
	$unix=new unix();
	$squidconf=$unix->SQUID_CONFIG_PATH();
	$group=null;
	if(!is_file($squidconf)){
		echo "Starting......: squidGuard unable to get squid configuration file\n";
		return "squid:squid";
	}
	
	writelogs("Open $squidconf");
	$array=explode("\n",@file_get_contents($squidconf));
	while (list ($index, $line) = each ($array)){
		if(preg_match("#cache_effective_user\s+(.+)#",$line,$re)){
			$user=trim($re[1]);
			$user=trim($re[1]);
		}
		if(preg_match("#cache_effective_group\s+(.+)#",$line,$re)){
			$group=trim($re[1]);
		}
	}
	if($group==null){$group="squid";}
	return "$user:$group";
}






function compilation_params(){
	@mkdir("/etc/artica-postfix/pids",0755,true);
	if(!is_file($GLOBALS["SQUIDBIN"])){return;}
	$EXEC_PID_FILE="/etc/artica-postfix/pids/".basename(__FILE__).".".__FUNCTION__.".build.pid";
	$EXEC_PID_TIME="/etc/artica-postfix/pids/".basename(__FILE__).".".__FUNCTION__.".build.time";
	
	$unix=new unix();
	$kill=$unix->find_program("kill");
	$oldpid=@file_get_contents($EXEC_PID_FILE);
	if($unix->process_exists($oldpid,basename(__FILE__))){die();}
	$cachefile="/usr/share/artica-postfix/ressources/logs/squid.compilation.params";
	
	$timefile=$unix->file_time_min($EXEC_PID_TIME);
	if($timefile<5){return;}
	@unlink($EXEC_PID_TIME);
	@file_put_contents($EXEC_PID_TIME, time());
	
	if(is_file($cachefile)){
		$timefile=$unix->file_time_min($cachefile);
		if($timefile<30){return;}
	}
	
	
	exec($GLOBALS["SQUIDBIN"]." -v",$results);
	$text=@implode("\n", $results);
	if(preg_match("#configure options:\s+(.+)#is", $text,$re)){$text=$re[1];}
	if(preg_match_all("#'(.+?)'#is", $text, $re)){
		while (list ($index, $line) = each ($re[1])){
			if(preg_match("#(.+?)=(.+)#", $line,$ri)){
				$key=$ri[1];
				$value=$ri[2];
				$key=str_replace("--", "", $key);
				$array[$key]=$value;
				continue;
			}
			$key=$line;
			$value=1;
			$key=str_replace("--", "", $key);
			$array[$key]=$value;
					
			
		}
		@unlink("/usr/share/artica-postfix/ressources/logs/squid.compilation.params");
		@file_put_contents("/usr/share/artica-postfix/ressources/logs/squid.compilation.params", base64_encode(serialize($array)));
		shell_exec("/bin/chmod 755 /usr/share/artica-postfix/ressources/logs/squid.compilation.params");
	}
}

function errors_details_txt(){
return;
//@copy("/usr/share/artica-postfix/bin/install/squid/error-details.txt", "/usr/share/squid3/errors/templates/error-details.txt");
shell_exec("/bin/chown -R squid:squid /usr/share/squid3");
	
}

function TemplatesInMysql_remote(){
	include_once(dirname(__FILE__)."/ressources/class.ccurl.inc");
	$users=new usersMenus();
	$sock=new sockets();
	$unix=new unix();
	$base="/usr/share/squid-langpack";
	@mkdir($base,0755,true);
	$RemoteStatisticsApplianceSettings=unserialize(base64_decode($sock->GET_INFO("RemoteStatisticsApplianceSettings")));
	if(!is_numeric($RemoteStatisticsApplianceSettings["SSL"])){$RemoteStatisticsApplianceSettings["SSL"]=1;}
	if(!is_numeric($RemoteStatisticsApplianceSettings["PORT"])){$RemoteStatisticsApplianceSettings["PORT"]=9000;}
	$GLOBALS["REMOTE_SSERVER"]=$RemoteStatisticsApplianceSettings["SERVER"];
	$GLOBALS["REMOTE_SPORT"]=$RemoteStatisticsApplianceSettings["PORT"];
	$GLOBALS["REMOTE_SSL"]=$RemoteStatisticsApplianceSettings["SSL"];
	if($GLOBALS["REMOTE_SSL"]==1){$refix="https";}else{$refix="http";}
	$uri="$refix://{$GLOBALS["REMOTE_SSERVER"]}:{$GLOBALS["REMOTE_SPORT"]}/ressources/databases/squid-lang-pack.tgz";
	$curl=new ccurl($uri,true);
	if(!$curl->GetFile("/tmp/squid-lang-pack.tgz")){
			ufdbguard_admin_events("Failed to download $uri aborting `$curl->error`",__FUNCTION__,__FILE__,__LINE__,"global-compile");
			EventsWatchdog("$uri `$curl->error`");
			return;
	}	
	$chown=$unix->find_program("chown");
	$tar=$unix->find_program("tar");
	shell_exec("$tar -xf /tmp/squid-lang-pack.tgz -C $base/");
	shell_exec("$chown -R squid:squid $base");
	
	EventsWatchdog("Writing /etc/artica-postfix/SQUID_TEMPLATE_DONE");
	@file_put_contents("/etc/artica-postfix/SQUID_TEMPLATE_DONE", time());
	
	Reload_Squid();
}



function TemplatesUniqueInMysql($zmd5){
	$sock=new sockets();
	$unix=new unix();
	$q=new mysql_squid_builder();
	$users=new usersMenus();
	$EnableRemoteStatisticsAppliance=$sock->GET_INFO("EnableRemoteStatisticsAppliance");
	$EnableWebProxyStatsAppliance=$sock->GET_INFO("EnableWebProxyStatsAppliance");
	if(!is_numeric($EnableWebProxyStatsAppliance)){$EnableWebProxyStatsAppliance=0;}
	if(!is_numeric($EnableRemoteStatisticsAppliance)){$EnableRemoteStatisticsAppliance=0;}
	$UnlockWebStats=$sock->GET_INFO("UnlockWebStats");
	if(!is_numeric($UnlockWebStats)){$UnlockWebStats=0;}
	if($UnlockWebStats==1){$EnableRemoteStatisticsAppliance=0;}	
	if($EnableRemoteStatisticsAppliance==1){if($GLOBALS["VERBOSE"]){echo "Use the Web statistics appliance to get template files...\n";}TemplatesInMysql_remote();return;}	
	
	$base="/usr/share/squid-langpack";
	@mkdir("/usr/share/squid3/errors/templates",0755,true);
	@mkdir($base,0755,true);
	if(!is_dir("$base/templates")){@mkdir("$base/templates",0755,true);}
	$sql="SELECT * FROM squidtpls WHERE `zmd5`='{$zmd5}'";
	$ligne=mysql_fetch_array($q->QUERY_SQL($sql));
	if(!$q->ok){echo $q->mysql_error."\n";return;}
	
	if($ligne["template_link"]==1){return;}
	$ligne["template_header"]=stripslashes($ligne["template_header"]);
	$ligne["template_title"]=stripslashes($ligne["template_title"]);
	$ligne["template_body"]=stripslashes($ligne["template_body"]);	
	
	
	$header=trim($ligne["template_header"]);
	if($ligne["template_name"]==null){return;}
	
	if(!$users->CORP_LICENSE){
		$header=null;
		$ligne["template_header"]=null;
		$ligne["template_body"]=null;
	}
	
	
	if($header==null){$header=@file_get_contents(dirname(__FILE__)."/ressources/databases/squid.default.header.db");}
	if(!preg_match("#ERR_.+#", $ligne["template_name"])){$ligne["template_name"]="ERR_".$ligne["template_name"];}
	$filename="$base/{$ligne["lang"]}/{$ligne["template_name"]}";
	$newheader=str_replace("{TITLE}", $ligne["template_title"], $header);
	$templateDatas="$newheader{$ligne["template_body"]}</body></html>";
	@mkdir(dirname($filename),0755,true);
	@file_put_contents($filename, $templateDatas);
	
	if($GLOBALS["VERBOSE"]){echo "Writing /usr/share/squid3/errors/{$ligne["lang"]}/{$ligne["template_name"]}\n";}
	@file_put_contents("/usr/share/squid3/errors/{$ligne["lang"]}/{$ligne["template_name"]}", $templateDatas);
	$unix->chown_func("squid","squid","/usr/share/squid3/errors/{$ligne["lang"]}/{$ligne["template_name"]}");
	$unix->chown_func("squid:squid",null, "/usr/share/squid3/errors/{$ligne["lang"]}/{$ligne["template_name"]}");
	$unix->chown_func("squid:squid",null, dirname($filename)."/*");
	if($ligne["lang"]=="en"){
		if($GLOBALS["VERBOSE"]){echo "Writing /usr/share/squid3/errors/templates/{$ligne["template_name"]}\n";}
		@file_put_contents("/usr/share/squid3/errors/templates/{$ligne["template_name"]}", $templateDatas);
		$unix->chown_func("squid:squid", null,"/usr/share/squid3/errors/templates/{$ligne["template_name"]}");
		
		if($GLOBALS["VERBOSE"]){echo "Writing $base/templates/{$ligne["template_name"]}\n";}
		@file_put_contents("$base/templates/{$ligne["template_name"]}", $templateDatas);
		$unix->chown_func("squid:squid", null,"$base/templates/{$ligne["template_name"]}");
	}
		
}


function TemplatesInMysql($aspid=false){
	$unix=new unix();
	$pidpath="/etc/artica-postfix/pids/".basename(__FILE__).".".__FUNCTION__.".pid";
	if(!$aspid){
		$oldpid=$unix->get_pid_from_file($pidpath);
		if($unix->process_exists($oldpid)){return;}
			
	}
	
	@file_put_contents($pidpath, getmypid());
	
	$sock=new sockets();
	$EnableRemoteStatisticsAppliance=$sock->GET_INFO("EnableRemoteStatisticsAppliance");
	$EnableWebProxyStatsAppliance=$sock->GET_INFO("EnableWebProxyStatsAppliance");
	if(!is_numeric($EnableWebProxyStatsAppliance)){$EnableWebProxyStatsAppliance=0;}
	if(!is_numeric($EnableRemoteStatisticsAppliance)){$EnableRemoteStatisticsAppliance=0;}
	$UnlockWebStats=$sock->GET_INFO("UnlockWebStats");
	if(!is_numeric($UnlockWebStats)){$UnlockWebStats=0;}
	if($UnlockWebStats==1){$EnableRemoteStatisticsAppliance=0;}	
	if($EnableRemoteStatisticsAppliance==1){
		EventsWatchdog("Using the Web statistics appliance to get template files");
		if($GLOBALS["VERBOSE"]){echo "Use the Web statistics appliance to get template files...\n";}
		TemplatesInMysql_remote();
		return;
	}	
		
	
	@mkdir("/etc/artica-postfix",0755,true);
	$base="/usr/share/squid-langpack";
	@mkdir($base,0755,true);
	if(!is_dir("$base/templates")){@mkdir("$base/templates",0755,true);}
	$headerTemp=@file_get_contents(dirname(__FILE__)."/ressources/databases/squid.default.header.db");
	
	$q=new mysql_squid_builder();
	if(!$q->BD_CONNECT(true)){
		EventsWatchdog("Error, unable to connect to MySQL");
		return;
	}
	
	$sql="CREATE TABLE IF NOT EXISTS `squidtpls` (
			  `zmd5` CHAR(32)  NOT NULL,
			  `template_name` varchar(128)  NOT NULL,
			  `template_body` LONGTEXT  NOT NULL,
			  `template_header` LONGTEXT  NOT NULL,
			  `template_title` varchar(255)  NOT NULL,
			  `template_time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			  `template_link` smallint(1) NOT NULL,
			  `template_uri` varchar(255)  NOT NULL,
			  `lang` varchar(5)  NOT NULL,
			  PRIMARY KEY (`zmd5`),
			  KEY `template_name` (`template_name`,`lang`),
			  KEY `template_title` (`template_title`),
			  KEY `template_time` (`template_time`),
			  KEY `template_link` (`template_link`),
			  FULLTEXT KEY `template_body` (`template_body`)
			)  ENGINE = MYISAM;";
	$q->QUERY_SQL($sql);
	
	EventsWatchdog("writing /etc/artica-postfix/SQUID_TEMPLATE_DONE");
	@file_put_contents("/etc/artica-postfix/SQUID_TEMPLATE_DONE", time());
	
	
	if($q->COUNT_ROWS("squidtpls")==0){DefaultTemplatesInMysql();}
	
	$sql="SELECT * FROM squidtpls";
	$results = $q->QUERY_SQL($sql);	
	if(!$q->ok){
		EventsWatchdog("$q->mysql_error");
		ufdbguard_admin_events("Fatal,$q->mysql_error", __FUNCTION__, __FILE__, __LINE__, "proxy");
		return;
	}
	
	while ($ligne = mysql_fetch_assoc($results)) {
		$ligne["template_header"]=stripslashes($ligne["template_header"]);
		$ligne["template_title"]=stripslashes($ligne["template_title"]);
		$ligne["template_body"]=stripslashes($ligne["template_body"]);
		$template_name=$ligne["template_name"];
		if($ligne["template_link"]==1){continue;}
		$header=trim($ligne["template_header"]);
		if($header==null){$header=$headerTemp;}
		if($GLOBALS["VERBOSE"]){
			echo "Template: `$template_name`: {$ligne["template_title"]}\n";
		}
		
		if(!preg_match("#^ERR_.+#", $ligne["template_name"])){
				$ligne["template_name"]="ERR_".$ligne["template_name"];
		}
		$filename="$base/{$ligne["lang"]}/{$ligne["template_name"]}";
		$newheader=str_replace("{TITLE}", $ligne["template_title"], $header);
		$templateDatas="$newheader{$ligne["template_body"]}</body></html>";
		
		if($GLOBALS["VERBOSE"]){
			echo "Template: `$template_name`: Path `$filename`\n";
		}
		
		@mkdir(dirname($filename),0755,true);
		@file_put_contents($filename, $templateDatas);
		@file_put_contents("/usr/share/squid3/errors/{$ligne["lang"]}/{$ligne["template_name"]}", $templateDatas);
		$unix->chown_func("squid","squid","/usr/share/squid3/errors/{$ligne["lang"]}/{$ligne["template_name"]}");
		$unix->chown_func("squid","squid","$filename");
		
		
		if(!is_dir("/usr/share/squid3/errors/{$ligne["lang"]}")){
			@mkdir("/usr/share/squid3/errors/{$ligne["lang"]}");
			$unix->chown_func("squid","squid","/usr/share/squid3/errors/{$ligne["lang"]}");
		}
		if($ligne["lang"]=="en"){
			if($GLOBALS["VERBOSE"]){echo "Writing /usr/share/squid3/errors/templates/{$ligne["template_name"]}\n";}
			@file_put_contents("/usr/share/squid3/errors/templates/{$ligne["template_name"]}", $templateDatas);
			$unix->chown_func("squid:squid", null,"/usr/share/squid3/errors/templates/{$ligne["template_name"]}");
			
			if($GLOBALS["VERBOSE"]){echo "Writing $base/templates/{$ligne["template_name"]}\n";}
			@file_put_contents("$base/templates/{$ligne["template_name"]}", $templateDatas);
			$unix->chown_func("squid:squid", null,"$base/templates/{$ligne["template_name"]}");
		}else{
			if(!IfTemplateExistsinEn($template_name)){
				@file_put_contents("/usr/share/squid3/errors/templates/{$ligne["template_name"]}", $templateDatas);
				@file_put_contents("$base/templates/{$ligne["template_name"]}", $templateDatas);
				$unix->chown_func("squid:squid", null,"$base/templates/{$ligne["template_name"]}");
				$unix->chown_func("squid:squid", null,"/usr/share/squid3/errors/templates/{$ligne["template_name"]}");
			}
		}
	}
	
	$unix=new unix();
	$tar=$unix->find_program("tar");
	$unix->chown_func("squid","squid", "$base/*");
	chdir($base);
	shell_exec("$tar -czf ".dirname(__FILE__)."/ressources/databases/squid-lang-pack.tgz *");
	
	if($EnableWebProxyStatsAppliance==1){
		if($GLOBALS["VERBOSE"]){echo "-> notify_remote_proxys()\n";}
		notify_remote_proxys("SQUID_LANG_PACK");
		if($GLOBALS["VERBOSE"]){echo "This is a statistics appliance, aborting next step\n";}
		return;
	}	
	
	
	Reload_Squid();
	
}

function EventsWatchdog($text){

	if(function_exists("debug_backtrace")){
		$trace=debug_backtrace();
		if(isset($trace[1])){
			$sourcefile=basename($trace[1]["file"]);
			$sourcefunction=$trace[1]["function"];
			$sourceline=$trace[1]["line"];
		}

	}

	$unix=new unix();
	$unix->events($text,"/var/log/squid.watchdog.log",false,$sourcefunction,$sourceline);
}


function IfTemplateExistsinEn($template_name){
	if(isset($GLOBALS["IfTemplateExistsinEn$template_name"])){return $GLOBALS["IfTemplateExistsinEn$template_name"];}
	$q=new mysql_squid_builder();
	$ligne=mysql_fetch_array($q->QUERY_SQL("SELECT zmd5 FROM squidtpls WHERE template_name='$template_name' AND lang='en'","artica_backup"));
	if($ligne["zmd5"]==null){$GLOBALS["IfTemplateExistsinEn$template_name"]=false;return false;}
	$GLOBALS["IfTemplateExistsinEn$template_name"]=true;
	return true;
}


function DefaultTemplatesInMysql(){
	$q=new mysql_squid_builder();
	$defaultdb=dirname(__FILE__)."/ressources/databases/squid.default.templates.db";
	if(!is_file($defaultdb)){echo "$defaultdb no such file\n";return;}
	$array=unserialize(@file_get_contents($defaultdb));
	if(!is_array($array)){echo "$defaultdb no such array\n";return;}
	$prefix="INSERT IGNORE INTO squidtpls (`zmd5`,`lang`,`template_name`,`template_body`,`template_title`) VALUES ";
	
	while (list ($language, $arrayTPL) = each ($array)){
		while (list ($templateName, $templateData) = each ($arrayTPL)){
			$title=$templateData["TITLE"];
			echo "Importing $title\n";
			$body=base64_decode($templateData["BODY"]);
			$md5=md5($language.$templateName);
			$body=addslashes($body);
			$title=addslashes($title);
			$ss="('$md5','$language','$templateName','$body','$title')";
			$q->QUERY_SQL($prefix.$ss);
			$f=array();
			if(!$q->ok){echo "$templateName ($language) FAILED ($q->mysql_error)\n";}
		}
	}
}

function StatsApplianceExportTables(){
	$f=new squid_stats_appliance();
	$f->export_tables();	
}

function notify_remote_proxys($COMMANDS=null){
	$unix=new unix();
	include_once(dirname(__FILE__)."/ressources/class.blackboxes.inc");
	$EXEC_PID_FILE="/etc/artica-postfix/".basename(__FILE__).".".__FUNCTION__.".pid";
	$EXEC_PID_TIME="/etc/artica-postfix/".basename(__FILE__).".".__FUNCTION__.".time";
	$oldpid=@file_get_contents($EXEC_PID_FILE);
	if($unix->process_exists($oldpid,basename(__FILE__))){
		$timefile=$unix->file_time_min($EXEC_PID_FILE);
		if($timefile<15){
			$unix->events("Skipping, Already executed pid $oldpid {$timefile}Mn","/var/log/stats-appliance.log");
			ufdbguard_admin_events("Skipping, Already executed pid $oldpid {$timefile}Mn...", __FUNCTION__, __FILE__, __LINE__, "communicate");return ;}
		$kill=$unix->find_program("kill");
		shell_exec("$kill -9 $oldpid");
	}	
	

	@file_put_contents($EXEC_PID_FILE, getmypid());
	
	if($COMMANDS==null){$COMMANDS="BUILDCONF";}
	
	if($COMMANDS=="PING"){
		$time=$unix->file_time_min($EXEC_PID_TIME);
		if(!$GLOBALS["VERBOSE"]){
			if($time<5){return;}
		}
		@unlink($EXEC_PID_TIME);
		@file_put_contents($EXEC_PID_TIME, time());
		$bb=new blackboxes();
		$bb->NotifyAll("PING");
		return;
	}
	
	
	$t=time();
	$f=new squid_stats_appliance();
	$f->export_tables();
	$took=$unix->distanceOfTimeInWords($t,time(),true);
	ufdbguard_admin_events("Exporting MySQL datas done... took:$took", 
	__FUNCTION__, __FILE__, __LINE__, "communicate");
	$unix->events("Exporting MySQL datas done... took:$took","/var/log/stats-appliance.log");
	
	include_once(dirname(__FILE__)."/ressources/class.blackboxes.inc");
	$unix->events("Send order to appliance(s)","/var/log/stats-appliance.log");
	$bb=new blackboxes();
	$bb->NotifyAll("BUILDCONF");
	
	
	
}
function watchdog_config(){
	$unix=new unix();
	$monit=$unix->find_program("monit");
	$chmod=$unix->find_program("chmod");
	if(!is_file($monit)){return;}
	$sock=new sockets();
	$MonitConfig=unserialize(base64_decode($sock->GET_INFO("SquidWatchdogMonitConfig")));
	if(!isset($MonitConfig["watchdog"])){$MonitConfig["watchdog"]=1;}
	if(!isset($MonitConfig["watchdogMEM"])){$MonitConfig["watchdogMEM"]=1500;}
	if(!isset($MonitConfig["watchdogCPU"])){$MonitConfig["watchdogCPU"]=95;}
	if(!is_numeric($MonitConfig["watchdog"])){$MonitConfig["watchdog"]=1;}
	if(!is_numeric($MonitConfig["watchdogCPU"])){$MonitConfig["watchdogCPU"]=95;}
	if(!is_numeric($MonitConfig["watchdogMEM"])){$MonitConfig["watchdogMEM"]=1500;}	
	$reloadmonit=false;
	$monit_file="/etc/monit/conf.d/squid.monitrc";
	$conf=file("/etc/squid3/squid.conf");
	while (list ($index, $line) = each ($conf)){
		if(preg_match("#http_port\s+(.*)#", $line,$re)){
			if(!preg_match("#(transparent|intercept)#", $line)){
				$http_port=trim($re[1]);
				break;
			}
		}
	}
	echo "Starting......: [WATCH]: Squid Monit found port line:$http_port\n";
	if($http_port<>null){
		if(preg_match("#([0-9\.]+):([0-9]+)#", $http_port,$re)){
			$re[1]=str_replace("0.0.0.0", "127.0.0.1", $re[1]);
			echo "Starting......: [WATCH]:[$http_port] -> host {$re[1]} port `{$re[2]}` on line ". __LINE__."\n";
			$http_port2="if failed host {$re[1]} port {$re[2]}  then restart";
		}
		if(preg_match("#^([0-9]+)$#", $http_port,$re)){
			$re[1]=str_replace("0.0.0.0", "127.0.0.1", $re[1]);
			echo "Starting......: [WATCH]: if failed port {$re[1]} then  on line ". __LINE__."\n";
			$http_port2="if failed port {$re[1]} then restart";
		}		
	}
	
	
	if($MonitConfig["watchdog"]==0){
		echo "Starting......: [WATCH]: Squid Monit is not enabled ({$MonitConfig["watchdog"]})\n";
		if(is_file($monit_file)){
			@unlink($monit_file);
			@unlink("/usr/sbin/squid-monit-start");
			@unlink("/usr/sbin/squid-monit-stop");
			$reloadmonit=true;}
	}
	
	if($MonitConfig["watchdog"]==1){
		$pidfile=$unix->LOCATE_SQUID_PID();
		echo "Starting......: [WATCH]: Squid Monit is enabled check pid `$pidfile`\n";
		$reloadmonit=true;
		$f[]="check process squid";
   		$f[]="with pidfile $pidfile";
   		$f[]="start program = \"/usr/sbin/squid-monit-start\"";
   		$f[]="stop program =  \"/usr/sbin/squid-monit-stop\"";
   		if($http_port2<>null){$f[]="$http_port2";}
   		if($MonitConfig["watchdogMEM"]){
  			$f[]="if totalmem > {$MonitConfig["watchdogMEM"]} MB for 5 cycles then alert";
   		}
   		if($MonitConfig["watchdogCPU"]>0){
   			$f[]="if cpu > {$MonitConfig["watchdogCPU"]}% for 5 cycles then alert";
   		}
	   $f[]="if 5 restarts within 5 cycles then timeout";
	   
	   @file_put_contents($monit_file, @implode("\n", $f));
	   $f=array();
	   $f[]="#!/bin/sh";
	   $f[]="PATH=/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin:/usr/X11R6/bin";
	   $f[]=$unix->LOCATE_PHP5_BIN()." /usr/share/artica-postfix/exec.squid.watchdog.php --start --watchdog";
	   $f[]="exit 0\n";
 	   @file_put_contents("/usr/sbin/squid-monit-start", @implode("\n", $f));
 	   shell_exec("$chmod 777 /usr/sbin/squid-monit-start");
	   $f=array();
	   $f[]="#!/bin/sh";
	   $f[]="PATH=/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin:/usr/X11R6/bin";
	   $f[]=$unix->LOCATE_PHP5_BIN()." /usr/share/artica-postfix/exec.squid.watchdog.php --stop --watchdog";
	   $f[]="exit 0\n";
 	   @file_put_contents("/usr/sbin/squid-monit-stop", @implode("\n", $f));
 	   shell_exec("$chmod 777 /usr/sbin/squid-monit-stop");	   
	}
	
	if($reloadmonit){
		$unix->THREAD_COMMAND_SET("/usr/share/artica-postfix/bin/artica-install --monit-check");
	}
	
}

function checkdatabase(){
	
	$f["webfilter_aclsdynamic"]=true;
	$f["webfilter_aclsdynlogs"]=true;
	$f["webfilter_assoc_groups"]=true;
	$f["webfilter_avwhitedoms"]=true;
	$f["webfilter_bannedexts"]=true;
	$f["webfilter_bannedextsdoms"]=true;
	$f["webfilter_blkcnt"]=true;
	$f["webfilter_blkgp"]=true;
	$f["webfilter_blklnk"]=true;
	$f["webfilter_blks"]=true;
	$f["webfilter_certs"]=true;
	$f["webfilter_dnsbl"]=true;
	$f["webfilter_group"]=true;
	$f["webfilter_members"]=true;
	$f["webfilter_rules"]=true;
	$f["webfilter_terms"]=true;
	$f["webfilter_termsassoc"]=true;
	$f["webfilter_termsg"]=true;
	$f["webfilter_ufdbexpr"]=true;
	$f["webfilter_ufdbexprassoc"]=true;
	$f["webfilter_updateev"]=true;
	$f["webfilters_backupeddbs"]=true;
	$f["webfilters_bigcatzlogs"]=true;
	$f["webfilters_blkwhlts"]=true;
	$f["webfilters_categories_caches"]=true;
	$f["webfilters_databases_disk"]=true;
	$f["webfilters_dbstats"]=true;
	$f["webfilters_dtimes_blks"]=true;
	$f["webfilters_dtimes_rules"]=true;
	$f["webfilters_ipaddr"]=true;
	$f["webfilters_nodes"]=true;
	$f["webfilters_quotas"]=true;
	$f["webfilters_rewriteitems"]=true;
	$f["webfilters_rewriterules"]=true;
	$f["webfilters_schedules"]=true;
	$f["webfilters_sqaclaccess"]=true;
	$f["webfilters_sqacllinks"]=true;
	$f["webfilters_sqacls"]=true;
	$f["webfilters_sqaclsports"]=true;
	$f["webfilters_sqgroups"]=true;
	$f["webfilters_sqitems"]=true;
	$f["webfilters_sqtimes_assoc"]=true;
	$f["webfilters_sqtimes_rules"]=true;
	$f["webfilters_thumbnails"]=true;
	$f["webfilters_updates"]=true;
	$f["webfilters_usersasks"]=true;
	$f["websites_caches_params"]=true;
	$q=new mysql_squid_builder();
	if(!$q->TestingConnection()){
		echo "Starting......: [MYSQL]: Connection failed...\n";
		return;
	}
	
	$build=false;
	while (list ($tablename, $DGRULE) = each ($numeric_fields)){
		if(!$q->TABLE_EXISTS($tablename)){
			echo "Starting......: [MYSQL]: Missing table `$tablename`\n";
			$build=true;
		}
	}
	
	if($build){
		echo "Starting......: [MYSQL]: Construct database\n";
		$q->CheckTables(null,true);
	}
	
}


function writeinitd(){
	
	$unix=new unix();
	$php=$unix->LOCATE_PHP5_BIN();
	$chmod=$unix->find_program("chmod");
	$ln=$unix->find_program("ln");
	$php=$unix->LOCATE_PHP5_BIN();
	shell_exec("$php /usr/share/artica-postfix/exec.squid.watchdog.php --init");
}

function caches_infos(){
	if(!$GLOBALS["VERBOSE"]){
		if(!$GLOBALS["FORCE"]){
			if(system_is_overloaded(basename(__FILE__))){
				EventsWatchdog("Overloaded system, aborting task...");
				writelogs("Overloaded system, aborting task...",__FUNCTION__,__FILE__,__LINE__);
				die();
			}
		}
	}
	if($GLOBALS["VERBOSE"]){echo "init...\n";}
	$unix=new unix();
	$sock=new sockets();
	$q=new mysql_squid_builder();
	if(!$GLOBALS["FORCE"]){
		$cacheFile="/etc/artica-postfix/pids/".basename(__FILE__).".".__FUNCTION__.".time";
		$CacheTime=$unix->file_time_min($cacheFile);
		if($CacheTime<15){
			EventsWatchdog("Max 15Mn, current=$CacheTime ($cacheFile)...");
			if($GLOBALS["VERBOSE"]){echo "Max 15Mn, current=$CacheTime\n";}return;}
		@unlink($cacheFile);
		@file_put_contents($cacheFile, time());
	}
	
	if($GLOBALS["VERBOSE"]){echo "q->CheckTables()...\n";}
	$q->CheckTables();
	$array=$unix->squid_get_cache_infos();
	
	for($i=0;$i<10;$i++){
			$check=true;
			
			if(!is_array($array)){
				if($GLOBALS["VERBOSE"]){echo "unix->squid_get_cache_infos() Not an array...\n";}
				$check=false;
				sleep(1);
				$array=$unix->squid_get_cache_infos();
				continue;
				
			}
			
			if(count($array)==0){
				if($GLOBALS["VERBOSE"]){echo "unix->squid_get_cache_infos() O items !!\n";}
				$check=false;
				sleep(1);
				$array=$unix->squid_get_cache_infos();
				continue;
			}
			if($check){
				break;
			}
	
	}
	
	if(!is_array($array)){if($GLOBALS["VERBOSE"]){echo "unix->squid_get_cache_infos() Not an array...\n";}return;}	
	if(count($array)==0){if($GLOBALS["VERBOSE"]){echo "unix->squid_get_cache_infos() O items !!...\n";}return;}
	
	
	
	$uuid=base64_decode($sock->getFrameWork("cmd.php?system-unique-id=yes"));
	
	$profix="INSERT IGNORE INTO cachestatus(uuid,cachedir,maxsize,currentsize,pourc) VALUES ";
	while (list ($directory, $arrayDir) = each ($array)){
		$directory=trim($directory);
		if($directory==null){continue;}
		if($GLOBALS["VERBOSE"]){echo "('$uuid','$directory','{$arrayDir["MAX"]}','{$arrayDir["CURRENT"]}','{$arrayDir["POURC"]}')\n";}
		$f[]="('$uuid','$directory','{$arrayDir["MAX"]}','{$arrayDir["CURRENT"]}','{$arrayDir["POURC"]}')";
	}
	if(count($f)>0){
		$q->QUERY_SQL("DELETE FROM cachestatus WHERE uuid='$uuid'");
		$q->QUERY_SQL("$profix".@implode(",", $f));
		if(!$q->ok){echo $q->mysql_error."\n";}
	}	
}

function restart_squid(){
	$unix=new unix();
	$timeFile="/etc/artica-postfix/pids/".basename(__FILE__).".".__FUNCTION__.".time";
	$TimeMin=$unix->file_time_min($timeFile);
	if($TimeMin<60){
		WriteToSyslogMail("restart_squid():: Fatal: Unable to restart squid-cache {$TimeMin}Mn need at least 60mn", basename(__FILE__));
		return;
		
	}
	
	@unlink($timeFile);
	@file_put_contents($timeFile, time());
	
	WriteMyLogs("Task = {$GLOBALS["SCHEDULE_ID"]}",__FUNCTION__,__FILE__,__LINE__);
	if(is_file("/etc/artica-postfix/WEBSTATS_APPLIANCE")){
			include_once(dirname(__FILE__)."/ressources/class.blackboxes.inc");
			$q=new mysql_blackbox();
			$sql="SELECT nodeid,hostname FROM nodes";
			$results=$q->QUERY_SQL($sql);
			ufdbguard_admin_events("Task `restart squid` is executed` for ".mysql_num_rows($results) ." nodes", __FUNCTION__, __FILE__, __LINE__, "tasks");
			while($ligne=mysql_fetch_array($results,MYSQL_ASSOC)){
				$blk=new blackboxes($ligne["nodeid"]);
				ufdbguard_admin_events("Restart squid on {$ligne["hostname"]}", __FUNCTION__, __FILE__, __LINE__, "tasks");
				$blk->restart_squid();
			}	

		return;
		
	}
	
	$nohup=$unix->find_program("nohup");
	exec("/etc/init.d/artica-postfix restart squid-cache 2>&1",$results);
	ufdbguard_admin_events("Task `restart squid` was executed`\n".@implode("\n", $results) , __FUNCTION__, __FILE__, __LINE__, "tasks");
	
}

function restart_kav4proxy(){
	$unix=new unix();
	$nohup=$unix->find_program("nohup");
	exec("/etc/init.d/artica-postfix restart kav4proxy 2>&1",$results);
	ufdbguard_admin_events("Task `restart Kav4Proxy` was executed`\n".@implode("\n", $results) , __FUNCTION__, __FILE__, __LINE__, "tasks");
}


function extract_schedules(){
	$sql="SELECT *  FROM webfilters_schedules WHERE enabled=1";
	$q=new mysql_squid_builder();
	$results = $q->QUERY_SQL($sql);	
	while ($ligne = mysql_fetch_assoc($results)) {
		$TaskType=$ligne["TaskType"];
		$TimeText=$ligne["TimeText"];		
		$TimeDescription=mysql_escape_string2($ligne["TimeDescription"]);
		$lines[]="\$array[$TaskType]=array(\"TimeText\"=>\"$TimeText\",\"TimeDescription\"=>\"$TimeDescription\");";
		
	}
	echo implode("\n", $lines);
	
}

function run_schedules($ID){
	$GLOBALS["SCHEDULE_ID"]=$ID;
	writelogs("Task $ID",__FUNCTION__,__FILE__,__LINE__);
	$q=new mysql_squid_builder();
	$ligne=mysql_fetch_array($q->QUERY_SQL("SELECT TaskType FROM webfilters_schedules WHERE ID=$ID"));
	
	$TaskType=$ligne["TaskType"];
	if($TaskType==0){continue;}	
	if(!isset($q->tasks_processes[$TaskType])){ufdbguard_admin_events("Unable to understand task type `$TaskType` For this task" , __FUNCTION__, __FILE__, __LINE__, "tasks");return;}
	$script=$q->tasks_processes[$TaskType];
	$unix=new unix();
	$nohup=$unix->find_program("nohup");
	$php5=$unix->LOCATE_PHP5_BIN();
	$WorkingDirectory=dirname(__FILE__);
	$cmd="$nohup $php5 $WorkingDirectory/$script --schedule-id=$ID >/dev/null 2>&1 &";
	writelogs("Task {$GLOBALS["SCHEDULE_ID"]} is executed with `$cmd` ",__FUNCTION__,__FILE__,__LINE__);
	ufdbguard_admin_events("Task is executed with `$cmd`" , __FUNCTION__, __FILE__, __LINE__, "tasks");
	shell_exec($cmd);
	
}

function build_schedules_tests(){
	$unix=new unix();
	if(!$unix->IsSquidTaskCanBeExecuted()){
		EventsWatchdog("IsSquidTaskCanBeExecuted() return false");
		
		return;}
	$pidTime="/etc/artica-postfix/pids/".basename(__FILE__).".".__FUNCTION__.".time";
	
	$pidTimeINT=$unix->file_time_min($pidTime);
	if(!$GLOBALS["VERBOSE"]){
		if($pidTimeINT<5){
			EventsWatchdog("Too short time to execute the process ($pidTime)");
			writelogs("To short time to execute the process",__FILE__,__FUNCTION__,__LINE__);
			return;
		}
	}

	@file_put_contents($pidTime, time());
	
	if(!is_file("/etc/artica-postfix/squid.schedules")){
		echo "No schedule yet....\n";
		shell_exec("/etc/init.d/artica-postfix restart watchdog");
	}
	
	
	$q=new mysql_squid_builder();
	$ligne=mysql_fetch_array($q->QUERY_SQL("SELECT TimeText FROM webfilters_schedules WHERE TaskType=14"));
	if($ligne["TimeText"]==null){
		$sql="INSERT INTO `webfilters_schedules` (`TimeText`, `TimeDescription`, `TaskType`, `enabled`) VALUES ('30 6 * * *', 'Optimize all tables  each day at 06h30', 14, 1);";
		$q->QUERY_SQL($sql);
		if(!$q->ok){writelogs("$q->mysql_error",__FUNCTION__,__FILE__,__LINE__);return;}
		shell_exec("/etc/init.d/artica-postfix restart watchdog");
	}
}

function rotate_logs(){
	$pidfile="/etc/artica-postfix/pids/".basename(__FILE__).".".__FUNCTION__.".pid";
	$unix=new unix();
	$oldpid=$unix->get_pid_from_file($pidfile);
	if($unix->process_exists($oldpid,basename(__FILE__))){
		if($GLOBALS["VERBOSE"]){echo "Already executed pid $oldpid\n";}
		ufdbguard_admin_events("Already executed pid $oldpid",__FILE__,__FUNCTION__,__LINE__,"logs");
		return;
	}
	
	$getmypid=getmypid();
	@file_put_contents($pidfile, getmypid());	
	
	
	
	
	$unix=new unix();
	$GLOBALS["SQUIDBIN"]=$unix->LOCATE_SQUID_BIN();
	$q=new mysql_squid_builder();
	
	if($GLOBALS["VERBOSE"]){echo date("H:i:s")."[$getmypid] Checking table squid_storelogs...\n";}
	if(!$q->TABLE_EXISTS("squid_storelogs")){$q->CheckTables();}
	
	
	if($GLOBALS["VERBOSE"]){echo date("H:i:s")."[$getmypid] Ask proxy to rotate logs....\n";}
	shell_exec("{$GLOBALS["SQUIDBIN"]} -k rotate >/dev/null 2>&1");
	
	if($GLOBALS["VERBOSE"]){echo date("H:i:s")."[$getmypid] Restart watchdog, please wait...\n";}
	shell_exec("/etc/init.d/auth-tail restart >/dev/null 2>&1");
	$t=time();
	$c=0;
	$globalSize=0;
	if($GLOBALS["VERBOSE"]){echo date("H:i:s")."[$getmypid] Scanning log path...\n";}
	foreach (glob("/var/log/squid/*") as $filename) {
		$SourceFileName=$filename;;
		$ext = $unix->file_extension($filename);
		
		$basename=basename($filename);
		$tt=explode(".", $basename);
		$fileprefix="{$tt[0]}-";
		if($GLOBALS["VERBOSE"]){echo date("H:i:s")."[$getmypid] Found: $basename Prefix:$fileprefix ext:$ext\n";}
		
		
		if(!is_numeric($ext)){
			if($GLOBALS["VERBOSE"]){echo date("H:i:s")."[$getmypid] skipping $basename\n";}
			continue;}
		$basename=str_replace(".$ext", "", $basename);
		$ext = $unix->file_extension($basename);
		$filetime=filemtime($filename);
		$filedate=date('Y-m-d H:i:s',$filetime);
		$filesize_src=$unix->file_size($filename);
		if($filesize_src==0){
			if($GLOBALS["VERBOSE"]){echo date("H:i:s")."[$getmypid] $basename $filename 0 bytes, remove it...\n";}
			@unlink($filename);
			continue;
		}
		
		
		if($GLOBALS["VERBOSE"]){echo date("H:i:s")."[$getmypid] $basename ($ext) $filetime $filedate compress it...\n";}
		$compressed_file="/tmp/".basename($filename).".gz";
		if(is_file($compressed_file)){@unlink($compressed_file);}
		if(!$unix->compress($filename, "$compressed_file")){
			if($GLOBALS["VERBOSE"]){echo date("H:i:s")."[$getmypid] Unable to compress $filename\n";}
			ufdbguard_admin_events("Unable to compress $filename", __FUNCTION__, __FILE__, __LINE__, "logs");
			continue;
		}
		@chmod("/tmp", 0777);
		@chmod($compressed_file, 0777);
		$filesize_comp=$unix->file_size($compressed_file);
		$sqlfilename="$fileprefix$filetime.$ext.gz";
		if($GLOBALS["VERBOSE"]){echo date("H:i:s")."[$getmypid] Creating $sqlfilename in MySQL database...\n";}
		$sql = "INSERT INTO `squid_storelogs` (`filename`,`fileext`,`filesize`,`Compressedsize`,`filecontent`,`filetime`) 
		VALUES ('$sqlfilename','$ext','$filesize_src','$filesize_comp', LOAD_FILE('$compressed_file'),'$filedate')";
		$q->QUERY_SQL($sql);
		if(!$q->ok){
			if($GLOBALS["VERBOSE"]){echo date("H:i:s")."[$getmypid] Fatal $q->mysql_error\n";}
			ufdbguard_admin_events("Fatal $q->mysql_error", __FUNCTION__, __FILE__, __LINE__, "logs");@unlink($compressed_file);continue;}
		
			if($GLOBALS["VERBOSE"]){echo date("H:i:s")."[$getmypid] Remove $SourceFileName\n";}	
		@unlink($compressed_file);
		@unlink($SourceFileName);
		$c++;
		$globalSize=$globalSize+$filesize_src;
		
		}	
	
	$took=$unix->distanceOfTimeInWords($t,time(),true);
	
	
	if($c>0){
		$addedtext=" $c {files} {size}:".FormatBytes($globalSize/1024);
	}
	$sock=new sockets();
	if($GLOBALS["VERBOSE"]){echo date("H:i:s")."[$getmypid] Done, $addedtext {took} $took\n";}
	
	$sock->TOP_NOTIFY("{proxy_logrotate_done}$addedtext {took} $took","info");
}

function build_schedules($notfcron=false){
	$unix=new unix();
	$q=new mysql_squid_builder();
	$sock=new sockets();
	@mkdir("/var/log/artica-postfix/youtube",0755,true);
	$pidfile="/etc/artica-postfix/pids/".basename(__FILE__).".".__FUNCTION__.".pid";
	$pidTime="/etc/artica-postfix/pids/".basename(__FILE__).".".__FUNCTION__.".time";
	$oldpid=$unix->get_pid_from_file($pidfile);
	if($unix->process_exists($oldpid,basename(__FILE__))){
		writelogs("Already executed pid $oldpid",__FILE__,__FUNCTION__,__LINE__);
		return;
	}
	
	$EnableRemoteStatisticsAppliance=$sock->GET_INFO("EnableRemoteStatisticsAppliance");
	if(!is_numeric($EnableRemoteStatisticsAppliance)){$EnableRemoteStatisticsAppliance=0;}		
	
	@file_put_contents($pidfile, getmypid());
	
	$pidTimeINT=$unix->file_time_min($pidTime);
	if(!$GLOBALS["VERBOSE"]){
		if($pidTimeINT<1){
			writelogs("To short time to execute the process",__FILE__,__FUNCTION__,__LINE__);
			return;
		}
	}
	
	@file_put_contents($pidTime, time());
	if(!$unix->IsSquidTaskCanBeExecuted()){
		if($GLOBALS["VERBOSE"]){echo "These tasks cannot be executed in this server\n";}
		return;
	}
	$q->CheckDefaultSchedules();
	if($q->COUNT_ROWS("webfilters_schedules")==0){die();}
	
	
	$sql="SELECT *  FROM webfilters_schedules WHERE enabled=1";
	
	$results = $q->QUERY_SQL($sql);	
	if(!$q->ok){return;}	
	
	@unlink("/etc/cron.d/SquidTailInjector");
	$php5=$unix->LOCATE_PHP5_BIN();
	$WorkingDirectory=dirname(__FILE__);
	$chmod=$unix->find_program("chmod");
	foreach (glob("/etc/cron.d/*") as $filename) {
		$file=basename($filename);
		
		if(preg_match("#squidsch-[0-9]+#", $filename)){if($GLOBALS["VERBOSE"]){echo "Removing old task $file\n";}@unlink($filename);}
	}
	@unlink("/etc/artica-postfix/TASKS_SQUID_CACHE.DB");
	$settings=unserialize(base64_decode($sock->GET_INFO("FcronSchedulesParams")));
	if(!isset($settings["max_nice"])){$settings["max_nice"]=19;}
	if(!isset($settings["max_load_avg5"])){$settings["max_load_avg5"]=3;}
	if(!isset($settings["max_load_wait"])){$settings["max_load_wait"]=10;}
	if(!is_numeric($settings["max_load_avg5"])){$settings["max_load_avg5"]="3";}
	if(!is_numeric($settings["max_load_wait"])){$settings["max_load_wait"]="10";}
	if(!is_numeric($settings["max_nice"])){$settings["max_nice"]="19";}	
	$max_load_wait=$settings["max_load_wait"];	
	
	$finalsettings="nice({$settings["max_nice"]}),lavg5({$settings["max_load_avg5"]}),until($max_load_wait)";
	@unlink("/etc/artica-postfix/squid.schedules");
	$nice=EXEC_NICE();
	$q=new mysql_squid_builder();
	$c=0;$d=0;
	while ($ligne = mysql_fetch_assoc($results)) {
		$allminutes="1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59";
		$TaskType=$ligne["TaskType"];
		$TimeText=$ligne["TimeText"];
		if($TaskType==0){continue;}
		if($ligne["TimeText"]==null){continue;}
		if($EnableRemoteStatisticsAppliance==1){if($q->tasks_remote_appliance[$TaskType]){$d++;continue;}}
		
		$md5=md5("$TimeText$TaskType");
		if(isset($alreadydone[$md5])){if($GLOBALS["VERBOSE"]){echo "Starting......: artica-postfix watchdog task {$ligne["ID"]} already set\n";}continue;}
		$alreadydone[$md5]=true;		
		
		
		if(!isset($q->tasks_processes[$TaskType])){
			if($GLOBALS["VERBOSE"]){echo "Starting......: artica-postfix task {$ligne["ID"]} no such task...\n";}
			$d++;continue;
		}
		if(isset($q->tasks_disabled[$TaskType])){
			if($GLOBALS["VERBOSE"]){echo "Starting......: artica-postfix task {$ligne["ID"]} is disabled or did not make sense...\n";}
			$d++;continue;}
		$script=$q->tasks_processes[$TaskType];
		if($GLOBALS["VERBOSE"]){echo "Starting......: artica-postfix create task {$ligne["ID"]} type $TaskType..\n";}
		if(trim($ligne["TimeText"]=="$allminutes * * * *")){$ligne["TimeText"]="* * * * *";}
		
		$f=array();
		$f[]="MAILTO=\"\"";
		$f[]="{$ligne["TimeText"]}  root $nice $php5 $WorkingDirectory/exec.schedules.php --run-squid {$ligne["ID"]} >/dev/null 2>&1";
		$f[]="";
		
		@file_put_contents("/etc/cron.d/squidsch-{$ligne["ID"]}", @implode("\n", $f));
		$c++;
		continue;
		
		
		if(trim($ligne["TimeText"]=="20,40,59 * * * *")){
			$f[]="@$finalsettings,mail(false) 20 $php5 $WorkingDirectory/$script --schedule-id={$ligne["ID"]} >/dev/null 2>&1";
			continue;
		}
		
		
		
		if(trim($ligne["TimeText"]=="0 * * * *")){
			$f[]="@$finalsettings,mail(false) 1h $php5 $WorkingDirectory/$script --schedule-id={$ligne["ID"]} >/dev/null 2>&1";
			continue;		
		}
		if(trim($ligne["TimeText"]=="10,20,30,40,50 * * * *")){
			$f[]="@$finalsettings,mail(false) 10 $php5 $WorkingDirectory/$script --schedule-id={$ligne["ID"]} >/dev/null 2>&1";
			continue;		
		}		
		
		
		
		if(trim($ligne["TimeText"]=="0 0,3,5,7,9,11,13,15,17,19,23 * * *")){
			$f[]="@$finalsettings,mail(false) 3h $php5 $WorkingDirectory/$script --schedule-id={$ligne["ID"]} >/dev/null 2>&1";
			continue;		
		}
		
		if(trim($ligne["TimeText"]=="0 2,4,6,8,10,12,14,16,18,20,22 * * *")){
			$f[]="@$finalsettings,mail(false) 2h $php5 $WorkingDirectory/$script --schedule-id={$ligne["ID"]} >/dev/null 2>&1";
			continue;		
		}
		if(trim($ligne["TimeText"]=="0 0,2,4,6,8,10,12,14,16,18,20,22 * * *")){
			$f[]="@$finalsettings,mail(false) 2h $php5 $WorkingDirectory/$script --schedule-id={$ligne["ID"]} >/dev/null 2>&1";
			continue;		
		}		
		
		$f[]="&$finalsettings,mail(false) {$ligne["TimeText"]} $php5 $WorkingDirectory/$script --schedule-id={$ligne["ID"]} >/dev/null 2>&1";
	}
	
	@file_put_contents("/etc/artica-postfix/squid.schedules",implode("\n",$f));
	if($notfcron){
		echo "Starting......: Squid $c scheduled tasks ($d disabled)\n";
		return;
	}
	$cron_path=$unix->find_program("cron");
	$kill=$unix->find_program("kill");
	$cron_pid=null;
	if(is_file("/var/run/cron.pid")){$cron_pid=$unix->get_pid_from_file("/var/run/cron.pid");}
	if(!$unix->process_exists($cron_pid)){$cron_pid=0;}
	if(!is_numeric($cron_pid) OR $cron_pid<5){$cron_pid=$unix->PIDOF("$cron_path");}
	if($cron_pid>5){
		if($GLOBALS["VERBOSE"]){echo "Starting......: artica-postfix reloading $cron_path [$cron_pid]...\n";}
		shell_exec("$kill -HUP $cron_pid");
	}
	
	if($GLOBALS["VERBOSE"]){echo "Starting......: artica-postfix reloading fcron...\n";}
	$nohup=$unix->find_program("nohup");
	shell_exec("$nohup /etc/init.d/artica-postfix restart fcron >/dev/null 2>&1 &");
	
}

function WriteMyLogs($text,$function=null,$file=null,$line=0){
	if(!isset($GLOBALS["MYPID"])){$GLOBALS["MYPID"]=getmypid();}
	if(function_exists("debug_backtrace")){
		$trace=debug_backtrace();
		if(isset($trace[1])){
			$sourcefile=basename($trace[1]["file"]);
			$sourcefunction=$trace[1]["function"];
			$sourceline=$trace[1]["line"];
		}
		
	}	
	$file=basename(__FILE__);
	if($function==null){$function=$sourcefunction;}
	if($line==0){$line=$sourceline;}
	if(!isset($GLOBALS["CLASS_UNIX"])){$GLOBALS["CLASS_UNIX"]=new unix();}
	$GLOBALS["CLASS_UNIX"]->events($text,"/var/log/squid.watchdog.log",false,$sourcefunction,$sourceline);
}

function squid_reconfigure_build_tool(){
	$unix=new unix();
	$squidbin=$unix->find_program("squid3");
	if(!is_file($squidbin)){$squidbin=$unix->find_program("squid");}	
	$php5=$unix->find_program("php5");
	$f[]="#! /bin/sh";
	$f[]="echo \"Reconfiguring proxy, please wait\"";
	$f[]="$php5 ".__FILE__." --build \$1";
	$f[]="exit 0";
	@file_put_contents("/bin/squidreconf", @implode("\n", $f));
	@chmod("/bin/squidreconf",0755);
}

function bandwithdebug(){
	$GLOBALS["VERBOSE"]=true;
	ini_set('display_errors', 1);	ini_set('html_errors',0);ini_set('display_errors', 1);ini_set('error_reporting', E_ALL);
	$ban=new squid_bandwith_builder();
	echo $ban->compile();
}

function output_acls(){
	$q=new squidbee();
	$acls=new squid_acls_groups();
	$squid=new squidbee();
	$sock=new sockets();
	$refreshpattern=$squid->refresh_pattern_list();
	$SquidBubbleMode=$sock->GET_INFO("SquidBubbleMode");
	if(!is_numeric($SquidBubbleMode)){$SquidBubbleMode=0;}
	$acl=new squid_acls_quotas_time();
	$squid_acls_quotas_time= $acl->build()."\n";
	$acls=new squid_acls();
	$acls->Build_Acls();
	$aclgroups=new squid_acls_groups();
	
	if(count($acls->acls_array)>0){
		$ACLS_TO_ADD=@implode("\n",$acls->acls_array);
	}
	echo "\nAcls\n-----------------\n".$ACLS_TO_ADD."\n-----------------\n\n";
	echo "\nQuotas Time\n-----------------\n".$squid_acls_quotas_time."\n-----------------\n\n";
	echo "\n\n-----------------\n".$aclgroups->buildacls_order(0)."\n-----------------\n\n";
	
	echo "######\n";
	$tcp_outgoing_address=$aclgroups->buildacls_bytype("tcp_outgoing_address");
	
	echo "\n\ntcp_outgoing_address -----------------\n";
	if(count($tcp_outgoing_address)>0){
		echo "Starting......: [ACLS]: Engine tcp_outgoing_address ".count($tcp_outgoing_address)." rules..\n";
		while (list ($index, $line) = each ($tcp_outgoing_address) ){
			
			echo "tcp_outgoing_address $line\n";}
	}else{
		echo "Starting......: [ACLS]: ACL Engine tcp_outgoing_address No rules..\n";
	}
	echo "\n-----------------\n\n";
	echo "######\n";
	
	$q=new mysql_squid_builder();
		
	if($SquidBubbleMode==1){
		
		$sql="SELECT * FROM webfilters_sqaclsports ORDER BY aclport";
		$results = $q->QUERY_SQL($sql);
		while ($ligne = mysql_fetch_assoc($results)) {
			echo "\n\n#--------- ACLS {$ligne["portname"]}\n\n";
			echo "\n\n".$aclgroups->buildacls_order($ligne["aclport"])."\n\n";
		}
	}	
	
	$acls_rules=$acls->build_http_access(0);
	echo "\n\n# Builded acls from engine [".count($acls_rules)."] items.\n";
	if(count($acls_rules)>0){
		echo "\n\n".@implode("\n", $acls_rules)."\n";
	}	
	if($SquidBubbleMode==1){
		$sql="SELECT * FROM webfilters_sqaclsports ORDER BY aclport";
		$results = $q->QUERY_SQL($sql);
		while ($ligne = mysql_fetch_assoc($results)) {
			$acls_rules=$acls->build_http_access($ligne["aclport"]);
			echo "\n\n# Builded acls from engine {$ligne["portname"]} [".count($acls_rules)."] items.\n";
			if(count($acls_rules)>0){
				echo "\n\n".@implode("\n", $acls_rules)."\n";
			}
		}		
		
	}
	
	
	echo "\n\n-----------------\n".$refreshpattern."\n-----------------\n\n";
}

function import_webfilter($filename){
	if(!is_file($filename)){echo "$filename no such file\n";return;}
	$unix=new unix();	
	$ext=Get_extension($filename);
	if($ext<>"gz"){
		echo "$filename not a compressed file\n";
		return;
	}

	$destinationfile=$unix->FILE_TEMP();
	$sqlsourcefile=$unix->FILE_TEMP().".sql";
	if(!$unix->uncompress($filename, $destinationfile)){
		echo "$filename corrupted GZ file...\n";
		;return;
	}

	$contentArray=unserialize(base64_decode(@file_get_contents($destinationfile)));
	if(!is_array($contentArray)){
		echo "$filename corrupted file not an array...\n";
		return;		
	}
	
	@file_put_contents($sqlsourcefile, $contentArray["SQL"]);
	$sock=new sockets();
	echo "Saving default rule...\n";
	$sock->SaveClusterConfigFile($contentArray["DansGuardianDefaultMainRule"], "DansGuardianDefaultMainRule");
	$mysqlbin=$unix->find_program("mysql");
	$q=new mysql_squid_builder();
	$password=null;
	$localdatabase="squidlogs";
	$q=new mysql_squid_builder();
	$cmdline="$mysqlbin --batch --force $q->MYSQL_CMDLINES";
	$cmd="$cmdline --database=$localdatabase <$sqlsourcefile 2>&1";
	if($GLOBALS["VERBOSE"]){echo $cmd."\n";}
	exec($cmd,$results);
	while (list ($key, $value) = each ($results)){
		echo "$value\n";
		
	}
	
}


function import_acls($filename){
	if(!is_file($filename)){echo "$filename no such file\n";return;}
	$unix=new unix();
	
	$ext=Get_extension($filename);
	if($ext=="acl"){
		import_acls_extacl($filename,null,0);
		return;
	}
	
	$destinationfile=$unix->FILE_TEMP();
	if(!$unix->uncompress($filename, $destinationfile)){
		echo "$filename corrupted GZ file...\n";
		;return;
	}
	
	$mysqlbin=$unix->find_program("mysql");
	$q=new mysql_squid_builder();
	$password=null;
	$localdatabase="squidlogs";
	
	
	
	$q=new mysql_squid_builder();
	
	$cmdline="$mysqlbin --batch --force $q->MYSQL_CMDLINES";
	$cmd="$cmdline --database=$localdatabase <$destinationfile 2>&1";
	if($GLOBALS["VERBOSE"]){echo $cmd."\n";}
	shell_exec($cmd);
	
}

function import_acls_extacl($filename=null,$ARRAY,$aclgpid=0){
	$q=new mysql_squid_builder();
	$acl=new squid_acls_groups();
	if($filename<>null){
		if(is_file($filename)){
			$ARRAY=unserialize(base64_decode(@file_get_contents($filename)));
		}
	}
	
	
	
	if(!is_array($ARRAY)){
		echo "$filename, unable to decode Array()\n";return;
	}
	
	if(!isset($ARRAY["webfilters_sqacls"])){
		echo "$filename, unable to decode webfilters_sqacls (".__LINE__.")\n";
		return;
	}
	
	
	if(!is_array($ARRAY["webfilters_sqacls"])){
		echo "$filename, unable to decode webfilters_sqacls\n";return;
	}
	
	if(isset($ARRAY["webfilters_sqaclaccess"])){
		if(!is_array($ARRAY["webfilters_sqaclaccess"])){
			if(!isset($ARRAY["SUBRULES"])){
				echo "$filename, unable to decode webfilters_sqaclaccess\n";return;
			}
		}
	}	
	
	if(!isset($ARRAY["SUBRULES"])){
		if(!is_array($ARRAY["webfilters_sqgroups"])){
			echo "$filename, unable to decode webfilters_sqgroups\n";return;
		}
	}	

	$keys=array();$values=array();
	while (list ($key, $value) = each ($ARRAY["webfilters_sqacls"])){
		$keys[]="`$key`";
		$values[]="'".mysql_escape_string2($value)."'";
		
	}
	if($aclgpid>0){
		echo "Prepare SUB-ACL Master ACL:$aclgpid\n";
		$keys[]="`aclgpid`";
		$values[]="'$aclgpid'";
	}
	
	$sql="INSERT IGNORE INTO webfilters_sqacls (".@implode(",", $keys).") VALUES (".@implode(",", $values).")";
	$q->QUERY_SQL($sql);
	if(!$q->ok){echo $q->mysql_error."\n$sql\n";return;}
	$ACLID=$q->last_id;
	
	
	echo "*** New ACL $ACLID ***\n";
	
	
	if(isset($ARRAY["SUBRULES"])){
		if(is_array($ARRAY["SUBRULES"])){
			while (list ($index, $arrayrule) = each ($ARRAY["SUBRULES"])){
				if($GLOBALS["VERBOSE"]){echo "import_acls_extacl(null,$arrayrule,$ACLID)\n";}
				import_acls_extacl(null,$arrayrule,$ACLID);
			}
		}
	}
	
	
	
	$keys=array();$values=array();
	if(isset($ARRAY["webfilters_sqaclaccess"])){
		$acl->aclrule_edittype($ACLID, $ARRAY["webfilters_sqaclaccess"]["httpaccess"], $ARRAY["webfilters_sqaclaccess"]["httpaccess_value"]);
		echo "New sqaclaccess for $ACLID {$ARRAY["webfilters_sqaclaccess"]["httpaccess"]}\n";
	}
	
	
	if(isset($ARRAY["webfilters_sqgroups"])){
			while (list ($index, $grouparray) = each ($ARRAY["webfilters_sqgroups"])){
				$GROUP_ARRAY=$grouparray["GROUP"];
				$GROUP_ITEMS=$grouparray["ITEMS"];
				$GROUP_DYN=$grouparray["DYN"];
				$keys=array();$values=array();
				while (list ($key, $value) = each ($GROUP_ARRAY)){
					$keys[]="`$key`";
					$values[]="'".mysql_escape_string2($value)."'";
				}
				$sql="INSERT IGNORE INTO webfilters_sqgroups (".@implode(",", $keys).") VALUES (".@implode(",", $values).")";
				$q->QUERY_SQL($sql);
				if(!$q->ok){echo $q->mysql_error."\n$sql\n";return;}
				$GPID=$q->last_id;
				$GROUPSACLS[$GPID]=true;
				
				
				while (list ($index, $itemsArray) = each ($GROUP_ITEMS)){
					$keys=array();$values=array();
				
					while (list ($key, $value) = each ($itemsArray)){
						$keys[]="`$key`";
						$values[]="'".mysql_escape_string2($value)."'";
					}
					$keys[]="`gpid`";
					$values[]="$GPID";
					$sql="INSERT IGNORE INTO webfilters_sqitems (".@implode(",", $keys).") VALUES (".@implode(",", $values).")";
					$q->QUERY_SQL($sql);
					if(!$q->ok){echo $q->mysql_error."\n$sql\n";return;}
					
				}
				
				if(count($GROUP_DYN)>0){
					$keys=array();$values=array();
					while (list ($key, $value) = each ($GROUP_DYN)){
						$keys[]="`$key`";
						$values[]="'".mysql_escape_string2($value)."'";
					}
					
					$keys[]="`gpid`";
					$values[]="$GPID";
					$sql="INSERT IGNORE INTO webfilter_aclsdynamic (".@implode(",", $keys).") VALUES (".@implode(",", $values).")";
					$q->QUERY_SQL($sql);
					if(!$q->ok){echo $q->mysql_error."\n$sql\n";return;}					
				}
			}
			
			
			while (list ($gpid, $value) = each ($GROUPSACLS)){
				echo "Linking ACL $ACLID with group $gpid\n";
				$md5=md5($ACLID.$gpid);
				$sql="INSERT IGNORE INTO webfilters_sqacllinks (zmd5,aclid,gpid) VALUES('$md5','$ACLID','$gpid')";
				$q->QUERY_SQL($sql);
			}
		
	}
	
	
	
}
function test_sarg(){
	$sock=new sockets();
	$EnableSargGenerator=$sock->GET_INFO("EnableSargGenerator");
	if(!is_numeric($EnableSargGenerator)){$EnableSargGenerator=0;}
	$unix=new unix();
	
	
	$SARGOK=false;
	$f=explode("\n",@file_get_contents("/etc/squid3/squid.conf"));
	while (list ($gpid, $line) = each ($f)){
		if(preg_match("#\/sarg\.log#", $line)){$SARGOK=true;break;}
		
	}
	
	$php=$unix->LOCATE_PHP5_BIN();
	
	if(!$SARGOK){
		if($EnableSargGenerator==0){return;}
		shell_exec("$php ".__FILE__." --build --force >/dev/null 2>&1 &");
		return;
	}else{
		if($EnableSargGenerator==1){return;}
		shell_exec("$php ".__FILE__." --build --force >/dev/null 2>&1 &");
		return;		
	}
}

?>