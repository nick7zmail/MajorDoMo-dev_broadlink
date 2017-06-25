<?php

function aes128_cbc_encrypt($key, $data, $iv) {
  return mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $data, MCRYPT_MODE_CBC, $iv);
}

function aes128_cbc_decrypt($key, $data, $iv) {
  return mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $data, MCRYPT_MODE_CBC, $iv);
}

class Broadlink{
	protected $name; 
    protected $host;
    protected $port = 80;
    protected $mac;
    protected $timeout = 10;
    protected $count;
    protected $key = array(0x09, 0x76, 0x28, 0x34, 0x3f, 0xe9, 0x9e, 0x23, 0x76, 0x5c, 0x15, 0x13, 0xac, 0xcf, 0x8b, 0x02);
    protected $iv = array(0x56, 0x2e, 0x17, 0x99, 0x6d, 0x09, 0x3d, 0x28, 0xdd, 0xb3, 0xba, 0x69, 0x5a, 0x2e, 0x6f, 0x58);
    protected $id = array(0, 0, 0, 0);
    protected $devtype;

    function __construct($h = "", $m = "", $p = 80, $d = 0) {

    	$this->host = $h;
    	$this->port = $p;
    	$this->devtype = is_string($d) ? hexdec($d) : $d;

    	if(is_array($m)){

    		$this->mac = $m;      		
    	}
    	else{

    		$this->mac = array();
		    $mac_str_array = explode(':', $m);

            foreach ( array_reverse($mac_str_array) as $value ) {
                array_push($this->mac, $value);
            }

    	}

    	 		
		$this->count = rand(0, 0xffff);

    }
    
    function __destruct() {

		    
    }

    public static function CreateDevice($h = "", $m = "", $p = 80, $d = 0){

        switch (self::getdevtype($d)) {
            case 0:
                return new SP1($h, $m, $p, $d);
                break;
            case 1:
                return new SP2($h, $m, $p, $d);
                break;
            case 2:
                return new RM($h, $m, $p, $d);
                break;    
            case 3:
                return new A1($h, $m, $p, $d);
                break;
            case 4:
                return new MP1($h, $m, $p, $d);
                break;
            case 5:
                return new MS1($h, $m, $p, $d);
                break;
            case 6:
                return new S1($h, $m, $p, $d);
                break;
            default:
        } 

        return NULL;
    }

    protected function key(){
    	return implode(array_map("chr", $this->key));
    }

    protected function iv(){
    	return implode(array_map("chr", $this->iv));
    }

    public function mac(){

    	$mac = "";

    	foreach ($this->mac as $value) {
    		$mac = sprintf("%02x", $value) . ':' . $mac;
    	}

    	return substr($mac, 0, strlen($mac) - 1);
    }

    public function host(){
    	return $this->host;
   	}

   	public function name(){
    	return $this->name;
   	}

   	public function devtype(){
    	return sprintf("0x%x", $this->devtype);
   	}

    public function devmodel(){
        return self::getdevtype($this->devtype);
    }

   	public function model(){
    	
    	$type = "Unknown";

    	switch ($this->devtype) {
    		case 0:
    			$type = "SP1";
    			break;
    		case 0x2711:
    			$type = "SP2";
    			break;
    		case 0x2719: 
    		case 0x7919:
    		case 0x271a:
    		case 0x791a:
    			$type = "Honeywell SP2";
    			break;
    		case 0x2720: 
    			$type = "SPMini";
    			break;
    		case 0x753e: 
    			$type = "SP3";
    			break;
    		case 0x2728: 
    			$type = "SPMini2";
    			break;
    		case 0x2733: 
    		case 0x273e:
    			$type = "OEM branded SPMini";
    			break;
    		case 0x7530: 
    		case 0x7918:
    			$type = "OEM branded SPMini2";
    			break;
    		case 0x2736: 
    			$type = "SPMiniPlus";
    			break;
			case 0x7547:
    			$type = "SC1 switch";
				break;
    		case 0x2712: 
    			$type = "RM2";
    			break;
    		case 0x2737: 
    			$type = "RM Mini";
    			break;
    		case 0x273d: 
    			$type = "RM Pro Phicomm";
    			break;
    		case 0x2783: 
    			$type = "RM2 Home Plus";
    			break;
    		case 0x277c: 
    			$type = "RM2 Home Plus";
    			break;														 	    			
    		case 0x277c: 
    			$type = "RM2 Home Plus GDT";
    			break;
    		case 0x272a: 
    			$type = "RM2 Pro Plus";
    			break;
    		case 0x2787: 
    			$type = "RM2 Pro Plus2";
    			break;
    		case 0x278b: 
    			$type = "RM2 Pro Plus BL";
    			break;														 	    			
    		case 0x278f: 
    			$type = "RM Mini Shate";
    			break;
    		case 0x2714: 
    			$type = "A1";
    			break;
    		case 0x4EB5: 
    			$type = "MP1";
    			break;
    		case 0x271F: 
    			$type = "MS1";
    			break;
    		case 0x2722: 
    			$type = "S1";
    			break;
    		default:
    			break;
    	}

    	return $type;
    }

