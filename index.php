<?php
session_start();
require_once 'utilities.php';

// Check if user is logged in
$is_logged_in = isset($_SESSION['user_id']);

// Get categories from the database
$categories = getCategories();

// Handle category selection and joke fetching
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['category'])) {
        $category = $_POST['category'];
        $joke = getRandomJoke($category);

        // Get category ID from the database
        $sql = "SELECT id FROM categories WHERE name = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $category);
        $stmt->execute();
        $result = $stmt->get_result();
        $category_id = $result->fetch_assoc()['id'];

        // Save the joke to the database
        $saved = saveJoke($joke, $category_id);
    }
}

// Handle user authentication
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['login'])) {
        $email = $_POST['email'];
        $password = $_POST['password'];

        // Retrieve user from the database
        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            // Verify password
            if (verifyPassword($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $is_logged_in = true;
            } else {
                $error = "Invalid email or password";
            }
        } else {
            $error = "Invalid email or password";
        }
    } elseif (isset($_POST['register'])) {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $hashed_password = hashPassword($password);

        // Insert new user into the database
        $sql = "INSERT INTO users (name, email, password) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $name, $email, $hashed_password);

        if ($stmt->execute()) {
            $success = "Registration successful! You can now log in.";
        } else {
            $error = "Registration failed: " . $conn->error;
        }
    }
}

// Handle adding jokes to favorites
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_favorite'])) {
    $joke_id = $_POST['joke_id'];
    $user_id = $_SESSION['user_id']; // Get the user_id from the session

    // Check if the joke is already in favorites
    $sql = "SELECT * FROM favorites WHERE user_id = ? AND joke_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $user_id, $joke_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        // Add the joke to favorites
        $sql = "INSERT INTO favorites (user_id, joke_id) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $user_id, $joke_id);
        $stmt->execute();

        // Prompt a message when a joke is added to favorites
        $message = "Joke added to favorites successfully!";
    } else {
        // Prompt a message if the joke is already in favorites
        $message = "This joke is already in your favorites!";
    }
}

// Display the message if it exists
if (isset($message)) {
    echo "<div class='alert alert-success'>$message</div>";
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
<?php if ($is_logged_in): ?>
    <!-- Display logout button if the user is logged in -->
    <form method="post" action="logout.php">
        <button type="submit" class="btn btn-danger">Logout</button>
    </form>
<?php endif; ?>
    <div class="container">
        <h1 class="text-center mb-4">Chuck Norris Jokes</h1>

        <?php if ($is_logged_in): ?>
            <div class="alert alert-success">
                You are logged in.
            </div>
        <?php else: ?>
            <div class="row">
                <div class="col-md-6">
                    <h2>Login</h2>
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <button type="submit" class="btn btn-primary" name="login">Login</button>
                    </form>
                </div>
                <div class="col-md-6">
                    <h2>Register</h2>
                    <?php if (isset($success)): ?>
                        <div class="alert alert-success"><?php echo $success; ?></div>
                    <?php endif; ?>
                    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                        <div class="mb">
                        <label for="name" class="form-label">Name</label>
                           <input type="text" class="form-control" id="name" name="name" required>
                       </div>
                       <div class="mb-3">
                           <label for="email" class="form-label">Email</label>
                           <input type="email" class="form-control" id="email" name="email" required>
                       </div>
                       <div class="mb-3">
                           <label for="password" class="form-label">Password</label>
                           <input type="password" class="form-control" id="password" name="password" required>
                       </div>
                       <button type="submit" class="btn btn-primary" name="register">Register</button>
                   </form>
               </div>
           </div>
       <?php endif; ?>

       <?php if ($is_logged_in): ?>
        <a href="favorites">Favorites</a>
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
                <input type="hidden" name="joke_id" value="<?php echo $joke['id']; ?>">
                <button type="submit" class="btn btn-primary" name="add_favorite">
                    <span class="favorite-icon" title="Add to Favorites">&#9829;</span> Add to Favorites
                </button>
            </form>
        </div>
    <?php endif; ?>
<?php endif; ?>
   </div>

 <!-- Modal Popup -->
 <div class="modal fade" id="recommendJokeModal" tabindex="-1" aria-labelledby="recommendJokeModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="recommendJokeModalLabel">Recommended Joke</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="recommendedJokeText"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript code -->
    <script>
        // PHP variables to JavaScript
        const categories = <?php echo json_encode($categories); ?>;
        const joke = <?php echo isset($joke) ? json_encode($joke) : 'null'; ?>;

        // Function to show the modal with recommended joke
        function showRecommendJokeModal(joke) {
            const recommendedJokeText = document.getElementById('recommendedJokeText');
            recommendedJokeText.textContent = joke;
            const modal = new bootstrap.Modal(document.getElementById('recommendJokeModal'));
            modal.show();
        }

        // Check if a joke is available and user is logged in, then show the modal
        if (joke && <?php echo $is_logged_in ? 'true' : 'false'; ?>) {
            showRecommendJokeModal(joke.value);
        }
    </script>
</body>
</html>
<?php
// Close the database connection
$conn->close();