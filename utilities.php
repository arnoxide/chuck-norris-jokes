<?php
require_once 'config.php';

function getCategories()
{
    global $conn;

    $sql = "SELECT * FROM categories";
    $result = $conn->query($sql);

    $categories = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $categories[] = $row;
        }
    }

    return $categories;
}

function getRandomJoke($category = null)
{
    $url = 'https://api.chucknorris.io/jokes/random';
    if ($category !== null) {
        $url .= '?category=' . urlencode($category);
    }

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    curl_close($ch);

    return json_decode($response, true);
}

function saveJoke($joke, $category_id)
{
    global $conn;

    $joke_value = $joke['value'];
    $sql = "INSERT INTO jokes (content, category_id) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $joke_value, $category_id);

    if ($stmt->execute()) {
        return true;
    } else {
        return false;
    }
}

function hashPassword($password)
{
    $hash = password_hash($password, PASSWORD_DEFAULT);
    return $hash;
}

function verifyPassword($password, $hash)
{
    if (password_verify($password, $hash)) {
        return true;
    } else {
        return false;
    }
}