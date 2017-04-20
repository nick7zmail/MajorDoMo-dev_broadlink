<?php


$dictionary=array(

/* end module names */
'BR_DARK'=>'dark',
'BR_DIM'=>'dim',
'BR_NORMAL'=>'normal',
'BR_BRIGHT'=>'bright',
'BR_UNKNOWN'=>'unknown',
'BR_EXCELLENT'=>'excellent',
'BR_GOOD'=>'good',
'BR_BAD'=>'bad',
'BR_QUIET'=>'quiet',
'BR_NOISY'=>'noisy'

);

foreach ($dictionary as $k=>$v) {
 if (!defined('LANG_'.$k)) {
  define('LANG_'.$k, $v);
 }
}
