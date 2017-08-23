<?php
	include_once('broadlink.class.php');
	chdir(dirname(__FILE__) . '/../../');
	include_once("./config.php");
	include_once("./lib/loader.php");
	$db = new mysql(DB_HOST, '', DB_USER, DB_PASSWORD, DB_NAME);
	include_once("./load_settings.php");
	
if($_GET['mode']=='decrypt'){
	header("Cache-Control: no-cache, must-revalidate, Content-Type: text/plain");
	echo '<tt>';

	$key = array(0x09, 0x76, 0x28, 0x34, 0x3f, 0xe9, 0x9e, 0x23, 0x76, 0x5c, 0x15, 0x13, 0xac, 0xcf, 0x8b, 0x02);
	$iv = array(0x56, 0x2e, 0x17, 0x99, 0x6d, 0x09, 0x3d, 0x28, 0xdd, 0xb3, 0xba, 0x69, 0x5a, 0x2e, 0x6f, 0x58);
	$id = array(0, 0, 0, 0);

	echo '<form action="" enctype="text/plain" id="code" method="get" name="code" target="_self">
		<p><br>
			<textarea cols="100" name="text" rows="10"></textarea></p>
		<p><input type="submit"></p>
	</form>';
	if ($_GET['text']) $wireshark = $_GET['text'];
	else
	//$wireshark = '5a a5 aa 55 5a a5 aa 55  02 00 09 00 44 51 74 03 02 00 09 00 00 00 00 00  36 40 a8 6e 14 07 00 00 5e d3 00 00 22 27 6a 00  41 82 91 73 96 0d 43 b4 01 00 00 00 b5 be 00 00  9a 11 20 97 a7 3c 38 29 f4 78 f0 8a f4 9f 88 bb';
	//$wireshark = '5aa5aa555aa5aa550200040037b0000002000300000000000d711ae60040000028e2000022276a004a819173960d43b401000000c3be0000c68c336528ad09321d89ef09a1a1a6ef52b7b41ef5318fdc40139fa717fb6856f36b3795dceb0728a5d086e4471c1c6d';
	//$wireshark = '5aa5aa555aa5aa5502000800f56e1d0002000800000000000d711ae6004000006fe2000022276a0082839173960d43b40100000069c200000d1dbe36e1149a49f18557e049bd51a0239ffd9f7f6f23a4b66210d58c162005d8bc916a802cd366931299bfab44f926';
	$wireshark = '5aa5aa555aa5aa5500000000000000000000000000000000000000000000000036fa000022276a0096809173960d43b401000000e7c700009352c90107572878e4baf02ee96809aba5f7796d3b369ef220209cfcc7f9993916546a2e47d21f333a54ebf8be579b61fa05744a7e5f5cada89879a5fd7b74da98b2cae17fda897370b6cb172760cf1caac47d32a483cf77a8629487808f15cd';

	$wireshark = str_replace("%0D","",$wireshark);
	$wireshark = str_replace("%0A","",$wireshark);
	$wireshark = str_replace("+","",$wireshark);
	$wireshark = str_replace(" ","",$wireshark);
	$wireshark = str_replace("\n","",$wireshark);
	$wireshark = strToUpper($wireshark);
	echo $wireshark.'<hr>';

	$wireshark_arr = str_split($wireshark, 2);
	//$wireshark_arr = explode(' ', $wireshark);
	echo '<hr>wireshark hex:<br>';
	print_r($wireshark_arr);

	$hex='';
		for ($i=0; $i < count($wireshark_arr); $i++){
			$ord1 = ord($wireshark_arr[$i][0])-48;
			$ord2 = ord($wireshark_arr[$i][1])-48;
			if ($ord1 > 16) $ord1 = $ord1 - 7;
			if ($ord2 > 16) $ord2 = $ord2 - 7;
			//echo '<br>$ord1='.$ord1.'$ord2='.$ord2;
			$hex[$i] = $ord1 * 16 + $ord2;
		}
	echo '<hr>hex:<br>';
	//print_r($hex);
	print_dec_table($hex);
	print_hex_table($hex);

	$enc_payload = array_slice($hex, 0x38);
	if(count($enc_payload) > 0){
		$payload = byte2array(aes128_cbc_decrypt(key2str(), byte($enc_payload), iv2str()));
	}

	if (($wireshark_arr[0x00]=='5A') && 
		($wireshark_arr[0x01]=='A5') && 
		($wireshark_arr[0x02]=='AA') &&
		($wireshark_arr[0x03]=='55') &&
		($wireshark_arr[0x04]=='5A') &&
		($wireshark_arr[0x05]=='A5') &&
		($wireshark_arr[0x06]=='AA') &&
		($wireshark_arr[0x07]=='55')) {
		echo '<hr>Заголовок пакета:<br>';
		echo 'контрольная сумма &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp[0x20-0x21]: 0x'.$wireshark_arr[0x21].$wireshark_arr[0x20].'<br>';
		echo 'тип пакета &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp[0x26-0x27]: 0x'.$wireshark_arr[0x27].$wireshark_arr[0x26];
		if (($wireshark_arr[0x27] == '00') && ($wireshark_arr[0x26] == '65')) echo '<font color="red"> (запрос авторизации)</font>';
		echo '<br>';
		echo 'счетчик пакета &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp[0x28-0x29]: 0x'.$wireshark_arr[0x29].$wireshark_arr[0x28].'<br>';
		echo 'MAC-адрес &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp[0x2a-0x2f]: '.$wireshark_arr[0x2a].':'.$wireshark_arr[0x2b].':'.$wireshark_arr[0x2c].':'.$wireshark_arr[0x2d].':'.$wireshark_arr[0x2e].':'.$wireshark_arr[0x2f].'<br>';
		echo 'ID устройства &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp[0x30-0x33]: 0x'.$wireshark_arr[0x33].$wireshark_arr[0x32].$wireshark_arr[0x31].$wireshark_arr[0x30].'<br>';
		echo 'контрольная сумма заголовка [0x34-0x35]: 0x'.$wireshark_arr[0x35].$wireshark_arr[0x34].'<br>';
		echo 'Шифрованный Payload &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp[0x38-до конца]:<br>';
		print_dec_table($enc_payload);
		print_hex_table($enc_payload);
	}
	echo '<hr>payload decrypted:<br>';
	print_dec_table($payload);
	print_hex_table($payload);
} else {
	header("Cache-Control: no-cache, must-revalidate, Content-Type: text/plain"); // HTTP/1.1
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
}

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

function key2str(){
	global $key;
	return implode(array_map("chr", $key));
}

function iv2str(){
	global $iv;
	return implode(array_map("chr", $iv));
}
?>
