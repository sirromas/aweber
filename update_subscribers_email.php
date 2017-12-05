<?php

require_once '/home/giovannirodriguez/theadriangee.com/aw-cpanel/classes/Aweber.php';
$aw = Aweber::getInstance(false);
$list_id = 4865768;
$aw->update_subscribers_email($list_id);