    public static function getdevtype($devtype){
    	
    	$type = -1;

        $devtype = is_string($devtype) ? hexdec($devtype) : $devtype;

    	switch ($devtype) {
    		case 0:
    			$type = 0;
    			break;
    		case 0x2711:
    			$type = 1;
    			break;
    		case 0x2719: 
    		case 0x7919:
    		case 0x271a:
    		case 0x791a:
    			$type = 1;
    			break;
    		case 0x2720: 
    			$type = 1;
    			break;
    		case 0x753e: 
    			$type = 1;
    			break;
    		case 0x2728: 
    			$type = 1;
    			break;
    		case 0x2733: 
    		case 0x273e:
    			$type = 1;
    			break;
    		case 0x7530: 
    		case 0x7918:
    			$type = 1;
    			break;
    		case 0x2736:
    			$type = 1;
    			break;
			case 0x7547:
			    $type = 1;
    			break;
    		case 0x2712: 
    			$type = 2;
    			break;
    		case 0x2737: 
    			$type = 2;
    			break;
    		case 0x273d: 
    			$type = 2;
    			break;
    		case 0x2783: 
    			$type = 2;
    			break;
    		case 0x277c: 
    			$type = 2;
    			break;														 	    			
    		case 0x277c: 
    			$type = 2;
    			break;
    		case 0x272a: 
    			$type = 2;
    			break;
    		case 0x2787: 
    			$type = 2;
    			break;
    		case 0x278b: 
    			$type = 2;
    			break;														 	    			
    		case 0x278f: 
    			$type = 2;
    			break;
    		case 0x2714: 
    			$type = 3;
    			break;
    		case 0x4EB5: 
    			$type = 4;
    			break;
    		case 0x271F: 
    			$type = 5;
    			break;
    		case 0x2722: 
    			$type = 6;
    			break;
    		default:
    			break;
    	}

    	return $type;
    } 	

    protected static function bytearray($size){

    	$packet = array();

	    for($i = 0 ; $i < $size ; $i++){
	    	$packet[$i] = 0;
	    }

	    return $packet;
    }

    protected static function byte2array($data){

	    return array_merge(unpack('C*', $data));
    }

    protected static function byte($array){

	    return implode(array_map("chr", $array));
    }

    public static function Discover(){

    	$devices = array();

    	$s = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
  		socket_connect($s ,'8.8.8.8', 53);  // connecting to a UDP address doesn't send packets
  		socket_getsockname($s, $local_ip_address, $port);
  		socket_close($s);

  		$cs = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);

   		if($cs){
   			socket_set_option($cs, SOL_SOCKET, SO_REUSEADDR, 1);
    		socket_set_option($cs, SOL_SOCKET, SO_BROADCAST, 1);
    		socket_set_option($cs, SOL_SOCKET, SO_RCVTIMEO, array('sec'=>1, 'usec'=>0));
    		socket_bind($cs, 0, 0);
   		}

  		$address = explode('.', $local_ip_address);
		$packet = self::bytearray(0x30);

	    $timezone = (int)intval(date("Z"))/-3600;
	  	$year = date("Y");

		if($timezone < 0){
		    $packet[0x08] = 0xff + $timezone - 1;
		    $packet[0x09] = 0xff;
		    $packet[0x0a] = 0xff;
		    $packet[0x0b] = 0xff;
		}
		else{

		    $packet[0x08] = $timezone;
		    $packet[0x09] = 0;
		    $packet[0x0a] = 0;
		    $packet[0x0b] = 0;
		}    

		$packet[0x0c] = $year & 0xff;
		$packet[0x0d] = $year >> 8;
		$packet[0x0e] = intval(date("i"));
		$packet[0x0f] = intval(date("H"));
		$subyear = substr($year, 2);
		$packet[0x10] = intval($subyear);
		$packet[0x11] = intval(date('N'));
		$packet[0x12] = intval(date("d"));
		$packet[0x13] = intval(date("m"));
		$packet[0x18] = intval($address[0]);
		$packet[0x19] = intval($address[1]);
		$packet[0x1a] = intval($address[2]);
		$packet[0x1b] = intval($address[3]);
		$packet[0x1c] = $port & 0xff;
		$packet[0x1d] = $port >> 8;
		$packet[0x26] = 6;

		$checksum = 0xbeaf;

		for($i = 0 ; $i < sizeof($packet) ; $i++){
	      $checksum += $packet[$i];
	    }

	   	$checksum = $checksum & 0xffff;

		$packet[0x20] = $checksum & 0xff;
		$packet[0x21] = $checksum >> 8;

		socket_sendto($cs, self::byte($packet), sizeof($packet), 0, '255.255.255.255', 80);
		while(socket_recvfrom($cs, $response, 2048, 0, $from, $port)){

			$host = '';

			$responsepacket = self::byte2array($response);


			$devtype = hexdec(sprintf("%x%x", $responsepacket[0x35], $responsepacket[0x34]));
			$host_array = array_slice($responsepacket, 0x36, 4);
			$mac = array_slice($responsepacket, 0x3a, 6);

			foreach ( array_reverse($host_array) as $ip ) {
 				$host .= $ip . ".";
			}

			$host = substr($host, 0, strlen($host) - 1);
			$device = Broadlink::CreateDevice($host, $mac, 80, $devtype);

			if($device != NULL){
                $device->name = str_replace(array("\0","\2"), '', Broadlink::byte(array_slice($responsepacket, 0x40)));
				array_push($devices, $device);
			}


		}

		if($cs){
			socket_close($cs);
		}

