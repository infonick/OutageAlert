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
    // if there is a result, the number of rows will be greater than zero
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

function new_property($name, $address, $aid, $lat, $lon) {
    global $conn;

    $sql = "INSERT INTO Property VALUES (NULL, '$name', '$address', $aid, $lat, $lon)";
    if (mysqli_query($conn, $sql)) {
        error_log("New property successfully added.");
    }
    else {
        error_log("Error: ").$sql.("<br>").$conn->error;
        echo $conn->error;
    }

    # TODO - this probably won't work with an empty account with no properties. should insert some checks for that.
    # TODO - definitely doesn't work with empty account. Need to fix this.
    // create recipient property entries for each existing recipient with new property
    $sproperties = get_property_id($aid);
    $properties = array();
    $i = 0;
    while($row = $sproperties->fetch_object()){
        // need to convert to a number value php recognizes to use in function
        $pid = intval(print_r($row->PropertyID, true));
        $properties[$i++] = $pid;
    }
    $totalp = sizeof($properties);
    $srecipients= get_recipient_names($aid);
    $recipients = array();
    $i = 0;
    while($row = $srecipients->fetch_object()){
        // need to convert to a string value php recognizes to use in function
        $uname = print_r($row->Name, true);
        $recipients[$i++] = $uname;
    }
    $totalr = sizeof($recipients);
    for ($i = 0; $i < $totalr; $i++) {
        $rname = $recipients[$i];
        for ($j = 0; $j < $totalp; $j++) {
            $pid = $properties[$j];
            new_recipient_property($aid, $rname, $pid);
        }
    }
}


function get_properties($userid) {
    global $conn;

    $sql = "SELECT `Property Name`, Address FROM Property WHERE AccountID=$userid";
    $result = mysqli_query($conn, $sql);

    return $result;
}


function get_property_id($userid) {
    global $conn;

    $sql = "SELECT PropertyID FROM Property WHERE AccountID=$userid ORDER BY PropertyID DESC LIMIT 1";
    $result = mysqli_query($conn, $sql);

    return $result;
}

function get_all_property_ids($userid) {
    global $conn;

    $sql = "SELECT PropertyID FROM Property WHERE AccountID=$userid";
    $result = mysqli_query($conn, $sql);

    return $result;
}

function edit_property($name, $address, $oname, $oaddress, $lat, $lon, $userid) {
    global $conn;

    $sql = "UPDATE Property SET `Property Name`='$name', Address='$address', Latitude=$lat, Longitude=$lon WHERE `Property Name`='$oname' AND Address='$oaddress' AND AccountID=$userid";
    $result = mysqli_query($conn, $sql);

    return $result;
}

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

    // create recipient property entries for all existing properties and new recipient
    $sproperties = get_all_property_ids($id);
    $properties = array();
    $i = 0;
    while($row = $sproperties->fetch_object()){
        $pid = intval(print_r($row->PropertyID, true));
        $properties[$i++] = $pid;
    }
    $totalp = sizeof($properties);

    for ($i = 0; $i < $totalp; $i++) {
        $pid = $properties[$i];
        new_recipient_property($id, $name, $pid);
    }

}

function edit_recipient($name, $number, $email, $oname, $onumber, $oemail, $userid) {
    global $conn;

    $sql = "UPDATE Recipients SET Name='$name', Phone=$number, `Contact Email`='$email' WHERE Name='$oname' AND Phone=$onumber AND `Contact Email`='$oemail' AND AccountID=$userid";
    if (mysqli_query($conn, $sql)) {
        error_log("New property successfully added.");
    }
    else {
        error_log("Error: ").$sql.("<br>").$conn->error;
        echo $conn->error;
    }
}

function get_recipients($userid) {
    global $conn;

    $sql = "SELECT Name, Phone, `Contact Email` FROM Recipients WHERE AccountID=$userid";
    $result = mysqli_query($conn, $sql);

    return $result;
}

function get_recipient_names($userid) {
    global $conn;

    $sql = "SELECT Name FROM Recipients WHERE AccountID=$userid";
    $result = mysqli_query($conn, $sql);

    return $result;
}

function new_recipient_property($aid, $name, $pid) {
    global $conn;

    $sql = "INSERT INTO `Recipient Properties` VALUES ($aid, '$name', $pid, 0)";
    $result = mysqli_query($conn, $sql);

    return $result;
}

function get_recipient_properties($userid, $name) {
    global $conn;

    $sql = "SELECT `Recipient Properties`.Name, Property.`Property Name`, `Recipient Properties`.Active, `Recipient Properties`.PropertyID FROM `Recipient Properties`, Property WHERE `Recipient Properties`.PropertyID=Property.PropertyID AND `Recipient Properties`.AccountID=$userid AND `Recipient Properties`.Name='$name'";
    $result = mysqli_query($conn, $sql);

    return $result;
}

function set_notification_status($name, $pid, $status) {
    // status 0: both off
    // status 1: sms only
    // status 2: email only
    // status 3: both on

    global $conn;

    $sql = "UPDATE `Recipient Properties` SET Active=$status WHERE PropertyID=$pid AND Name='$name'";
    $result = mysqli_query($conn, $sql);

    return $result;
}

# checks if the Locked column is equal to 0, 0 meaning the account is not locked
function check_account_status($email) {
    global $conn;

    $sql = "SELECT Locked FROM Account WHERE Email ='$email'";
    $result = mysqli_query($conn, $sql);

    while ($row = mysqli_fetch_assoc($result)) {
        if($row["Locked"] == 0) {
            return true;
        }
        else{
            error_log("Account is locked");
            return false;
        }
    }

}

# TODO - test if this funtions properly
function reset_password($email, $oldpassword, $newpassword) {
    global $conn;

    $valid = check_validity($email, $oldpassword);

    if ($valid = true) {
        $sql = "UPDATE Account SET Password = '$newpassword";
        $result = mysqli_query($conn, $sql);
        error_log("Password updated");
        return $result;
    }
    else {
        error_log("Could not update password");
        return false;
    }


}

?>