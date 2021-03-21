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
    <script src="https://maps.googleapis.com/maps/api/js?libraries=places&callback=initAutocomplete&language=nl&output=json&key=AIzaSyD-bcVR4yqiiFJDknvl8RG6wu63KGNst00" async defer></script>
    <style>
        .pac-container {
            background-color: #FFF;
            z-index: 20;
            position: fixed;
            display: inline-block;
            float: left;
        }
        .modal{
            z-index: 20;
        }
        .modal-backdrop{
            z-index: 10;
        }
    </style>
    <script>
        // hide the create account form on initial load (done using regular javascript to prevent flickering)
        // probably a better way to do this. still minor flickering. should look into that
        window.addEventListener("load", function () {
            loadProperties();
            loadRecipients();
        });
    </script>
    <script>
        $(document).ready(function () {
            // validates, submits, and resets the new property form
            $('#nproperty-button').click(function () {
                checkNewProperty();
                setTimeout(function () {
                    document.getElementById("new-property-form").reset();
                }, 1000);
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
                }, 1000);
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
                setTimeout(function () {
                    document.getElementById("new-property-form").reset();
                }, 1000);
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
            // validates, submits, and resets the edit notification form
            $('#erecipient-button').click(function () {
                checkEditRecipient();
                setTimeout(function () {
                    $('#edit-notification').hide();
                    document.getElementById("edit-notification-form").reset();
                }, 1100);
            });
            // resets the edit notification form on clicking cancel
            $('#erecipient-cancel').click(function () {
                $('#edit-notification').hide();
                document.getElementById("edit-notification-form").reset();
            });
            // clears errors from input when changing it
            $('#erecipient-name').on('input', function () {
                var form = $('#edit-notification-form');
                let field = form.find('[name="erecipient-name"]')
                if (field.hasClass("is-invalid")) {
                    field.removeClass("is-invalid");
                }
            });
            $('#eemail').on('input', function () {
                var form = $('#edit-notification-form');
                let field = form.find('[name="eemail"]')
                if (field.hasClass("is-invalid")) {
                    field.removeClass("is-invalid");
                }
            });
            $('#ephone-number').on('input', function () {
                var form = $('#edit-notification-form');
                let field = form.find('[name="ephone-number"]')
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
                    page: "MainPage", command: "NewProperty", name: name, address: address, lat: latitude, lon: longitude
                },
                function (result) {
                    $('#nproperty-success').toast({delay:1000});
                    $('#nproperty-success').toast('show');
                    loadProperties();
                    loadRecipients();
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
                    page: "MainPage", command: "EditProperty", name: name, address: address, oname: originalName, oaddress: originalAddress, lat: latitude, lon: longitude
                },
                function (result) {
                    $('#eproperty-success').toast({delay:1000});
                    $('#eproperty-success').toast('show');
                    loadProperties();
                    loadRecipients();
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
                    loadRecipients();
                    setTimeout(function () {
                        $('#new-notification').modal('hide');
                    }, 1000);
                });
        }

        function checkEditRecipient() {
            var form = $('#edit-notification-form');
            var controller = "controller.php";
            var name = document.getElementById("erecipient-name").value;
            var email = document.getElementById("eemail").value;
            var pnumber = document.getElementById("ephone-number").value;
            if (name == "") {
                let field = form.find('[name="erecipient-name"]');
                field.addClass("is-invalid");
                document.getElementById("ername-error").innerText = "Please enter a name.";
                return false;
            }
            if (email == "" && pnumber == "") {
                let field = form.find('[name="eemail"]');
                let field2 = form.find('[name="ephone-number"]');
                field.addClass("is-invalid");
                field2.addClass("is-invalid");
                document.getElementById("eemail-error").innerText = "Please enter an email or phone number.";
                return false;
            }
            // only need an email or a phone number, so we check if they are not empty before validating
            if (email != "" && (!email.includes("@") || !email.includes("."))) {
                let field = form.find('[name="eemail"]');
                field.addClass("is-invalid");
                document.getElementById("eemail-error").innerText = "Please enter a valid email address.";
                return false;
            }
            if (pnumber != "" && pnumber.length != 10) {
                let field = form.find('[name="ephone-number"]');
                field.addClass("is-invalid");
                document.getElementById("epnumber-error").innerText = "Please enter a 10-digit phone number.";
                return false;
            }
            $.post(controller,
                {
                    page: "MainPage", command: "EditRecipient", name: name, email: email, pnumber: pnumber, oname: originalName, oemail: originalEmail, onumber: originalPhone
                },
                function (result) {
                    $('#erecipient-success').toast({delay:1000});
                    $('#erecipient-success').toast('show');
                    loadRecipients();
                    setTimeout(function () {
                        $('#edit-notification').modal('hide');
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
                        item = (obj[i])[j]; // property name
                        col = 1;
                    }
                    else {
                        item2 = (obj[i])[j]; // property address
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

        var originalName = ""; // global variable to hold property's original name for update condition
        var originalAddress = ""; // global variable to hold property's original address for update condition
        function editProperty(propertyName, address) {
            originalName = propertyName;
            originalAddress = address;
            document.getElementById("eproperty-name").value = propertyName;
            document.getElementById("eaddress").value = address;
            $('#edit-property').show();
        }

        var recipientNames = []; // global variable to hold recipient names for creating properties subtables
        function createRecipientsTable(jsonArray) {
            var obj = JSON.parse(jsonArray);
            var table = "<table class='table table-striped'><thead class='thead-dark'><tr><th>Recipient Name</th><th>Phone Number</th><th>Email Address</th><th></th></tr></thead><tbody>";
            for (var i = 0; i < obj.length; i++) {
                table += `<tr onclick="getElementById('entry${i}').click()" style="cursor: pointer">`;
                var col = 0
                for (var j in obj[i]) {
                    if (col == 0) {
                        item = (obj[i])[j];
                        recipientNames.push(item);
                        col = 1;
                    }
                    else if (col == 1) {
                        item2 = (obj[i])[j];
                        if (item2 == "") {item2 = "-";}
                        col = 2;
                    }
                    else if (col == 2) {
                        item3 = (obj[i])[j];
                        if (item2 == "") {item2 = "-";}
                    }
                    table += "<td>" + (obj[i])[j] + "</td>";
                }
                col = 0;
                var ptable = loadPropertiesSubtables(item);
                table += `<td><button type='button' class='btn btn-secondary' onclick='editRecipient(\"${item}\", \"${item2}\", \"${item3}\")'>Edit</button><button type='button' data-toggle='collapse' id='entry${i}' style='visibility:hidden' data-target='#row${i}'></button></td></tr><tr id='row${i}' class='collapse'><td colspan='4'><div id='props${i}'>${ptable}</div></td></tr>`;
            }
            table += "</tbody></table>";
            return table;
        }

        var tableNumber = 0; // global variable to hold subtable number, used for getting div id to update subtables when changing notification status
        function createPropertiesSubtable(jsonArray, uname) {
            tableNumber = 0;
            var obj = JSON.parse(jsonArray);
            var table = "<table class='table table-borderless'><thead class='thead-dark'><tr><th>Property Name</th><th>Send SMS</th><th>Send Email</th></tr></thead><tbody>";
            for (var i = 0; i < obj.length; i++) {
                if (obj[i].Name == uname) {
                    table += "<tr id='checkboxrow${i}'>";
                    var col = 0;
                        for (var j in obj[i]) {

                                if (col == 0) {
                                    item = (obj[i])[j]; // recipient name
                                }
                                else if (col == 1) {
                                    item2 = (obj[i])[j];
                                    table += "<td>" + item2 + "</td>"; // property name
                                }
                                else if (col == 2) {
                                    item4 = (obj[i])[j]; // notification status
                                }
                                else if (col == 3) {
                                    item3 = (obj[i])[j]; // property id
                                }
                            col++;
                            }

                    // if both sms and email are off, set checked value and functions accordingly
                    if (item4 == 0) {
                        table += `<td><input type='checkbox' value='' onclick='changeNotificationStatus(\"${item}\", \"${item3}\", 1, ${tableNumber})'></td><td><input type='checkbox' value='' onclick='changeNotificationStatus(\"${item}\", \"${item3}\", 2, ${tableNumber})'></td></tr>`;
                    }
                    // if sms is on and email is off
                    else if (item4 == 1) {
                        table += `<td><input type='checkbox' value='' onclick='changeNotificationStatus(\"${item}\", \"${item3}\", 0, ${tableNumber})' checked></td><td><input type='checkbox' onclick='changeNotificationStatus(\"${item}\", \"${item3}\", 3, ${tableNumber})' value=''></td></tr>`;
                    }
                    // if sms is off and email is on
                    else if (item4 == 2) {
                        table += `<td><input type='checkbox' onclick='changeNotificationStatus(\"${item}\", \"${item3}\", 3, ${tableNumber})' value=''></td><td><input type='checkbox' value='' onclick='changeNotificationStatus(\"${item}\", \"${item3}\", 0,  ${tableNumber})' checked></td></tr>`;
                    }
                    // if both sms and email are on
                    else if (item4 == 3) {
                        table += `<td><input type='checkbox' value='' onclick='changeNotificationStatus(\"${item}\", \"${item3}\", 2, ${tableNumber})' checked></td><td><input type='checkbox' value='' onclick='changeNotificationStatus(\"${item}\", \"${item3}\", 1, ${tableNumber})' checked></td></tr>`;
                    }
                    tableNumber++;
                }
            }
            table += "</tbody></table>";
            return table;
        }

        var counter = 0; // number corresponding to recipients, used to correctly create corresponding subtable
        function loadPropertiesSubtables(uname) {
            var controller = "controller.php";
            $.post(controller,
                {page: "MainPage", command: "GetRecipientProperties", name: uname},
                function (result) {
                    var table = createPropertiesSubtable(result, recipientNames[counter]);
                    document.getElementById("props" + counter).innerHTML = table;
                    counter++;
                });
        }

        // reloads a single subtable when changing notification
        function reloadPropertiesSubtable(uname, divNumber) {
            var controller = "controller.php";
            $.post(controller,
                {page: "MainPage", command: "GetRecipientProperties", name: uname},
                function(result){
                    var table = createPropertiesSubtable(result, uname);
                    document.getElementById("props" + divNumber).innerHTML = table;
                });
        }

        function loadRecipients() {
            var controller = "controller.php";
            $.post(controller,
                {page: "MainPage", command: "GetRecipients"},
                function(result){
                    var table = createRecipientsTable(result);
                    document.getElementById("recipients-pane").innerHTML = table;
                });
        }


        function changeNotificationStatus(name, pid, status, parentDiv) {
            var controller = "controller.php";
            $.post(controller,
                {page: "MainPage", command: "ChangeNotificationStatus", name: name, pid: pid, status: status},
                function(result){
                    reloadPropertiesSubtable(name, parentDiv);
                });
        }

        var originalRName = "";
        var originalPhone = 0;
        var originalEmail = ""
        function editRecipient(name, number, email) {
            originalRName = name;
            originalPhone = number;
            originalEmail = email;
            document.getElementById("erecipient-name").value = name;
            document.getElementById("ephone-number").value = number;
            document.getElementById("eemail").value = email;
            $('#edit-notification').show();
        }

        var latitude = 0.0;
        var longitude = 0.0;
        //
        function initAutocomplete() {
            var addressinput = document.getElementById("address");
            var eaddressinput = document.getElementById("eaddress");
            var options = {
                types: ['address'], // return address information only
                componentRestrictions: {country: ['ca']} // restrict searches to canada
            };
            var autocomplete = new google.maps.places.Autocomplete(addressinput, options);
            var eautocomplete = new google.maps.places.Autocomplete(eaddressinput, options);
            google.maps.event.addListener(autocomplete, 'place_changed', function() {
                var place = autocomplete.getPlace();
                latitude = place.geometry.location.lat(); // get latitude of address
                longitude = place.geometry.location.lng(); // get longitude of address
            });
            google.maps.event.addListener(eautocomplete, 'place_changed', function() {
                var place = eautocomplete.getPlace();
                latitude = place.geometry.location.lat();
                longitude = place.geometry.location.lng();
            });
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
                                <input type="text" class="form-control" id="property-name" name="property-name" placeholder="property-name" required>
                                <div class="invalid-feedback" id="pname-error"></div>
                            </div>
                            <div class="form-group">
                                <label for="address">Address:</label>
                                <input type="text" class="form-control" id="address" name="address" placeholder="address" required>
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
                                <input type="text" class="form-control" id="eproperty-name" name="property-name" placeholder="property-name" required>
                                <div class="invalid-feedback" id="epname-error"></div>
                            </div>
                            <div class="form-group">
                                <label for="eaddress">Address:</label>
                                <input type="text" class="form-control" id="eaddress" name="address" placeholder="address" required>
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
            <h4 style="padding-top: 100px; padding-left: 100px">Notifications (Click Recipient to Expand Settings)</h4>
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
                                <input type="text" class="form-control" id="recipient-name" name="recipient-name" placeholder="recipient-name">
                                <div class="invalid-feedback" id="rname-error"></div>
                            </div>
                            <div class="form-group">
                                <label for="email">Email Address:</label>
                                <input type="email" class="form-control" id="email" name="email" placeholder="email" required>
                                <div class="invalid-feedback" id="email-error"></div>
                            </div>
                            <div class="form-group">
                                <label for="phone-number">Phone Number:</label>
                                <input type="tel" class="form-control" id="phone-number" name="phone-number" placeholder="123-456-7890" pattern="[0-9]{3}-[0-9]{3}-[0-9]{4}">
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
        <div class="modal" id="edit-notification">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Edit Notification</h4>
                    </div>
                    <div class="modal-body">
                        <form id="edit-notification-form" action="https://ec2-35-183-181-30.ca-central-1.compute.amazonaws.com/controller.php" method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>">
                            <div class="form-group">
                                <label for="erecipient-name">Recipient Name:</label>
                                <input type="text" class="form-control" id="erecipient-name" name="recipient-name" placeholder="recipient-name">
                                <div class="invalid-feedback" id="ername-error"></div>
                            </div>
                            <div class="form-group">
                                <label for="eemail">Email Address:</label>
                                <input type="email" class="form-control" id="eemail" name="email" placeholder="email" required>
                                <div class="invalid-feedback" id="eemail-error"></div>
                            </div>
                            <div class="form-group">
                                <label for="ephone-number">Phone Number:</label>
                                <input type="tel" class="form-control" id="ephone-number" name="phone-number" placeholder="123-456-7890" pattern="[0-9]{3}-[0-9]{3}-[0-9]{4}">
                                <div class="invalid-feedback" id="epnumber-error"></div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger mr-auto" data-dismiss="modal" id="erecipient-cancel">Cancel</button>
                        <button type="button" class="btn btn-success" id="erecipient-button">Add</button>
                        <div class="toast text-white bg-success float-right" id="erecipient-success">
                            <div class="toast-body">Recipient details updated.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>