<?php

$language = 'en_US';
putenv ("LANG=$language");
setlocale(LC_ALL, "");
bindtextdomain('messages', './locale');
textdomain('messages');

?>
