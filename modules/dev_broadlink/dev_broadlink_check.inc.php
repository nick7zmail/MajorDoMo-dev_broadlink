<?php
	if (SETTINGS_SITE_LANGUAGE && file_exists(ROOT . 'languages/' . $this->name. '_' .SETTINGS_SITE_LANGUAGE . '.php')) {
		include_once (ROOT . 'languages/' . $this->name. '_' .SETTINGS_SITE_LANGUAGE . '.php');
	} else {
		include_once (ROOT . 'languages/'. $this->name. '_default.php');
	}
	$this->getConfig();
	$db_rec=SQLSelect("SELECT * FROM dev_httpbrige_devices");
	if ($this->config['API']=='httpbrige') {
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
		foreach ($db_rec as $key => $rec) {
			$response = '';
			$properties = '';
			$rm = Broadlink::CreateDevice($rec['IP'], $rec['MAC'], 80, $rec['DEVTYPE']);
			if(!is_null($rm)) {
				$rm->Auth();
				$table='dev_broadlink_commands';
				$id=$rec['ID'];
				if ($rec['TYPE']=='rm') {
						$response = $rm->Check_temperature();
						if(isset($response) && $response!='') {
							$properties=SQLSelectOne("SELECT * FROM $table WHERE TITLE='temperature' AND DEVICE_ID='$id'");
							$total=count($properties);
							if ($total) {
								$properties['VALUE']=(float)$response;
								SQLUpdate($table, $properties);
								if(isset($properties['LINKED_OBJECT']) && $properties['LINKED_OBJECT']!='' && isset($properties['LINKED_PROPERTY']) && $properties['LINKED_PROPERTY']!='') {
									sg($properties['LINKED_OBJECT'].'.'.$properties['LINKED_PROPERTY'], (float)$response);
								}
							} else {
								$properties['VALUE']=(float)$response;
								$properties['DEVICE_ID']=$rec['ID'];
								$properties['TITLE']='temperature';
								SQLInsert($table, $properties);
							}
						}
				}
				if ($rec['TYPE']=='rm3') {
				}
				if ($rec['TYPE']=='a1') {
						$response = $rm->Check_sensors();
						if(isset($response) && $response!='') {
							$properties=SQLSelectOne("SELECT * FROM $table WHERE TITLE='temperature' AND DEVICE_ID='$id'");
							$total=count($properties);
							if ($total) {
								$properties['VALUE']=(float)$response['temperature'];
								SQLUpdate($table, $properties);
								if(isset($properties['LINKED_OBJECT']) && $properties['LINKED_OBJECT']!='' && isset($properties['LINKED_PROPERTY']) && $properties['LINKED_PROPERTY']!='') {
									sg($properties['LINKED_OBJECT'].'.'.$properties['LINKED_PROPERTY'], (float)$response['temperature']);
								}
							} else {
								$properties['VALUE']=(float)$response['temperature'];
								$properties['DEVICE_ID']=$rec['ID'];
								$properties['TITLE']='temperature';
								SQLInsert($table, $properties);								
							}
							$properties=SQLSelectOne("SELECT * FROM $table WHERE TITLE='humidity' AND DEVICE_ID='$id'");
							$total=count($properties);						
							if ($total) {
								$properties['VALUE']=(float)$response['humidity'];
								SQLUpdate($table, $properties);
								if(isset($properties['LINKED_OBJECT']) && $properties['LINKED_OBJECT']!='' && isset($properties['LINKED_PROPERTY']) && $properties['LINKED_PROPERTY']!='') {
									sg($properties['LINKED_OBJECT'].'.'.$properties['LINKED_PROPERTY'], (float)$response['humidity']);
								}
							} else {
								$properties['VALUE']=(float)$response['humidity'];
								$properties['DEVICE_ID']=$rec['ID'];
								$properties['TITLE']='humidity';
								SQLInsert($table, $properties);								
							}							
							$properties=SQLSelectOne("SELECT * FROM $table WHERE TITLE='noise' AND DEVICE_ID='$id'");
							$total=count($properties);						
							if ($total) {
								$properties['VALUE']=(int)$response['noise'];
								SQLUpdate($table, $properties);
								if(isset($properties['LINKED_OBJECT']) && $properties['LINKED_OBJECT']!='' && isset($properties['LINKED_PROPERTY']) && $properties['LINKED_PROPERTY']!='') {
									sg($properties['LINKED_OBJECT'].'.'.$properties['LINKED_PROPERTY'], (int)$response['noise']);
								}
							} else {
								$properties['VALUE']=(int)$response['noise'];
								$properties['DEVICE_ID']=$rec['ID'];
								$properties['TITLE']='noise';
								SQLInsert($table, $properties);								
							}
							$properties=SQLSelectOne("SELECT * FROM $table WHERE TITLE='light' AND DEVICE_ID='$id'");
							$total=count($properties);						
							if ($total) {
								$properties['VALUE']=(int)$response['light'];
								SQLUpdate($table, $properties);
								if(isset($properties['LINKED_OBJECT']) && $properties['LINKED_OBJECT']!='' && isset($properties['LINKED_PROPERTY']) && $properties['LINKED_PROPERTY']!='') {
									sg($properties['LINKED_OBJECT'].'.'.$properties['LINKED_PROPERTY'], (int)$response['light']);
								}
							} else {
								$properties['VALUE']=(int)$response['light'];
								$properties['DEVICE_ID']=$rec['ID'];
								$properties['TITLE']='light';
								SQLInsert($table, $properties);								
							}
							$properties=SQLSelectOne("SELECT * FROM $table WHERE TITLE='air_quality' AND DEVICE_ID='$id'");
							$total=count($properties);						
							if ($total) {
								$properties['VALUE']=(int)$response['air_quality'];
								SQLUpdate($table, $properties);
								if(isset($properties['LINKED_OBJECT']) && $properties['LINKED_OBJECT']!='' && isset($properties['LINKED_PROPERTY']) && $properties['LINKED_PROPERTY']!='') {
									sg($properties['LINKED_OBJECT'].'.'.$properties['LINKED_PROPERTY'], (int)$response['air_quality']);
								}
							} else {
								$properties['VALUE']=(int)$response['air_quality'];
								$properties['DEVICE_ID']=$rec['ID'];
								$properties['TITLE']='air_quality';
								SQLInsert($table, $properties);								
							}
							$properties=SQLSelectOne("SELECT * FROM $table WHERE TITLE='light_word' AND DEVICE_ID='$id'");
							$total=count($properties);						
							if ($total) {
								$properties['VALUE']=$response['light_word'];
								SQLUpdate($table, $properties);
								if(isset($properties['LINKED_OBJECT']) && $properties['LINKED_OBJECT']!='' && isset($properties['LINKED_PROPERTY']) && $properties['LINKED_PROPERTY']!='') {
									sg($properties['LINKED_OBJECT'].'.'.$properties['LINKED_PROPERTY'], $response['light_word']);
								}
							} else {
								$properties['VALUE']=$response['light_word'];
								$properties['DEVICE_ID']=$rec['ID'];
								$properties['TITLE']='light_word';
								SQLInsert($table, $properties);								
							}
							$properties=SQLSelectOne("SELECT * FROM $table WHERE TITLE='noise_word' AND DEVICE_ID='$id'");
							$total=count($properties);						
							if ($total) {
								$properties['VALUE']=$response['noise_word'];
								SQLUpdate($table, $properties);
								if(isset($properties['LINKED_OBJECT']) && $properties['LINKED_OBJECT']!='' && isset($properties['LINKED_PROPERTY']) && $properties['LINKED_PROPERTY']!='') {
									sg($properties['LINKED_OBJECT'].'.'.$properties['LINKED_PROPERTY'], $response['noise_word']);
								}
							} else {
								$properties['VALUE']=$response['noise_word'];
								$properties['DEVICE_ID']=$rec['ID'];
								$properties['TITLE']='noise_word';
								SQLInsert($table, $properties);								
							}
							$properties=SQLSelectOne("SELECT * FROM $table WHERE TITLE='air_quality_word' AND DEVICE_ID='$id'");
							$total=count($properties);						
							if ($total) {
								$properties['VALUE']=$response['air_quality_word'];
								SQLUpdate($table, $properties);
								if(isset($properties['LINKED_OBJECT']) && $properties['LINKED_OBJECT']!='' && isset($properties['LINKED_PROPERTY']) && $properties['LINKED_PROPERTY']!='') {
									sg($properties['LINKED_OBJECT'].'.'.$properties['LINKED_PROPERTY'], $response['air_quality_word']);
								}
							} else {
								$properties['VALUE']=$response['air_quality_word'];
								$properties['DEVICE_ID']=$rec['ID'];
								$properties['TITLE']='air_quality_word';
								SQLInsert($table, $properties);								
							}							
						}
				}
				if ($rec['TYPE']=='sp2' || $rec['TYPE'] == 'spmini' || $rec['TYPE'] == 'sp3') {
					$response = $rm->Check_Power();	
						if(isset($response)) {
							$properties=SQLSelectOne("SELECT * FROM $table WHERE TITLE='status' AND DEVICE_ID='$id'");
							$total=count($properties);						
							if ($total) {
								$properties['VALUE']=(int)$response;
								SQLUpdate($table, $properties);
								if(isset($properties['LINKED_OBJECT']) && $properties['LINKED_OBJECT']!='' && isset($properties['LINKED_PROPERTY']) && $properties['LINKED_PROPERTY']!='') {
									sg($properties['LINKED_OBJECT'].'.'.$properties['LINKED_PROPERTY'], (int)$response);
								}
							} else {
								$properties['VALUE']=(int)$response;
								$properties['DEVICE_ID']=$rec['ID'];
								$properties['TITLE']='status';
								SQLInsert($table, $properties);								
							}
						}
				}
				if ($rec['TYPE']=='mp1') {
					$response = $rm->Check_Power();	
						if(isset($response) && $response!='') {
							for($i=0;$i<4;$i++) {
								$properties=SQLSelectOne("SELECT * FROM $table WHERE TITLE='status".($i+1)."' AND DEVICE_ID='$id'");
								$total=count($properties);
								if ($total) {
									$properties['VALUE']=(int)$response[$i];
									SQLUpdate($table, $properties);
									if(isset($properties['LINKED_OBJECT']) && $properties['LINKED_OBJECT']!='' && isset($properties['LINKED_PROPERTY']) && $properties['LINKED_PROPERTY']!='') {
										sg($properties['LINKED_OBJECT'].'.'.$properties['LINKED_PROPERTY'], (int)$response[$i]);
									}
								} else {
									$properties['VALUE']=(int)$response[$i];
									$properties['DEVICE_ID']=$rec['ID'];
									$properties['TITLE']='status'.($i+1);
									SQLInsert($table, $properties);								
								}
							}
						}
				}
				if(isset($response) && $response!='') {
					$rec['UPDATED']=date('Y-m-d H:i:s');
					SQLUpdate('dev_httpbrige_devices', $rec);
				}
			} else {
				DebMes('Device '.$rec['TITLE'].' is not available');
			}
		}
	}
?>