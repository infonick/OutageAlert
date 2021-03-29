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
                if(check_existence($_POST['email'])){
                    include('model_passreset.php');
                    $newpass = tempPassword($_POST['email']);
                    $passreset = passwordResetEmail($_POST['email'], $newpass);
                    if $passreset {
                        echo blind_reset_password($_POST['email'], $newpass);
                    }
                    else {
                        echo false;
                    }
                }
                else {
                    echo false;
                }
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
                # TODO - get actual latitude and longitude
                new_property($_POST['name'], $_POST['address'], $_SESSION['userid'], $_POST['lat'], $_POST['lon']);
                exit();
            case 'EditProperty':
                edit_property($_POST['name'], $_POST['address'], $_POST['oname'], $_POST['oaddress'], $_POST['lat'], $_POST['lon'], $_SESSION['userid']);
                exit();
            case 'GetProperties':
                $sproperties = get_properties($_SESSION['userid']);
                $properties = array();
                $i = 0;
                // create array of the rows of the query result
                while($row = mysqli_fetch_assoc($sproperties)){
                    $properties[$i++] = $row;
                }
                // encode this array as json to be parsed in mainpage
                $places = json_encode($properties);
                // send this json to the mainpage
                echo $places;
                exit();
            case 'NewRecipient':
                new_recipient($_POST['name'], $_POST['pnumber'], $_POST['provider'], $_POST['email'], $_SESSION['userid']);
                exit();
            case 'EditRecipient':
                edit_recipient($_POST['name'], $_POST['pnumber'], $_POST['provider'], $_POST['email'], $_POST['oname'], $_POST['onumber'], $_POST['oprovider'], $_POST['oemail'], $_SESSION['userid']);
                exit();
            case 'GetRecipients':
                $srecipients = get_recipients($_SESSION['userid']);
                $recipients = array();
                $i = 0;
                while($row = mysqli_fetch_assoc($srecipients)){
                    $recipients[$i++] = $row;
                }
                $people = json_encode($recipients);
                echo $people;
                exit();
            case 'GetRecipientProperties':
                // just a test for now with simpler parameters
                $sproperties = get_recipient_properties($_SESSION['userid'], $_POST['name']);
                $properties = array();
                $i = 0;
                while($row = mysqli_fetch_assoc($sproperties)){
                    $properties[$i++] = $row;
                }
                $places = json_encode($properties);
                echo $places;
                exit();
            case 'ChangeNotificationStatus':
                set_notification_status($_POST['name'], $_POST['pid'], $_POST['status']);
                exit();
        }
    }
}

?>
