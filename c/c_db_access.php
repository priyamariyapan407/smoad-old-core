<?php error_reporting(5);
$db = mysqli_connect("127.0.0.1", "root", "", "smoad") or die("Error connecting to database.");
setlocale(LC_MONETARY, 'en_IN');
include "c_db_api_set1.php";
