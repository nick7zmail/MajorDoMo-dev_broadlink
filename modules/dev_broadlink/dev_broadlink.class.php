<?php
/**
* BroadlinkHTTPBrige 
* @package project
* @author Wizard <sergejey@gmail.com>
* @copyright http://majordomo.smartliving.ru/ (c)
* @version 0.1 (wizard, 16:06:00 [Jun 28, 2016])
*/
//
//
class dev_broadlink extends module {
/**
* dev_httpbrige
*
* Module class constructor
*
* @access private
*/
function dev_broadlink() {
  $this->name="dev_broadlink";
  $this->title="Broadlink";
  $this->module_category="<#LANG_SECTION_DEVICES#>";
  $this->checkInstalled();
}
/**
* saveParams
*
* Saving module parameters
*
* @access public
*/
function saveParams($data=0) {
 $p=array();
 if (IsSet($this->id)) {
  $p["id"]=$this->id;
 }
 if (IsSet($this->view_mode)) {
  $p["view_mode"]=$this->view_mode;
 }
 if (IsSet($this->edit_mode)) {
  $p["edit_mode"]=$this->edit_mode;
 }
 if (IsSet($this->data_source)) {
  $p["data_source"]=$this->data_source;
 }
 if (IsSet($this->tab)) {
  $p["tab"]=$this->tab;
 }
 return parent::saveParams($p);
}
/**
* getParams
*
* Getting module parameters from query string
*
* @access public
*/
function getParams() {
  global $id;

  global $mode;
  global $mac;
  global $host;
  global $title;
  global $devtype;
  global $view_mode;
  global $edit_mode;
  global $data_source;
  global $tab;
  global $title_new;

   if (isset($title)) {
   $this->title=$title;
  }
  if (isset($id)) {
   $this->id=$id;
  }
  if (isset($mode)) {
   $this->mode=$mode;
  }
  if (isset($mac)) {
   $this->mac=$mac;
  }
    if (isset($host)) {
   $this->host=$host;
  }
  if (isset($devtype)) {
   $this->devtype=$devtype;
  }
  if (isset($view_mode)) {
   $this->view_mode=$view_mode;
  }
  if (isset($edit_mode)) {
   $this->edit_mode=$edit_mode;
  }
  if (isset($data_source)) {
   $this->data_source=$data_source;
  }
  if (isset($tab)) {
   $this->tab=$tab;
  }
  if (isset($title_new)) {
   $this->title_new=$title_new;
  }
}
/**
* Run
*
* Description
*
* @access public
*/
function run() {
 global $session;
  $out=array();
  if ($this->action=='admin') {
   $this->admin($out);
  } else {
   $this->usual($out);
  }
  if (IsSet($this->owner->action)) {
   $out['PARENT_ACTION']=$this->owner->action;
  }
  if (IsSet($this->owner->name)) {
   $out['PARENT_NAME']=$this->owner->name;
  }
  $out['VIEW_MODE']=$this->view_mode;
  $out['EDIT_MODE']=$this->edit_mode;
  $out['MODE']=$this->mode;
  $out['ACTION']=$this->action;
  $out['DATA_SOURCE']=$this->data_source;
  $out['TAB']=$this->tab;
  $this->data=$out;
  $p=new parser(DIR_TEMPLATES.$this->name."/".$this->name.".html", $this->data, $this);
  $this->result=$p->result;
}
/**
* BackEnd
*
* Module backend
*
* @access public
*/
function admin(&$out) {
 $this->getConfig();
 $out['API_URL']=$this->config['API_URL'];
 if (!$out['API_URL']) {
  $out['API_URL']='http://';
 }
 $out['API_TYPE']=$this->config['API'];
 $out['API_METHOD']=$this->config['API_METHOD'];
 if ($this->view_mode=='update_settings') {
   global $api_type;
   $this->config['API']=$api_type;
   global $api_url;
   $this->config['API_URL']=$api_url;
   global $api_method;
   $this->config['API_METHOD']=$api_method;
   $this->saveConfig();
   $this->redirect("?");
 }
 if (isset($this->data_source) && !$_GET['data_source'] && !$_POST['data_source']) {
  $out['SET_DATASOURCE']=1;
 }
 if ($this->data_source=='dev_httpbrige_devices' || $this->data_source=='') {
  if ($this->view_mode=='' || $this->view_mode=='search_dev_httpbrige_devices') {
   $this->search_dev_httpbrige_devices($out);
  }
  if ($this->view_mode=='edit_dev_httpbrige_devices') {
   $this->edit_dev_httpbrige_devices($out, $this->id);
  }
  if ($this->view_mode=='delete_dev_httpbrige_devices') {
   $this->delete_dev_httpbrige_devices($this->id);
   $this->redirect("?data_source=dev_httpbrige_devices");
  }
  if ($this->view_mode=='broadlink_devices_scan') {
  //print("View - broadlink_devices_scan");
         $this->broadlink_devices_scan($out);
  }

 }
 if (isset($this->data_source) && !$_GET['data_source'] && !$_POST['data_source']) {
  $out['SET_DATASOURCE']=1;
 }
 if ($this->data_source=='dev_broadlink_commands') {
  if ($this->view_mode=='' || $this->view_mode=='search_dev_broadlink_commands') {
   $this->search_dev_broadlink_commands($out);
  }
  if ($this->view_mode=='edit_dev_broadlink_commands') {
   $this->edit_dev_broadlink_commands($out, $this->id);
  }
 }
}
/**
* FrontEnd
*
* Module frontend
*
* @access public
*/
function usual(&$out) {
 $this->admin($out);
}
/**
* dev_httpbrige_devices search
*
* @access public
*/
 function search_dev_httpbrige_devices(&$out) {
  require(DIR_MODULES.$this->name.'/dev_httpbrige_devices_search.inc.php');
 }
/**
* broadlink_devices_scan search
*
* @access public
*/
  function broadlink_devices_scan(&$out) {
    //print("in metod broadlink_devices_scan");
        require(DIR_MODULES.$this->name.'/broadlink_devices_scan.inc.php');
    }

/**
* dev_httpbrige_devices edit/add
*
* @access public
*/
 function edit_dev_httpbrige_devices(&$out, $id) {
  require(DIR_MODULES.$this->name.'/dev_httpbrige_devices_edit.inc.php');
 }
/**
* dev_httpbrige_devices delete record
*
* @access public
*/
 function delete_dev_httpbrige_devices($id) {
  $rec=SQLSelectOne("SELECT * FROM dev_httpbrige_devices WHERE ID='$id'");
  if ($rec['TYPE'] == 'sp2' || $rec['TYPE'] == 'spmini' || $rec['TYPE'] == 'sp3') {
	removeLinkedProperty($rec['LINKED_OBJECT'], 'status', $this->name);
  }
  if ($rec['TYPE'] == 'sp3') {
	removeLinkedProperty($rec['LINKED_OBJECT'], 'lightstatus', $this->name);
  }
  SQLExec("DELETE FROM dev_httpbrige_devices WHERE ID='".$rec['ID']."'");
 }
/**
* dev_broadlink_commands search
*
* @access public
*/
 function search_dev_broadlink_commands(&$out) {
  require(DIR_MODULES.$this->name.'/dev_broadlink_commands_search.inc.php');
 }
/**
* dev_broadlink_commands edit/add
*
* @access public
*/
 function edit_dev_broadlink_commands(&$out, $id) {
  require(DIR_MODULES.$this->name.'/dev_broadlink_commands_edit.inc.php');
 }
 function propertySetHandle($object, $property, $value) {
  $this->getConfig();
  if ($this->config['API_URL']=='httpbrige') {
   $table='dev_httpbrige_devices';
   $properties=SQLSelect("SELECT * FROM $table WHERE LINKED_OBJECT LIKE '".DBSafe($object)."'");
   $total=count($properties);
   if ($total) {
    for($i=0;$i<$total;$i++) {
     //to-do
		if ($properties[$i]['TYPE'] == 'sp2' || $properties[$i]['TYPE'] == 'spmini' || $properties[$i]['TYPE'] == 'sp3') {
			if ($property == 'status') {
				if (gg($properties[$i]['LINKED_OBJECT'].'.'.'status') == 1 ) {
					$api_command=$this->config['API_URL'].'/?devMAC='.$properties[$i]['MAC'].'&action=on';
					getUrl($api_command);
				} else {
					$api_command=$this->config['API_URL'].'/?devMAC='.$properties[$i]['MAC'].'&action=off';
					getUrl($api_command);
				}
			}
			if ($property == 'lightstatus') {
				if (gg($properties[$i]['LINKED_OBJECT'].'.'.'status') == 1 ) {
					$api_command=$this->config['API_URL'].'/?devMAC='.$properties[$i]['MAC'].'&action=lighton';
					getUrl($api_command);
				} else {
					$api_command=$this->config['API_URL'].'/?devMAC='.$properties[$i]['MAC'].'&action=lightoff';
					getUrl($api_command);
				}
			}
		}
    }
   }
  } else {
	$table='dev_broadlink_commands';
	$properties=SQLSelect("SELECT * FROM $table WHERE LINKED_OBJECT LIKE '".DBSafe($object)."' AND LINKED_PROPERTY LIKE '".DBSafe($property)."'");
	$total=count($properties);
	if ($total) {
    for($i=0;$i<$total;$i++) {
     if ($value==1) {
		 	include_once(DIR_MODULES.$this->name.'/broadlink.class.php');
			$id=$properties[$i]['DEVICE_ID'];
			$data=$properties[$i]['VALUE'];
			$rec=SQLSelectOne("SELECT * FROM dev_httpbrige_devices WHERE ID='$id'");
			$rm = Broadlink::CreateDevice($rec['IP'], $rec['MAC'], 80, $rec['DEVTYPE']);
			$rm->Auth();
			$rm->Send_data($data);
			sg($object.".".$property, 0);
	 }
    }
   }
   $table2='dev_httpbrige_devices';
   $properties2=SQLSelect("SELECT * FROM $table2 WHERE LINKED_OBJECT LIKE '".DBSafe($object)."'");
   $total2=count($properties2);
   if ($total2) {
	for($i=0;$i<$total2;$i++) {
	  if ($properties2[$i]['TYPE'] == 'sp2' || $properties2[$i]['TYPE'] == 'spmini' || $properties2[$i]['TYPE'] == 'sp3') {	
		if ($value==1) {
			include_once(DIR_MODULES.$this->name.'/broadlink.class.php');
			$rm = Broadlink::CreateDevice($properties2[$i]['IP'], $properties2[$i]['MAC'], 80, $properties2[$i]['DEVTYPE']);
			$rm->Auth();
			$rm->Set_Power(1);
		} else {
			include_once(DIR_MODULES.$this->name.'/broadlink.class.php');
			$rm = Broadlink::CreateDevice($properties2[$i]['IP'], $properties2[$i]['MAC'], 80, $properties2[$i]['DEVTYPE']);
			$rm->Auth();
			$rm->Set_Power(0);			
		}
	  }
	  if ($properties2[$i]['TYPE'] == 'mp1') {	
		if ($value==1) {
			include_once(DIR_MODULES.$this->name.'/broadlink.class.php');
			$rm = Broadlink::CreateDevice($properties2[$i]['IP'], $properties2[$i]['MAC'], 80, $properties2[$i]['DEVTYPE']);
			$rm->Auth();
			$rm->Set_Power(substr($property, -1), 1);			
		} else {
			include_once(DIR_MODULES.$this->name.'/broadlink.class.php');
			$rm = Broadlink::CreateDevice($properties2[$i]['IP'], $properties2[$i]['MAC'], 80, $properties2[$i]['DEVTYPE']);
			$rm->Auth();
			$rm->Set_Power(substr($property, -1), 0);			
		}
	  }
	}
   }
  }
 }
 
function processSubscription($event_name, $details='') {
  if ($event_name=='HOURLY') {
		$this->check_params();
  }
 }
 
