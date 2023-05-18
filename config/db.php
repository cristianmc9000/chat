<?php
require("func.php");
require("value.php");

$hostname = "localhost";
$username = "root";
$password = "";
$dbname = "sistemasenvibol_1";

$db = mysqli_connect($hostname, $username, $password) or die("can't connect this database");
mysqli_select_db($db, $dbname);
mysqli_set_charset($db, 'utf8');



