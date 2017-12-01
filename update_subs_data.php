<?php

require_once './classes/Helper.php';
$h = new Helper();
$item = $_POST['item'];
$h->update_subs_data(json_decode($item));