		return $devices;

    }


    function send_packet($command, $payload){

    	$cs = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);

   		if($cs){
   			socket_set_option($cs, SOL_SOCKET, SO_REUSEADDR, 1);
   			socket_set_option($cs, SOL_SOCKET, SO_BROADCAST, 1);
   			socket_bind($cs, 0, 0);
   		}

	    $this->count = ($this->count + 1) & 0xffff;

	    $packet = $this->bytearray(0x38);

	    $packet[0x00] = 0x5a;
	    $packet[0x01] = 0xa5;
	    $packet[0x02] = 0xaa;
	    $packet[0x03] = 0x55;
	    $packet[0x04] = 0x5a;
	    $packet[0x05] = 0xa5;
	    $packet[0x06] = 0xaa;
	    $packet[0x07] = 0x55;
	    $packet[0x24] = 0x2a;
	    $packet[0x25] = 0x27;
	    $packet[0x26] = $command;
	    $packet[0x28] = $this->count & 0xff;
	    $packet[0x29] = $this->count >> 8;
	    $packet[0x2a] = $this->mac[0];
	    $packet[0x2b] = $this->mac[1];
	    $packet[0x2c] = $this->mac[2];
	    $packet[0x2d] = $this->mac[3];
	    $packet[0x2e] = $this->mac[4];
	    $packet[0x2f] = $this->mac[5];
	    $packet[0x30] = $this->id[0];
	    $packet[0x31] = $this->id[1];
	    $packet[0x32] = $this->id[2];
	    $packet[0x33] = $this->id[3];

	    $checksum = 0xbeaf;
	    for($i = 0 ; $i < sizeof($payload) ; $i++){
	      $checksum += $payload[$i];
	      $checksum = $checksum & 0xffff;  
	    }	    

	    $aes = $this->byte2array(aes128_cbc_encrypt($this->key(), $this->byte($payload), $this->iv()));

	    $packet[0x34] = $checksum & 0xff;
	    $packet[0x35] = $checksum >> 8;

	    for($i = 0 ; $i < sizeof($aes) ; $i++){
	      array_push($packet, $aes[$i]);
	    }

	    $checksum = 0xbeaf;
	    for($i = 0 ; $i < sizeof($packet) ; $i++){
	      $checksum += $packet[$i];
	      $checksum = $checksum & 0xffff;
	    }	    

	    $packet[0x20] = $checksum & 0xff;
	    $packet[0x21] = $checksum >> 8;

	    $starttime = time();


	    $from = '';
	    socket_sendto($cs, $this->byte($packet), sizeof($packet), 0, $this->host, $this->port);
	    socket_set_option($cs, SOL_SOCKET, SO_RCVTIMEO, array('sec'=>$this->timeout, 'usec'=>0));

	    $ret = socket_recvfrom($cs, $response, 2048, 0, $from, $port);

	    if($cs){
	    	socket_close($cs);
	    }

	    return $this->byte2array($response);

    }

    public function Auth($id_authorized = null, $key_authorized = null){
		if (!isset($id_authorized) || !isset($key_authorized)) {
			$payload = $this->bytearray(0x50);
			$payload[0x04] = 0x31;
			$payload[0x05] = 0x31;
			$payload[0x06] = 0x31;
			$payload[0x07] = 0x31;
			$payload[0x08] = 0x31;
			$payload[0x09] = 0x31;
			$payload[0x0a] = 0x31;
			$payload[0x0b] = 0x31;
			$payload[0x0c] = 0x31;
			$payload[0x0d] = 0x31;
			$payload[0x0e] = 0x31;
			$payload[0x0f] = 0x31;
			$payload[0x10] = 0x31;
			$payload[0x11] = 0x31;
			$payload[0x12] = 0x31;
			$payload[0x1e] = 0x01;
			$payload[0x2d] = 0x01;
			$payload[0x30] = ord('T');
			$payload[0x31] = ord('e');
			$payload[0x32] = ord('s');
			$payload[0x33] = ord('t');
			$payload[0x34] = ord(' ');
			$payload[0x35] = ord(' ');
			$payload[0x36] = ord('1');

			$response = $this->send_packet(0x65, $payload);
			$enc_payload = array_slice($response, 0x38);

			$payload = $this->byte2array(aes128_cbc_decrypt($this->key(), $this->byte($enc_payload), $this->iv()));

			$this->id = array_slice($payload, 0x00, 4);
			$this->key = array_slice($payload, 0x04, 16);
			
			$data['id']=$this->id;
			$data['key']=$this->key;
			$data['time']=time();
			
			return $data;
		} else {
			$this->id = $id_authorized;
			$this->key = $key_authorized;
		}
    }


}

class SP1 extends Broadlink{

    function __construct($h = "", $m = "", $p = 80, $d = 0x2712) {

         parent::__construct($h, $m, $p, $d);

    }

    public function Set_Power($state){

        $packet = self::bytearray(4);
        $packet[0] = $state;

        $this->send_packet(0x66, $packet);
    }   

}

class SP2 extends Broadlink{

    function __construct($h = "", $m = "", $p = 80, $d = 0x2712) {

         parent::__construct($h, $m, $p, $d);

    }

    public function Set_Power($state){

        $packet = self::bytearray(16);
        $packet[0] = 0x02;
        $packet[4] = (int)$state;

        $this->send_packet(0x6a, $packet);
    }

    public function Check_Power(){

        $packet = self::bytearray(16);
        $packet[0] = 0x01;

        $response = $this->send_packet(0x6a, $packet);
        $err = hexdec(sprintf("%x%x", $response[0x23], $response[0x22]));
        

        if($err == 0){
            $enc_payload = array_slice($response, 0x38);

            if(count($enc_payload) > 0){

                $payload = $this->byte2array(aes128_cbc_decrypt($this->key(), $this->byte($enc_payload), $this->iv()));
				if ($payload[0x4] & 0x01) $data['power_state'] = 1; else $data['power_state'] = 0;
				if ($payload[0x4] & 0x02) $data['light_state'] = 1; else $data['light_state'] = 0;	//for sp3		
				return $data;
            }

        }

        return false;

        
    }   

}

