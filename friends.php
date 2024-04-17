<?php
session_start();

// Include database connection file
require_once "database.php";

// Check if user is logged in
if(!isset($_SESSION['user_id'])) {
    // Redirect to login page if user is not logged in
    header("Location: login.php");
    exit(); // Stop further execution
}

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

// Home Redirect
if(isset($_POST['home_button'])) {
    header("Location: index.php");
    exit(); // Stop further execution
}

// ADD FRIEND
if(isset($_POST['add_friend_button'])) {
    // Get the friend ID from the form submission
    $friendID = mysqli_real_escape_string($conn, $_POST['friend_id']);
    
    // Check if the friendship already exists
    $checkQuery = "SELECT * FROM friendships WHERE (User1_ID = $session_id AND User2_ID = $friendID) OR (User1_ID = $friendID AND User2_ID = $session_id)";
    $checkResult = mysqli_query($conn, $checkQuery);
    
    if(mysqli_num_rows($checkResult) == 0) {
        // Friendship does not exist, insert into database
        $insertQuery = "INSERT INTO friendships (User1_ID, User2_ID) VALUES ($session_id, $friendID)";
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
    <title>Friends Page</title>
</head>
<body>
    <form method="post">
        <button type="submit" name="home_button">Home</button>
    </form>
    <h1>Friends Page</h1>

    <?php
        // Query friendships table to get the Friend_IDs of current user's friends
        $friendshipsQuery = "SELECT User2_ID FROM friendships WHERE User1_ID = $session_id";
        $friendshipsResult = mysqli_query($conn, $friendshipsQuery);

        // Store the Friend_IDs in an array
        $friendIDs = array();
        while ($friendshipRow = mysqli_fetch_assoc($friendshipsResult)) {
            $friendIDs[] = $friendshipRow['User2_ID'];
        }

        // Check if there are any friend IDs
        if (!empty($friendIDs)) {
            // Query to retrieve current friends of the user
            $friendsQuery = "SELECT users.id, users.full_name FROM users INNER JOIN friendships ON users.id = friendships.User2_ID WHERE friendships.User1_ID = $session_id";
            $friendsResult = mysqli_query($conn, $friendsQuery);

            // Check if any friends are found
            if(mysqli_num_rows($friendsResult) > 0) {
                echo "<h3>Your Friends:</h3>";
                echo "<ul>";
                while($friendRow = mysqli_fetch_assoc($friendsResult)) {
                    $friendID = $friendRow['id'];
                    $friendFullName = $friendRow['full_name'];
                    echo "<li>$friendFullName ";
                    // No need for a form as these are already friends
                    echo "</li>";
                }
                echo "</ul>";
            } else {
                echo "You have no friends yet.";
            }
            // Query to retrieve users who are not friends with the current user
            $notFriendsQuery = "SELECT id, full_name FROM users WHERE id <> $session_id AND id NOT IN (" . implode(',', $friendIDs) . ")";
            $notFriendsResult = mysqli_query($conn, $notFriendsQuery);

            // Check if any users who are not friends are found
            if(mysqli_num_rows($notFriendsResult) > 0) {
                echo "<h3>Users who are not your friends:</h3>";
                echo "<ul>";
                while($notFriendRow = mysqli_fetch_assoc($notFriendsResult)) {
                    $notFriendID = $notFriendRow['id'];
                    $notFriendFullName = $notFriendRow['full_name'];
                    echo "<li>$notFriendFullName ";
                    echo "<form method='post' style='display: inline;'>";
                    echo "<input type='hidden' name='friend_id' value='$notFriendID'>";
                    echo "<button type='submit' name='add_friend_button'>Add as Friend</button>";
                    echo "</form>";
                    echo "</li>";
                }
                echo "</ul>";
            } else {
                echo "You are already friends with all users.";
            }
        } else {
            // If the user has no friends yet, display all users except the current user
            $allUsersQuery = "SELECT id, full_name FROM users WHERE id <> $session_id";
            $allUsersResult = mysqli_query($conn, $allUsersQuery);

            if(mysqli_num_rows($allUsersResult) > 0) {
                echo "<h3>All Users:</h3>";
                echo "<ul>";
                while($userRow = mysqli_fetch_assoc($allUsersResult)) {
                    $userID = $userRow['id'];
                    $fullName = $userRow['full_name'];
                    echo "<li>$fullName ";
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
        }
    ?>
</body>
</html>
