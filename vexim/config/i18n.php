<?php

$language = 'ru_RU';
#$language = 'en_EN';
putenv ("LANG=$language");
setlocale(LC_ALL, "");
bindtextdomain('messages', './locale');
textdomain('messages');

?>
