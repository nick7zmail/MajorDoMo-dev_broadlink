<?php
include_once(DIR_MODULES.'/dev_broadlink/broadlink.class.php');

function brLinkCommand($command)
{
$command_arr=SQLSelectOne("SELECT * FROM dev_broadlink_commands WHERE TITLE='$command'");
$id=$command_arr['DEVICE_ID'];
$info=SQLSelectOne("SELECT * FROM dev_httpbrige_devices WHERE ID='$id'");
$data = $command_arr['VALUE'];
$rm = Broadlink::CreateDevice($info['IP'], $info['MAC'], 80, $info['DEVTYPE']);
$rm->Auth();
$rm->Send_data($data);
}
?>
