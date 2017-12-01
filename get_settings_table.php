<?php

require_once './classes/Helper.php';
$hp = new Helper();
$list = $hp->get_lists_config_data();
echo $list;