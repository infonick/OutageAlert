<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <!-- Bootstrap - Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- jQuery library -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <!-- Popper JS - Required for Bootstrap 4 -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <!-- Bootstrap - Latest compiled JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script async>
        $(document).ready(function () {
            $('#signupform').hide();
            $('#forgotpassform').hide();
            // show the create account form
            $('#create_account_btn').click(function () {
               $('#signinform').hide();
               $('#signupform').show();
            });
            // show the signin form
            $('#signin_btn').click(function () {
                $('#signupform').hide();
                $('#signinform').show();
            })
            // show or hide the password for the signin form
            $('#showpasslogin').click(function () {
                if ($(this).is(':checked')){
                    $('#signin-password').attr('type', 'text');
                }
                else{
                    $('#signin-password').attr('type', 'password');
                }
            });
            // show or hide the password for the join form
            $('#showpassjoin').click(function () {
                if ($(this).is(':checked')){
                    $('#join-password').attr('type', 'text');
                }
                else{
                    $('#join-password').attr('type', 'password');
                }
            });
            // submit given information for validation when clicking create account button
            $('#create-account').click(function () {
                checkCreateAccount();
                //$('#signupform').submit();
            });
            // submit given information for validation when clicking sign in button
            $('#sign-in-button').click(function () {
                checkSignIn();
            });
            // show the reset password form
            $('#forgotpasslink').click(function () {
                $('#signinform').hide();
                $('#signupform').hide();
                $('#forgotpassform').show();
            });
            $('#reset-password').click( function () {
                resetAccountPassword();
            });
            // close reset form, go to sign in form
            $('#reset_signin_btn').click(function () {
                $('#forgotpassform').hide();
                $('#signinform').show();
            });
            // close reset form, go to create account form
            $('#reset_create_account_btn').click(function () {
                $('#forgotpassform').hide();
                $('#signupform').show();
            });
            // clear error from signup form email input when changing the text
            $('#join-email').on('input', function () {
                var form = $('#signupform');
                let field = form.find('[name="email"]')
                if (field.hasClass("is-invalid")) {
                    field.removeClass("is-invalid");
                }
            });
            // clear error from signup form password input when changing the text
            $('#join-password').on('input', function () {
                var form = $('#signupform');
                let field = form.find('[name="password"]')
                if (field.hasClass("is-invalid")) {
                    field.removeClass("is-invalid");
                }
            });
            // clear error from signup form email input when changing the text
            $('#signin-email').on('input', function () {
                var form = $('#signinform');
                let field = form.find('[name="email"]')
                if (field.hasClass("is-invalid")) {
                    field.removeClass("is-invalid");
                }
            });
            // clear error from signup form password input when changing the text
            $('#signin-password').on('input', function () {
                var form = $('#signinform');
                let field = form.find('[name="password"]')
                if (field.hasClass("is-invalid")) {
                    field.removeClass("is-invalid");
                }
            });
        });
        // validates the email and password and if good submits the signup form
        // TODO: test this
        function checkCreateAccount() {
            var form = $('#signupform');
            var controller = "controller.php";
            var text = document.getElementById("join-email").value;
            var pass = document.getElementById("join-password").value;
            if (text == "") {
                let field = form.find('[name="email"]');
                field.addClass("is-invalid");
                document.getElementById("join-email-error").innerText = "Please enter an email address.";
                return false;
            } else if (!text.includes("@") || !text.includes(".")) {
                let field = form.find('[name="email"]');
                field.addClass("is-invalid");
                document.getElementById("join-email-error").innerText = "Please enter a valid email address.";
                return false;
            }
            if (pass == "") {
                let field = form.find('[name="password"]');
                field.addClass("is-invalid");
                document.getElementById("join-password-error").innerText = "Please enter a password.";
                return false;
            }
            $.post(controller,
                {
                    page: "StartPage", command: "JoinCheck", email: text, password: pass
                },
                function (result) {
                    let field = form.find('[name="email"]')
                    if (result == true) {
                        field.addClass("is-invalid")
                        // if already exists, set invalid feedback of div
                        document.getElementById("join-email-error").innerText = "An account under this email already exists."
                    } else {
                        $('#create-success').toast({delay:1000});
                        $('#create-success').toast('show');
                        // delay submitting form so the toast doesn't disappear immediately
                        setTimeout(function () {
                            $('#signupform').submit();
                        }, 1000);
                    }
                });
        }
        function checkSignIn() {
            var form = $('#signinform');
            var controller = "controller.php";
            var text = document.getElementById("signin-email").value;
            var pass = document.getElementById("signin-password").value;
            if (text == "") {
                let field = form.find('[name="email"]');
                field.addClass("is-invalid");
                document.getElementById("signin-email-error").innerText = "Please enter an email address.";
                return false;
            } else if (!text.includes("@") || !text.includes(".")) {
                let field = form.find('[name="email"]');
                field.addClass("is-invalid");
                document.getElementById("signin-email-error").innerText = "Please enter a valid email address.";
                return false;
            }
            if (pass == "") {
                let field = form.find('[name="password"]');
                field.addClass("is-invalid");
                document.getElementById("signin-password-error").innerText = "Please enter a password.";
                return false;
            }
            $.post(controller,
                {
                    page: "StartPage", command: "SignInCheck", email: text, password: pass
                },
                function (result) {
                    let field = form.find('[name="email"]')
                    let field2 = form.find('[name="password"]')
                    if (result == false) {
                        field.addClass("is-invalid")
                        field2.addClass("is-invalid")
                        // if already exists, set invalid feedback of div
                        document.getElementById("signin-email-error").innerText = "Invalid sign in information."
                        document.getElementById("signin-password-error").innerText = ""
                    } else {
                        $('#signin-success').toast({delay:1000});
                        $('#signin-success').toast('show');
                        setTimeout(function () {
                            $('#signinform').submit();
                        }, 1000);
                    }
                });
        }

        // Function to reset account password from startpage.
        function resetAccountPassword() {
            var form = $('#forgotpassform');
            var controller = "controller.php";
            var text = document.getElementById("reset-email").value;
            if (text == "") {
                let field = form.find('[name="email"]');
                field.addClass("is-invalid");
                field.removeClass("is-valid");
                document.getElementById("forgotpw-email-error").innerHTML = "Please enter an email address.";
                return false;
            } else if (!text.includes("@") || !text.includes(".")) {
                let field = form.find('[name="email"]');
                field.addClass("is-invalid");
                field.removeClass("is-valid");
                document.getElementById("forgotpw-email-error").innerHTML = "Please enter a valid email address.";
                return false;
            }

            $.post(controller,
                {
                    page: "StartPage", command: "ForgotPassword", email: text
                },
                function (result) {
                    let field = form.find('[name="email"]')
                    if (result == true) {
                        field.addClass("is-valid");
                        field.removeClass("is-invalid");
                        document.getElementById("forgotpw-email-ok").innerHTML = "Email sent - please check your inbox.";
                    } else {
                        field.addClass("is-invalid");
                        field.removeClass("is-valid");
                        document.getElementById("forgotpw-email-error").innerHTML = "ERROR - please check your email or <a href='mailto:aoutage@gmail.com'>contact the administrators</a> for assistance.";
                    }
                });
        }
            // TODO: sign in server-side validation
    </script>
    <title>Outage Alert</title>
