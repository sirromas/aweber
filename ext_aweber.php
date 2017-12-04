<?php

$url = 'http://www.theadriangee.com/aw-cpanel/process_single_subscriber.php';
$result = file_get_contents($url);
echo $result;
