<?php


$dictionary=array(

/* end module names */
//states
'BR_DARK'=>'темно',
'BR_DIM'=>'тускло',
'BR_NORMAL'=>'норма',
'BR_BRIGHT'=>'ярко',
'BR_UNKNOWN'=>'неизвестно',
'BR_EXCELLENT'=>'превосходно',
'BR_GOOD'=>'хорошо',
'BR_BAD'=>'плохо',
'BR_QUIET'=>'тихо',
'BR_NOISY'=>'шумно'

//rm_bridge
'BR_STEP1'=>'Шаг 1: Укажите адрес RM-brige',
'BR_STEP1_DESC'=>'Введите ip-адрес, указанный в приложении <i>Android RM Bridge</i> и нажмите кнопку <i><b>Проверить устройства</b></i>.',
);

foreach ($dictionary as $k=>$v) {
 if (!defined('LANG_'.$k)) {
  define('LANG_'.$k, $v);
 }
}
