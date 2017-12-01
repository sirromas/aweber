<?php

require_once './classes/Helper.php';
$h= new Helper();
$item = $_POST['item'];
$h->add_list_config_item(json_decode($item));
