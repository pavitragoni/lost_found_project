<?php
$host     = "mysql.railway.internal";
$dbname   = "railway";
$username = "root";
$password = "MhARqysgQAAsMGfnokjQuoQrlFMIDfPE";
$port     = 3306;

$conn = mysqli_connect($host, $username, $password, $dbname, $port);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>