</head>
<body>
<div class="container-fluid">
    <div class="row">

        <!-- TODO: add column sizes for small and large -->
        <div class="col-lg-6">
            <h2 style="padding: 25px">Outage Alert</h2>
            <h5 style="padding: 0 25px 0 25px">Email and SMS Notification Service for Power Outages in British Columbia</h5>
            <div style="text-align: center; padding-top: 150px">

                <!-- TODO: think up some better landing page text -->
                <p>Do you depend on constant power?</p>
                <p>Do you need to know immediately if</p>
                <p>the power goes out?</p>
                <p>Our service will notify you by text or</p>
                <p>email when a power outage is </p>
                <p>affecting your property.</p>
            </div>
        </div>
        <div class="col-lg-6" style="padding-top: 175px; padding-right: 100px">
                <form id="signinform" action="https://ec2-35-183-181-30.ca-central-1.compute.amazonaws.com/controller.php" method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>">
                <input type="hidden" name="page" value="StartPage">
                <h4>Sign In</h4>
                <div class="form-group">
                    <label for="email">Email Address:</label>
                    <input type="email" class="form-control" id="signin-email" name="email" placeholder="" required>
                    <div class="invalid-feedback" id="signin-email-error"></div>
                </div>
                <div class="form-group">
                    <label for="loginpassword">Password:</label>
                    <input type="password" class="form-control" id="signin-password" name="password" placeholder="" required>
                    <div class="invalid-feedback" id="signin-password-error"></div>

                    <!-- TODO: figure out how best to handle resetting a password. jquery, popup window, idk? -->
                    <a href="#" id="forgotpasslink">Forgot Your Password?</a>
                </div>
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="showpasslogin">
                    <label class="form-check-label" for="showpasslogin">Show Password</label>
                    <button type="button" class="btn btn-primary float-right" id="sign-in-button">Sign In</button>
                    <input type="hidden" name="command" value="SignIn">
                    <div class="toast text-white bg-success float-right" id="signin-success">
                        <div class="toast-header">Account Information Verified</div>
                        <div class="toast-body">Logging you in now.</div>
                    </div>
                </div>
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="rememberme" value="rememberme">
                    <label class="form-check-label" for="rememberme">Remember Me</label>
                </div>
                <hr/>
                <div class="form-group" style="text-align: center">
                    <p>Not Registered?</p>
                    <button type="button" class="btn btn-primary" id="create_account_btn">Create an Account</button>
                </div>
            </form>
            <form id="signupform" action="https://ec2-35-183-181-30.ca-central-1.compute.amazonaws.com/controller.php" method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>">
                <input type="hidden" name="page" value="StartPage">
                <h4>Create Account</h4>
                <div class="form-group">
                    <label for="email">Email Address:</label>
                    <input type="email" class="form-control" name="email" placeholder="" id="join-email" required>
                    <div class="invalid-feedback" id="join-email-error"></div>
                </div>
                <div class="form-group">
                    <label for="joinpassword">Password:</label>
                    <input type="password" class="form-control" id="join-password" name="password" placeholder="" required>
                    <div class="invalid-feedback" id="join-password-error"></div>
                </div>
                <div class="form-check" style="padding-bottom: 25px">
                    <input type="checkbox" class="form-check-input" name="showpass" id="showpassjoin">
                    <label class="form-check-label" for="showpassjoin">Show Password</label>
                    <button type="button" class="btn btn-primary float-right" id="create-account">Create Account</button>
                    <input type="hidden" name="command" value="Join">
                    <div class="toast text-white bg-success float-right" id="create-success">
                        <div class="toast-header">Account Created</div>
                        <div class="toast-body">Logging you in now.</div>
                    </div>
                </div>
                <hr/>
                <div class="form-group" style="text-align: center">
                    <p>Already Registered?</p>
                    <button type="button" class="btn btn-primary" id="signin_btn">Sign In</button>
                </div>
            </form>
            <!--TODO - update with proper URL -->
            <form id="forgotpassform" action="https://ec2-35-183-181-30.ca-central-1.compute.amazonaws.com/controller.php" method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>">
                <input type="hidden" name="page" value="StartPage">
                <h4>Reset Password</h4>
                <div class="form-group">
                    <label for="email">Email Address:</label>
                    <input type="email" class="form-control" id="reset-email" name="email" placeholder="" required>
                    <div class="invalid-feedback" id="forgotpw-email-error"></div>
                    <div class="valid-feedback" id="forgotpw-email-ok"></div>
                </div>
                <div class="form-group" style="padding-bottom: 50px">
                    <button type="button" class="btn btn-primary float-right" id="reset-password">Send Reset Request</button>
                    <input type="hidden" name="command" value="ResetPass">
                </div>
                <hr/>
                <div class="form-group" style="text-align: center">
                    <button type="button" class="btn btn-primary" id="reset_signin_btn">Sign In</button>
                    <button type="button" class="btn btn-primary" id="reset_create_account_btn">Create an Account</button>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>
