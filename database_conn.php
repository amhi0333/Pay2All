<?php

$config = parse_ini_file("config.ini");
$servername = $config["DB_HOST"];
$username = $config["DB_USER"];
$password = $config["DB_PASS"];
$dbname = $config["DB_NAME"];
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    $conn_stat = false;
    // die("Connection failed: " . $conn->connect_error);
}
 $conn_stat = true;
?>