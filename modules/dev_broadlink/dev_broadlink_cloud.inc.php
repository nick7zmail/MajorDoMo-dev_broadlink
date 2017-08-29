<?php
  if ($this->owner->name=='panel') {
   $out['CONTROLPANEL']=1;
  }
  $this->getConfig();
  if(isset($this->config['userid']) && isset($this->config['loginsession']) && $this->config['userid']!='' && $this->config['loginsession']!='') {
	$out['NEED_LOGIN']=false;
	$out['LOGIN']=$this->config['username'];
  } else {
	$out['NEED_LOGIN']=true;
  }
  if($this->mode=='login') {
	global $username;
	global $password;
	$cloud = Broadlink::Cloud();
	$response = $cloud->Auth($username, $password);
	if($response['error']==0) {
		$this->config['username']=$response['nickname'];
		$this->config['userid']=$response['userid'];
		$this->config['loginsession']=$response['loginsession'];
		$this->saveConfig();
		$this->redirect("?view_mode=cloud");
	} else {
		$out['WARN']='<#LANG_STRING_ERROR#>: '.$response['error'].': '.$response['msg'];
	}
  }
  if($this->mode=='unlogin') {
	$this->config['username']='';
	$this->config['userid']='';
	$this->config['loginsession']='';
	$this->saveConfig();
	$this->redirect("?view_mode=cloud");
  }
  if($this->mode=='get_last') {
	$cloud = Broadlink::Cloud($this->config['username'], $this->config['userid'], $this->config['loginsession']);
	$response = $cloud->GetLastBackup();
	if($response['error']==0) {
		$out['OK']='<#LANG_UNPACKED#> '.$response['msg'];
	} else {
		$out['WARN']='<#LANG_STRING_ERROR#>: '.$response['error'].': '.$response['msg'];
	}
  } 
  if($this->mode=='get_list') {
	$cloud = Broadlink::Cloud($this->config['username'], $this->config['userid'], $this->config['loginsession']);
	$response = $cloud->GetListBackups();
	if($response['error']==0) {
		$i=0;
		foreach($response['list'] as $value){
			$properties[$i]['PATH']=$value['pathname'];
			$properties[$i]['SIZE']=$value['size'];
			$properties[$i]['ID']=$i;
			$i++;
		}
		$out['PROPERTIES']=$properties;
		$out['OK']=$response['msg'];
	} else {
		$out['WARN']='<#LANG_STRING_ERROR#>: '.$response['error'].': '.$response['msg'];
	}
  }  
  if($this->mode=='get_one') {
	global $id;
	$cloud = Broadlink::Cloud($this->config['username'], $this->config['userid'], $this->config['loginsession']);
	$response = $cloud->GetBackup($properties[$id]['PATH']);
	if($response['error']==0) {
		$out['OK']='<#LANG_UNPACKED#> '.$response['msg'];
	} else {
		$out['WARN']='<#LANG_STRING_ERROR#>: '.$response['error'].': '.$response['msg'];
	}
  }
  if($this->mode=='cloud_export') {
	$sharedDataDir = ROOT.'cached'.DIRECTORY_SEPARATOR.'broadlink'.DIRECTORY_SEPARATOR.'SharedData';
	$arrayDevice = json_decode(file_get_contents($sharedDataDir.'/jsonDevice'), true);
	$arraySubIr  = json_decode(file_get_contents($sharedDataDir.'/jsonSubIr'), true);
	$arrayButton = json_decode(file_get_contents($sharedDataDir.'/jsonButton'), true);
	$arrayIrCode = json_decode(file_get_contents($sharedDataDir.'/jsonIrCode'), true);
	$i=0;
	foreach ($arrayIrCode as $arrayIrCode["id"]) {
		$IrCode  = $arrayIrCode["id"];
		$code    = implode(array_map("bin2hex", array_map("chr", $IrCode["code"])));
		$Button  = $arrayButton[search($arrayButton,$IrCode["buttonId"])];
		$SubIr   = $arraySubIr[search($arraySubIr,$Button["subIRId"])];
		$Device  = $arrayDevice[search($arrayDevice,$SubIr["deviceId"])];
		$cmd_name=$SubIr["name"];
		if ($Button["name"]) $cmd_name.='_'.$Button["name"];
		$cmd_name.=	'_'.$IrCode["buttonId"];
		$response[$i]['name'] = $cmd_name;
		$response[$i]['data'] = $code;
		$i++;
	}
	$out['TEXTAREA']=json_encode($response, JSON_UNESCAPED_UNICODE);
  } 

function search($array,$id) {
	$i=0;
	$y=count($array);
	do 
		if ($array[$i]['id'] == $id) return $i;
	while (++$i<$y);
}  
?>
