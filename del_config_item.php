<?php

require_once './classes/Helper.php';
$h = new Helper();
$id = $_POST['id'];
$h->delete_config_item($id);
