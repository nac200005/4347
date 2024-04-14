<?php
session_start();
include('database.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {


    if (isset($_POST['update_account'])) {
        
        //parse
        $username = mysqli_real_escape_string($connection, $_POST['username']);
        $email = mysqli_real_escape_string($connection, $_POST['email']);
        $major = mysqli_real_escape_string($connection, $_POST['major']);
        
        //temporary, set to however we're handling known data later
        $user_id = $_SESSION['user_id'];

        //updating
        $query = "UPDATE `USER` SET Name='$username', UTD_Email='$email', Major='$major' WHERE User_ID = '$user_id'";
        $result = mysqli_query($connection, $query);

        //checking
        if ($result && mysqli_affected_rows($connection) > 0) {
            echo "Account settings updated successfully!";
        } else {
            echo "Failed to update account settings.";
        }


    } elseif (isset($_POST['update_privacy'])) {

        //parse
        $password = mysqli_real_escape_string($connection, $_POST['password']);
        $hashed_password = password_hash($password, PASSWORD_DEFAULT); //Hash
        $visibility = mysqli_real_escape_string($connection, $_POST['visibility']);
        $notifications = isset($_POST['notifications']) ? 1 : 0;
        
        //temporary, set to however we're handling known data later
        $user_id = $_SESSION['user_id'];

        //updating
        $query = "UPDATE `USER` SET User_Pass='$hashed_password', Visibility='$visibility', Notifications='$notifications' WHERE User_ID = '$user_id'";
        $result = mysqli_query($connection, $query);

        //checking
        if ($result && mysqli_affected_rows($connection) > 0) {
            echo "Privacy settings updated successfully!";
        } else {
            echo "Failed to update privacy settings.";
        }
    } else {
        echo "Invalid form submission.";
    }
}

?>
