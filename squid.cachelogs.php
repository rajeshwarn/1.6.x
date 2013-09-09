<?php
	if(isset($_GET["verbose"])){$GLOBALS["VERBOSE"]=true;ini_set('display_errors', 1);ini_set('error_reporting', E_ALL);ini_set('error_prepend_string',null);ini_set('error_append_string',null);}
	include_once('ressources/class.templates.inc');
	include_once('ressources/class.ldap.inc');
	include_once('ressources/class.users.menus.inc');
	include_once('ressources/class.squid.inc');
	
	
$usersmenus=new usersMenus();
if(!$usersmenus->AsSquidAdministrator){
	$tpl=new templates();
	$alert=$tpl->_ENGINE_parse_body('{ERROR_NO_PRIVS}');
	echo "alert('$alert');";
	die();	
}


if(isset($_GET["cachelogs-events-list"])){events_search();exit;}

page();

function page(){
	$page=CurrentPageName();
	$tpl=new templates();
	$events=$tpl->_ENGINE_parse_body("{events}");
	$zdate=$tpl->_ENGINE_parse_body("{zDate}");

	$title=$tpl->_ENGINE_parse_body("{today}: {proxy_service_events} ".date("H")."h");
	
	$t=time();
	$html="
	<div style='margin:-10px;margin-left:-15px'>
	<table class='flexRT$t' style='display: none' id='flexRT$t' style='width:100%'></table>
	</div>
	
<script>
function flexRTStart$t(){
$('#flexRT$t').flexigrid({
	url: '$page?cachelogs-events-list=yes',
	dataType: 'json',
	colModel : [
		{display: '$zdate', name : 'zDate', width :120, sortable : true, align: 'left'},
		{display: '$events', name : 'events', width : 778, sortable : false, align: 'left'},
		],
	
	searchitems : [
		{display: '$events', name : 'events'}
		],
	sortname: 'zDate',
	sortorder: 'desc',
	usepager: true,
	title: '$title',
	useRp: true,
	rp: 50,
	showTableToggleBtn: false,
	width: 942,
	height: 420,
	singleSelect: true,
	rpOptions: [10, 20, 30, 50,100,200]
	
	});  
	$('table-1-selected').remove();
	$('flex1').remove();	 
}

function SelectGrid2(com, grid) {
	var items = $('.trSelected',grid);
	var id=items[0].id;
	id = id.substring(id.lastIndexOf('row')+3);
	if (com == 'Select') {
			LoadAjax('table-1-selected','$page?familysite-show='+id);
		}
	}
	 
setTimeout('flexRTStart$t()',800);		 

</script>
	
	
	";
	
	echo $html;
	
}

function events_search(){
$page=CurrentPageName();
$tpl=new templates();
$sock=new sockets();


	
	if(isset($_POST["sortname"])){if($_POST["sortname"]<>null){$ORDER="ORDER BY {$_POST["sortname"]} {$_POST["sortorder"]}";}}	
	if(isset($_POST['page'])) {$page = $_POST['page'];}	
	if(isset($_POST['rp'])) {$rp = $_POST['rp'];}

	if($_POST["query"]<>null){
		$search=base64_encode($_POST["query"]);
		$datas=unserialize(base64_decode($sock->getFrameWork("squid.php?cachelogs=$search&rp={$_POST["rp"]}")));
		$total=count($datas);
		
	}else{
		$datas=unserialize(base64_decode($sock->getFrameWork("squid.php?cachelogs=&rp={$_POST["rp"]}")));
		$total=count($datas);
	}
	
		
	$pageStart = ($page-1)*$rp;
	
	if(isset($_POST["sortname"])){if($_POST["sortname"]<>null){
		if($_POST["sortname"]=="zDate"){
			if($_POST["sortorder"]=="asc"){
				krsort($datas);
			}
		}
	$ORDER="ORDER BY {$_POST["sortname"]} {$_POST["sortorder"]}";}}	
	
	$data = array();
	$data['page'] = $page;
	$data['total'] = $total;
	$data['rows'] = array();
	
	while (list ($key, $line) = each ($datas) ){
		
		$date="&nbsp;";
		if($classtr=="oddRow"){$classtr=null;}else{$classtr="oddRow";}
		if(preg_match("#^([0-9\.\/\s+:]+)\s+#",$line,$re)){
			$date=$re[1];
			$line=str_replace($date,"",$line);
		
			$date=str_replace($current,"{today}",$date);
		}
		if(preg_match("#FATAL#i", $line)){$line="<span style='color:#680000'>$line</line>";}
		if(preg_match("#abnormally#i", $line)){$line="<span style='color:#680000'>$line</line>";}
		if(preg_match("#Reconfiguring#i", $line)){$line="<span style='color:#003D0D;font-weight:bold'>$line</line>";}
		if(preg_match("#Accepting HTTP#i", $line)){$line="<span style='color:#003D0D;font-weight:bold'>$line</line>";}
		if(preg_match("#Ready to serve requests#i", $line)){$line="<span style='color:#003D0D;font-weight:bold'>$line</line>";}
		
		$data['rows'][] = array(
			'id' => md5($line),
			'cell' => array($date, $line)
		);
	}
	echo json_encode($data);	
}

function events_search_old(){
	
$page=CurrentPageName();
$tpl=new templates();
$sock=new sockets();

$search=base64_encode($_GET["search"]);
$datas=unserialize(base64_decode($sock->getFrameWork("squid.php?cachelogs=$search")));
krsort($datas);	
$html="<center>
<table cellspacing='0' cellpadding='0' border='0' class='tableView' style='width:99%'>
<thead class='thead'>
	<tr>
		
		<th width=99% colspan=2>&nbsp;</th>
	</tr>
</thead>
<tbody class='tbody'>";
$current=date("Y/m/d");
	while (list ($key, $line) = each ($datas) ){
		$date="&nbsp;";
		if($classtr=="oddRow"){$classtr=null;}else{$classtr="oddRow";}
		if(preg_match("#^([0-9\.\/\s+:]+)\s+#",$line,$re)){
			$date=$re[1];
			$line=str_replace($date,"",$line);
		
			$date=str_replace($current,"{today}",$date);
		}
		if(preg_match("#FATAL#i", $line)){$line="<span style='color:#680000'>$line</line>";}
		if(preg_match("#abnormally#i", $line)){$line="<span style='color:#680000'>$line</line>";}
		if(preg_match("#Reconfiguring#i", $line)){$line="<span style='color:#003D0D;font-weight:bold'>$line</line>";}
		if(preg_match("#Accepting HTTP#i", $line)){$line="<span style='color:#003D0D;font-weight:bold'>$line</line>";}
		if(preg_match("#Ready to serve requests#i", $line)){$line="<span style='color:#003D0D;font-weight:bold'>$line</line>";}
		
			$html=$html."
		<tr class=$classtr>
			<td width=1% style='font-size:13px' nowrap>$date</td>
			<td width=99% style='font-size:13px'>$line</td>
		</tr>
		";
	
	
	}
	
	$html=$html."</table>
	</center>";
	echo $tpl->_ENGINE_parse_body($html);
	
}
