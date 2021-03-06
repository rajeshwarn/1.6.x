<?php


function Samba_must_be_enabled($enabled=0){
	
	if(is_file("/etc/artica-postfix/SQUID_APPLIANCE")){
		if(is_file("/etc/artica-postfix/settings/Daemons/EnableKerbAuth")){
			return @intval(@file_get_contents("/etc/artica-postfix/settings/Daemons/EnableKerbAuth"));
		}
		
	}
	
	return $enabled;
}




//========================================================================================================================================================
function smbd(){
	$master_pid=null;
	$pid_path=null;
	$smbd_bin=$GLOBALS["CLASS_UNIX"]->find_program("smbd");
	if($smbd_bin==null){
		if(is_file("/home/artica/samba.tar.gz.old")){
			$tar=$GLOBALS["CLASS_UNIX"]->find_program("tar");
			shell_exec2("$tar xf /home/artica/samba.tar.gz.old -C /");
				

			$smbd_bin=$GLOBALS["CLASS_UNIX"]->find_program("smbd");
			if($smbd_bin==null){
				shell_exec2("{$GLOBALS["nohup"]} {$GLOBALS["NICE"]} {$GLOBALS["PHP5"]} /usr/share/artica-postfix/compile-samba.php >/dev/null 2>&1 &");
			}
		}
		return;
	}

	if(!$GLOBALS["CLASS_USERS"]->SAMBA_INSTALLED){
		if($GLOBALS["VERBOSE"]){echo __FUNCTION__." not installed\n";}
		return null;
	}
	$enabled=$GLOBALS["CLASS_SOCKETS"]->GET_INFO("SambaEnabled");
	$AsCategoriesAppliance=intval(@file_get_contents("/etc/artica-postfix/settings/Daemons/AsCategoriesAppliance"));
	if($enabled==null){$enabled=1;}
	if($AsCategoriesAppliance==1){$enabled=0;}
	$enabled=Samba_must_be_enabled($enabled);

	if($enabled==1){
		$pid_path=$GLOBALS["CLASS_UNIX"]->LOCATE_SMBD_PID();
		if($pid_path==null){$pid_path="/var/run/samba/smbd.pid";}
		$master_pid=trim(@file_get_contents($pid_path));
		if(!$GLOBALS["CLASS_UNIX"]->process_exists($master_pid)){
			if($GLOBALS["VERBOSE"]){echo "pid path \"$pid_path\" no pid found\n";}
			$master_pid=$GLOBALS["CLASS_UNIX"]->PIDOF($smbd_bin);
			if($GLOBALS["VERBOSE"]){echo "pid:$master_pid after pidof\n";}
			if(is_file($pid_path)){
				if($GLOBALS["VERBOSE"]){echo "write $master_pid in \"$pid_path\"\n";}
				if($master_pid>1){@file_put_contents($pid_path,$master_pid);}
			}
		}
	}

	$l[]="[SAMBA_SMBD]";
	$l[]="service_name=APP_SAMBA_SMBD";
	$l[]="master_version=".samba_version();
	$l[]="service_cmd=/etc/init.d/samba";
	$l[]="service_disabled=$enabled";
	$l[]="pid_path=$pid_path";
	$l[]="remove_cmd=--samba-remove";
	$l[]="family=storage";
	if(!$GLOBALS["CLASS_UNIX"]->process_exists($master_pid)){
		if($enabled==1){
			shell_exec2("{$GLOBALS["NICE"]} /etc/init.d/samba start");
		}
		$l[]="running=0\ninstalled=1";
		$l[]="";
		return implode("\n",$l);
	}else{
		if($enabled==0){
			shell_exec2("{$GLOBALS["NICE"]} /etc/init.d/samba stop");
			
		}
		
	}
	$l[]="running=1";
	$l[]=GetMemoriesOf($master_pid);
	$l[]="";

	if(is_dir("/var/run/samba/winbindd_privileged")){
		$chmod=$GLOBALS["CLASS_UNIX"]->find_program("chmod");
		chmod("/var/run/samba/winbindd_privileged",0750);
		shell_exec2("$chmod 0750 /var/run/samba/winbindd_privileged >/dev/null 2>&1");
	}

	return implode("\n",$l);
}
//========================================================================================================================================================
function nmbd(){
	$master_pid=null;
	if(!$GLOBALS["CLASS_USERS"]->SAMBA_INSTALLED){
		if($GLOBALS["VERBOSE"]){echo __FUNCTION__." not installed\n";}
		return null;
	}
	$enabled=$GLOBALS["CLASS_SOCKETS"]->GET_INFO("SambaEnabled");
	if($enabled==null){$enabled=1;}
	$enabled=Samba_must_be_enabled($enabled);
	$pid_path=$GLOBALS["CLASS_UNIX"]->LOCATE_NMBD_PID();
	$nmbd_bin=$GLOBALS["CLASS_UNIX"]->find_program("nmbd");
	if(is_file($pid_path)){
		if($GLOBALS["VERBOSE"]){"NMBD: PID: $pid_path\n";}
		$master_pid=trim(@file_get_contents($pid_path));
	}

	if(!$GLOBALS["CLASS_UNIX"]->process_exists($master_pid)){
		if($GLOBALS["VERBOSE"]){echo "NMBD: pid path \"$pid_path\" no pid found\n";}
		$master_pid=$GLOBALS["CLASS_UNIX"]->PIDOF($nmbd_bin);
		if($GLOBALS["VERBOSE"]){echo "NMBD: pid:$master_pid after pidof `$nmbd_bin`\n";}
		if(is_file($pid_path)){
			if($GLOBALS["VERBOSE"]){echo "NMBD: write $master_pid in \"$pid_path\"\n";}
			if($master_pid>1){@file_put_contents($pid_path,$master_pid);}
		}
	}

	$l[]="[SAMBA_NMBD]";
	$l[]="service_name=APP_SAMBA_NMBD";
	$l[]="master_version=".samba_version();
	$l[]="service_cmd=/etc/init.d/samba";
	$l[]="service_disabled=$enabled";
	$l[]="pid_path=$pid_path";
	$l[]="remove_cmd=--samba-remove";
	$l[]="family=storage";
	if(!$GLOBALS["CLASS_UNIX"]->process_exists($master_pid)){
		if($enabled==1){shell_exec2("{$GLOBALS["NICE"]} /etc/init.d/samba start >/dev/null 2>&1 &");}

		$l[]="running=0\ninstalled=1";
		$l[]="";
		return implode("\n",$l);
	}else{
		if($enabled==0){
			shell_exec2("{$GLOBALS["NICE"]} /etc/init.d/samba stop");
			
		}
		
	}
	$l[]="running=1";
	$l[]=GetMemoriesOf($master_pid);
	$l[]="";
	return implode("\n",$l);return;
}


