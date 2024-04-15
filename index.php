<?php
session_start();

// Include database connection file
require_once "database.php";

// Check if user is logged in
if(isset($_SESSION['user_id'])) {
    // Retrieve the user ID from the session
    $session_id = $_SESSION['user_id'];

    // Query to fetch full name from database based on user ID
    $sql = "SELECT full_name FROM users WHERE id='$session_id'";
    $result = mysqli_query($conn, $sql);

    if($result && mysqli_num_rows($result) > 0) {
        // Fetch the full name from the result set
        $row = mysqli_fetch_assoc($result);
        $name = $row['full_name'];
    } else {
        // Handle error if user data not found
        $name = "Unknown";
    }
} else {
    // Redirect to login page if user is not logged in
    header("Location: login.php");
    exit(); // Stop further execution
}

//Settings Redirect
if(isset($_POST['settings_button'])) {
    header("Location: settings.php");
    exit(); // Stop further execution
}
//Create Post Redirect
if(isset($_POST['create_post_button'])) {
    header("Location: create_post.php");
    exit(); // Stop further execution
}
//Log out
if(isset($_POST['logout_button'])) {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit(); // Stop further execution
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Welcome to your Dashboard, <i><?php echo $name; ?><i>!</h1>
    </div>
    <form method="post">
        <button type="submit" name="settings_button">Go to Settings</button>
    </form>
    <form method="post">
        <button type="submit" name="create_post_button">Post</button>
    </form>
    <form method="post">
        <button type="submit" name="logout_button">Log out</button>
    </form>

    <!-- POSTS LOGIC -->
    <?php
    // Query to fetch the latest 10 posts with user information and Like_Count
    $sql = "SELECT posts.*, users.full_name FROM posts JOIN users ON posts.User_ID = users.id ORDER BY Creation_Date DESC LIMIT 10";
    $result = mysqli_query($conn, $sql);

    // Check if there are any posts
    if(mysqli_num_rows($result) > 0) {
        // Loop through each row to display post information
        while($row = mysqli_fetch_assoc($result)) {
            $title = $row['Title'];
            $body = $row['Body'];
            $creationDate = date('d-m', strtotime($row['Creation_Date'])); // Format creation date to DD-MM
            $creatorName = $row['full_name'];
            $likeCount = $row['Like_Count'];
            
            // Display post information
            echo "<div class='container'>";
            echo "<div class='post'>";
            echo "<h2>$title</h2>";
            echo "<p>Created by: $creatorName</p>";
            echo "<p>Creation date: $creationDate</p>";
            echo "<p>Likes: $likeCount</p>";
            echo "<p>$body</p>";
            echo "</div>"; // Closing tag for post
            echo "</div>"; // Closing tag for post-container
        }
    } else {
        // If there are no posts
        echo "<p>No posts found.</p>";
    }
    ?>
</body>
</html>
