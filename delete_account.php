<?php
session_start();

// Include database connection file
require_once "database.php";

// Check if user is logged in
if(isset($_SESSION['user_id'])) {
    $session_id = $_SESSION['user_id'];
    // Retrieve the user ID from the session
    // Get all post IDs of the user
    $get_post_ids_query = "SELECT Post_ID FROM post_entries WHERE User_ID = $session_id";
    $result = mysqli_query($conn, $get_post_ids_query);

    if ($result) {
        $post_ids_to_delete = array();
        // Fetch all post IDs into an array
        while ($row = mysqli_fetch_assoc($result)) {
            $post_ids_to_delete[] = $row['Post_ID'];
        }

        // Delete posts using a loop
        if (count($post_ids_to_delete) > 0) {
            $delete_posts_query = "DELETE FROM posts WHERE Post_ID IN (" . implode(',', $post_ids_to_delete) . ")";
            $result = mysqli_query($conn, $delete_posts_query);

            if ($result) {
            // Delete post entries after successful post deletion
            $delete_entries_query = "DELETE FROM post_entries WHERE User_ID = $session_id";
            $result = mysqli_query($conn, $delete_entries_query);
            if ($result) {
                echo "User's posts deleted successfully!";
            } else {
                echo "Failed to delete post entries: " . mysqli_error($conn);
            }
            } else {
            echo "Failed to delete posts: " . mysqli_error($conn);
            }
        } else {
            echo "User has no posts to delete.";
        }
        } else {
        echo "Failed to retrieve post IDs: " . mysqli_error($conn);
    }

    if ($result) {
        // Delete user record if posts were deleted successfully
        $delete_user_query = "DELETE FROM users WHERE id = $session_id";
        $result = mysqli_query($conn, $delete_user_query);
        if ($result) {
            echo "User account deleted successfully!";
        } else {
            echo "Failed to delete user account: " . mysqli_error($conn);
        }
        $delete_user_query = "DELETE FROM user_info WHERE user_id = $session_id";
        $result = mysqli_query($conn, $delete_user_query);
        if ($result) {
            echo "User account deleted successfully!";
        } else {
            echo "Failed to delete user account: " . mysqli_error($conn);
        }
    } else {
        echo "Failed to delete user's posts: " . mysqli_error($conn);
    }

    // Query to delete user account from database
    $deleteSql = "DELETE FROM users WHERE id=?";
    $deleteStmt = mysqli_prepare($conn, $deleteSql);
    mysqli_stmt_bind_param($deleteStmt, "i", $session_id);
    $deleteResult = mysqli_stmt_execute($deleteStmt);

    if($deleteResult) {
        // Account deleted successfully
        // Destroy the session and redirect to the login page
        session_destroy();
        header("Location: login.php");
        exit();
    } else {
        // Error occurred while deleting account
        echo "Error deleting account: " . mysqli_error($conn);
    }
} else {
    // Redirect to login page if user is not logged in
    header("Location: login.php");
    exit(); // Stop further execution
}
?>
