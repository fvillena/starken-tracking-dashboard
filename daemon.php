<?php
$dir = dirname(__FILE__);
exec("php ".$dir."/shipments.php 2>&1" , $output);
var_dump($output);
exec("php ".$dir."/orders.php 2>&1" , $output);
var_dump($output);
exec("php ".$dir."/data.php 2>&1" , $output);
var_dump($output);
exec("php ".$dir."/update.php 2>&1" , $output);
var_dump($output);
?>