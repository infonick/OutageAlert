<?php

# set initial view to startpage with signin dialog
if (!isset($_POST["page"])){
    include ("view_startpage.html");
    $display_type = "signin";
    exit();
}

require ('model.php');

session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST'){
    if ($_POST['page'] == 'StartPage'){
        $command = $_POST['command'];
        switch ($command){
            case 'SignIn':
                # check if valid signin information - if yes set session variables and redirect to main page
                if(check_validity($_POST['email'], $_POST['password'])){
                    # TODO: check if account is locked, set content to indicate this if so
                    $_SESSION['signedin'] = 'YES';
                    include('view_mainpage.html');
                }
                else{
                    $error_msg = 'Invalid email and/or password.';
                }
                exit();
            case 'Join':
                # TODO: do this check in startpage instead to update bootstrap invalid feedback div via ajax request
                if(check_existence($_POST['email'])){
                    $result = true;
                    echo $result;
                }
                else {
                    $result = false;
                    echo $result;
                }
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
