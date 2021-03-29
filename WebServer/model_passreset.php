<?php
//Load controller, if not already done so.
if (!@include_once('controller.php'))
	exit();


// Create a randomized temporary password between 10 and 13 characters long
function tempPassword($email) {

    $salt = date("Y.m.d.l").date("H:i:sa").random_int(0,32000);

    $hash = hash("SHA256", $email.$salt);
    
    $out = substr($hash, 0, random_int(10, 13));

    return $out;
}


// Send email to user with newly reset password
function passwordResetEmail($email, $newPassword) {

    $subject = "OutageAlert Password Reset";
    $txt = "A request to reset your OutageAlert password has been received. Please use the following password to log in: " . $newPassword;
    $headers = "From: aoutage@gmail.com";

    $success = false;
    if (mail($email,$subject,$txt,$headers)){
        //print("success!");
        $success = true;
    }
    else {
        //print("FAIL!");
    }
    
    return $success;
}


?>