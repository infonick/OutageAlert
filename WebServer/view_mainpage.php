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
    <script>
        // hide the create account form on initial load (done using regular javascript to prevent flickering)
        // probably a better way to do this. still minor flickering. should look into that
        window.addEventListener("load", function () {
            loadProperties();
        });
    </script>
    <script>
        $(document).ready(function () {
            // validates, submits, and resets the new property form
            $('#nproperty-button').click(function () {
                checkNewProperty();
                setTimeout(function () {
                    document.getElementById("new-property-form").reset();
                }, 1100);
            });
            // resets the new property form on clicking cancel
            $('#nproperty-cancel').click(function () {
                document.getElementById("new-property-form").reset();
            });
            // clears errors from input when changing it
            $('#property-name').on('input', function () {
                var form = $('#new-property-form');
                let field = form.find('[name="property-name"]')
                if (field.hasClass("is-invalid")) {
                    field.removeClass("is-invalid");
                }
            });
            $('#address').on('input', function () {
                var form = $('#new-property-form');
                let field = form.find('[name="address"]')
                if (field.hasClass("is-invalid")) {
                    field.removeClass("is-invalid");
                }
            });
            $('#eproperty-button').click(function () {
                checkEditProperty();
                setTimeout(function () {
                    $('#edit-property').hide();
                    document.getElementById("edit-property-form").reset();
                }, 1100);
            });
            // resets the new property form on clicking cancel
            $('#eproperty-cancel').click(function () {
                $('#edit-property').hide();
                document.getElementById("edit-property-form").reset();
            });
            // clears errors from input when changing it
            $('#eproperty-name').on('input', function () {
                var form = $('#edit-property-form');
                let field = form.find('[name="property-name"]')
                if (field.hasClass("is-invalid")) {
                    field.removeClass("is-invalid");
                }
            });
            $('#eaddress').on('input', function () {
                var form = $('#edit-property-form');
                let field = form.find('[name="address"]')
                if (field.hasClass("is-invalid")) {
                    field.removeClass("is-invalid");
                }
            });
            // validates, submits, and resets the new notification form
            $('#nrecipient-button').click(function () {
               checkNewRecipient();
                document.getElementById("new-notification-form").reset();
            });
            // resets the new notification form on clicking cancel
            $('#nrecipient-cancel').click(function () {
                document.getElementById("new-notification-form").reset();
            });
            // clears errors from input when changing it
            $('#recipient-name').on('input', function () {
                var form = $('#new-notification-form');
                let field = form.find('[name="recipient-name"]')
                if (field.hasClass("is-invalid")) {
                    field.removeClass("is-invalid");
                }
            });
            $('#email').on('input', function () {
                var form = $('#new-notification-form');
                let field = form.find('[name="email"]')
                if (field.hasClass("is-invalid")) {
                    field.removeClass("is-invalid");
                }
            });
            $('#phone-number').on('input', function () {
                var form = $('#new-notification-form');
                let field = form.find('[name="phone-number"]')
                if (field.hasClass("is-invalid")) {
                    field.removeClass("is-invalid");
                }
            });
        });
        // TODO - better method for adding address. google places api?
        function checkNewProperty() {
            var form = $('#new-property-form');
            var controller = "controller.php";
            var name = document.getElementById("property-name").value;
            var address = document.getElementById("address").value;
            if (name == "") {
                let field = form.find('[name="property-name"]');
                field.addClass("is-invalid");
                document.getElementById("pname-error").innerText = "Please enter a name.";
                return false;
            }
            if (address == "") {
                let field = form.find('[name="address"]');
                field.addClass("is-invalid");
                document.getElementById("address-error").innerText = "Please enter an address.";
                return false;
            }
            $.post(controller,
                {
                    page: "MainPage", command: "NewProperty", name: name, address: address
                },
                function (result) {
                    $('#nproperty-success').toast({delay:1000});
                    $('#nproperty-success').toast('show');
                    loadProperties();
                    setTimeout(function () {
                        $('#new-property').modal('hide');
                    }, 1000);
                });
        }
        function checkEditProperty() {
            var form = $('#edit-property-form');
            var controller = "controller.php";
            var name = document.getElementById("eproperty-name").value;
            var address = document.getElementById("eaddress").value;
            if (name == "") {
                let field = form.find('[name="property-name"]');
                field.addClass("is-invalid");
                document.getElementById("epname-error").innerText = "Please enter a name.";
                return false;
            }
            if (address == "") {
                let field = form.find('[name="address"]');
                field.addClass("is-invalid");
                document.getElementById("eaddress-error").innerText = "Please enter an address.";
                return false;
            }
            $.post(controller,
                {
                    page: "MainPage", command: "EditProperty", name: name, address: address, oname: originalName, oaddress: originalAddress
                },
                function (result) {
                    $('#eproperty-success').toast({delay:1000});
                    $('#eproperty-success').toast('show');
                    loadProperties();
                    setTimeout(function () {
                        $('#new-property').modal('hide');
                    }, 1000);
                });
        }
        function checkNewRecipient() {
            var form = $('#new-notification-form');
            var controller = "controller.php";
            var name = document.getElementById("recipient-name").value;
            var email = document.getElementById("email").value;
            var pnumber = document.getElementById("phone-number").value;
            if (name == "") {
                let field = form.find('[name="recipient-name"]');
                field.addClass("is-invalid");
                document.getElementById("rname-error").innerText = "Please enter a name.";
                return false;
            }
            if (email == "" && pnumber == "") {
                let field = form.find('[name="email"]');
                let field2 = form.find('[name="phone-number"]');
                field.addClass("is-invalid");
                field2.addClass("is-invalid");
                document.getElementById("email-error").innerText = "Please enter an email or phone number.";
                return false;
            }
            // only need an email or a phone number, so we check if they are not empty before validating
            if (email != "" && (!email.includes("@") || !email.includes("."))) {
                let field = form.find('[name="email"]');
                field.addClass("is-invalid");
                document.getElementById("email-error").innerText = "Please enter a valid email address.";
                return false;
            }
            if (pnumber != "" && pnumber.length != 10) {
                let field = form.find('[name="phone-number"]');
                field.addClass("is-invalid");
                document.getElementById("pnumber-error").innerText = "Please enter a 10-digit phone number.";
                return false;
            }
            $.post(controller,
                {
                    page: "MainPage", command: "NewRecipient", name: name, email: email, pnumber: pnumber
                },
                function (result) {
                    $('#nrecipient-success').toast({delay:1000});
                    $('#nrecipient-success').toast('show');
                    setTimeout(function () {
                        $('#new-notification').modal('hide');
                    }, 1000);
                });
        }
        function createPropertiesTable(jsonArray) {
            var obj = JSON.parse(jsonArray);
            var table = "<table class='table table-borderless'><thead class='thead-dark'><tr><th>Property Name</th><th>Address</th><th></th></tr></thead><tbody>";
            for (var i = 0; i < obj.length; i++) {
                table += "<tr>";
                var col = 0
                for (var j in obj[i]) {
                    table += "<td>" + (obj[i])[j] + "</td>";
                    if (col == 0) {
                        item = (obj[i])[j];
                        col = 1;
                    }
                    else {
                        item2 = (obj[i])[j];
                    }
                }
                col = 0;
                table += `<td><button type='button' class='btn btn-secondary' onclick='editProperty(\"${item}\", \"${item2}\")'>Edit</button></td></tr>`;
            }
            table += "</tbody></table>";
            return table;
        }
        function loadProperties() {
            var controller = "controller.php";
            $.post(controller,
                {page: "MainPage", command: "GetProperties"},
                function(result){
                    var table = createPropertiesTable(result);
                    document.getElementById("properties-pane").innerHTML = table;
                });
        }
        var originalName = "";
        var originalAddress = "";
        function editProperty(propertyName, address) {
            originalName = propertyName;
            originalAddress = address;
            document.getElementById("eproperty-name").value = propertyName;
            document.getElementById("eaddress").value = address;
            $('#edit-property').show();
        }
    </script>
    <title>Outage Alert</title>
