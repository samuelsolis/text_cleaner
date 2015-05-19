<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
require 'includes/csvWorker.class.php';

$work = new csvWorker();
$work->set_debug(true);

$work->test();
$work->init();
$work->work();

?>
