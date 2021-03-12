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
            case 'SignInCheck':
                if(!check_validity($_POST['email'], $_POST['password'])){
                    # TODO: check if account is locked, set content to indicate this if so
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
                include('view_mainpage.php');

                exit();
            case 'ForgotPassword':
                # TODO: set up some sort of email based reset password configuration
                exit();
        }
    }
    elseif ($_POST['page'] == 'MainPage'){
        $command = $_POST['command'];
        switch ($command){
            case 'SignOut':
                exit();
            case 'NewProperty':
                exit();
            case 'EditProperty':
                exit();
            case 'NewRecipient':
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
