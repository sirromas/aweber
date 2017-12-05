<?php

require_once '/home/giovannirodriguez/theadriangee.com/aw-cpanel/classes/Aweber.php';
$aw = Aweber::getInstance(false);
$old_list = 4865768;
$src = 4878186;
$list_name = 'Face Fears Opt ins';
$aw->move_subscribers_back($src, $old_list);
