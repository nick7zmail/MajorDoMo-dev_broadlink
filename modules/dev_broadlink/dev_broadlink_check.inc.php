<?php
	if (SETTINGS_SITE_LANGUAGE && file_exists(ROOT . 'languages/' . $this->name. '_' .SETTINGS_SITE_LANGUAGE . '.php')) {
		include_once (ROOT . 'languages/' . $this->name. '_' .SETTINGS_SITE_LANGUAGE . '.php');
	} else {
		include_once (ROOT . 'languages/'. $this->name. '_default.php');
	}
	$this->getConfig();
	if(isset($chtime) && $chtime!='all' && $chtime!='') {
		$db_rec=SQLSelect("SELECT * FROM dev_httpbrige_devices WHERE CHTIME='$chtime'");
	} elseif (isset($chtime) && $chtime!='all') {
		$db_rec=SQLSelect("SELECT * FROM dev_httpbrige_devices");
	} else {
		$db_rec=SQLSelect("SELECT * FROM dev_httpbrige_devices WHERE CHTIME<>'none'");
	}
	if ($this->config['API']=='httpbrige') {
		$db_rec=SQLSelect("SELECT * FROM dev_httpbrige_devices");
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
		foreach ($db_rec as $rec) {
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
									sg($properties['LINKED_OBJECT'].'.'.$properties['LINKED_PROPERTY'], $properties['VALUE']);
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
							foreach ($response as $key => $value) {
								$properties=SQLSelectOne("SELECT * FROM $table WHERE TITLE='$key' AND DEVICE_ID='$id'");
								if ($total) {
									$properties['VALUE']=$value;
									SQLUpdate($table, $properties);
									if(isset($properties['LINKED_OBJECT']) && $properties['LINKED_OBJECT']!='' && isset($properties['LINKED_PROPERTY']) && $properties['LINKED_PROPERTY']!='') {
										sg($properties['LINKED_OBJECT'].'.'.$properties['LINKED_PROPERTY'], $properties['VALUE']);
									}
								} else {
									$properties['VALUE']=$value;
									$properties['DEVICE_ID']=$rec['ID'];
									$properties['TITLE']=$key;
									SQLInsert($table, $properties);								
								}	
							}							
						}
				}
				if ($rec['TYPE']=='sp2' || $rec['TYPE'] == 'spmini' || $rec['TYPE'] == 'sp3') {
					$response = $rm->Check_Power();	
						if(isset($response) && $response!='') {
							$properties=SQLSelectOne("SELECT * FROM $table WHERE TITLE='status' AND DEVICE_ID='$id'");
							$total=count($properties);						
							if ($total) {
								$properties['VALUE']=(int)$response['power_state'];
								SQLUpdate($table, $properties);
								if(isset($properties['LINKED_OBJECT']) && $properties['LINKED_OBJECT']!='' && isset($properties['LINKED_PROPERTY']) && $properties['LINKED_PROPERTY']!='') {
									sg($properties['LINKED_OBJECT'].'.'.$properties['LINKED_PROPERTY'], $properties['VALUE']);
								}
							} else {
								$properties['VALUE']=(int)$response['power_state'];
								$properties['DEVICE_ID']=$rec['ID'];
								$properties['TITLE']='status';
								SQLInsert($table, $properties);								
							}
							if ($rec['TYPE'] == 'sp3') {
								$properties=SQLSelectOne("SELECT * FROM $table WHERE TITLE='lightstatus' AND DEVICE_ID='$id'");
								$total=count($properties);
								if ($total) {
									$properties['VALUE']=(int)$response['light_state'];
									SQLUpdate($table, $properties);
									if(isset($properties['LINKED_OBJECT']) && $properties['LINKED_OBJECT']!='' && isset($properties['LINKED_PROPERTY']) && $properties['LINKED_PROPERTY']!='') {
										sg($properties['LINKED_OBJECT'].'.'.$properties['LINKED_PROPERTY'], $properties['VALUE']);
									}
								} else {
									$properties['VALUE']=(int)$response['light_state'];
									$properties['DEVICE_ID']=$rec['ID'];
									$properties['TITLE']='lightstatus';
									SQLInsert($table, $properties);								
								}
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
										sg($properties['LINKED_OBJECT'].'.'.$properties['LINKED_PROPERTY'], $properties['VALUE']);
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
				if ($rec['TYPE']=='s1') {
					$response = $rm->Check_Sensors();
					if(isset($response) && $response!='') {
						for($i=0;$i<$response['col_sensors'];$i++) {
							$sens_arr=$response[$i];
							$sens_name='['.$sens_arr['sensor_number'].'] '.$sens_arr['product_type'];
							$properties=SQLSelectOne("SELECT * FROM $table WHERE TITLE='$sens_name' AND DEVICE_ID='$id'");
							if ($total) {
								$properties['VALUE']=json_encode($sens_arr);
								SQLUpdate($table, $properties);
								if(isset($properties['LINKED_OBJECT']) && $properties['LINKED_OBJECT']!='' && isset($properties['LINKED_PROPERTY']) && $properties['LINKED_PROPERTY']!='') {
									sg($properties['LINKED_OBJECT'].'.'.$properties['LINKED_PROPERTY'], $sens_arr['status']);
								}
							} else {
								$properties['VALUE']=json_encode($sens_arr);
								$properties['DEVICE_ID']=$rec['ID'];
								$properties['TITLE']=$sens_name;
								SQLInsert($table, $properties);								
							}
						}
					}
					$response = $rm->Check_Status();
					if(isset($response) && $response!='') {
							$properties=SQLSelectOne("SELECT * FROM $table WHERE TITLE='status' AND DEVICE_ID='$id'");
							if ($total) {
								$properties['VALUE']=$response['status'];
								SQLUpdate($table, $properties);
								if(isset($properties['LINKED_OBJECT']) && $properties['LINKED_OBJECT']!='' && isset($properties['LINKED_PROPERTY']) && $properties['LINKED_PROPERTY']!='') {
									sg($properties['LINKED_OBJECT'].'.'.$properties['LINKED_PROPERTY'], $response['status']);
								}
							} else {
								$properties['VALUE']=$response['status'];
								$properties['DEVICE_ID']=$rec['ID'];
								$properties['TITLE']='status';
								SQLInsert($table, $properties);								
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
