<?php


$dictionary=array(

/* end module names */
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

);

foreach ($dictionary as $k=>$v) {
 if (!defined('LANG_'.$k)) {
  define('LANG_'.$k, $v);
 }
}
