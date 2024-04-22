<?php
session_start();
require_once 'config.php';
require_once 'api.php';

$is_logged_in = isset($_SESSION['user_id']);

if (!$is_logged_in) {
    header("Location: login.php");
    exit;
}

$categories = getCategories();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['category'])) {
        $category = $_POST['category'];
        $joke = getRandomJoke($category);

        $sql = "SELECT id FROM categories WHERE name = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $category);
        $stmt->execute();
        $result = $stmt->get_result();
        $category_id = $result->fetch_assoc()['id'];

        $saved_joke_id = saveJoke($joke, $category_id);

        if ($saved_joke_id) {
           // $message = "Joke fetched successfully!";
        } else {
            $message = "Failed to get joke!";
        }
    }

    if (isset($_POST['add_favorite'])) {
        $joke_id = $_POST['joke_id'];
        $user_id = $_SESSION['user_id'];

        $sql = "SELECT * FROM favorites WHERE user_id = ? AND joke_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $user_id, $joke_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            $sql = "INSERT INTO favorites (user_id, joke_id) VALUES (?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $user_id, $joke_id);
            $stmt->execute();

            $message = "Joke added to favorites successfully!";
        } else {
            $message = "This joke is already in your favorites!";
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chuck Norris Jokes</title>
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

        .joke-container {
            background-color: #fff;
            border-radius: 5px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .favorite-icon {
            color: #dc3545;
            cursor: pointer;
        }

        .favorite-icon:hover {
            color: #b02a37;
        }
    </style>
</head>
<body>
<div class="container">
    <h1 class="text-center mb-4">Chuck Norris Jokes</h1>

    <div class="alert alert-success">
        You are logged in.
    </div>
    <a href="user.php">User Profile</a>
    <a href="favorites.php">Favorites</a>
    <?php if ($is_logged_in): ?>
        <!-- Display logout button if the user is logged in -->
        <form method="post" action="logout.php">
            <button type="submit" class="btn btn-danger">Logout</button>
        </form>
    <?php endif; ?>

    <h2 class="mt-4">Select a Category</h2>
    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <div class="mb-3">
            <select class="form-select" name="category">
                <option value="">Select a Category</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?php echo $category['name']; ?>"><?php echo $category['name']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Get Joke</button>
    </form>

    <?php if (isset($joke)): ?>
        <div class="joke-container">
            <p><?php echo $joke['value']; ?></p>
            <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <input type="hidden" name="joke_id" value="<?php echo $saved_joke_id; ?>">
                <button type="submit" class="btn btn-primary" name="add_favorite">
                    <span class="favorite-icon" title="Add to Favorites">&#9829;</span> Add to Favorites
                </button>
            </form>
        </div>
    <?php endif; ?>

    <?php if (isset($message)): ?>
        <div class="alert alert-success"><?php echo $message; ?></div>
    <?php endif; ?>
</div>

</body>
</html>
<?php
$conn->close();
?>
