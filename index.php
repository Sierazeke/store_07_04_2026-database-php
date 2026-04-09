<?php
$servername = "172.22.144.1";
$username = "store_2026_04_07_user";
$password = "password";
$dbname = "store_2026_04_07";

$conn = new mysqli($servername, $username, $password, $dbname); 

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "Connected successfully";

$conn->close();
?>