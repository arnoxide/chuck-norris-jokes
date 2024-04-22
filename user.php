<?php
session_start();
require_once 'api.php';

$is_logged_in = isset($_SESSION['user_id']);

if (!$is_logged_in) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Get user details
$sql = "SELECT name, email FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Get user's favorite jokes
$sql = "SELECT j.content, c.name as category
        FROM favorites f
        JOIN jokes j ON f.joke_id = j.id
        JOIN categories c ON j.category_id = c.id
        WHERE f.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$favorite_jokes = $result->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center mb-4">User Profile</h1>
        <form method="post" action="index.php">
        <button type="submit" class="btn btn-success">Back</button>
    </form>
        <div class="card mb-4">
            <div class="card-body">
                <h2 class="card-title">User Details</h2>
                <p><strong>Name:</strong> <?php echo $user['name']; ?></p>
                <p><strong>Email:</strong> <?php echo $user['email']; ?></p>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h2 class="card-title">Favorite Jokes</h2>
                <?php if (count($favorite_jokes) > 0): ?>
                    <ul class="list-group">
                        <?php foreach ($favorite_jokes as $joke): ?>
                            <li class="list-group-item">
                                <p><?php echo $joke['content']; ?></p>
                                <small class="text-muted">Category: <?php echo $joke['category']; ?></small>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p>You haven't added any jokes to your favorites yet.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>