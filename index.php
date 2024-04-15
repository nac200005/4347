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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <script>
        // JavaScript function to update the like count dynamically
        function updateLike(post_id) {
            
            // Send an AJAX request to update the like count for the post
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "update_like.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function() {
                // alert(xhr.readyState + " " + xhr.status);
                if (xhr.readyState == 4 && xhr.status == 200) {
                    
                    // Update the like count on the page
                    document.querySelector('.post-container[data-post-id="' + post_id + '"] .like-count').innerHTML = xhr.responseText;
                }
            };
            xhr.send("post_id=" + post_id);
            
        }
        function updateView(post_id) {
            
            // Send an AJAX request to update the like count for the post
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "update_view.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.send("post_id=" + post_id);
            
        }
    </script>

    <!-- <div class="container">
        <h1>Welcome to your Dashboard, <i><?php echo $name; ?><i>!</h1>
    </div> -->
    
    <div class="title">
        <form method="post" class="button-form">
            <button type="submit" name="settings_button">
                <i class="fas fa-cog"></i> <!-- Font Awesome cog icon -->
            </button>
        </form>
        <span class="title-text">Friend Finder</span> <!-- Text to be centered -->
        <form method="post" class="button-form logout-button">
            <button type="submit" name="logout_button">Log out</button>
        </form>
    </div>







    <!-- POSTS LOGIC -->
    <?php

    // Fetch and display posts
    $sql = "SELECT posts.*, users.full_name FROM posts JOIN users ON posts.User_ID = users.id ORDER BY Creation_Date DESC LIMIT 5";
    $result = mysqli_query($conn, $sql);
    echo "<div class='pad'></div>";
    if(mysqli_num_rows($result) > 0) {
        while($row = mysqli_fetch_assoc($result)) {
            // Retrieve post data
            $post_id = $row['Post_ID'];
            $title = $row['Title'];
            $body = $row['Body'];
            $creationDate = date('m-d', strtotime($row['Creation_Date'])); // Format creation date to DD-MM
            $creatorName = $row['full_name'];
            $likeCount = $row['Like_Count'];
            echo "<script>updateView($post_id)</script>";
            
            // Display post information
            echo "<div class='post-container' data-post-id='$post_id'>";
            echo "<div class='post'>";
            echo "<div class='post-header'>";
            echo "<h3>$creatorName - $creationDate</h3>"; // Format: Name - Date
            echo "<h2 class='post-title'>$title</h2>"; // Title slightly larger than heading
            echo "</div>"; // Closing tag for post-header
            echo "<p class='post-body'>$body</p>"; // Text slightly smaller than heading

            // Display likes count with Font Awesome like symbol as a button
            echo "<div class='like-section'>";
            echo "<button class='like-button' onclick='updateLike($post_id)'><i class='fas fa-heart'></i> <span class='like-count'>$likeCount</span></button>";
            echo "</div>"; // Closing tag for like-section

            echo "</div>"; // Closing tag for post
            echo "</div>"; // Closing tag for post-container
        }
    } else {
        echo "No posts found.";
    }

    // Close the database connection
    mysqli_close($conn);
    ?>
    <form method="post" class="button-form-post">
        <button type="submit" name="create_post_button">Post</button>
    </form>
</body>
</html>
