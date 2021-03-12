<?php

# server details variables for connection to MySQL server
$servername = "localhost";
$username = "OutageAlert";
$password = "VqD4fDBJtt40iwFP";
$dbname = "mydb";

# open a connection to the MySQL server
$conn = mysqli_connect($servername, $username, $password, $dbname);

# check if connection is successful
if(mysqli_connect_errno()){
    die("Connection failed: " . mysqli_connect_error());
}
error_log("Connected Successfully");

# method to check if the user has already registered an account under the given email
function check_existence($email) {
    global $conn;

    $sql = "SELECT Email FROM Account WHERE Email='$email'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
        return true;
    }
    else { return false; }
}

# TODO - method to check if user information entered is valid
function check_validity($email, $password) {
    global $conn;

    $hashed_password = hash("SHA256", $password);

    $sql = "SELECT * FROM Account WHERE Email='$email' AND Password='$hashed_password'";
    $result = mysqli_query($conn, $sql);

    while ($row = mysqli_fetch_assoc($result)) {
        if ($hashed_password == $row["Password"]) {
            return true;
        }
        else {
            return false;
        }
    }
}

# TODO - method to check if a user's account is locked
# TODO - method to add a new user
function new_account($email, $password) {
    global $conn;

    $created_date = date("Ymd");
    $hashed_password = hash("SHA256", $password);

    $sql = "INSERT INTO Account VALUES (NULL, '$hashed_password', '$email', $created_date, 0)";
    if (mysqli_query($conn, $sql)) {
        error_log("New user successfully added.");
        print("SUCCESS???!!");
    }
    else {
        error_log("Error: ").$sql.("<br>").$conn->error;
        print($conn->error);
    }
}

# TODO - method to change user's password

# TODO - method to add a new property
# TODO - method to edit a property's details

# TODO - method to add a new recipient

# TODO - method to edit a recipient's details
# TODO - method to change if user receives text message
# TODO - method to change if user receives email

?>