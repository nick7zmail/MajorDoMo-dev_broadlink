<?php
include_once('broadlink.class.php');
chdir(dirname(__FILE__) . '/../../');
include_once("./config.php");
include_once("./lib/loader.php");
$db = new mysql(DB_HOST, '', DB_USER, DB_PASSWORD, DB_NAME);
include_once("./load_settings.php");

function brLinkCommand($command)
{
$command_arr=SQLSelectOne("SELECT * FROM dev_broadlink_commands WHERE TITLE='$command'");
$id=$command_arr['DEVICE_ID'];
$info=SQLSelectOne("SELECT * FROM dev_httpbrige_devices WHERE ID='$id'");
$data = $command_arr['VALUE'];
$rm = Broadlink::CreateDevice($info['IP'], $info['MAC'], 80, $info['DEVTYPE']);
$rm->Auth();
$rm->Send_data($data);
$db->Disconnect();
flush();
return $result;
}
?>
