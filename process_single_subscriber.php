<?php

require_once '/home/giovannirodriguez/theadriangee.com/aw-cpanel/classes/Aweber.php';
$aw = Aweber::getInstance(false);
$aw->process_list_single_subscriber();
