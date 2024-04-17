<?php
session_start();

// Include database connection file
require_once "database.php";

// Check if user is logged in
if(isset($_SESSION['user_id'])) {
    // Retrieve the user ID from the session
    $session_id = $_SESSION['user_id'];

    // Query to fetch full name from database based on user ID
    $sql = "SELECT full_name FROM user_info WHERE user_id='$session_id'";
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

//Home Redirect
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
    <title>Create a post!</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <form method="post">
        <button type="submit" name="home_button">Home</button>
    </form>
    <div class="container">
        <?php
        if(isset($_POST["Post"])){ 
            $title = $_POST["title"];
            $body = $_POST["body"];
        
            $errors = array();
        
            // Check if title and body are empty
            if(empty($title) || empty($body)){
                array_push($errors,"Both title and body are required");
            }
        
            if(count($errors) > 0){
                foreach($errors as $error){
                    echo "<div class='alert alert-danger'>$error</div>";
                }
            } else {
                // Insert data to the database
                require_once "database.php";
                
                // Prepare the SQL statement
                $sql = "INSERT INTO post_entries (User_ID) VALUES (?)";
                $stmt = mysqli_stmt_init($conn);
                if(mysqli_stmt_prepare($stmt, $sql)){
                    // Bind parameters and execute the statement
                    mysqli_stmt_bind_param($stmt, "i", $session_id);
                    mysqli_stmt_execute($stmt);
        
                    // Check if the insertion was successful
                    if(mysqli_stmt_affected_rows($stmt) > 0){
                        // Get the last inserted ID
                        $post_id = mysqli_insert_id($conn);
                        
                        // Insert the post details into the posts table
                        $sql_post = "INSERT INTO posts (Post_ID, Title, Body) VALUES (?, ?, ?)";
                        $stmt_post = mysqli_stmt_init($conn);
                        if(mysqli_stmt_prepare($stmt_post, $sql_post)){
                            // Bind parameters and execute the statement
                            mysqli_stmt_bind_param($stmt_post, "iss", $post_id, $title, $body);
                            mysqli_stmt_execute($stmt_post);
        
                            // Check if the insertion was successful
                            if(mysqli_stmt_affected_rows($stmt_post) > 0){
                                echo "<div class='alert alert-success'>Post successfully created!</div>";
                            } else {
                                echo "<div class='alert alert-danger'>Failed to create post.</div>";
                            }
                        } else {
                            echo "<div class='alert alert-danger'>Failed to prepare statement.</div>";
                        }
                    } else {
                        echo "<div class='alert alert-danger'>Failed to create post.</div>";
                    }
                } else {
                    echo "<div class='alert alert-danger'>Failed to prepare statement.</div>";
                }
        
                // Close the statement and connection
                mysqli_stmt_close($stmt);
                mysqli_close($conn);
            
            }
        }
        
        ?>
    <h1>Create a post, <i><?php echo $name; ?><i>!</h1>
        <form action="create_post.php" method="post">
            <div class="form-group">
                <input type="text" placeholder="Enter Title" name="title" class="form-control">
            </div>
            <div class="form-group">
                <textarea placeholder="Tell the world something!" name="body" class="form-control"></textarea>
            </div>
            <div class="form-btn">
                <input type="submit" value="Post" name="Post" class="btn btn-primary">
            </div>
        </form>
    </div>
    
</body>
</html>
