<?php
	header("Pragma: no-cache");	
	header("Expires: 0");
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
	if(isset($_GET["verbose"])){$GLOBALS["VERBOSE"]=true;}
	header("Cache-Control: no-cache, must-revalidate");
	include_once('ressources/class.templates.inc');
	include_once('ressources/class.ldap.inc');
	include_once('ressources/class.users.menus.inc');
	
	include_once('ressources/class.ini.inc');
	include_once('ressources/class.ccurl.inc');
	include_once('ressources/class.system.network.inc');
	include_once('ressources/class.mysql.syslogs.inc');
	

	$user=new usersMenus();
	if($user->AsSquidAdministrator==false){
		$tpl=new templates();
		echo "alert('". $tpl->javascript_parse_text("{ERROR_NO_PRIVS}")."');";
		die();exit();
	}
	if(isset($_GET["popup"])){popup();exit;}
	if(isset($_GET["wizard1"])){wizard1();exit;}
	if(isset($_GET["wizard2"])){wizard2();exit;}
	if(isset($_GET["wizard3"])){wizard3();exit;}
	if(isset($_GET["wizard4"])){wizard4();exit;}
	if(isset($_GET["wizard5"])){wizard5();exit;}
	if(isset($_GET["wizard6"])){wizard6();exit;}
	if(isset($_GET["wizard7"])){wizard7();exit;}
	if(isset($_GET["wizard8"])){wizard8();exit;}
	if(isset($_GET["wizard9"])){wizard9();exit;}
	if(isset($_GET["wizard10"])){wizard10();exit;}
	if(isset($_GET["wizard11"])){wizard11();exit;}
	if(isset($_GET["wizard12"])){wizard12();exit;}
	
	if(isset($_POST["EnableRemoteStatisticsAppliance"])){Save();exit;}
	if(isset($_POST["SERVER"])){wizard_save();exit;}
	if(isset($_POST["SquidDBListenPort"])){wizard_save();exit;}
js();


function js(){
	$page=CurrentPageName();
	$tpl=new templates();
	header("content-type: application/x-javascript");
	$title=$tpl->_ENGINE_parse_body("{STATISTICS_APPLIANCE}");
	$html="YahooWin2(689,'$page?wizard1=yes','$title')";
	echo $html;
}

