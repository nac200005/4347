<?php
session_start();

// Include database connection file
require_once "database.php";

// Check if user is logged in
if(isset($_SESSION['user_id'])) {
    // Retrieve the user ID from the session
    $session_id = $_SESSION['user_id'];

    // Query to fetch user info from database based on user ID
    $sql = "SELECT * FROM user_info WHERE user_id='$session_id'";
    $result = mysqli_query($conn, $sql);

    if($result && mysqli_num_rows($result) > 0) {
        // Fetch the user information from the result set
        $row = mysqli_fetch_assoc($result);
        $username = $row['username'];
        $major = $row['major'];
        $interests = $row['Interests'];
        $courses = $row['Courses'];
        $schedule = $row['Schedule'];
        $hobbies = $row['Hobbies'];
    } else {
        // Handle error if user data not found
        $username = "";
        $major = "";
        $interests = "";
        $courses = "";
        $schedule = "";
        $hobbies = "";
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
    $interests = $_POST['interests'];
    $courses = $_POST['courses'];
    $schedule = $_POST['schedule'];
    $hobbies = $_POST['hobbies'];

    // Update user info
    $updateSql = "UPDATE user_info SET username=?, major=?, Interests=?, Courses=?, Schedule=?, Hobbies=? WHERE user_id=?";
    $updateStmt = mysqli_prepare($conn, $updateSql);
    mysqli_stmt_bind_param($updateStmt, "ssssssi", $username, $major, $interests, $courses, $schedule, $hobbies, $session_id);
    mysqli_stmt_execute($updateStmt);

    echo "<div class='alert alert-success'>Settings updated successfully.</div>";
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
            <input type="text" id="username" name="username" value="<?php echo $username; ?>">
        </div>

        <div class="form-group">
            <label for="major">Update Major:</label>
            <input type="text" id="major" name="major" value="<?php echo $major; ?>">
        </div>

        <div class="form-group">
            <label for="interests">Update Interests:</label>
            <textarea id="interests" name="interests" rows="4"><?php echo $interests; ?></textarea>
        </div>

        <div class="form-group">
            <label for="courses">Update Courses:</label>
            <textarea id="courses" name="courses" rows="4"><?php echo $courses; ?></textarea>
        </div>

        <div class="form-group">
            <label for="schedule">Update Schedule:</label>
            <textarea id="schedule" name="schedule" rows="4"><?php echo $schedule; ?></textarea>
        </div>

        <div class="form-group">
            <label for="hobbies">Update Hobbies:</label>
            <textarea id="hobbies" name="hobbies" rows="4"><?php echo $hobbies; ?></textarea>
        </div>

        <div class="form-btn">
            <input type="submit" value="Update Settings" name="update_settings" class="btn btn-primary">
        </div>
    </form>       
</div>  

</body>
</html>