function rpcbind(){
	
	$master_pid=null;
	$tokill[]="/usr/sbin/acpid";
	$tokill[]="/usr/sbin/atd";
	$tokill[]="/usr/sbin/rpc.idmapd";
	$tokill[]="/sbin/rpc.statd";
	$tokill[]="/usr/bin/dbus-daemon";
	if($GLOBALS["CLASS_SOCKETS"]->EnableIntelCeleron==1){$tokill[]="/usr/sbin/irqbalance";}
	
	while (list ($pid, $process) = each ($tokill) ){
		if(!is_file($process)){continue;}
		$master_pid=$GLOBALS["CLASS_UNIX"]->PIDOF($process);
		if($GLOBALS["CLASS_UNIX"]->process_exists($master_pid)){$GLOBALS["CLASS_UNIX"]->KILL_PROCESS($master_pid,9);}
	}

	
	
	if(!is_file("/sbin/rpcbind")){return;}
	$enabled=intval($GLOBALS["CLASS_SOCKETS"]->GET_INFO("EnableRPCBind"));
	$unix=new unix();
	$master_pid=$GLOBALS["CLASS_UNIX"]->PIDOF("/sbin/rpcbind");
	if($enabled==0){
		if($unix->process_exists($master_pid)){
			$unix->KILL_PROCESS($master_pid,9);
		}
	}
	
	
}


