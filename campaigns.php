<?php

require_once './classes/Aweber.php';
$aw = new Aweber();
$listid = '4865768';
$aw->get_list_campaigns($listid);
