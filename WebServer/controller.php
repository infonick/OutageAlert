<?php

# set initial view to startpage with signin dialog
if (!isset($_POST["page"])){
    include ("view_startpage.php");
    $display_type = "signin";
    $error_msg = "testing";
    exit();
}

require ('model.php');

session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST'){
    if ($_POST['page'] == 'StartPage'){
        $command = $_POST['command'];
        switch ($command){
            # server-side validation from startpage via ajax post
            # echoed result is used for post callback function
            case 'SignInCheck':
                if(!check_validity($_POST['email'], $_POST['password'])){
                    $result = false;
                    echo $result;
                }
                else {
                    $result = true;
                    echo $result;
                }
                exit();
            case 'SignIn':
                # TODO: check if account is locked, set content to indicate this if so
                $_SESSION['signedin'] = 'YES';
                $_SESSION['email'] = $_POST['email'];
                $_SESSION['userid'] = get_user_id($_SESSION['email']);
                include('view_mainpage.php');

                exit();
            case 'JoinCheck':
                if(check_existence($_POST['email'])){
                    $result = true;
                    echo $result;
                }
                else {
                    $result = false;
                    echo $result;
                }
                exit();
            case 'Join':
                # TODO: do this check in startpage instead to update bootstrap invalid feedback div via ajax request
                new_account($_POST['email'], $_POST['password']);
                $_SESSION['signedin'] = 'YES';
                $_SESSION['email'] = $_POST['email'];
                $_SESSION['userid'] = get_user_id($_SESSION['email']);
                include('view_mainpage.php');

                exit();
            case 'ForgotPassword':
                # TODO: set up some sort of email based reset password configuration
                exit();
        }
    }
    elseif ($_POST['page'] == 'MainPage'){
        if (!isset($_SESSION['signedin'])) {
            $display_type = "none";
            include "view_startpage.php";
            exit();
        }
        $command = $_POST['command'];
        switch ($command){
            case 'SignOut':
                session_unset();
                session_destroy();
                include "view_startpage.php";
                exit();
            case 'NewProperty':
                // lat and long were just set at 150 to test
                new_property($_POST['property-name'], $_POST['address'], $_SESSION['userid'], 150, 150);
                //create_recipient_properties($_SESSION['userid']);
                exit();
            case 'EditProperty':
                exit();
            case 'NewRecipient':
                new_recipient($_POST['name'], $_POST['pnumber'], $_POST['email'], $_SESSION['userid']);
                exit();
            case 'EditRecipient':
                exit();
            case 'PhoneNotification':
                exit();
            case 'EmailNotification':
                exit();
        }
    }
}

?>
