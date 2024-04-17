<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Form</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <?php
        session_start(); // Start the session
        if(isset($_POST["register"])){
            header("Location: registration.php");
            exit(); // Stop further execution
        }
        if(isset($_POST["login"])){
            $email = $_POST["email"];
            $password = $_POST["password"];
            require_once "database.php";
            
            $sql = "SELECT * FROM users WHERE email = '$email'";
            $result = mysqli_query($conn, $sql);
        
            if($result && mysqli_num_rows($result) > 0) {
                $user = mysqli_fetch_assoc($result);
                
                if(password_verify($password, $user["password"])){
                    // If password matches, set the user ID in the session
                    $_SESSION['user_id'] = $user['id'];
                    header("Location: index.php");
                    exit(); // Stop further execution
                } else {
                    // Password does not match
                    echo "<div class='alert alert-danger'>Password does not match</div>";
                }
            } else {
                // Email does not exist
                echo "<div class='alert alert-danger'>Email does not exist</div>";
            }
        }
        

        ?>
        <h1 style="font-size: 60px;">Welcome to Friend Finder!</h1>
        <h2 style="font-size: 30px;">Login to existing account</h2>
        <form action="login.php" method="post">
            <div class="form-group">
                <input type="email" placeholder="Enter Email" name="email" class="form-control">
            </div>
            <div class="form-group">
                <input type="password" placeholder="Enter Password" name="password" class="form-control">
            </div>
            <div class="button-container">
            <div class="form-btn">
                <input type="submit" value="Login" name="login" class="btn btn-primary button-form">
                <h4>or</h4>
                <input type="submit" class="btn btn-primary button-form" name="register" value="Register">
            </div>

            
        </form>
    </div>
    
</body>
</html>