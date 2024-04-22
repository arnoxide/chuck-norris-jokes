<?php
require_once 'config.php';

function getCategories() {
    global $conn;

    $sql = "SELECT * FROM categories";
    $result = $conn->query($sql);

    if ($result !== null && $result->num_rows > 0) {
        $categories = [];
        while ($row = $result->fetch_assoc()) {
            $categories[] = $row;
        }
        return $categories;
    } else {
        // Handle the case when there are no categories or the query failed
        return [];
    }
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
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode == 200) {
        $joke = json_decode($response, true);
        if ($joke !== null) {
            return $joke;
        } else {
            return [
                'value' => 'Error: Invalid response from the API',
                'id' => null
            ];
        }
    } else {
        return [
            'value' => 'Error: Failed to fetch joke from the API',
            'id' => null
        ];
    }
}

function saveJoke($joke, $category_id) {
    global $conn;
    $joke_value = $joke['value'];

    $sql = "INSERT INTO jokes (content, category_id) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $joke_value, $category_id);
    $stmt->execute();

    // Return the ID of the newly inserted joke
    return $conn->insert_id;
}

function hashPassword($password) {
    $hash = password_hash($password, PASSWORD_DEFAULT);
    return $hash;
}

function verifyPassword($password, $hash) {
    if (password_verify($password, $hash)) {
        return true;
    } else {
        return false;
    }
}