<?php
include_once('broadlink.class.php');

if (isset($_GET['command'])) {
chdir(dirname(__FILE__) . '/../../');
include_once("./config.php");
include_once("./lib/loader.php");
$db = new mysql(DB_HOST, '', DB_USER, DB_PASSWORD, DB_NAME);
include_once("./load_settings.php");
$command = isset($_GET['command']) ? $_GET['command'] : "";
$json = array();
$json['code'] = -1;
$command_arr=SQLSelectOne("SELECT * FROM dev_broadlink_commands WHERE TITLE='$command'");
$id=$command_arr['DEVICE_ID'];
$info=SQLSelectOne("SELECT * FROM dev_httpbrige_devices WHERE ID='$id'");
$data = $command_arr['VALUE'];

$json['code'] = -1;	
$rm = Broadlink::CreateDevice($info['IP'], $info['MAC'], 80, $info['DEVTYPE']);
$decoded_keys=json_decode($info['KEYS']);
$rm->Auth($decoded_keys->id, $decoded_keys->key);
$rm->Send_data($data);
$json['code'] = 1;

$result = json_encode($json, JSON_NUMERIC_CHECK);
header('Content-Type: application/json');
header("Content-length: " . strlen($result));
echo $result;
$db->Disconnect(); 
flush();
}
?>
