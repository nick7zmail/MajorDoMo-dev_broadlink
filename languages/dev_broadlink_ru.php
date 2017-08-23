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
'BR_NOISY'=>'шумно',

//rm_bridge
'BR_STEP1'=>'Шаг 1: Укажите адрес RM-brige',
'BR_STEP1_DESC'=>'Введите ip-адрес, указанный в приложении <i>Android RM Bridge</i> и нажмите кнопку <i><b>Проверить устройства</b></i>.',
//s1
'BRS1_NO_PERSON'=>'Никого',
'BRS1_PERSON_DETECTED'=>'Обнаружено',
'BRS1_UNKNOWN'=>'Неизвестен: ',
'BRS1_CLOSED'=>'Закрыто',
'BRS1_CLOSED_NOW'=>'Закрыто только что',
'BRS1_OPENED'=>'Открыто',
'BRS1_CANCEL_SOS'=>'Отмена SOS',
'BRS1_DISARM'=>'Отключена',
'BRS1_ARMED_FULL'=>'Полная охрана',
'BRS1_ARMED_PART'=>'Частичная охрана',
'BRS1_PART'=>'Частичная',
'BRS1_FULL'=>'Полная',
'BRS1_ZONE'=>'Зона: ',
'BRS1_PART_U'=>'ЧАСТ',
'BRS1_FULL_U'=>'ПОЛН',
//cloud
'LOGIN'=>'Войти',
'GET_LAST'=>'Скачать последний бэкап',
'GET_LIST'=>'Загрузить список бэкапов',
'SIZE'=>'Размер',
'DOWNLOAD'=>'Скачать',
'UNPACKED'=>'Архив распакован в папку ',
'CLOUD_FUNC'=>'Облачные функции',
'CLOUD_EXPORT'=>'Экспорт облачных команд',
);

foreach ($dictionary as $k=>$v) {
 if (!defined('LANG_'.$k)) {
  define('LANG_'.$k, $v);
 }
}
