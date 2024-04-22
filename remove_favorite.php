<?php
session_start();
require_once 'config.php';


if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$joke_id = $_POST['joke_id'];


$sql = "DELETE FROM favorites WHERE user_id = ? AND joke_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $user_id, $joke_id);
$stmt->execute();


header("Location: favorites.php");
exit();