</head>
<body>
    <div class="container-fluid">
        <nav class="navbar navbar-expand-sm bg-dark navbar-dark justify-content-between">
            <a class="navbar-brand">Outage Alert</a>
            <form class="form-inline" action="https://ec2-35-183-181-30.ca-central-1.compute.amazonaws.com/controller.php" method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>">
                <span class="navbar-text"><?php echo $_SESSION['email']?></span>
                <button class="btn btn-dark" type="submit">Sign Out</button>
                <input type="hidden" name="page" value="MainPage">
                <input type="hidden" name="command" value="SignOut">
            </form>
        </nav>
        <div class="row">
            <h4 style="padding-top: 100px; padding-left: 100px">Properties</h4>
        </div>
        <!-- div to hold the properties, to be set by calls to the controller -->
        <div class="row" id="properties-pane">

        </div>
        <div class="row" style="padding-left: 100px">
            <button class="btn btn-secondary" data-toggle="modal" data-target="#new-property">Add New Property</button>
        </div>
        <div class="modal" id="new-property">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">New Property</h4>
                    </div>
                    <div class="modal-body">
                        <form id="new-property-form" action="https://ec2-35-183-181-30.ca-central-1.compute.amazonaws.com/controller.php" method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>">
                            <div class="form-group">
                                <label for="property-name">Property Name:</label>
                                <input type="text" class="form-control" id="property-name" name="property-name" value="property-name" required>
                                <div class="invalid-feedback" id="pname-error"></div>
                            </div>
                            <div class="form-group">
                                <label for="address">Address:</label>
                                <input type="text" class="form-control" id="address" name="address" value="address" required>
                                <div class="invalid-feedback" id="address-error"></div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger mr-auto" data-dismiss="modal" id="nproperty-cancel">Cancel</button>
                        <button type="button" class="btn btn-success" id="nproperty-button">Add</button>
                        <div class="toast text-white bg-success float-right" id="nproperty-success">
                            <div class="toast-body">New property added.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal" id="edit-property">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Edit Property</h4>
                    </div>
                    <div class="modal-body">
                        <form id="edit-property-form" action="https://ec2-35-183-181-30.ca-central-1.compute.amazonaws.com/controller.php" method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>">
                            <div class="form-group">
                                <label for="eproperty-name">Property Name:</label>
                                <input type="text" class="form-control" id="eproperty-name" name="property-name" value="property-name" required>
                                <div class="invalid-feedback" id="epname-error"></div>
                            </div>
                            <div class="form-group">
                                <label for="eaddress">Address:</label>
                                <input type="text" class="form-control" id="eaddress" name="address" value="address" required>
                                <div class="invalid-feedback" id="eaddress-error"></div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger mr-auto" data-dismiss="modal" id="eproperty-cancel">Cancel</button>
                        <button type="button" class="btn btn-success" id="eproperty-button">Submit</button>
                        <div class="toast text-white bg-success float-right" id="eproperty-success">
                            <div class="toast-body">Property details changed.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <h4 style="padding-top: 100px; padding-left: 100px">Notifications</h4>
        </div>
        <div class="row" id="recipients-pane">

        </div>
        <div class="row" style="padding-left: 100px">
            <button class="btn btn-secondary" data-toggle="modal" data-target="#new-notification">Add New Notification</button>
        </div>
        <div class="modal" id="new-notification">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">New Notification</h4>
                    </div>
                    <div class="modal-body">
                        <form id="new-notification-form" action="https://ec2-35-183-181-30.ca-central-1.compute.amazonaws.com/controller.php" method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>">
                            <div class="form-group">
                                <label for="recipient-name">Recipient Name:</label>
                                <input type="text" class="form-control" id="recipient-name" name="recipient-name" value="recipient-name">
                                <div class="invalid-feedback" id="rname-error"></div>
                            </div>
                            <div class="form-group">
                                <label for="email">Email Address:</label>
                                <input type="email" class="form-control" id="email" name="email" value="email" required>
                                <div class="invalid-feedback" id="email-error"></div>
                            </div>
                            <div class="form-group">
                                <label for="phone-number">Phone Number:</label>
                                <input type="number" class="form-control" id="phone-number" name="phone-number">
                                <div class="invalid-feedback" id="pnumber-error"></div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger mr-auto" data-dismiss="modal" id="nrecipient-cancel">Cancel</button>
                        <button type="button" class="btn btn-success" id="nrecipient-button">Add</button>
                        <div class="toast text-white bg-success float-right" id="nrecipient-success">
                            <div class="toast-body">New recipient added.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>