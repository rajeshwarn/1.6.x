<?php
$GLOBALS["BYPASS"]=true;
$GLOBALS["REBUILD"]=false;
$GLOBALS["OLD"]=false;
$GLOBALS["FORCE"]=false;
if(preg_match("#schedule-id=([0-9]+)#",implode(" ",$argv),$re)){$GLOBALS["SCHEDULE_ID"]=$re[1];}
if(is_array($argv)){
	if(preg_match("#--verbose#",implode(" ",$argv))){$GLOBALS["VERBOSE"]=true;$GLOBALS["DEBUG_MEM"]=true;}
	if(preg_match("#--old#",implode(" ",$argv))){$GLOBALS["OLD"]=true;}
	if(preg_match("#--force#",implode(" ",$argv))){$GLOBALS["FORCE"]=true;}
	if(preg_match("#--rebuild#",implode(" ",$argv))){$GLOBALS["REBUILD"]=true;}
}
if($GLOBALS["VERBOSE"]){ini_set('display_errors', 1);	ini_set('html_errors',0);ini_set('display_errors', 1);ini_set('error_reporting', E_ALL);}

if($GLOBALS["VERBOSE"]){"echo Loading...\n";}
include_once(dirname(__FILE__).'/ressources/class.templates.inc');
include_once(dirname(__FILE__).'/ressources/class.ccurl.inc');
include_once(dirname(__FILE__).'/ressources/class.ini.inc');
include_once(dirname(__FILE__).'/ressources/class.mysql.inc');
include_once(dirname(__FILE__).'/framework/class.unix.inc');
include_once(dirname(__FILE__).'/ressources/class.squid.inc');
include_once(dirname(__FILE__).'/ressources/class.os.system.inc');
include_once(dirname(__FILE__)."/framework/frame.class.inc");
include_once(dirname(__FILE__).'/ressources/whois/whois.main.php');
include_once(dirname(__FILE__).'/ressources/class.squid.youtube.inc');

$sock=new sockets();
$EnableRemoteSyslogStatsAppliance=$sock->GET_INFO("EnableRemoteSyslogStatsAppliance");
if(!is_numeric($EnableRemoteSyslogStatsAppliance)){$EnableRemoteSyslogStatsAppliance=0;}
if($EnableRemoteSyslogStatsAppliance==1){die();}

if($argv[1]=="--reset"){members_uid_reset();die();}

members_uid();

function members_uid_reset(){
	$q=new mysql_squid_builder();
	$q->QUERY_SQL("TRUNCATE TABLE members_uid");
	$q->QUERY_SQL("UPDATE tables_day SET members_uid=0");
	members_uid();
}

