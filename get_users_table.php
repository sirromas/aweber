<?php

require_once './classes/Helper.php';
$h = new Helper();
$list = $h->get_users_table();
echo $list;