class A1 extends Broadlink{

    function __construct($h = "", $m = "", $p = 80) {

         parent::__construct($h, $m, $p, 0x2714);

    }

    public function Check_sensors(){

        $data = array();

        $packet = self::bytearray(16);
        $packet[0] = 0x01;

        $response = $this->send_packet(0x6a, $packet);
        $err = hexdec(sprintf("%x%x", $response[0x23], $response[0x22]));
        

        if($err == 0){
            $enc_payload = array_slice($response, 0x38);

            if(count($enc_payload) > 0){

                $payload = $this->byte2array(aes128_cbc_decrypt($this->key(), $this->byte($enc_payload), $this->iv()));

                $data['temperature'] = ($payload[0x4] * 10 + $payload[0x5]) / 10.0;
                $data['humidity'] = ($payload[0x6] * 10 + $payload[0x7]) / 10.0;
                $data['light'] = $payload[0x8];
                $data['air_quality'] = $payload[0x0a];
                $data['noise'] = $payload[0x0c];

                switch ($data['light']) {
                    case 0:
                        $data['light_word'] = constant('LANG_BR_DARK');
                        break;
                    case 1:
                        $data['light_word'] = constant('LANG_BR_DIM');
                        break;                        
                    case 2:
                        $data['light_word'] = constant('LANG_BR_NORMAL');
                        break;
                    case 3:
                        $data['light_word'] = constant('LANG_BR_BRIGHT');
                        break;
                    default:
                        $data['light_word'] = constant('LANG_BR_UNKNOWN');
                        break;
                }

                switch ($data['air_quality']) {
                    case 0:
                        $data['air_quality_word'] = constant('LANG_BR_EXCELLENT');
                        break;
                    case 1:
                        $data['air_quality_word'] = constant('LANG_BR_GOOD');
                        break;                        
                    case 2:
                        $data['air_quality_word'] = constant('LANG_BR_NORMAL');
                        break;
                    case 3:
                        $data['air_quality_word'] = constant('LANG_BR_BAD');
                        break;
                    default:
                        $data['air_quality_word'] = constant('LANG_BR_UNKNOWN');
                        break;
                }

                switch ($data['noise']) {
                    case 0:
                        $data['noise_word'] = constant('LANG_BR_QUIET');
                        break;
                    case 1:
                        $data['noise_word'] = constant('LANG_BR_NORMAL');
                        break;                        
                    case 2:
                        $data['noise_word'] = constant('LANG_BR_NOISY');
                        break;
                    default:
                        $data['noise_word'] = constant('LANG_BR_UNKNOWN');
                        break;
                }

            }

        }

        return $data;
        
    }   

}


class RM extends Broadlink{

	function __construct($h = "", $m = "", $p = 80, $d = 0x2712) {

    	 parent::__construct($h, $m, $p, $d);

    }

    public function Enter_learning(){

    	$packet = self::bytearray(16);
    	$packet[0] = 0x03;
    	$this->send_packet(0x6a, $packet);

	}

    public function Send_data($data){

    	$packet = self::bytearray(4);
    	$packet[0] = 0x02;

    	if(is_array($data)){
    		$packet = array_merge($packet, $data);
    	}
    	else{
    		for($i = 0 ; $i < strlen($data) ; $i+=2){
    			array_push($packet, hexdec(substr($data, $i, 2)));
    		}
    	}

    	$this->send_packet(0x6a, $packet);
    }	

	public function Check_data(){

		$code = array();

		$packet = self::bytearray(16);
  
    	$packet[0] = 0x04;
    	$response = $this->send_packet(0x6a, $packet);
    	$err = hexdec(sprintf("%x%x", $response[0x23], $response[0x22]));
    	

    	if($err == 0){
	   		$enc_payload = array_slice($response, 0x38);

	   		if(count($enc_payload) > 0){

	    		$payload = $this->byte2array(aes128_cbc_decrypt($this->key(), $this->byte($enc_payload), $this->iv()));
		    	
				$code = array_slice($payload, 0x04);
    		}
    	}

    	return $code;
	}

	public function Check_temperature(){

    	$temp = 0;

    	$packet = $this->bytearray(16);

	    $packet[0] = 0x01;
    	$response = $this->send_packet(0x6a, $packet);
    	$err = hexdec(sprintf("%x%x", $response[0x23], $response[0x22]));

    	if($err == 0){
	   		$enc_payload = array_slice($response, 0x38);

	   		if(count($enc_payload) > 0){

	    		$payload = $this->byte2array(aes128_cbc_decrypt($this->key(), $this->byte($enc_payload), $this->iv()));
		    	
				$temp = ($payload[0x4] * 10 + $payload[0x5]) / 10.0;

    		}
    	}
      
      	return $temp;

    }

}

class MP1 extends Broadlink{

    function __construct($h = "", $m = "", $p = 80, $d = 0x4EB5) {

         parent::__construct($h, $m, $p, $d);

    }

    public function Set_Power_Mask($sid_mask, $state){

        $packet = self::bytearray(16);
        $packet[0x00] = 0x0d;
        $packet[0x02] = 0xa5;
        $packet[0x03] = 0xa5;
        $packet[0x04] = 0x5a;
        $packet[0x05] = 0x5a;
        $packet[0x06] = 0xb2 + ($state ? ($sid_mask<<1) : $sid_mask);
        $packet[0x07] = 0xc0;
        $packet[0x08] = 0x02;
        $packet[0x0a] = 0x03;
        $packet[0x0d] = $sid_mask;
        $packet[0x0e] = $state ? $sid_mask : 0;

        $this->send_packet(0x6a, $packet);
    }