function members_uid(){
	$GLOBALS["Q"]=new mysql_squid_builder();
	if($GLOBALS["VERBOSE"]){"echo Loading...\n";}
	$unix=new unix();
	if($GLOBALS["VERBOSE"]){"echo Loading done...\n";}
	$pidfile="/etc/artica-postfix/pids/".basename(__FILE__).".".__FUNCTION__.".pid";
	$timefile="/etc/artica-postfix/pids/".basename(__FILE__).".".__FUNCTION__.".time";
	$oldpid=@file_get_contents($pidfile);
	if(!$GLOBALS["FORCE"]){
		if($oldpid<100){$oldpid=null;}
		$unix=new unix();
		if($unix->process_exists($oldpid,basename(__FILE__))){
			if($GLOBALS["VERBOSE"]){echo "Already executed pid $oldpid\n";}
			return;
		}
		
		$timeexec=$unix->file_time_min($timefile);
		if($timeexec<540){return;}
		$mypid=getmypid();
		@file_put_contents($pidfile,$mypid);	
	}
	
	
	if(isset($GLOBALS["members_uid_executed"])){return;}
	$GLOBALS["members_uid_executed"]=true;
	$q=new mysql_squid_builder();
	$sql="SELECT tablename,zDate FROM `tables_day` WHERE members_uid=0 AND zDate<DATE_SUB(NOW(),INTERVAL 1 DAY)";
	$results=$q->QUERY_SQL($sql);
	if(!$q->ok){if($GLOBALS["VERBOSE"]){echo "############# ERROR #########\n$q->mysql_error\Line:".__LINE__."\n#############\n";}}

	if(mysql_num_rows($results)>0){
		while($ligne=@mysql_fetch_array($results,MYSQL_ASSOC)){
			$date=$ligne["zDate"];
			$time=strtotime($date." 00:00:00");
				
			$tablename=$ligne["tablename"];
			if($q->TABLE_EXISTS($tablename)){
				if(members_uid_from_dansguardian_events($tablename,$time)){
					$q->QUERY_SQL("UPDATE tables_day SET members_uid=1 WHERE tablename='$tablename'");
					continue;
				}
			}
				
			$hourtable=date("Ymd",$time)."_hour";
			if($q->TABLE_EXISTS($hourtable)){
				if(members_uid_from_hourtable($hourtable,$time)){
					$q->QUERY_SQL("UPDATE tables_day SET members_uid=1 WHERE tablename='$tablename'");
					continue;
				}

			}
				
			if($GLOBALS["VERBOSE"]){echo "############# ERROR #########\nNO TABLE FOR $date\n#############\n";}
				
		}
	}
	
	$php5=$unix->LOCATE_PHP5_BIN();
	$nohup=$unix->find_program("nohup");
	shell_exec("$nohup $php5 /usr/share/artica-postfix/exec.squid.websites_uid.php >/dev/null 2>&1 &");
	if($GLOBALS["VERBOSE"]){echo "--> members_mac();\n";}
	shell_exec("$nohup $php5 /usr/share/artica-postfix/exec.squid.members_mac.php  --schedule-id={$GLOBALS["SCHEDULE_ID"]}>/dev/null 2>&1 &");
	shell_exec("$nohup $php5 /usr/share/artica-postfix/exec.squid.members_macip.php --schedule-id={$GLOBALS["SCHEDULE_ID"]}>/dev/null 2>&1 &");
	shell_exec("$nohup $php5 /usr/share/artica-postfix/exec.squid.blocked_uid.php --schedule-id={$GLOBALS["SCHEDULE_ID"]} >/dev/null 2>&1 &");
	shell_exec("$nohup $php5 /usr/share/artica-postfix/exec.squid.youtube_uid.php --schedule-id={$GLOBALS["SCHEDULE_ID"]} >/dev/null 2>&1 &");
}
function members_uid_from_hourtable($tablename,$time){
	$zdate=date("Y-m-d",$time);
	$q=new mysql_squid_builder();
	$sql="SELECT uid, SUM(size) as size,SUM(hits) as hits FROM $tablename GROUP BY uid
	HAVING LENGTH(uid)>0";
	$results=$q->QUERY_SQL($sql);
	if(!$q->ok){if($GLOBALS["VERBOSE"]){echo "############# ERROR #########\n$q->mysql_error\Line:".__LINE__."\n#############\n";}return false;}

	$prefix="INSERT IGNORE INTO `members_uid` (zmd5,zDate,uid,size,hits) VALUES ";

	if(mysql_num_rows($results)==0){return true;}
	while($ligne=@mysql_fetch_array($results,MYSQL_ASSOC)){
		$uid=$ligne["uid"];
		$size=$ligne["size"];
		$hits=$ligne["hits"];
		$md5=md5("$uid$zdate");
		$f[]="('$md5','$zdate','$uid','$size','$hits')";

		if(count($f)>500){
			$q->QUERY_SQL($prefix.@implode(",", $f));
			if($GLOBALS["VERBOSE"]){echo "From: $tablename ".count($f)." items\n";}
			if(!$q->ok){if($GLOBALS["VERBOSE"]){echo "############# ERROR #########\n$q->mysql_error\Line:".__LINE__."\n#############\n";}}
			$f=array();
			if(!$q->ok){return false;}
		}

	}

	if(count($f)>0){
		$q->QUERY_SQL($prefix.@implode(",", $f));
		if($GLOBALS["VERBOSE"]){echo "From: $tablename ".count($f)." items\n";}
		$f=array();
		if(!$q->ok){if($GLOBALS["VERBOSE"]){echo "############# ERROR #########\n$q->mysql_error\Line:".__LINE__."\n#############\n";}}
		if(!$q->ok){return false;}
	}

	return true;

}
function members_uid_from_dansguardian_events($tablename,$time){
	$zdate=date("Y-m-d",$time);
	$q=new mysql_squid_builder();
	$sql="SELECT uid, SUM(QuerySize) as size,SUM(hits) as hits FROM $tablename GROUP BY uid
	HAVING LENGTH(uid)>0";
	$results=$q->QUERY_SQL($sql);
	if(!$q->ok){if($GLOBALS["VERBOSE"]){echo "############# ERROR #########\n$q->mysql_error\Line:".__LINE__."\n#############\n";}return false;}
	$prefix="INSERT IGNORE INTO `members_uid` (zmd5,zDate,uid,size,hits) VALUES ";

	if(mysql_num_rows($results)==0){return true;}
	while($ligne=@mysql_fetch_array($results,MYSQL_ASSOC)){
		$uid=$ligne["uid"];
		$size=$ligne["size"];
		$hits=$ligne["hits"];
		$md5=md5("$uid$zdate");
		$f[]="('$md5','$zdate','$uid','$size','$hits')";

		if(count($f)>500){
			$q->QUERY_SQL($prefix.@implode(",", $f));
			if($GLOBALS["VERBOSE"]){echo "From: $tablename ".count($f)." items\n";}
			$f=array();
			if(!$q->ok){if($GLOBALS["VERBOSE"]){echo "############# ERROR #########\n$q->mysql_error\Line:".__LINE__."\n#############\n";}return false;}
		}

	}

	if(count($f)>0){
		$q->QUERY_SQL($prefix.@implode(",", $f));
		if($GLOBALS["VERBOSE"]){echo "From: $tablename ".count($f)." items\n";}
		$f=array();
		if(!$q->ok){if($GLOBALS["VERBOSE"]){echo "############# ERROR #########\n$q->mysql_error\Line:".__LINE__."\n#############\n";}return false;}
	}

	return true;

}