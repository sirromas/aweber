<?php

require_once './classes/Aweber.php';
$aw = new Aweber();
$aw->discover_lists();