    public function Set_Power($sid, $state){

        $sid_mask = 0x01 << ($sid - 1);

        $this->Set_Power_Mask($sid_mask, $state);
    }

    public function Check_Power_Raw(){

        $packet = self::bytearray(16);
        $packet[0x00] = 0x0a;
        $packet[0x02] = 0xa5;
        $packet[0x03] = 0xa5;
        $packet[0x04] = 0x5a;
        $packet[0x05] = 0x5a;
        $packet[0x06] = 0xae;
        $packet[0x07] = 0xc0;
        $packet[0x08] = 0x01;
		
        $response = $this->send_packet(0x6a, $packet);
        $err = hexdec(sprintf("%x%x", $response[0x23], $response[0x22]));
        

        if($err == 0){
            $enc_payload = array_slice($response, 0x38);

            if(count($enc_payload) > 0){

                $payload = $this->byte2array(aes128_cbc_decrypt($this->key(), $this->byte($enc_payload), $this->iv()));
                return $payload[0x0e];    
            }

        }

        return false;

        
    }

    public function Check_Power(){

        $data = array();

        if(!is_null($state = $this->Check_Power_Raw())){
			if ($state & 0x01) $data[0] = 1; else $data[0] = 0;
			if ($state & 0x02) $data[1] = 1; else $data[1] = 0;
			if ($state & 0x04) $data[2] = 1; else $data[2] = 0;
			if ($state & 0x08) $data[3] = 1; else $data[3] = 0; 
        }
        return $data;

    }  

}

class MS1 extends Broadlink{

    function __construct($h = "", $m = "", $p = 80, $d = 0x271F) {

         parent::__construct($h, $m, $p, $d);

    } 
	public function Check_Power(){

        $packet = self::bytearray(16);
        $packet[0] = 0x01;

        $response = $this->send_packet(0x6a, $packet);
        $err = hexdec(sprintf("%x%x", $response[0x23], $response[0x22]));
        

        if($err == 0){
            $enc_payload = array_slice($response, 0x38);

            if(count($enc_payload) > 0){

                $payload = $this->byte2array(aes128_cbc_decrypt($this->key(), $this->byte($enc_payload), $this->iv()));

//				foreach($payload as $val) {
//					file_put_contents ('test_payload', $val.PHP_EOL ,FILE_APPEND);
//				}
				return $data;
            }

        }

        return false;

        
    }  

}

class S1 extends Broadlink{

    function __construct($h = "", $m = "", $p = 80, $d = 0x2722) {

         parent::__construct($h, $m, $p, $d);

    }

