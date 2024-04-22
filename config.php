<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'chuck_norris_jokes');

// Database connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// $connect = new mysqli('db', 'php_docker', 'password', 'chuck_norris_jokes');

// // Check connection
// if ($connect->connect_error) {
//     die("Connection failed: " . $connect->connect_error);
// }

// $conn = $connect;