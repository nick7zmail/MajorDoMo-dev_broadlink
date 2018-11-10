<?php
include_once(DIR_MODULES.'/dev_broadlink/broadlink.class.php');

function brLinkRawCommand($devid, $data)
{
	$info = SQLSelectOne("SELECT * FROM dev_httpbrige_devices WHERE ID='".DbSafe($devid)."'");
	$rm = Broadlink::CreateDevice($info['IP'], $info['MAC'], 80, $info['DEVTYPE']);
	$decoded_keys = json_decode($info['KEYS']);
	$rm->Auth($decoded_keys->id, $decoded_keys->key);
	$rm->Send_data($data);
}

function brLinkCommand($command)
{
	$command_arr = SQLSelectOne("SELECT * FROM dev_broadlink_commands WHERE TITLE='".DbSafe($command)."'");
	$devid = $command_arr['DEVICE_ID'];
	$data = $command_arr['VALUE'];
	brLinkRawCommand($devid, $data);
}

?>