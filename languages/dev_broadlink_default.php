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
);

foreach ($dictionary as $k=>$v) {
 if (!defined('LANG_'.$k)) {
  define('LANG_'.$k, $v);
 }
}
