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
	$this->config['username']=$response['nickname'];
	$this->config['userid']=$response['userid'];
	$this->config['loginsession']=$response['loginsession'];
	$this->saveConfig();
	$this->redirect("?view_mode=cloud");
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
	$out['OK']=$response['msg'];
  }  
?>