 function check_params() {
	$this->getConfig();
	$db_rec=SQLSelect("SELECT * FROM dev_httpbrige_devices");
	if ($this->config['API_URL']=='httpbrige') {
		for ($i = 1; $i <= count($db_rec); $i++) {
			$response ='';
			$rec=$db_rec[$i-1];
			if ($rec['TYPE']=='rm') {
					$ctx = stream_context_create(array('http' => array('timeout'=>2)));
					$response = file_get_contents($this->config['API_URL'].'/?devMAC='.$rec['MAC'], 0, $ctx);
					if(isset($response) && $response!='') {
						sg($rec['LINKED_OBJECT'].'.temperature', (float)$response);
					}
			}
			if ($rec['TYPE']=='rm3') {
			}
			if ($rec['TYPE']=='a1') {
					$ctx = stream_context_create(array('http' => array('timeout'=>2)));
					$response = file_get_contents($this->config['API_URL'].'/?devMAC='.$rec['MAC'], 0, $ctx);
					if(isset($response) && $response!='') { 
						$json = json_decode($response);	
						sg($rec['LINKED_OBJECT'].'.temperature', (float)$json->{'temperature'});
						sg($rec['LINKED_OBJECT'].'.humidity', (float)$json->{'humidity'});
						sg($rec['LINKED_OBJECT'].'.noise', (int)$json->{'noisy'});
						sg($rec['LINKED_OBJECT'].'.luminosity', (int)$json->{'light'});
						sg($rec['LINKED_OBJECT'].'.air', (int)$json->{'air'});	
					}
			}
			if ($rec['TYPE']=='sp2') {
					$ctx = stream_context_create(array('http' => array('timeout'=>2)));
					$response = file_get_contents($this->config['API_URL'].'/?devMAC='.$rec['MAC'], 0, $ctx);
					if(isset($response) && $response!='') {
						sg($rec['LINKED_OBJECT'].'.status', (int)$response);
					}
					
					$response = file_get_contents($this->config['API_URL'].'/?devMAC='.$rec['MAC'].'&action=power ', 0, $ctx);
					if(isset($response) && $response!='') {
						sg($rec['LINKED_OBJECT'].'.power', $response);
					}
			}
			if ($rec['TYPE']=='spmini') {
					$ctx = stream_context_create(array('http' => array('timeout'=>2)));
					$response = file_get_contents($this->config['API_URL'].'/?devMAC='.$rec['MAC'], 0, $ctx);
					if(isset($response) && $response!='') {
						sg($rec['LINKED_OBJECT'].'.status', (int)$response);
					}
			}
			if ($rec['TYPE']=='sp3') {
					$ctx = stream_context_create(array('http' => array('timeout'=>2)));
					$response = file_get_contents($this->config['API_URL'].'/?devMAC='.$rec['MAC'], 0, $ctx);
					if(isset($response) && $response!='') {
						sg($rec['LINKED_OBJECT'].'.status', (int)$response);
					}
					$response = file_get_contents($this->config['API_URL'].'/?devMAC='.$rec['MAC'].'&action=lightstatus', 0, $ctx);
					if(isset($response) && $response!='') {
						sg($rec['LINKED_OBJECT'].'.lightstatus', $response);
					}
			}
			if(isset($response) && $response!='') {
				$rec['UPDATED']=date('Y-m-d H:i:s');
				SQLUpdate('dev_httpbrige_devices', $rec);
			}
		}
	} else {
		include_once(DIR_MODULES.$this->name.'/broadlink.class.php');
		for ($i = 1; $i <= count($db_rec); $i++) {
			$response = '';
			$rec=$db_rec[$i-1];
			if ($rec['TYPE']=='rm') {
					$rm = Broadlink::CreateDevice($rec['IP'], $rec['MAC'], 80, $rec['DEVTYPE']);
					$rm->Auth();
					$response = $rm->Check_temperature();
					if(isset($response) && $response!='') {
						sg($rec['LINKED_OBJECT'].'.temperature', (float)$response);
					}
			}
			if ($rec['TYPE']=='rm3') {
			}
			if ($rec['TYPE']=='a1') {
					$rm = Broadlink::CreateDevice($rec['IP'], $rec['MAC'], 80, $rec['DEVTYPE']);
					$rm->Auth();
					$response = $rm->Check_sensors();
					if(isset($response) && $response!='') {
						sg($rec['LINKED_OBJECT'].'.temperature', (float)$response['temperature']);
						sg($rec['LINKED_OBJECT'].'.humidity', (float)$response['humidity']);
						sg($rec['LINKED_OBJECT'].'.noise', (int)$response['noise']);
						sg($rec['LINKED_OBJECT'].'.light', (int)$response['light']);
						sg($rec['LINKED_OBJECT'].'.air_quality', (int)$response['air_quality']);	
						sg($rec['LINKED_OBJECT'].'.light_word', $response['light_word']);
						sg($rec['LINKED_OBJECT'].'.air_quality_word', $response['air_quality_word']);
						sg($rec['LINKED_OBJECT'].'.noise_word', $response['noise_word']);
					}
			}
			if ($rec['TYPE']=='sp2' || $rec['TYPE'] == 'spmini' || $rec['TYPE'] == 'sp3') {
				include_once(DIR_MODULES.$this->name.'/broadlink.class.php');
				$rm = Broadlink::CreateDevice($rec['IP'], $rec['MAC'], 80, $rec['DEVTYPE']);
				$rm->Auth();
				$response = $rm->Check_Power();	
					if(isset($response)) {
						sg($rec['LINKED_OBJECT'].'.status', $response);
					}
			}
			if ($rec['TYPE']=='mp1') {
				include_once(DIR_MODULES.$this->name.'/broadlink.class.php');
				$rm = Broadlink::CreateDevice($rec['IP'], $rec['MAC'], 80, $rec['DEVTYPE']);
				$rm->Auth();
				$response = $rm->Check_Power();	
					if(isset($response) && $response!='') {
						sg($rec['LINKED_OBJECT'].'.status1', $response[0]);
						sg($rec['LINKED_OBJECT'].'.status2', $response[1]);
						sg($rec['LINKED_OBJECT'].'.status3', $response[2]);
						sg($rec['LINKED_OBJECT'].'.status4', $response[3]);
					}
			}
			if(isset($response) && $response!='') {
				$rec['UPDATED']=date('Y-m-d H:i:s');
				SQLUpdate('dev_httpbrige_devices', $rec);
			}
		}
	}
 }
/**
* Install
*
* Module installation routine
*
* @access private
*/
 function install($data='') {
 subscribeToEvent($this->name, 'HOURLY');
  parent::install();
 }
/**
* Uninstall
*
* Module uninstall routine
*
* @access public
*/
 function uninstall() {
  unsubscribeFromEvent($this->name, 'HOURLY');
  SQLExec('DROP TABLE IF EXISTS dev_httpbrige_devices');
  SQLExec('DROP TABLE IF EXISTS dev_broadlink_commands');
  parent::uninstall();
 }
/**
* dbInstall
*
* Database installation routine
*
* @access private
*/
 function dbInstall() {
/*
dev_httpbrige_devices - 
*/
  $data = <<<EOD
 dev_httpbrige_devices: ID int(10) unsigned NOT NULL auto_increment
 dev_httpbrige_devices: TYPE varchar(10) NOT NULL DEFAULT ''
 dev_httpbrige_devices: TITLE varchar(100) NOT NULL DEFAULT ''
 dev_httpbrige_devices: DEVTYPE varchar(10) NOT NULL DEFAULT ''
 dev_httpbrige_devices: IP varchar(20) NOT NULL DEFAULT ''
 dev_httpbrige_devices: MAC varchar(20) NOT NULL DEFAULT ''
 dev_httpbrige_devices: LINKED_OBJECT varchar(100) NOT NULL DEFAULT ''
 dev_httpbrige_devices: LINKED_PROPERTY varchar(100) NOT NULL DEFAULT ''
 dev_httpbrige_devices: LINKED_METHOD varchar(100) NOT NULL DEFAULT ''
 dev_httpbrige_devices: UPDATED datetime
 dev_broadlink_commands: ID int(10) unsigned NOT NULL auto_increment
 dev_broadlink_commands: TITLE varchar(100) NOT NULL DEFAULT ''
 dev_broadlink_commands: VALUE varchar(1024) NOT NULL DEFAULT ''
 dev_broadlink_commands: DEVICE_ID int(10) NOT NULL DEFAULT '0'
 dev_broadlink_commands: LINKED_OBJECT varchar(100) NOT NULL DEFAULT ''
 dev_broadlink_commands: LINKED_PROPERTY varchar(100) NOT NULL DEFAULT ''
EOD;
  parent::dbInstall($data);
 }
// --------------------------------------------------------------------
}
/*
*
* TW9kdWxlIGNyZWF0ZWQgSnVuIDI4LCAyMDE2IHVzaW5nIFNlcmdlIEouIHdpemFyZCAoQWN0aXZlVW5pdCBJbmMgd3d3LmFjdGl2ZXVuaXQuY29tKQ==
*
*/