function wizard2(){
	$page=CurrentPageName();
	$tpl=new templates();
	$sock=new sockets();
	$WizardStatsAppliance=unserialize(base64_decode($sock->GET_INFO("WizardStatsAppliance")));
	$t=$_GET["t"];
	$tt=time();
	echo $tpl->_ENGINE_parse_body("
	<center style='font-size:18px'>{connecting_to}: {$WizardStatsAppliance["SERVER"]}:{$WizardStatsAppliance["PORT"]}</center>
	<div id='$tt'></div>
	<script>
		LoadAjax('$tt','$page?wizard3=yes&t=$t');
	</script>
	");
}

function wizard_restart(){
	$page=CurrentPageName();
	return "<center style='margin:20px'>".button("{back}", "Loadjs('$page');",22)."</center>";
}

function wizard3(){
	$page=CurrentPageName();
	$tpl=new templates();
	$sock=new sockets();
	$WizardStatsAppliance=unserialize(base64_decode($sock->GET_INFO("WizardStatsAppliance")));
	$t=$_GET["t"];	
	$tt=time()+rand(0,time());
	
	$proto="http";
	if($WizardStatsAppliance["SSL"]==1){$proto="https";}
	$uri="$proto://{$WizardStatsAppliance["SERVER"]}:{$WizardStatsAppliance["PORT"]}/nodes.listener.php?test-connection=yes";
	
	$curl=new ccurl($uri,true,null,true);
	$curl->NoHTTP_POST=true;
	$curl->noproxyload=true;
	
	if(!$curl->get()){
		$deb=debug_curl($curl->CURL_ALL_INFOS);
	 	echo FATAL_WARNING_SHOW_128($curl->error."<hr><strong>{$WizardStatsAppliance["SERVER"]}:{$WizardStatsAppliance["PORT"]} SSL:{$WizardStatsAppliance["SSL"]}</strong>$deb<hr>".wizard_restart());
	 	return;
	}
	
	if(strpos($curl->data, "CONNECTIONOK")==0){
		$deb=debug_curl($curl->CURL_ALL_INFOS);
		echo FATAL_WARNING_SHOW_128("<hr><strong>{$WizardStatsAppliance["SERVER"]}:{$WizardStatsAppliance["PORT"]} SSL:{$WizardStatsAppliance["SSL"]}</strong>{protocol_error}$deb".wizard_restart());
		return;
	}
	
	$tt=time();
	echo $tpl->_ENGINE_parse_body("
			<center style='font-size:18px'>{connected_to}: {$WizardStatsAppliance["SERVER"]}:{$WizardStatsAppliance["PORT"]}</center>
			<center style='font-size:18px'>{checking_compatibilities}</center>
			<div id='$tt'></div>
			<script>
			LoadAjax('$tt','$page?wizard4=yes&t=$t');
					</script>
		");	
	
}

function wizard4(){
	$page=CurrentPageName();
	$tpl=new templates();
	$sock=new sockets();
	$WizardStatsAppliance=unserialize(base64_decode($sock->GET_INFO("WizardStatsAppliance")));
	$t=$_GET["t"];
	$VERBOSED=null;
	$credentialsuri=null;
	$tt=time()+rand(0,time());
	if($GLOBALS["VERBOSE"]){$VERBOSED="&verbose=yes";}
	$cnxlog="<strong>{$WizardStatsAppliance["SERVER"]}:{$WizardStatsAppliance["PORT"]} SSL:{$WizardStatsAppliance["SSL"]}</strong><hr>";
	
	$proto="http";
	if($WizardStatsAppliance["SSL"]==1){$proto="https";}
	
	$credentials["MANAGER"]=$WizardStatsAppliance["MANAGER"];
	$credentials["PASSWORD"]=$WizardStatsAppliance["MANAGER-PASSWORD"];
	$credentialsuri="&creds=".urlencode(base64_encode(serialize($credentials)));
	
	
	$uri="$proto://{$WizardStatsAppliance["SERVER"]}:{$WizardStatsAppliance["PORT"]}/nodes.listener.php?stats-appliance-compatibility=yes&AS_DISCONNECTED={$WizardStatsAppliance["AS_DISCONNECTED"]}$credentialsuri$VERBOSED";
	if($GLOBALS["VERBOSE"]){echo "<H1>$uri</H1>\n";}
	$curl=new ccurl($uri,true,null,true);
	$curl->NoHTTP_POST=true;
	$curl->x_www_form_urlencoded=true;
	if(!$curl->get()){
		$deb=debug_curl($curl->CURL_ALL_INFOS);
		echo FATAL_WARNING_SHOW_128($curl->error."<hr>$cnxlog$deb<hr>".wizard_restart());
		return;
	}
	
	if($GLOBALS["VERBOSE"]){echo "<hr>$curl->data</hr>\n";}
	
	if(!preg_match("#<RESULTS>(.+?)</RESULTS>#is", $curl->data,$re)){
		echo FATAL_WARNING_SHOW_128("<hr>$cnxlog{artica_protocol_error}$deb".wizard_restart());
		return;
	}

	$array=unserialize(base64_decode($re[1]));
	
	if(!is_array($array)){
		echo FATAL_WARNING_SHOW_128("<hr>$cnxlog{artica_protocol_error}$deb".wizard_restart());
		return;
	}

	if(count($array["DETAILS"])>0){
		$tR[]="<table style='width:100%'>";
		
		while (list ($num, $val) = each ($array["DETAILS"]) ){
			
			$tR[]="<tr><td class=legend style='font-size:14px'>$val</td></tr>";
		
		}
		$tR[]="</table>";
		$details=@implode("", $tR);
	}
	
	if(isset($array["APP_CREDS"])){
		if($array["APP_CREDS"]==false){
			echo FATAL_WARNING_SHOW_128("<hr>$cnxlog{error_wrong_credentials}$details".wizard_restart());
			return;
			
		}
		
		echo $tpl->_ENGINE_parse_body("
				<center style='font-size:18px'>{compatible}</center>
				<div id='$tt'></div>
				<script>
				LoadAjax('$tt','$page?wizard12=yes&t=$t');
				</script>
				");
		return;		
		
		
	}
	
	
	if($array["APP_SYSLOG_DB"]==false){
		echo FATAL_WARNING_SHOW_128("<hr>$cnxlog{error_syslogdb_not_installed}$details".wizard_restart());
		return;
	}
	
	
	
	if($array["APP_SQUID_DB"]==false){
		echo FATAL_WARNING_SHOW_128("{error_squiddb_not_installed}$details".wizard_restart());
		return;
	}
	
	echo $tpl->_ENGINE_parse_body("
			<center style='font-size:18px'>{compatible}</center>
			<center style='font-size:18px'>{retreive_informations}</center>
			<div id='$tt'></div>
			<script>
			LoadAjax('$tt','$page?wizard5=yes&t=$t');
			</script>
			");
		
	
}
function wizard5(){
	$page=CurrentPageName();
	$tpl=new templates();
	$sock=new sockets();
	$WizardStatsAppliance=unserialize(base64_decode($sock->GET_INFO("WizardStatsAppliance")));
	$t=$_GET["t"];
	$tt=time()+rand(0,time());

	$cnxlog="<strong>{$WizardStatsAppliance["SERVER"]}:{$WizardStatsAppliance["PORT"]} SSL:{$WizardStatsAppliance["SSL"]}</strong><hr>";

	$proto="http";
	if($WizardStatsAppliance["SSL"]==1){$proto="https";}
	$uri="$proto://{$WizardStatsAppliance["SERVER"]}:{$WizardStatsAppliance["PORT"]}/nodes.listener.php?stats-appliance-ports=yes";

	$curl=new ccurl($uri,true,null,true);
	$curl->NoHTTP_POST=true;
	if(!$curl->get()){
		$deb=debug_curl($curl->CURL_ALL_INFOS);
		echo FATAL_WARNING_SHOW_128($curl->error."<hr>$cnxlog$deb<hr>".wizard_restart());
		return;
	}

	if(!preg_match("#<RESULTS>(.+?)</RESULTS>#is", $curl->data,$re)){echo FATAL_WARNING_SHOW_128("<hr>$cnxlog{artica_protocol_error}$deb".wizard_restart());return;}
	$array=unserialize(base64_decode($re[1]));
	if(!is_array($array)){echo FATAL_WARNING_SHOW_128("<hr>$cnxlog{artica_protocol_error}$deb".wizard_restart());return;}

	
	$html="
	<div id='$tt'>
	<div style='width:98%' class=form>
	<div class=text-info style='font-size:16px'>{STATISTICS_APPLIANCEV2_EXPLAIN_2}</div>
	<table style='width:100%'>
	<tr>
		<td class=legend style='font-size:16px'>{APP_SYSLOG_DB} {listen_port}:</td>
		<td style='font-size:16px'><strong>{$array["SyslogListenPort"]}</td>		
	</tr>
	<tr>
		<td class=legend style='font-size:16px'>{APP_SQUID_DB} {listen_port}:</td>
		<td style='font-size:16px'><strong>{$array["SquidDBListenPort"]}</td>		
	</tr>
	</table><hr>
	<table style='width:100%'>
	<tr>
	<td width=50% align=left>".button("{back}", "Loadjs('$page');",22)."</td>
	<td width=50% align=right>".button("{next}","Wizard2$tt()",22)."</td>
	</tr>
	</table>
	</div>	
	</div>	
	<script>
	
		var xWizard2$tt=function (obj) {
			var results=obj.responseText;
			if(results.length>10){alert(results);}	
			LoadAjax('$tt','$page?wizard6=yes&t=$t');

		}	
	
	function Wizard2$tt(){
		var XHR = new XHRConnection();
		XHR.appendData('SquidDBListenPort','{$array["SquidDBListenPort"]}');
		XHR.appendData('SyslogListenPort','{$array["SyslogListenPort"]}');
		AnimateDiv('$tt');
		XHR.sendAndLoad('$page', 'POST',xWizard2$tt);	
	}
	</script>					
	";
	echo $tpl->_ENGINE_parse_body($html);
}

function wizard6(){
	$page=CurrentPageName();
	$tpl=new templates();
	$sock=new sockets();
	$WizardStatsAppliance=unserialize(base64_decode($sock->GET_INFO("WizardStatsAppliance")));
	$t=$_GET["t"];
	$tt=time()+rand(0,time());
	echo $tpl->_ENGINE_parse_body("
			<center style='font-size:18px'>{creating_privileges}...</center>
			<div id='$tt'></div>
			<script>
			LoadAjax('$tt','$page?wizard7=yes&t=$t');
			</script>
	");
}

function wizard7(){
	$page=CurrentPageName();
	$tpl=new templates();
	$sock=new sockets();
	$WizardStatsAppliance=unserialize(base64_decode($sock->GET_INFO("WizardStatsAppliance")));
	$t=$_GET["t"];
	$tt=time()+rand(0,time());

	$proto="http";
	if($WizardStatsAppliance["SSL"]==1){$proto="https";}
	$uri="$proto://{$WizardStatsAppliance["SERVER"]}:{$WizardStatsAppliance["PORT"]}/nodes.listener.php?stats-perform-connection=yes";
	$cnxlog="<strong>{$WizardStatsAppliance["SERVER"]}:{$WizardStatsAppliance["PORT"]} SSL:{$WizardStatsAppliance["SSL"]}</strong><hr>";
	$curl=new ccurl($uri,true,null,true);
	$curl->NoHTTP_POST=true;
	if(!$curl->get()){
		$deb=debug_curl($curl->CURL_ALL_INFOS);
		echo FATAL_WARNING_SHOW_128($curl->error."<hr><strong>{$WizardStatsAppliance["SERVER"]}:{$WizardStatsAppliance["PORT"]} SSL:{$WizardStatsAppliance["SSL"]}</strong>$deb<hr>".wizard_restart());
		return;
	}

	if(!preg_match("#<RESULTS>(.+?)</RESULTS>#is", $curl->data,$re)){echo FATAL_WARNING_SHOW_128("<hr>$cnxlog{artica_protocol_error}$deb".wizard_restart());return;}
	$array=unserialize(base64_decode($re[1]));
	if(!is_array($array)){echo FATAL_WARNING_SHOW_128("<hr>$cnxlog{artica_protocol_error}$deb".wizard_restart());return;}

	
	
	if(isset($array["ERROR"])){
		echo FATAL_WARNING_SHOW_128("<hr>$cnxlog{error}<hr>{$array["ERROR"]}<hr>$deb".wizard_restart());
		return;
		
	}
	
	if(!isset($array["mysql"]["username"])){
		echo FATAL_WARNING_SHOW_128("<hr>{error}<hr>username not retreived<hr>$deb".wizard_restart());return;
	
	}
	if(!isset($array["mysql"]["password"])){
		echo FATAL_WARNING_SHOW_128("<hr>{error}<hr>Password not retreived<hr>$deb".wizard_restart());return;
	
	}	
	
	$WizardStatsAppliance["username"]=$array["mysql"]["username"];
	$WizardStatsAppliance["password"]=$array["mysql"]["password"];
	$sock->SaveConfigFile(base64_encode(serialize($WizardStatsAppliance)), "WizardStatsAppliance");
	
	echo $tpl->_ENGINE_parse_body("
			<center style='font-size:18px'>{saving_parameters}</center>
			<div id='$tt'></div>
			<script>
			LoadAjax('$tt','$page?wizard8=yes&t=$t');
			</script>
			");

}

function wizard8(){
	
	$page=CurrentPageName();
	$tpl=new templates();
	$sock=new sockets();
	$WizardStatsAppliance=unserialize(base64_decode($sock->GET_INFO("WizardStatsAppliance")));
	$t=$_GET["t"];
	$tt=time()+rand(0,time());
	
	
	
	$sock->SET_INFO("DisableLocalStatisticsTasks",1);
	$sock->SET_INFO("EnableSquidRemoteMySQL",1);
	$sock->SET_INFO("EnableRemoteStatisticsAppliance",0);
	
	
	$sock->SET_INFO("squidRemostatisticsServer",$WizardStatsAppliance["SERVER"]);
	$sock->SET_INFO("squidRemostatisticsPort",$WizardStatsAppliance["SquidDBListenPort"]);
	$sock->SET_INFO("squidRemostatisticsUser",$WizardStatsAppliance["username"]);
	$sock->SET_INFO("squidRemostatisticsPassword",$WizardStatsAppliance["password"]);
	$sock->SET_INFO("UseRemoteUfdbguardService",1);
	
	$datas=unserialize(base64_decode($sock->GET_INFO("ufdbguardConfig")));
	$datas["UseRemoteUfdbguardService"]=1;
	$datas["remote_server"]=$WizardStatsAppliance["SERVER"];
	$datas["remote_port"]=3977;
	$sock->SaveConfigFile(base64_encode(serialize($datas)),"ufdbguardConfig");
	
	$sock->getFrameWork("cmd.php?reload-squidguard=yes");
	$sock->getFrameWork("cmd.php?restart-artica-status=yes");
	$sock->getFrameWork("squidstats.php?migrate-local=yes");
	
	
	echo $tpl->_ENGINE_parse_body("
			<center style='font-size:18px'>{APP_SQUID_DB}:{$WizardStatsAppliance["username"]}@{$WizardStatsAppliance["SERVER"]}:{$WizardStatsAppliance["SquidDBListenPort"]}</center>
			<center style='font-size:18px'>{saving_parameters}</center>
			<div id='$tt'></div>
			<script>
			LoadAjax('$tt','$page?wizard9=yes&t=$t');
			</script>
			");	
	
	
}

function wizard9(){
	$page=CurrentPageName();
	$tpl=new templates();
	$sock=new sockets();
	$WizardStatsAppliance=unserialize(base64_decode($sock->GET_INFO("WizardStatsAppliance")));
	$t=$_GET["t"];
	$tt=time()+rand(0,time());
	
	
	
	$sock->SET_INFO("MySQLSyslogType",2);
	$TuningParameters=unserialize(base64_decode($sock->GET_INFO("MySQLSyslogParams")));
	$TuningParameters["username"]=$WizardStatsAppliance["username"];
	$TuningParameters["password"]=$WizardStatsAppliance["password"];
	$TuningParameters["mysqlserver"]=$WizardStatsAppliance["SERVER"];
	$TuningParameters["RemotePort"]=$WizardStatsAppliance["SyslogListenPort"];
	$sock->SaveConfigFile(base64_encode(serialize($TuningParameters)), "MySQLSyslogParams");
	
	
	$sock->getFrameWork("cmd.php?restart-artica-status=yes");
	$sock->getFrameWork("cmd.php?squid-rebuild=yes");
	
	echo $tpl->_ENGINE_parse_body("
			<center style='font-size:18px'>{APP_SYSLOG_DB}:{$WizardStatsAppliance["username"]}@{$WizardStatsAppliance["SERVER"]}:{$WizardStatsAppliance["SyslogListenPort"]}</center>
			<center style='font-size:18px'>{testing_connections}</center>
			<div id='$tt'></div>
			<script>
			LoadAjax('$tt','$page?wizard10=yes&t=$t');
			</script>
			");	
	
}
function wizard12(){
	$page=CurrentPageName();
	$tpl=new templates();
	$sock=new sockets();
	$WizardStatsAppliance=unserialize(base64_decode($sock->GET_INFO("WizardStatsAppliance")));
	$t=$_GET["t"];
	$tt=time()+rand(0,time());

	$sock->SET_INFO("DisableLocalStatisticsTasks",0);
	$sock->SET_INFO("EnableSquidRemoteMySQL",0);
	$sock->SET_INFO("EnableRemoteStatisticsAppliance",0);
	$sock->SET_INFO("UseRemoteUfdbguardService",0);
	$sock->SET_INFO("MySQLSyslogType",1);
	$TuningParameters["username"]=null;
	$TuningParameters["password"]=null;
	$TuningParameters["mysqlserver"]=null;
	$TuningParameters["RemotePort"]=null;
	$sock->SaveConfigFile(base64_encode(serialize($TuningParameters)), "MySQLSyslogParams");


	$sock->getFrameWork("cmd.php?restart-artica-status=yes");
	$sock->getFrameWork("cmd.php?squid-rebuild=yes");

	echo $tpl->_ENGINE_parse_body("
			<center style='font-size:18px;margin:10px'>{APP_WIZARD_STATS_APPLIANCE_DISCONNECTED}</center>
			
			<center style='font-size:18px;margin:10px'>". button("{close}","YahooWin2Hide()",22)."</center>
			
			
			<div id='$tt'></div>
			<script>
			
			</script>
			");

}



function wizard10(){
	
	$q=new mysql_squid_builder();
	$page=CurrentPageName();
	$tpl=new templates();
	$sock=new sockets();
	$t=$_GET["t"];
	$tt=time()+rand(0,time());
	if(!$q->BD_CONNECT()){
		echo FATAL_WARNING_SHOW_128("<hr>{error}<hr>{statistics_database}<hr>$q->mysql_error".wizard_restart());return;
	}
	
	$q=new mysql_storelogs();
	if(!$q->BD_CONNECT()){
		echo FATAL_WARNING_SHOW_128("<hr>{error}<hr>{logs_database}<hr>$q->mysql_error".wizard_restart());return;
	}
	
	echo $tpl->_ENGINE_parse_body("<center style='font-size:18px'>{statistics_database}:OK</center>
			<center style='font-size:18px'>{logs_database}:OK</center>")."
			<div id='$tt'></div>
			<script>
			LoadAjax('$t','$page?wizard11=yes&t=$t');
			</script>
	";
	
}


function wizard11(){
	$sock=new sockets();
	$sock->SET_INFO("EnableMySQLSyslogWizard",1);
	$page=CurrentPageName();
	$tpl=new templates();
	$sock=new sockets();
	$WizardStatsAppliance=unserialize(base64_decode($sock->GET_INFO("WizardStatsAppliance")));
	$t=$_GET["t"];
	$tt=time()+rand(0,time());
	echo $tpl->_ENGINE_parse_body("<center style='font-size:18px' class=text-info>{STATISTICS_APPLIANCEV2_EXPLAIN_3}</center>")."

	<script>
		Loadjs('squid.compile.progress.php');
		RefreshTab('artica_squid_stats_tabs');
	</script>
	";

}

function wizard_save(){
	$sock=new sockets();
	$WizardStatsAppliance=unserialize(base64_decode($sock->GET_INFO("WizardStatsAppliance")));
	
	$_POST["MANAGER-PASSWORD"]=url_decode_special_tool($_POST["MANAGER-PASSWORD"]);
	
	while (list ($a, $b) = each ($_POST) ){
		$WizardStatsAppliance[$a]=$b;
	}
	
	$SERVER=$_POST["SERVER"];
	$ip=new networking();
	$ips=$ip->ALL_IPS_GET_ARRAY();
	if(isset($ips[$SERVER])){
		echo "$SERVER is only the slave and cannot be the statistics appliance itself.\nUse a remote Artica server\n";
		die();
	}
	$sock->SET_INFO("WizardStatsApplianceDisconnected", $_POST["AS_DISCONNECTED"]);
	$sock->SET_INFO("WgetBindIpAddress", $_POST["WgetBindIpAddress"]);
	$sock->SaveConfigFile(base64_encode(serialize($WizardStatsAppliance)), "WizardStatsAppliance");
}

function debug_curl($array){
	$t[]="<table style='width:100%'>";
	
	while (list ($num, $val) = each ($array) ){
		if(is_array($val)){
			while (list ($a, $b) = each ($val) ){$tt[]="<li>$a = $b</li>";}
			$val=null;
			$val=@implode("\n", $tt);
		}
		$t[]="<tr>
		<td class=legend style='font-size:14px'>$num:</td>
		<td style='font-size:14px'>$val</td>
		</tr>";	
		
	}
	$t[]="</table>";
	
	return @implode("\n", $t);
	
}

function wizard1(){
	$page=CurrentPageName();
	$tpl=new templates();
	$sock=new sockets();
	$t=time();
	$WizardStatsAppliance=unserialize(base64_decode($sock->GET_INFO("WizardStatsAppliance")));
	$sock->SET_INFO("WizardStatsApplianceSeen", 1);
	
	if(!is_numeric($WizardStatsAppliance["SSL"])){$WizardStatsAppliance["SSL"]=1;}
	if(!is_numeric($WizardStatsAppliance["PORT"])){$WizardStatsAppliance["PORT"]=9000;}
	$ip=new networking();
	
	while (list ($eth, $cip) = each ($ip->array_TCP) ){
		if($cip==null){continue;}
		$arrcp[$cip]=$cip;
	}
	

	
	$arrcp[null]="{default}";
	
	$WgetBindIpAddress=$sock->GET_INFO("WgetBindIpAddress");
	$WgetBindIpAddress=Field_array_Hash($arrcp,"WgetBindIpAddress",$WgetBindIpAddress,null,null,0,"font-size:20px;padding:3px;");
		
	$html="
	<div id='$t'>		
	<div class=text-info style='font-size:16px'>{STATISTICS_APPLIANCEV2_EXPLAIN_1}</div>
	<div class=form style='width:95%'>
	<table style='width:100%'>	
	<tr>
		<td class=legend style='font-size:20px'>{WgetBindIpAddress}:</td>
		<td style='font-size:14px'>$WgetBindIpAddress</td>
		<td>&nbsp;</td>
	</tr>		
	<tr>
		<td class=legend style='font-size:20px'>{hostname}/IP:</td>
		<td style='font-size:14px'>". Field_text("SERVER-$t",$WizardStatsAppliance["SERVER"],"font-size:20px;font-weight:bold;width:200px")."</td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td class=legend style='font-size:20px'>{listen_port}:</td>
		<td style='font-size:20px'>". Field_text("PORT-$t",$WizardStatsAppliance["PORT"],"font-size:20px;width:90px")."</td>
		<td>&nbsp;</td>
	</tr>		
	<tr>
		<td class=legend style='font-size:20px'>{use_ssl}:</td>
		<td style='font-size:14px'>". Field_checkbox_design("SSL-$t",1,$WizardStatsAppliance["SSL"])."</td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td colspan=3><p>&nbsp;</p>
	</tr>
	<tr>
		<td class=legend style='font-size:20px'>{disconnected_mode}:</td>
		<td style='font-size:14px'>". Field_checkbox_design("AS_DISCONNECTED-$t",1,$WizardStatsAppliance["AS_DISCONNECTED"],"DisconnectCheck$t()")."</td>
		<td>&nbsp;</td>
	</tr>	
	<tr>
		<td class=legend style='font-size:20px'>{manager}:</td>
		<td style='font-size:14px'>". Field_text("MANAGER-$t",$WizardStatsAppliance["MANAGER"],"font-size:20px;font-weight:bold;width:200px")."</td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td class=legend style='font-size:20px'>{password}:</td>
		<td style='font-size:14px'>". Field_password("MANAGER-PASSWORD-$t",$WizardStatsAppliance["MANAGER-PASSWORD"],"font-size:19px;font-weight:bold;width:200px")."</td>
		<td>&nbsp;</td>
	</tr>				
				
	<tr>
		<td colspan=3 align='right'><hr>". button("{next}","Wizard1$t()",22)."</td>
	</tr>
	</table>	
	<script>
	
var xWizard1$t=function (obj) {
	var results=obj.responseText;
	if(results.length>10){
		alert(results);
		return;
	}	
	LoadAjax('$t','$page?wizard2=yes&t=$t');
}	
	
function Wizard1$t(){
	var XHR = new XHRConnection();
	if(document.getElementById('SSL-$t').checked){XHR.appendData('SSL','1');}else{XHR.appendData('SSL','0');}
	if(document.getElementById('AS_DISCONNECTED-$t').checked){XHR.appendData('AS_DISCONNECTED','1');}else{XHR.appendData('AS_DISCONNECTED','0');}
	XHR.appendData('SERVER',document.getElementById('SERVER-$t').value);
	XHR.appendData('PORT',document.getElementById('PORT-$t').value);
	XHR.appendData('MANAGER',document.getElementById('MANAGER-$t').value);
	XHR.appendData('MANAGER-PASSWORD',encodeURIComponent(document.getElementById('MANAGER-PASSWORD-$t').value));
	XHR.appendData('WgetBindIpAddress',document.getElementById('WgetBindIpAddress').value);
	XHR.sendAndLoad('$page', 'POST',xWizard1$t);	
}

function DisconnectCheck$t(){
	document.getElementById('MANAGER-$t').disabled=true;
	document.getElementById('MANAGER-PASSWORD-$t').disabled=true;
	if(document.getElementById('AS_DISCONNECTED-$t').checked){
		document.getElementById('MANAGER-$t').disabled=false;
		document.getElementById('MANAGER-PASSWORD-$t').disabled=false;		
	}
}

DisconnectCheck$t();	
RefreshTab('squid_main_svc');
	</script>				
	";
	
	echo $tpl->_ENGINE_parse_body($html);
}


function popup(){
	$sock=new sockets();
	$tpl=new templates();
	$page=CurrentPageName();
	
	
	$EnableRemoteStatisticsAppliance=$sock->GET_INFO("EnableRemoteStatisticsAppliance");
	$EnableRemoteSyslogStatsAppliance=intval($sock->GET_INFO("EnableRemoteSyslogStatsAppliance"));
	if(!is_numeric($EnableRemoteStatisticsAppliance)){$EnableRemoteStatisticsAppliance=0;}
	if(!is_numeric($EnableRemoteSyslogStatsAppliance)){$EnableRemoteSyslogStatsAppliance=0;}
	$RemoteStatisticsApplianceSettings=unserialize(base64_decode($sock->GET_INFO("RemoteStatisticsApplianceSettings")));
	
	
	if(!is_numeric($RemoteStatisticsApplianceSettings["SSL"])){$RemoteStatisticsApplianceSettings["SSL"]=1;}
	if(!is_numeric($RemoteStatisticsApplianceSettings["PORT"])){$RemoteStatisticsApplianceSettings["PORT"]=9000;}
	$uuid=$sock->getFrameWork("services.php?GetMyHostId=yes");	
	
	

	
	//$RemoteStatisticsApplianceSettings["SERVER"]
	$html="


	<table style='width:99%' class=form>
	<tbody>
	<tr>
		<td class=legend style='font-size:14px'>{uuid}:</td>
		<td style='font-size:14px;font-weight:bold' colspan=2>$uuid</td>
	</tr>			
	<tr>
		<td class=legend style='font-size:14px'>{use_stats_appliance}:</td>
		<td style='font-size:14px'>". Field_checkbox("EnableRemoteStatisticsAppliance",1,$EnableRemoteStatisticsAppliance,"EnableRemoteStatisticsApplianceCheck()")."</td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td class=legend style='font-size:14px'>{hostname}:</td>
		<td style='font-size:14px'>". Field_text("StatsServervame",$RemoteStatisticsApplianceSettings["SERVER"],"font-size:19px;font-weight:bold;width:200px")."</td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td class=legend style='font-size:14px'>{listen_port}:</td>
		<td style='font-size:14px'>". Field_text("StatsServerPort",$RemoteStatisticsApplianceSettings["PORT"],"font-size:14px;width:60px")."</td>
		<td>&nbsp;</td>
	</tr>		
	<tr>
		<td class=legend style='font-size:14px'>{use_ssl}:</td>
		<td style='font-size:14px'>". Field_checkbox("StatsServerSSL",1,$RemoteStatisticsApplianceSettings["SSL"])."</td>
		<td>&nbsp;</td>
	</tr>						
	<tr>
		<td class=legend style='font-size:14px'>{send_syslogs_to_server}:</td>
		<td style='font-size:14px'>". Field_checkbox_design("EnableRemoteSyslogStatsAppliance",1,$EnableRemoteSyslogStatsAppliance)."</td>
		<td>". help_icon("{send_syslogs_to_server_client_explain}")."</td>
	</tr>	


	<tr>
	<td colspan=3 align='right'><hr>". button("{apply}","SaveStatsApp()",16)."</td>
	</tr>
	</tbody>
	</table>
		<div class=text-info style='font-size:13px' id='STATISTICS_APPLIANCE_EXPLAIN_DIV'>{STATISTICS_APPLIANCE_EXPLAIN}</div>
	<script>
		var x_SaveStatsApp=function (obj) {
			var results=obj.responseText;
			if(results.length>10){alert(results);}	
			if(document.getElementById('squid-status')){LoadAjax('squid-status','squid.main.quicklinks.php?status=yes');}			
			CacheOff();
			YahooWin2Hide();

		}
	
	
		function EnableRemoteStatisticsApplianceCheck(){
			document.getElementById('StatsServervame').disabled=true;
			document.getElementById('StatsServerPort').disabled=true;
			document.getElementById('StatsServerSSL').disabled=true;
			document.getElementById('EnableRemoteSyslogStatsAppliance').disabled=true;
			
			if(document.getElementById('EnableRemoteStatisticsAppliance').checked){
				document.getElementById('StatsServervame').disabled=false;
				document.getElementById('StatsServerPort').disabled=false;
				document.getElementById('StatsServerSSL').disabled=false;
				document.getElementById('EnableRemoteSyslogStatsAppliance').disabled=true;	
				document.getElementById('EnableRemoteSyslogStatsAppliance').checked=true;			
			
			}
		
		}
		
	function SaveStatsApp(){
		var XHR = new XHRConnection();
		if(document.getElementById('EnableRemoteStatisticsAppliance').checked){XHR.appendData('EnableRemoteStatisticsAppliance','1');}else{XHR.appendData('EnableRemoteStatisticsAppliance','0');}
		if(document.getElementById('EnableRemoteSyslogStatsAppliance').checked){XHR.appendData('EnableRemoteSyslogStatsAppliance','1');}else{XHR.appendData('EnableRemoteSyslogStatsAppliance','0');}
		if(document.getElementById('StatsServerSSL').checked){XHR.appendData('StatsServerSSL','1');}else{XHR.appendData('StatsServerSSL','0');}
		XHR.appendData('StatsServervame',document.getElementById('StatsServervame').value);
		XHR.appendData('StatsServerPort',document.getElementById('StatsServerPort').value);
		AnimateDiv('STATISTICS_APPLIANCE_EXPLAIN_DIV');
		XHR.sendAndLoad('$page', 'POST',x_SaveStatsApp);	
	}
	EnableRemoteStatisticsApplianceCheck();
	</script>
	";
	echo $tpl->_ENGINE_parse_body($html);
	
}

function Save(){
	$sock=new sockets();
	$ArticaHttpsPort=$sock->GET_INFO("ArticaHttpsPort");
	if(!is_numeric($ArticaHttpsPort)){$ArticaHttpsPort=9000;}	
	$sock->SET_INFO("EnableRemoteStatisticsAppliance",0);
	
	$RemoteStatisticsApplianceSettings["SSL"]=$_POST["StatsServerSSL"];
	$RemoteStatisticsApplianceSettings["PORT"]=$_POST["StatsServerPort"];
	$RemoteStatisticsApplianceSettings["SERVER"]=$_POST["StatsServervame"];
	$sock->SaveConfigFile(base64_encode(serialize($RemoteStatisticsApplianceSettings)),"RemoteStatisticsApplianceSettings");
	$sock->SET_INFO("EnableRemoteSyslogStatsAppliance",$_POST["EnableRemoteSyslogStatsAppliance"]);
	$sock->getFrameWork("cmd.php?syslog-client-mode=yes");		
	writelogs("EnableRemoteStatisticsAppliance -> {$_POST["EnableRemoteStatisticsAppliance"]}",__FUNCTION__,__FILE__,__LINE__);
	if($_POST["EnableRemoteStatisticsAppliance"]==1){
		$sock->getFrameWork("services.php?netagent=yes");
	}	
	
	
}
