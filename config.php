<?php
// $connect = new mysqli('db', 'php_docker', 'password', 'chuck_norris_jokes');

// // Check connection
// if ($connect->connect_error) {
//     die("Connection failed: " . $connect->connect_error);
// }

// $conn = $connect;

$connect = new mysqli('localhost', 'root', '', 'chuck_norris_jokes');

// Check connection
if ($connect->connect_error) {
    die("Connection failed: " . $connect->connect_error);
}

$conn = $connect;