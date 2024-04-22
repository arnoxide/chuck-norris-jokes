<?php
session_start();
require_once 'api.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT jokes.id AS joke_id, jokes.content, categories.name AS category
        FROM favorites
        JOIN jokes ON favorites.joke_id = jokes.id
        JOIN categories ON jokes.category_id = categories.id
        WHERE favorites.user_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$favorites = $result->fetch_all(MYSQLI_ASSOC);


$favorite_count = count($favorites);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Favorite Jokes</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h1 class="text-center mb-4">Favorite Jokes</h1>
        <a href="index.php" class="btn btn-primary">Home</a>
        <p>Total favorite jokes: <?php echo $favorite_count; ?></p> 
        <?php if (!empty($favorites)): ?>
            <?php $counter = 1; ?>
            <?php foreach ($favorites as $favorite): ?>
                <div class="joke-container mb-3">
                    <p><?php echo $counter++; ?>. <?php echo $favorite['content']; ?></p>
                    <small class="text-muted">Category: <?php echo $favorite['category']; ?></small>
                    <form method="post" action="remove_favorite.php">
                        <input type="hidden" name="joke_id" value="<?php echo $favorite['joke_id']; ?>">
                        <button type="submit" class="btn btn-danger btn-sm">Remove</button>
                    </form>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>You don't have any favorite jokes yet.</p>
        <?php endif; ?>
    </div>
</body>
</html>

<?php // Close the database connection $conn->close(); ?>
