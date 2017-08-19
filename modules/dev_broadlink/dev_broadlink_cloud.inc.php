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
?>