	protected function sensors($payload){

		$data = array();
		
		$data['col_sensors'] = $payload[0x04];
		for ($i=0;$i<$data['col_sensors'];$i++) {
			$offset = 0x05+$i*0x53;
			$status = $payload[$offset+0x00]*256+$payload[$offset+0x01];
			$data[$i]['sensor_number'] = $payload[$offset+0x02];
			$data[$i]['product_id'] = $payload[$offset+0x04];
			$data[$i]['photo'] = 'http://jp-clouddb.ibroadlink.com/sensor/picture/'.$data[$i]['product_id'].'.png';
			$data[$i]['location'] = '';
			switch ($data[$i]['product_id']) {
				case 0x21:
					$data[$i]['product_type'] = 'Wall Motion Sensor';
					switch ($status) {
						case 0x0000:
							$data[$i]['status'] = 0;	// in last 30 sec
							$data[$i]['status_val'] = constant('LANG_BRS1_NO_PERSON');
							break;
						case 0x0080:
							$data[$i]['status'] = 0;	// in last 6 min
							$data[$i]['status_val'] = constant('LANG_BRS1_NO_PERSON');
							break;
						case 0x0010:
							$data[$i]['status'] = 1;
							$data[$i]['status_val'] = constant('LANG_BRS1_PERSON_DETECTED');
							break;
						default:
							$data[$i]['status'] = constant('LANG_BRS1_UNKNOWN').$status;
					}
					break;
				case 0x31:
					$data[$i]['product_type'] = 'Door Sensor';
					switch ($status) {
						case 0x0000:
						case 0x9501:
						case 0x0080:
							$data[$i]['status'] = 0;
							$data[$i]['status_val'] = constant('LANG_BRS1_CLOSED');
							break;
						case 0x9581:
							$data[$i]['status'] = 0;
							$data[$i]['status_val'] = constant('LANG_BRS1_CLOSED_NOW');
							break;
						case 0x0010:
						case 0x0090:
						case 0x9591:
							$data[$i]['status'] = 1;
							$data[$i]['status_val'] = constant('LANG_BRS1_OPENED');
							break;
						default:
							$data[$i]['status'] = constant('LANG_BRS1_UNKNOWN').$status;
					}
					switch ($payload[$offset+0x26]) {
						case 0x00:
							$data[$i]['location'] = 'Drawer';
							break;
						case 0x01:
							$data[$i]['location'] = 'Door';
							break;
						case 0x02:
							$data[$i]['location'] = 'Window';
							break;
						default:
							$data[$i]['location'] = 'Unknown: '.$payload[$offset+0x2c];
					}
					break;
				case 0x91:
					$data[$i]['product_type'] = 'Key Fob';
					$data[$i]['status']=$status;
					switch ($status) {
						case 0x0000:
							$data[$i]['status_val'] = constant('LANG_BRS1_CANCEL_SOS');
							break;
						case 0x0010:
							$data[$i]['status_val'] = constant('LANG_BRS1_DISARM');
							break;
						case 0x0020:
							$data[$i]['status_val'] = constant('LANG_BRS1_ARMED_FULL');
							break;
						case 0x0040:
							$data[$i]['status_val'] = constant('LANG_BRS1_ARMED_PART');
							break;
						case 0x0080:
							$data[$i]['status_val'] = 'SOS';
							break;
						default:
							$data[$i]['status_val'] = constant('LANG_BRS1_UNKNOWN').$status;
					}
					break;
				// for future:
				case 0x40:
					$data[$i]['product_type'] = 'Gaz Sensor';
					switch ($status) {
						case 0x0000:
						case 0x0010:
						default:
							$data[$i]['status'] = constant('LANG_BRS1_UNKNOWN').$status;
					}
					break;
				case 0x51:
					$data[$i]['product_type'] = 'Fire Sensor';
					switch ($status) {
						case 0x0000:
						case 0x0010:
						default:
							$data[$i]['status'] = constant('LANG_BRS1_UNKNOWN').$status;
					}
					break;
				default:
					$data[$i]['product_type'] = 'Unknown: '.$data[$i]['product_id'];
			}
			$data[$i]['product_name'] = ""; for ($j=$offset+0x05;$j<$offset+0x15;$j++) if (!$payload[$j]) $data[$i]['product_name'] .= chr($payload[$j]);
			$data[$i]['device_id'] = $payload[$offset+0x1e]*16777216+$payload[$offset+0x1d]*65536+$payload[$offset+0x1c]*256+$payload[$offset+0x1b];
			$data[$i]['s1_pwd'] = dechex($payload[$offset+0x22]).dechex($payload[$offset+0x21]).dechex($payload[$offset+0x20]).dechex($payload[$offset+0x1f]);
			
			switch ($payload[$offset+0x23]) {
				case 0x00:
					$data[$i]['armFull'] = false;
					$data[$i]['armPart'] = false;
					break;
				case 0x02:
					$data[$i]['armFull'] = true;
					$data[$i]['armPart'] = false;
					break;
				case 0x03:
					$data[$i]['armFull'] = true;
					$data[$i]['armPart'] = true;
					break;
				default:
					$data[$i]['armFull'] = true;
					$data[$i]['armPart'] = false;
			}
			
			switch ($payload[$offset+0x25]) {
				case 0x00:
					$data[$i]['zone'] = 'Not specified';
					break;
				case 0x01:
					$data[$i]['zone'] = 'Living room';
					break;
				case 0x02:
					$data[$i]['zone'] = 'Main bedroom';
					break;
				case 0x03:
					$data[$i]['zone'] = 'Secondary room 1';
					break;
				case 0x04:
					$data[$i]['zone'] = 'Secondary room 2';
					break;
				case 0x05:
					$data[$i]['zone'] = 'Kitchen';
					break;
				case 0x06:
					$data[$i]['zone'] = 'Bathroom';
					break;
				case 0x07:
					$data[$i]['zone'] = 'Veranda';
					break;
				case 0x08:
					$data[$i]['zone'] = 'Garage';
					break;
				default:
					$data[$i]['zone'] = constant('LANG_BRS1_UNKNOWN').$payload[$offset+0x2b];
			}

			$data[$i]['delay_online'] 			= $payload[$offset+0x39]*256+$payload[$offset+0x38];
			$data[$i]['delay_battery'] 			= $payload[$offset+0x41]*256+$payload[$offset+0x40];
			$data[$i]['delay_tamper_switch'] 	= $payload[$offset+0x49]*256+$payload[$offset+0x48];
			$data[$i]['delay_detect'] 			= $payload[$offset+0x50]*256+$payload[$offset+0x4f];
		}
		return $data;
	}
	
	public function Check_Sensors(){
	
		$data = array();
		
		$packet = self::bytearray(16);
		$packet[0] = 0x06;
		
		$response = $this->send_packet(0x6a, $packet);
		$err = hexdec(sprintf("%x%x", $response[0x23], $response[0x22]));
		
		if($err == 0){
			$enc_payload = array_slice($response, 0x38);
			if(count($enc_payload) > 0){
				$payload = $this->byte2array(aes128_cbc_decrypt($this->key(), $this->byte($enc_payload), $this->iv()));
				$data = $this->sensors($payload);
			}
		}
		return $data;
	}
	
	public function Check_Status(){
	
		$data = array();
		
		$packet = self::bytearray(16);
		$packet[0] = 0x12;
		
		$response = $this->send_packet(0x6a, $packet);
		$err = hexdec(sprintf("%x%x", $response[0x23], $response[0x22]));
		
		if($err == 0){
			$enc_payload = array_slice($response, 0x38);
			if(count($enc_payload) > 0){
				$payload = $this->byte2array(aes128_cbc_decrypt($this->key(), $this->byte($enc_payload), $this->iv()));
				$data['status'] = $payload[0x04];
				$data['delay_time_m'] = $packet[0x08];
				$data['delay_time_s'] = $packet[0x09];
				$data['alarm_buzzing'] = $packet[0x0a];
				$data['alarm_buzzing_duration'] = $packet[0x0b];
				$data['beep_mute'] = $packet[0x0d];
				$data['alarm_detector'] = $packet[0x28];
				switch ($data['status']) {
					case 0x00:
						$data['status_val'] = constant('LANG_BRS1_DISARM');
						break;
					case 0x01:
						$data['status_val'] = constant('LANG_BRS1_PART');
						break;
					case 0x02:
						$data['status_val'] = constant('LANG_BRS1_FULL');
						break;
					default:
						$data['status'] = constant('LANG_BRS1_UNKNOWN').$data['status'];
				}
			}
		}
		return $data;
	}
	
