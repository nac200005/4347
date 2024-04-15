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

// Home Redirect
if(isset($_POST['home_button'])) {
    header("Location: index.php");
    exit(); // Stop further execution
}

//ADD FRIEND
if(isset($_POST['add_friend_button'])) {
    // Get the friend ID from the form submission
    $friendID = $_POST['friend_id'];
    
    // Get the ID of the current user (assuming it's stored in $currentUserID)
    $currentUserID = $session_id; // Example value
    
    // Check if the friendship already exists
    $checkQuery = "SELECT * FROM friendships WHERE (User_ID = $currentUserID AND Friend_ID = $friendID) OR (User_ID = $friendID AND Friend_ID = $currentUserID)";
    $checkResult = mysqli_query($conn, $checkQuery);
    
    if(mysqli_num_rows($checkResult) == 0) {
        // Friendship does not exist, insert into database
        $insertQuery = "INSERT INTO friendships (User_ID, Friend_ID) VALUES ($currentUserID, $friendID)";
        $insertResult = mysqli_query($conn, $insertQuery);
        
        if($insertResult) {
            echo "Friend added successfully!";
        } else {
            echo "Error adding friend: " . mysqli_error($conn);
        }
    } else {
        // Friendship already exists
        echo "You are already friends!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form method="post">
        <button type="submit" name="home_button">Home</button>
    </form>
    <h1>Friends Page</h1>

    <?php
        // Assuming $currentUserID contains the ID of the current user

        // Query friendships table to get the Friend_IDs of current user's friends
        $friendshipsQuery = "SELECT Friend_ID FROM friendships WHERE User_ID = $session_id";
        $friendshipsResult = mysqli_query($conn, $friendshipsQuery);

        // Store the Friend_IDs in an array
        $friendIDs = array();
        while ($friendshipRow = mysqli_fetch_assoc($friendshipsResult)) {
            $friendIDs[] = $friendshipRow['Friend_ID'];
        }

        // Check if there are any friend IDs
        if (!empty($friendIDs)) {
            // Query users table to get the full names of current user's friends
            $friendsQuery = "SELECT full_name FROM users WHERE id IN (" . implode(',', $friendIDs) . ")";
            $friendsResult = mysqli_query($conn, $friendsQuery);

            // Check if any friends are found
            if(mysqli_num_rows($friendsResult) > 0) {
                echo "<h3>Friends:</h3>";
                echo "<ul>";
                while($friendRow = mysqli_fetch_assoc($friendsResult)) {
                    $friendFullName = $friendRow['full_name'];
                    echo "<li>$friendFullName</li>";
                }
                echo "</ul>";
            } else {
                echo "You have no friends.";
            }
        } else {
            echo "You have no friends.";
        }

        //DISPLAY ALL USERS - DANGEROUS
        $usersQuery = "SELECT id, full_name FROM users";
        $usersResult = mysqli_query($conn, $usersQuery);

        // Check if any users are found
        if(mysqli_num_rows($usersResult) > 0) {
            echo "<h3>All Users:</h3>";
            echo "<ul>";
            // Loop through each user
            while($userRow = mysqli_fetch_assoc($usersResult)) {
                $userID = $userRow['id'];
                $fullName = $userRow['full_name'];
                // Display user's full name
                echo "<li>$fullName ";
                // Add button to add as friend
                echo "<form method='post' style='display: inline;'>";
                echo "<input type='hidden' name='friend_id' value='$userID'>";
                echo "<button type='submit' name='add_friend_button'>Add as Friend</button>";
                echo "</form>";
                echo "</li>";
            }
            echo "</ul>";
        } else {
            echo "No users found.";
        }

    ?>
</body>
</html>