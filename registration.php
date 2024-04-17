<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration Form</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="container">
        <?php
        if(isset($_POST["Login"])){
            // Redirect to login page if user is not logged in
            header("Location: login.php");
            exit(); // Stop further execution
        }
        if(isset($_POST["Register"])){
            $fullname = $_POST["fullname"];
            $email = $_POST["email"];
            $password = $_POST["password"];
            $passwordRepeat = $_POST["repeat-password"];
        
            $passwordHash =  password_hash($password,PASSWORD_DEFAULT);
            $errors = array();
        
            if(empty($fullname) OR empty($email) OR empty($password) OR empty($passwordRepeat)){
                array_push($errors,"All fields are required");
            }
            if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
                array_push($errors, "Invalid Email");
            }
            if(strlen($password) < 4){
                array_push($errors,"Password too short");
            }
            if($password !== $passwordRepeat){
                array_push($errors, "Password does not match"); // Fix typo here
            }

            //Checking if email is already registereed
            require_once "database.php";
            $sql = "SELECT * FROM users WHERE email = '$email'";
            $result = mysqli_query($conn, $sql);
            $rowCount = mysqli_num_rows($result);
            if($rowCount>0){
                array_push($errors, "Email already exists");
            }


            if(count($errors) > 0){
                foreach($errors as $error){
                    echo "<div class='alert alert-danger'>$error</div>";
                }
            } else {
                // Insert data to the database
                
                $sql = "INSERT INTO users (email,password) VALUES (?, ?)";
                $stmt = mysqli_stmt_init($conn);
                $prepareStmt = mysqli_stmt_prepare($stmt,$sql);
                if($prepareStmt){
                    mysqli_stmt_bind_param($stmt,"ss",$email,$passwordHash);
                    mysqli_stmt_execute($stmt);
                    
                    // Get the ID of the inserted user
                    $user_id = mysqli_insert_id($conn);

                    // Insert data to the user_info table
                    $sql = "INSERT INTO user_info (user_id, email, full_name, username) VALUES (?, ?, ?, ?)";
                    $stmt = mysqli_stmt_init($conn);
                    if(mysqli_stmt_prepare($stmt, $sql)){
                        mysqli_stmt_bind_param($stmt, "isss", $user_id, $email, $fullname, $email);
                        mysqli_stmt_execute($stmt);
                        echo "<div class='alert alert-success'>You are now registered!</div>";
                    } else {
                        die("Something went wrong");
                    }
                } else {
                    die("Something went wrong");
                }
            }
        }
        
        ?>
        <form action="registration.php" method="POST">
            <div class="form-btn">
                <input type="submit" class="btn btn-primary" name="Login" value="Login">
            </div>
        </form>
        <h1 style="font-size: 60px;">Welcome to Friend Finder!</h1>
        <h2 style="font-size: 30px;">Register a new account</h2>
        <form action="registration.php" method="POST">
            <div class="form-group">
                <input type="text" class="form-control" name="fullname" placeholder="Full Name:">
            </div>
            <div class="form-group">
                <input type="email" class="form-control" name="email" placeholder="Email:">
            </div>
            <div class="form-group">
                <input type="password" class="form-control" name="password" placeholder="Password:">
            </div>
            <div class="form-group">
                <input type="password" class="form-control" name="repeat-password" placeholder="Repeat Password:">
            </div>
            <div class="form-btn">
                <input type="submit" class="btn btn-primary" name="Register" value="Register">
            </div>
        </form>
    </div> 
</body>
</html>