	public function Set_Arm($params){
	
		$data = array();
		
		$packet = self::bytearray(48);
		
		$packet[0x00] = 0x11;
		$packet[0x04] = $params['status']; //2 - full, 1 - part, 0 - disarm
		$packet[0x08] = $params['delay_time_m'];
		$packet[0x09] = $params['delay_time_s'];
		$packet[0x0a] = $params['alarm_buzzing'];
		$packet[0x0b] = $params['alarm_buzzing_duration'];
		$packet[0x0d] = $params['beep_mute'];
		$packet[0x28] = $params['alarm_detector'];
		
		$response = $this->send_packet(0x6a, $packet);
		$err = hexdec(sprintf("%x%x", $response[0x23], $response[0x22]));
		
		if($err == 0){
			$enc_payload = array_slice($response, 0x38);
			if(count($enc_payload) > 0){
				$payload = $this->byte2array(aes128_cbc_decrypt($this->key(), $this->byte($enc_payload), $this->iv()));
				$data['status'] = $payload[0x04];
				$data['delay_time_m'] = $packet[0x08];
				$data['delay_time_s'] = $packet[0x09];
				$data['alarm_buzzing'] = $packet[0x0a];
				$data['alarm_buzzing_duration'] = $packet[0x0b];
				$data['beep_mute'] = $packet[0x0d];
				$data['alarm_detector'] = $packet[0x28];
				switch ($data['status']) {
					case 0x00:
						$data['status_val'] = constant('LANG_BRS1_DISARM');;
						break;
					case 0x01:
						$data['status_val'] = constant('LANG_BRS1_PART');
						break;
					case 0x02:
						$data['status_val'] = constant('LANG_BRS1_FULL');
						break;
					default:
						$data['status'] = constant('LANG_BRS1_UNKNOWN').$data['status'];
				}
			}
		}
		return $data;
	}
	