function winbindd(){
	$master_pid=null;
	
	if(!$GLOBALS["CLASS_USERS"]->WINBINDD_INSTALLED){
		if($GLOBALS["VERBOSE"]){echo __FUNCTION__." not installed\n";}
		return null;
	}

	if(is_file("/etc/artica-postfix/WORDPRESS_APPLIANCE")){
		$master_pid=winbind_pid();
		if(!$GLOBALS["CLASS_UNIX"]->process_exists($master_pid)){
			shell_exec2("/etc/init.d/winbind stop");
		}
	}

	if(!$GLOBALS["CLASS_USERS"]->SAMBA_INSTALLED){
		if($GLOBALS["VERBOSE"]){echo __FUNCTION__." not installed\n";}
		$master_pid=winbind_pid();
		if(!$GLOBALS["CLASS_UNIX"]->process_exists($master_pid)){
			shell_exec2("/etc/init.d/winbind stop");
		}
		return null;
	}





	$enabled=$GLOBALS["CLASS_SOCKETS"]->GET_INFO("SambaEnabled");
	$DisableWinbindd=$GLOBALS["CLASS_SOCKETS"]->GET_INFO("DisableWinbindd");
	$DisableSambaFileSharing=$GLOBALS["CLASS_SOCKETS"]->GET_INFO("DisableSambaFileSharing");
	$EnableSambaActiveDirectory=$GLOBALS["CLASS_SOCKETS"]->GET_INFO("EnableSambaActiveDirectory");
	$AsCategoriesAppliance=intval(@file_get_contents("/etc/artica-postfix/settings/Daemons/AsCategoriesAppliance"));

	$squid=$GLOBALS["CLASS_UNIX"]->LOCATE_SQUID_BIN();
	if(is_file($squid)){
		$EnableKerbAuth=$GLOBALS["CLASS_SOCKETS"]->GET_INFO("EnableKerbAuth");
		
		if(!is_numeric($EnableKerbAuth)){$EnableKerbAuth=0;}
		if($EnableKerbAuth==1){$DisableWinbindd=0;}
	}



	if(!is_numeric($EnableSambaActiveDirectory)){$EnableSambaActiveDirectory=0;}
	if(!is_numeric($DisableWinbindd)){$DisableWinbindd=0;}
	if($EnableSambaActiveDirectory==1){$DisableWinbindd=0;}
	if($DisableSambaFileSharing==1){if($EnableSambaActiveDirectory==0){$enabled=0;}}

	if($DisableWinbindd==1){$enabled=0;}
	if(!is_numeric($enabled)){$enabled=1;}
	$enabled=Samba_must_be_enabled($enabled);


	if($AsCategoriesAppliance==1){
		$DisableWinbindd=1;
		$enabled=0;
	}


	if(is_file("/etc/artica-postfix/SQUID_APPLIANCE")){
		if($EnableKerbAuth==0){$enabled=0;}

	}

	if($GLOBALS["VERBOSE"]){
		echo "enabled.................................: $enabled\n";
		echo "DisableWinbindd.........................: $DisableWinbindd\n";
		echo "EnableSambaActiveDirectory..............: $EnableSambaActiveDirectory\n";

	}

	$l[]="[SAMBA_WINBIND]";
	$l[]="service_name=APP_SAMBA_WINBIND";
	$l[]="master_version=".samba_version();
	$l[]="service_cmd=samba";
	$l[]="service_disabled=$enabled";
	$l[]="remove_cmd=--samba-remove";
	$l[]="family=storage";

	squid_watchdog_events("[winbindd]: Enabled = $enabled");

	$master_pid=winbind_pid();
	if($enabled==0){
		if($GLOBALS["CLASS_UNIX"]->process_exists($master_pid)){
			squid_admin_mysql(1,"[NTLM]: Stopping winbindd daemon (not necessary to use it)");
			if(!$GLOBALS["DISABLE_WATCHDOG"]){
				shell_exec2("{$GLOBALS["NICE"]} {$GLOBALS["PHP5"]} /usr/share/artica-postfix/exec.winbindd.php --stop");
			}
		}
		$l[]="running=0\ninstalled=1";$l[]="";
		winbindd_logs(true);
		return implode("\n",$l);

	}


	$smbcontrol=$GLOBALS["CLASS_UNIX"]->find_program('smbcontrol');
	if(!$GLOBALS["DISABLE_WATCHDOG"]){
		shell_exec2("{$GLOBALS["NICE"]} {$GLOBALS["PHP5"]} /usr/share/artica-postfix/exec.samba.php --ping-ads &");
	}




	$pid_path=$GLOBALS["CLASS_UNIX"]->LOCATE_WINBINDD_PID();
	$master_pid=winbind_pid();

	if(!$GLOBALS["CLASS_UNIX"]->process_exists($master_pid)){
		$nmbd_bin=$GLOBALS["CLASS_UNIX"]->find_program("winbindd");
		if($GLOBALS["VERBOSE"]){echo "pid path \"$pid_path\" no pid found\n";}
		$master_pid=$GLOBALS["CLASS_UNIX"]->PIDOF($nmbd_bin);
		if($GLOBALS["VERBOSE"]){echo "pid:$master_pid after pidof\n";}
		if(is_file($pid_path)){
			if(!$GLOBALS["DISABLE_WATCHDOG"]){
				if($GLOBALS["VERBOSE"]){echo "write $master_pid in \"$pid_path\"\n";}
				if($master_pid>1){@file_put_contents($pid_path,$master_pid);}
			}
		}
	}

	if($GLOBALS["VERBOSE"]){
		echo "enabled.................................: $enabled\n";
		echo "DisableWinbindd.........................: $DisableWinbindd\n";
		echo "EnableSambaActiveDirectory..............: $EnableSambaActiveDirectory\n";

	}





	if(!$GLOBALS["CLASS_UNIX"]->process_exists($master_pid)){
		if($enabled==1){
			Winbindd_events("Winbindd service Failed -> start it",__FUNCTION__,__LINE__);
			if(!$GLOBALS["DISABLE_WATCHDOG"]){
				shell_exec2("{$GLOBALS["PHP5"]} /usr/share/artica-postfix/exec.winbindd.php --start");
			}
		}
		$l[]="running=0\ninstalled=1";$l[]="";
		return implode("\n",$l);
			
	}
	if(!$GLOBALS["DISABLE_WATCHDOG"]){
		if($enabled==1){if(strlen($smbcontrol)>3){winbindd_ping();}}
	}

	$l[]="running=1";
	$l[]=GetMemoriesOf($master_pid);
	$l[]="";
	winbindd_logs();
	shell_exec2("{$GLOBALS["nohup"]} {$GLOBALS["NICE"]} {$GLOBALS["PHP5"]} ".dirname( __FILE__)."/exec.adusers.php --computers schedule-id=0 >/dev/null 2>&1 &");

	return implode("\n",$l);return;
}
//========================================================================================================================================================
