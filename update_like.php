<?php
// Start the session
session_start();

// Include your database connection file and establish the connection
require_once "database.php";

// Check if post ID is submitted
if(isset($_POST['post_id'])) {
    $post_id = $_POST['post_id'];

    // Increment the like count for the corresponding post in the database
    $sql = "UPDATE posts SET Like_Count = Like_Count + 1 WHERE Post_ID = $post_id";
    if(mysqli_query($conn, $sql)) {
        // Fetch the updated like count

        $sql = "SELECT Like_Count FROM posts WHERE Post_ID = $post_id";
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_assoc($result);
        $likeCount = $row['Like_Count'];
        
        // Return the updated like count
        echo $likeCount;
    } else {
        // Handle any errors in executing the SQL query
        echo "Error updating like count: " . mysqli_error($conn);
    }
} else {
    // Handle case where post ID is not submitted
    echo "Post ID not submitted";
}

// Close the database connection
mysqli_close($conn);
?>