	public function Add_Sensor($serialnumb){
		
		$data = array();
		
		$serial[0] = mb_strtoupper($serialnumb[0].$serialnumb[1], "UTF-8");
		if ($serial[0] != 'BL') {
			return false;
		}
		for ($i=2; $i < strlen($serialnumb)-1; $i+=2){
			$serial[$i/2] = hexdec($serialnumb[$i].$serialnumb[$i+1]);
		}
		
		$packet = self::bytearray(96);
		$packet[0x00] = 0x07;
		$packet[0x05] = $serial[2];
		$packet[0x06] = $serial[3];
		switch ($serial[3]) {
			case 0x21:	//http://jp-clouddb.ibroadlink.com/sensor/picture/33.png /35.png and /36.png
				$packet[0x07] = ord('W');
				$packet[0x08] = ord('a');
				$packet[0x09] = ord('l');
				$packet[0x0A] = ord('l');
				$packet[0x0B] = ord(' ');
				$packet[0x0C] = ord('M');
				$packet[0x0D] = ord('o');
				$packet[0x0E] = ord('t');
				$packet[0x0F] = ord('i');
				$packet[0x10] = ord('o');
				$packet[0x11] = ord('n');
				$packet[0x12] = ord(' ');
				$packet[0x13] = ord('S');
				$packet[0x14] = ord('e');
				$packet[0x15] = ord('n');
				$packet[0x16] = ord('s');
				$packet[0x17] = ord('o');
				$packet[0x18] = ord('r');
				// ..0x1C - zeros
				break;
			case 0x31:	//http://jp-clouddb.ibroadlink.com/sensor/picture/49.png
				$packet[0x07] = ord('D');
				$packet[0x08] = ord('o');
				$packet[0x09] = ord('o');
				$packet[0x0A] = ord('r');
				$packet[0x0B] = ord(' ');
				$packet[0x0C] = ord('S');
				$packet[0x0D] = ord('e');
				$packet[0x0E] = ord('n');
				$packet[0x0F] = ord('s');
				$packet[0x10] = ord('o');
				$packet[0x11] = ord('r');
				// ..0x1C - zeros
				break;
			case 0x40:	//http://jp-clouddb.ibroadlink.com/sensor/picture/64.png
				$packet[0x07] = ord('G');
				$packet[0x08] = ord('a');
				$packet[0x09] = ord('z');
				$packet[0x0A] = ord(' ');
				$packet[0x0B] = ord('S');
				$packet[0x0C] = ord('e');
				$packet[0x0D] = ord('n');
				$packet[0x0E] = ord('s');
				$packet[0x0F] = ord('o');
				$packet[0x10] = ord('r');
				// ..0x1C - zeros
				break;
			case 0x51:	//http://jp-clouddb.ibroadlink.com/sensor/picture/81.png
				$packet[0x07] = ord('F');
				$packet[0x08] = ord('i');
				$packet[0x09] = ord('r');
				$packet[0x0A] = ord('e');
				$packet[0x0B] = ord(' ');
				$packet[0x0C] = ord('S');
				$packet[0x0D] = ord('e');
				$packet[0x0E] = ord('n');
				$packet[0x0F] = ord('s');
				$packet[0x10] = ord('o');
				$packet[0x11] = ord('r');
				// ..0x1C - zeros
				break;
			case 0x91:	//http://jp-clouddb.ibroadlink.com/sensor/picture/145.png
				$packet[0x07] = ord('K');
				$packet[0x08] = ord('e');
				$packet[0x09] = ord('y');
				$packet[0x0A] = ord(' ');
				$packet[0x0B] = ord('F');
				$packet[0x0C] = ord('o');
				$packet[0x0D] = ord('b');
				// ..0x1C - zeros
				break;
			default:	//http://jp-clouddb.ibroadlink.com/sensor/picture/224.png /239.png
				$packet[0x07] = ord('U');
				$packet[0x08] = ord('n');
				$packet[0x09] = ord('k');
				$packet[0x0A] = ord('n');
				$packet[0x0B] = ord('o');
				$packet[0x0C] = ord('w');
				$packet[0x0D] = ord('n');
				// ..0x1C - zeros
		}
		$packet[0x1D] = $serial[4];
		$packet[0x1E] = $serial[5];
		$packet[0x1F] = $serial[6];
		$packet[0x20] = $serial[7];
		switch ($serial[3]) {
			case 0x21:	//s1_pwd = 0x774eecd6
				$packet[0x21] = 0xd6;
				$packet[0x22] = 0xec;
				$packet[0x23] = 0x4e;
				$packet[0x24] = 0x77;
				break;
			case 0x31:	//s1_pwd = 0x95a1faf1
				$packet[0x21] = 0xf1;
				$packet[0x22] = 0xfa;
				$packet[0x23] = 0xa1;
				$packet[0x24] = 0x95;
				break;
			case 0x91:	//s1_pwd = 0x5d6f7647
				$packet[0x21] = 0x47;
				$packet[0x22] = 0x76;
				$packet[0x23] = 0x6f;
				$packet[0x24] = 0x5d;
				break;
		}
		$packet[0x25] = 0x02;	//0x00 = Full-arm disabled, Part-arm disabled
								//0x02 = Full-arm enabled, Part-arm disabled
								//0x03 = Full-arm enabled, Part-arm enabled
		//$packet[0x26] = 0x00;
		if (($serial[3] == 0x21)||($serial[3] == 0x31)) {
			$packet[0x27] = 0x00;	//0x00 = Not specified
									//0x01 = Living room
									//0x02 = Main bedroom
									//0x03 = Secondary room 1
									//0x04 = Secondary room 2
									//0x05 = Kitchen
									//0x06 = Bathroom
									//0x07 = Veranda
									//0x08 = Garage
		}
		if ($serial[3] == 0x31) {
			$packet[0x28] = 0x00;	//0x00 = Drawer (for "Door Sensor")
									//0x01 = Door
									//0x02 = Window
		}
		//0x29..0x34:	zeros
		//Online Status
		$packet[0x35] = 0x1f;
		if ($serial[3] == 0x91) {
			$packet[0x36] = 0x01;
			//$packet[0x37] = 0x00;
			//$packet[0x38] = 0x00;
			$packet[0x39] = 0x0a;
		}
		if ($serial[3] == 0x31) {
			$packet[0x3a] = 0x2d;	//Delay time 45 sec (0x00 0x2D)
			$packet[0x3b] = 0x00;	//Delay time 45 sec (0x00 0x2D)
		}
		//$packet[0x3c] = 0x00;
		
		//Battery
		$packet[0x3d] = 0x1e;
		if (($serial[3] == 0x21)||($serial[3] == 0x31)) {
			$packet[0x3e] = 0x08;
		}
		//$packet[0x3f] = 0x00;
		//$packet[0x40] = 0x00;
		//$packet[0x41] = 0x00;
		$packet[0x42] = 0x00;	//Delay time 0 sec (0x00 0x00)
		$packet[0x43] = 0x00;	//Delay time 0 sec (0x00 0x00)
		//$packet[0x44] = 0x00;
		
		//Tamper Switch
		$packet[0x45] = 0x1d;
		if (($serial[3] == 0x21)||($serial[3] == 0x31)) {
			$packet[0x46] = 0x08;
		}
		//$packet[0x47] = 0x00;
		//$packet[0x48] = 0x00;
		//$packet[0x49] = 0x00;
		$packet[0x4a] = 0x00;	//Delay time 0 sec (0x00 0x00)
		$packet[0x4b] = 0x00;	//Delay time 0 sec (0x00 0x00)
		//$packet[0x4c] = 0x00;
		
		//Detected Status
		$packet[0x4d] = 0x1c;
		if (($serial[3] == 0x21)||($serial[3] == 0x31)) {
			$packet[0x4e] = 0x0b;
		}
		//$packet[0x4f] = 0x00;
		//$packet[0x50] = 0x00;
		//$packet[0x51] = 0x00;
		if ($serial[3] == 0x21) {
			$packet[0x52] = 0x68;	//Delay time 6 min (0x01 0x68)
			$packet[0x53] = 0x01;	//Delay time 6 min (0x01 0x68)
		}
		//0x54..0x5f:	zeros
		
		$response = $this->send_packet(0x6a, $packet);
		$err = hexdec(sprintf("%x%x", $response[0x23], $response[0x22]));
		
		if($err == 0){
			$enc_payload = array_slice($response, 0x38);
			if(count($enc_payload) > 0){
				$payload = $this->byte2array(aes128_cbc_decrypt($this->key(), $this->byte($enc_payload), $this->iv()));
				$data = $this->sensors($payload);
			}
		}
		return $data;
	}


}

?>
