<?php

require_once './classes/Helper.php';
$h = new Helper();
$id = $_POST['id'];
$list = $h->get_list_edit_dialog($id);
echo $list;