<?php
include("broadlink.class.php");

global $session;

if ($this->owner->name == 'panel') {
    $out['CONTROLPANEL'] = 1;
}

$qry = "1";
// search filters
// QUERY READY
global $save_qry;
if ($save_qry) {
    $qry = $session->data['ssdp_devices_qry'];
} else {
    $session->data['ssdp_devices_qry'] = $qry;
}
if (!$qry) $qry = "1";
//="ID DESC";
//$out['SORTBY']=$sortby_ssdp_devices;
// SEARCH RESULTS
$res = Scan();
$out['RESULT'] = $res;
if ($res[0]['ID']) {
$current .= "in if $res[0]['ID']\n";
    //paging($res, 100, $out); // search result paging
    $total = count($res);
    for ($i = 0; $i < $total; $i++) {
        // some action for every record if required
        $tmp = explode(' ', $res[$i]['UPDATED']);
        $res[$i]['UPDATED'] = fromDBDate($tmp[0]) . " " . $tmp[1];
    }
    $out['RESULT'] = $res;
}
//$out['RESULT'] = $res;


function Scan()
{
//print("in scan");
$result = array();
$devices = Broadlink::Discover();

 
   foreach ($devices as $device) {
	$obj = array();
	$obj['DEVTYPE'] = $device->devtype();
	$obj['NAME'] = $device->name();
	$obj['MAC'] = $device->mac();
	$obj['HOST'] = $device->host();
	$obj['MODEL'] = $device->model();

	if($obj['MODEL'] == "RM2"){

		$device->Auth();
		$temperature = $device->Check_temperature();
		$obj['TEMPERATURE'] = $temperature;
	}
	else if($obj['MODEL'] == "A1"){

		$device->Auth();
		$data = $device->Check_sensors();

		$obj = array_merge($obj, $data);

	}
	array_push($result, $obj);
}
    return $result;
}

function array_search_result($array, $key, $value)
{
    //  global $result;
    foreach ($array as $k => $v) {
        if (array_key_exists($key, $v) && ($v[$key] == $value)) {
            return true;
        }
    }
    // return $result;;
}