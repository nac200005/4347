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

// Handle form submission
if(isset($_POST['update_settings'])) {
    // Retrieve form data
    $username = $_POST['username'];
    $major = $_POST['major'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm-password'];

    // Check if password and confirm password match
    if($password !== $confirmPassword) {
        echo "<div class='alert alert-danger'>Passwords do not match.</div>";
    } else {
        // Update username and major
        $updateSql = "UPDATE user_info SET username=?, major=? WHERE user_id=?";
        $updateStmt = mysqli_prepare($conn, $updateSql);
        mysqli_stmt_bind_param($updateStmt, "ssi", $username, $major, $session_id);
        mysqli_stmt_execute($updateStmt);

        // Check if password field is not empty, then update password
        if(!empty($password)) {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $updatePasswordSql = "UPDATE users SET password=? WHERE user_id=?";
            $updatePasswordStmt = mysqli_prepare($conn, $updatePasswordSql);
            mysqli_stmt_bind_param($updatePasswordStmt, "si", $hashedPassword, $session_id);
            mysqli_stmt_execute($updatePasswordStmt);
        }

        echo "<div class='alert alert-success'>Settings updated successfully.</div>";
    }
}

// Home Redirect
if(isset($_POST['home_button'])) {
    header("Location: index.php");
    exit(); // Stop further execution
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <form method="post">
        <button type="submit" name="home_button">Home</button>
    </form>

    <div class="container">
        <h2>Account Settings</h2>
        <form action="settings.php" method="post">
            <div class="form-group">
                <label for="username">Update Username:</label>
                <input type="text" id="username" name="username" value="johndoe123">
            </div>

            <div class="form-group">
                <label for="major">Update Major:</label>
                <input type="major" id="major" name="major">
            </div>

            <h2>Privacy Settings</h2>

            <div class="form-group">
                <label for="password">Update Password:</label>
                <input type="password" id="password" name="password">
            </div>

            <div class="form-group">
                <label for="confirm-password">Confirm Password:</label>
                <input type="password" id="confirm-password" name="confirm-password">
            </div>
            
            <!-- <label for="profile-pic">Update profile picture:</label>
            <input type="file" id="profile-pic" name="profile-pic" accept="image/*"><br><br> -->
                
            <div class="form-btn">
                <input type="submit" value="Update Settings" name="update_settings" class="btn btn-primary">
            </div>
        </form>       
    </div>    
</body>
</html>
