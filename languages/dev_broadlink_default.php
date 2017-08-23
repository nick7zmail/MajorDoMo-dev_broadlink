<?php


$dictionary=array(

/* end module names */
//states
'BR_DARK'=>'dark',
'BR_DIM'=>'dim',
'BR_NORMAL'=>'normal',
'BR_BRIGHT'=>'bright',
'BR_UNKNOWN'=>'unknown',
'BR_EXCELLENT'=>'excellent',
'BR_GOOD'=>'good',
'BR_BAD'=>'bad',
'BR_QUIET'=>'quiet',
'BR_NOISY'=>'noisy',

//rm_bridge
'BR_STEP1'=>'Шаг 1: Укажите адрес RM-brige',
'BR_STEP1_DESC'=>'Введите ip-адрес, указанный в приложении <i>Android RM Bridge</i> и нажмите кнопку <i><b>Проверить устройства</b></i>.',
//s1
'BRS1_NO_PERSON'=>'No Person',
'BRS1_PERSON_DETECTED'=>'Person Detected',
'BRS1_UNKNOWN'=>'Unknown: ',
'BRS1_CLOSED'=>'Closed',
'BRS1_CLOSED_NOW'=>'Closed now',
'BRS1_OPENED'=>'Opened',
'BRS1_CANCEL_SOS'=>'Cancel SOS',
'BRS1_DISARM'=>'Disarm',
'BRS1_ARMED_FULL'=>'Armed Full',
'BRS1_ARMED_PART'=>'Armed Part',
'BRS1_PART'=>'Part',
'BRS1_FULL'=>'Full',
'BRS1_ZONE'=>'Zone: ',
'BRS1_PART_U'=>'PART',
'BRS1_FULL_U'=>'FULL',
//cloud
'LOGIN'=>'Login',
'GET_LAST'=>'Get the latest backup',
'GET_LIST'=>'Get backup list',
'SIZE'=>'Size',
'DOWNLOAD'=>'Download',
'UNPACKED'=>'Archive is unpacked into the directory',
'CLOUD_FUNC'=>'Cloud functions',
'CLOUD_EXPORT'=>'Export cloud codes',
);

foreach ($dictionary as $k=>$v) {
 if (!defined('LANG_'.$k)) {
  define('LANG_'.$k, $v);
 }
}
