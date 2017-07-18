<?php
header("Cache-Control: no-cache, must-revalidate, Content-Type: text/plain"); // HTTP/1.1
include_once('broadlink.class.php');
chdir(dirname(__FILE__) . '/../../');
include_once("./config.php");
include_once("./lib/loader.php");
$db = new mysql(DB_HOST, '', DB_USER, DB_PASSWORD, DB_NAME);
include_once("./load_settings.php");

echo '<tt>';

$host = $_GET['ip'];
$rec=SQLSelectOne("SELECT * FROM dev_httpbrige_devices WHERE IP='$host'");

$rm = Broadlink::CreateDevice($rec['IP'], $rec['MAC'], 80, $rec['DEVTYPE']);
$decoded_keys=json_decode($rec['KEYS']);
$rm->Auth($decoded_keys->id, $decoded_keys->key);
$payload = $rm->some_req();			//команда (для примера...можно менять на другие)

echo '<hr>payload decrypted:<br>';
print_dec_table($payload);
print_hex_table($payload);
$db->Disconnect(); 
flush();

function print_hex_table($payload){
	echo '<br>';
	for ($y=0; $y<(count($payload)/16) ; $y++) {
		for ($i=0; $i<16; $i++) {
			if (($i+$y*16)<count($payload)){
				if (($i==0) && ($y<16)) echo '00'.dechex($y).'0&nbsp&nbsp&nbsp';
				if (($i==0) && ($y>=16)) echo '0'.dechex($y).'0&nbsp&nbsp&nbsp';
				if ($i==8) echo '&nbsp';
				if (($payload[$i+$y*16]<16) && ($payload[$i+$y*16]!=0)) echo '<font color="silver">0</font>';
				if ($payload[$i+$y*16]==0) echo '<font color="silver">00 </font>';
				else echo dechex($payload[$i+$y*16]).' ';
			}
		}
		echo '<br>';
	}
}

function print_dec_table($payload){
	echo '<br>';
	for ($y=0; $y<(count($payload)/16) ; $y++) {
		for ($i=0; $i<16; $i++) {
			if (($i+$y*16)<count($payload)){
				if (($i==0) && ($y<16)) echo '00'.dechex($y).'0&nbsp&nbsp&nbsp';
				if (($i==0) && ($y>=16)) echo '0'.dechex($y).'0&nbsp&nbsp&nbsp';
				if ($i==8) echo '&nbsp';
				if ($payload[$i+$y*16]<10) echo '<font color="silver">00</font>';
				else if ($payload[$i+$y*16]<100) echo '<font color="silver">0</font>';
				echo $payload[$i+$y*16].' ';
			}
		}
		echo '<br>';
	}
}

?>
