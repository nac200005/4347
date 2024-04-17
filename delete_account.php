<?php
session_start();

// Include database connection file
require_once "database.php";

// Check if user is logged in
if(isset($_SESSION['user_id'])) {
    // Retrieve the user ID from the session
    $session_id = $_SESSION['user_id'];

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
