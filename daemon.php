<?php
$dir = dirname(__FILE__);
echo $dir;
exec("php ".$dir."/shipments.php");
exec("php ".$dir."/orders.php");
exec("php ".$dir."/data.php");
exec("php ".$dir."/update.php");
?>