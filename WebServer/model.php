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

# method to check if user information entered is valid
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
# method to add a new user
function new_account($email, $password) {
    global $conn;

    $created_date = date("Ymd");
    $hashed_password = hash("SHA256", $password);

    $sql = "INSERT INTO Account VALUES (NULL, '$hashed_password', '$email', $created_date, 0)";
    if (mysqli_query($conn, $sql)) {
        error_log("New user successfully added.");
    }
    else {
        error_log("Error: ").$sql.("<br>").$conn->error;
    }
}
# method to get user id for faster database access
function get_user_id($email) {
    global $conn;

    $sql = "SELECT AccountID FROM Account WHERE Email='$email'";
    $result = mysqli_query($conn, $sql);
    while ($row = mysqli_fetch_assoc($result)) {
        return $row["AccountID"];
    }
    return -1;
}

# TODO - method to change user's password

# TODO - method to add a new property
function new_property($name, $address, $aid, $lat, $lon) {
    global $conn;

    $sql = "INSERT INTO Property VALUES (NULL, '$name', '$address', $aid, $lat, $lon)";
    if (mysqli_query($conn, $sql)) {
        error_log("New property successfully added.");
    }
    else {
        error_log("Error: ").$sql.("<br>").$conn->error;
    }
}

# TODO - method to add recipient properties into database
# TODO - perhaps do this when creating property so each property has association with all recipients?

function get_properties($userid) {
    global $conn;

    $sql = "SELECT Property Name, Address FROM Property WHERE AccountID='$userid'";
    $result = mysqli_query($conn, $sql);

    return $result;
}

# TODO - method to edit a property's details

# method to add a new recipient
function new_recipient($name, $phonenumber, $email, $id) {
    global $conn;

    $sql = "INSERT INTO Recipients VALUES ('$name', $phonenumber, '$email', $id)";
    if (mysqli_query($conn, $sql)) {
        error_log("New recipient successfully added.");
    }
    else {
        error_log("Error: ").$sql.("<br>").$conn->error;
    }
}

function get_recipients($userid) {
    global $conn;

    $sql = "SELECT Name, Phone, Contact Email FROM Recipients WHERE AccountID='$userid'";
    $result = mysqli_query($conn, $sql);

    return $result;
}

function get_recipient_properties($userid) {
    global $conn;

    $sql = "SELECT Name, Active FROM Recipient Properties WHERE AccountID='$userid'";
    $result = mysqli_query($conn, $sql);

    return $result;
}

# TODO - method to edit a recipient's details
# TODO - method to change if user receives text message
# TODO - method to change if user receives email

?>