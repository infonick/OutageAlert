<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <!-- Bootstrap - Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- jQuery library -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <!-- Popper JS - Required for Bootstrap 4 -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <!-- Bootstrap - Latest compiled JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?libraries=places&callback=initAutocomplete&language=nl&output=json&key=AIzaSyDucgnJ1zvFoS84k88hhjB58MKFO-qXFas" async defer></script>
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
    <script defer>
        $(document).ready(function () {
            recipientNames = [];
            tableNumber = 0;
            loadProperties();
            loadRecipients();
            $('[data-toggle="tooltip"]').tooltip();
            // validates, submits, and resets the new property form
            $('#nproperty-button').click(function () {
                if (checkNewProperty() != false) {
                    setTimeout(function () {
                        document.getElementById("new-property-form").reset();
                    }, 1000);
                }
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
                if (checkEditProperty() != false) {
                    setTimeout(function () {
                        $('#edit-property').hide();
                        document.getElementById("edit-property-form").reset();
                    }, 1000);
                }
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
                if (checkNewRecipient() != false) {
                    setTimeout(function () {
                        document.getElementById("new-notification-form").reset();
                    }, 1000);
                }
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
                if (checkEditRecipient() != false) {
                    setTimeout(function () {
                        $('#edit-notification').hide();
                        document.getElementById("edit-notification-form").reset();
                    }, 1100);
                }
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
            // processes the 'change user password' form or clears the form when cancelled
            $('#cnguserpass-button').click(function () {
                changeUserPassword();
            });
            $('#cnguserpass-cancel').click(function () {
                $('#changeUserPasswordModal').modal('hide');
                document.getElementById("change-user-password-form").reset();
                $('#cnguserpass-old').removeClass("is-invalid");
                $('#cnguserpass-old').removeClass("is-valid");
                $('#cnguserpass-new').removeClass("is-invalid");
                $('#cnguserpass-new').removeClass("is-valid");
            });
        });

        // TODO - Undefined error when adding a new property in recipient properties subtable
        // TODO - Bug. Can't have duplicate property names
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
                    recipientNames = [];
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
                $('#eproperty-name').addClass("is-invalid");
                document.getElementById("epname-error").innerText = "Please enter a name.";
                return false;
            }
            if (address == "") {
                $('#eaddress').addClass("is-invalid");
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
                    recipientNames = [];
                    loadRecipients();
                    setTimeout(function () {
                        $('#new-property').modal('hide');
                    }, 1000);
                });
        }

        // TODO -
        function checkNewRecipient() {
            var form = $('#new-notification-form');
            var controller = "controller.php";
            var name = document.getElementById("recipient-name").value;
            var email = document.getElementById("email").value;
            var pnumber = document.getElementById("phone-number").value;
            var provider = document.getElementById("carrier").value;
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
            else if (email != "" && (!email.includes("@") || !email.includes("."))) {
                let field = form.find('[name="email"]');
                field.addClass("is-invalid");
                document.getElementById("email-error").innerText = "Please enter a valid email address.";
                return false;
            }
            else if (pnumber != "" && pnumber.length != 10) {
                let field = form.find('[name="phone-number"]');
                field.addClass("is-invalid");
                document.getElementById("pnumber-error").innerText = "Please enter a 10-digit phone number.";
                return false;
            }
            // TODO - bug here. can't pass empty value "" to number column in database
            $.post(controller,
                {
                    page: "MainPage", command: "NewRecipient", name: name, email: email, pnumber: pnumber, provider: provider
                },
                function (result) {
                    $('#nrecipient-success').toast({delay:1000});
                    $('#nrecipient-success').toast('show');
                    recipientNames = [];
                    tableNumber = 0;
                    loadRecipients();
                    setTimeout(function () {
                        $('#new-notification').modal('hide');
                    }, 1000);
                });
        }

        // TODO - does not update the database
        function checkEditRecipient() {
            var form = $('#edit-notification-form');
            var controller = "controller.php";
            var name = document.getElementById("erecipient-name").value;
            var email = document.getElementById("eemail").value;
            var pnumber = document.getElementById("ephone-number").value;
            var provider = document.getElementById("ecarrier").value;
            if (name == "") {
                $('#erecipient-name').addClass("is-invalid");
                document.getElementById("ername-error").innerText = "Please enter a name.";
                return false;
            }
            if (email == "" && pnumber == "") {
                $('#eemail').addClass("is-invalid");
                $('#ephone-number').addClass("is-invalid");
                document.getElementById("eemail-error").innerText = "Please enter an email or phone number.";
                return false;
            }
            // only need an email or a phone number, so we check if they are not empty before validating
            if (email != "" && (!email.includes("@") || !email.includes("."))) {
                $('#eemail').addClass("is-invalid");
                document.getElementById("eemail-error").innerText = "Please enter a valid email address.";
                return false;
            }
            if (pnumber != "" && pnumber.length != 10) {
                $('#ephone-number').addClass("is-invalid");
                document.getElementById("epnumber-error").innerText = "Please enter a 10-digit phone number.";
                return false;
            }
            $.post(controller,
                {
                    page: "MainPage", command: "EditRecipient", name: name, email: email, pnumber: pnumber, provider: provider, oname: originalRName, oemail: originalEmail, onumber: originalPhone, oprovider: originalProvider
                },
                function (result) {
                    $('#erecipient-success').toast({delay:1000});
                    $('#erecipient-success').toast('show');
                    recipientNames = [];
                    tableNumber = 0;
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
        var tableNumber = 0; // global variable to hold subtable number, used for getting div id to update subtables when changing notification status
        function createRecipientsTable(jsonArray) {
            var obj = JSON.parse(jsonArray);
            var table = "<table class='table table-striped'><thead class='thead-dark'><tr><th>Recipient Name</th><th>Phone Number</th><th>Provider</th><th>Email Address</th><th></th></tr></thead><tbody>";
            for (var i = 0; i < obj.length; i++) {
                    table += `<tr id="row${i}">`;
                var col = 0
                for (var j in obj[i]) {
                    if (col == 0) {
                        item = (obj[i])[j];
                        recipientNames.push(item);
                        col = 1;
                    }
                    else if (col == 1) {
                        item2 = (obj[i])[j];
                        if (item2 == null) { (obj[i])[j] = "-"; }
                        col = 2;
                    }
                    else if (col == 2) {
                        item3 = (obj[i])[j];
                        col = 3;
                    }
                    else if (col == 3) {
                        item4 = (obj[i])[j];
                    }
                    table += `<td onclick="getElementById('entry${i}').click(); setActive(${i})" style="cursor: pointer">` + (obj[i])[j] + `</td>`;
                }
                col = 0;
                table += `<td><button type='button' class='btn btn-secondary' onclick="editRecipient(\'${item}\', ${item2}, \`${item3}\`, \'${item4}\')">Edit</button><button type='button' class='rbtn' data-toggle='collapse' id='entry${i}' style='visibility:hidden' data-target='#props${i}'></button></td></tr><tr id='col${i}' style='margin: auto'><td colspan='3' style='padding: 0 0 0 50px'><div id='props${i}' class='collapse'></div></td></tr>`;
            }
            table += "</tbody></table>";
            return table;
        }

        function setActive(row) {
            if ($("#row" + row).hasClass("table-info")) {
                $("#row" + row).removeClass("table-info");
            }
            else {
                $("#row" + row).addClass("table-info");
            }
        }

        function createPropertiesSubtable(jsonArray, uname) {
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
                }
            }
            tableNumber++;
            table += "</tbody></table>";
            return table;
        }

         // number corresponding to recipients, used to correctly create corresponding subtable
        function loadPropertiesSubtables(uname, counter) {
            var controller = "controller.php";
            $.post(controller,
                {page: "MainPage", command: "GetRecipientProperties", name: uname},
                function (result) {
                    var table = createPropertiesSubtable(result, recipientNames[counter]);
                    document.getElementById("props" + counter).innerHTML = table;
                });
        }


        // reloads a single subtable when changing notification
        function reloadPropertiesSubtable(uname, divNumber) {
            var controller = "controller.php";
            $.post(controller,
                {page: "MainPage", command: "GetRecipientProperties", name: uname},
                function(result){
                    tableNumber = divNumber;
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
                    for (var i = 0; i < recipientNames.length; i++) {
                        loadPropertiesSubtables(recipientNames[i], i);
                    }
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
        var originalEmail = "";
        var originalProvider = "";
        function editRecipient(name, number, provider, email) {
            originalRName = name;
            originalPhone = number;
            originalProvider = provider;
            originalEmail = email;
            document.getElementById("erecipient-name").value = name;
            document.getElementById("ephone-number").value = number;
            document.getElementById("ecarrier").value = provider;
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

        // CODE TO CHANGE A USER PASSWORD ----->
        function changeUserPassword() {
            var controller = "controller.php";
            var oldpass = document.getElementById('cnguserpass-old').value;
            var newpass = document.getElementById('cnguserpass-new').value;
            var form = $('#change-user-password-form');
            $('#cnguserpass-old').removeClass("is-invalid");
            $('#cnguserpass-old').removeClass("is-valid");
            $('#cnguserpass-new').removeClass("is-invalid");
            $('#cnguserpass-new').removeClass("is-valid");
            if (oldpass == "") {
                let field = form.find('[name="cnguserpass-old"]');
                field.addClass("is-invalid");
                field.removeClass("is-valid");
                document.getElementById("cnguserpass-old-error").innerHTML = "Please enter your current password.";
                return false;
            }
            else{
                let field = form.find('[name="cnguserpass-old"]');
                field.removeClass("is-invalid");
                field.removeClass("is-valid");
            }

            if (newpass == "") {
                let field = form.find('[name="cnguserpass-new"]');
                field.addClass("is-invalid");
                field.removeClass("is-valid");
                document.getElementById("cnguserpass-new-error").innerHTML = "Please enter a new password";
                return false;
            }
            else{
                let field = form.find('[name="cnguserpass-new"]');
                field.removeClass("is-invalid");
                field.removeClass("is-valid");
            }

            $.post(controller,
                {page: "MainPage", command: "ChangeUserPassword", oldpassword: oldpass, newpassword: newpass},
                function(result){
                    //let field = form.find('[name="changePassword"]') //NEEDS UPDATING
                    console.log("RESULT: " + result)
                    document.getElementById("cnguserpass-old-error").innerHTML = "";
                    document.getElementById("cnguserpass-new-error").innerHTML = "";
                    $('#cnguserpass-old').removeClass("is-invalid");
                    $('#cnguserpass-old').removeClass("is-valid");
                    $('#cnguserpass-new').removeClass("is-invalid");
                    $('#cnguserpass-new').removeClass("is-valid");
                    if (result == true) {
                        $('#cnguserpass-old').addClass("is-valid");
                        $('#cnguserpass-new').addClass("is-valid");
                        //document.getElementById("cnguserpass-success-toast").innerHTML = "Password successfully changed.";
                        $('#cnguserpass-success').toast({delay:1000});
                        $('#cnguserpass-success').toast('show');
                        setTimeout(function () {
                            $('#changeUserPasswordModal').modal('hide');
                            document.getElementById("change-user-password-form").reset();
                            //document.getElementById("cnguserpass-success-toast").innerHTML = "";
                        }, 1100);
                    } else {
                        $('#cnguserpass-new').add("is-invalid");
                        $('#cnguserpass-old').addClass("is-invalid");
                        //document.getElementById("cnguserpass-fail-toast").innerHTML = "Password successfully changed.";
                        $('#cnguserpass-fail').toast({delay:3000});
                        $('#cnguserpass-fail').toast('show');
                        setTimeout(function () {
                            //document.getElementById("cnguserpass-fail-toast").innerHTML = "";
                        }, 1100);
                    }
                });
        }
        //clears errors from input when changing it
        $('#cnguserpass-old').on('input', function () {
            var form = $('#edit-notification-form');
            let field = form.find('[name="cnguserpass-old"]')
            if (field.hasClass("is-invalid")) {
                field.removeClass("is-invalid");
            }
            if (field.hasClass("is-valid")) {
                field.removeClass("is-valid");
            }
        });
        $('#cnguserpass-new').on('input', function () {
            var form = $('#edit-notification-form');
            let field = form.find('[name="cnguserpass-new"]')
            if (field.hasClass("is-invalid")) {
                field.removeClass("is-invalid");
            }
            if (field.hasClass("is-valid")) {
                field.removeClass("is-valid");
            }
        });
    </script>
    <title>Outage Alert</title>
</head>
<body>
    <div class="container-fluid">
        <nav class="navbar navbar-expand-sm bg-dark navbar-dark justify-content-between">
            <a class="navbar-brand">Outage Alert</a>
            <div>
                <span class="navbar-text d-inline" style="vertical-align: middle"><?php echo $_SESSION['email']?></span>
                <button class="btn btn-dark d-inline" data-toggle="modal" data-target="#changeUserPasswordModal">Change Password</button>
                <form class="form-inline d-inline" action="https://ec2-35-183-181-30.ca-central-1.compute.amazonaws.com/controller.php" method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>">
                    <button class="btn btn-dark" type="submit">Sign Out</button>
                    <input type="hidden" name="page" value="MainPage">
                    <input type="hidden" name="command" value="SignOut">
                </form>
            </div>
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
                                <input type="text" class="form-control" id="property-name" name="property-name" placeholder="" required>
                                <div class="invalid-feedback" id="pname-error"></div>
                            </div>
                            <div class="form-group">
                                <label for="address">Address:</label>
                                <input type="text" class="form-control" id="address" name="address" placeholder="" required>
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
                                <input type="text" class="form-control" id="eproperty-name" name="property-name" placeholder="" required>
                                <div class="invalid-feedback" id="epname-error"></div>
                            </div>
                            <div class="form-group">
                                <label for="eaddress">Address:</label>
                                <input type="text" class="form-control" id="eaddress" name="address" placeholder="" required>
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
            <h4 style="padding-top: 100px; padding-left: 100px">Recipients</h4>
        </div>
        <div class="row">
            <h6 style="padding-left: 100px">(Click Rows to Expand Notification Settings)</h6>
        </div>
        <div class="row" id="recipients-pane">

        </div>
        <div class="row" style="padding-left: 100px">
            <button class="btn btn-secondary" data-toggle="modal" data-target="#new-notification">Add New Recipient</button>
        </div>
        <div class="modal" id="new-notification">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">New Recipient</h4>
                    </div>
                    <div class="modal-body">
                        <form id="new-notification-form" action="https://ec2-35-183-181-30.ca-central-1.compute.amazonaws.com/controller.php" method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>">
                            <div class="form-group">
                                <label for="recipient-name">Recipient Name:</label>
                                <input type="text" class="form-control" id="recipient-name" name="recipient-name" placeholder="">
                                <div class="invalid-feedback" id="rname-error"></div>
                            </div>
                            <div class="form-group">
                                <label for="email">Email Address (optional):</label>
                                <input type="email" class="form-control" id="email" name="email" placeholder="" required>
                                <div class="invalid-feedback" id="email-error"></div>
                            </div>
                            <div class="form-group">
                                <label for="phone-number">Phone Number (optional):</label>
                                <input type="tel" class="form-control" id="phone-number" name="phone-number" pattern="[0-9]{3}-[0-9]{3}-[0-9]{4}" data-toggle="tooltip" data-placement="top" title="Enter a 10 digit number with no special characters (e.g. 1234567890)">
                                <div class="invalid-feedback" id="pnumber-error"></div>
                            </div>
                            <div class="form-group">
                                <label for="carrier">Phone Service Provider: <i class="fa fa-info-circle" data-toggle="tooltip" data-placement="right" title="Why do we ask for this? We use Email to SMS offered by service providers to reduce our costs. As such, we need your provider's name to ensure the messages get to the right place."></i></label>
                                <select class="form-control" id="carrier">
                                    <option>N/A</option>
                                    <option>Telus</option>
                                    <option>Bell Mobility</option>
                                    <option>Rogers</option>
                                    <option>Freedom Mobile</option>
                                    <option>Fido</option>
                                    <option>Microcell</option>
                                    <option>PC Mobile</option>
                                    <option>Solo Mobile</option>
                                    <option>Virgin Mobile</option>
                                    <option>Koodo</option>
                                    <option>Chatr</option>
                                    <option>Sasktel</option>
                                </select>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <p><small>All notifications (Email and SMS) are off by default. Notifications can be turned on and off in the user's notification settings.</small></p>
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
                        <h4 class="modal-title">Edit Recipient</h4>
                    </div>
                    <div class="modal-body">
                        <form id="edit-notification-form" action="https://ec2-35-183-181-30.ca-central-1.compute.amazonaws.com/controller.php" method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>">
                            <div class="form-group">
                                <label for="erecipient-name">Recipient Name:</label>
                                <input type="text" class="form-control" id="erecipient-name" name="recipient-name" placeholder="recipient-name">
                                <div class="invalid-feedback" id="ername-error"></div>
                            </div>
                            <div class="form-group">
                                <label for="eemail">Email Address (optional):</label>
                                <input type="email" class="form-control" id="eemail" name="email" placeholder="" required>
                                <div class="invalid-feedback" id="eemail-error"></div>
                            </div>
                            <div class="form-group">
                                <label for="ephone-number">Phone Number (optional):</label>
                                <input type="tel" class="form-control" id="ephone-number" name="phone-number" pattern="[0-9]{3}-[0-9]{3}-[0-9]{4}" data-toggle="tooltip" data-placement="top" title="Enter a 10 digit number with no special characters (e.g. 1234567890)">
                                <div class="invalid-feedback" id="epnumber-error"></div>
                            </div>
                            <div class="form-group">
                                <label for="ecarrier">Phone Service Provider: <i class="fa fa-info-circle" data-toggle="tooltip" data-placement="right" title="Why do we ask for this? We use Email to SMS offered by service providers to reduce our costs. As such, we need your provider's name to ensure the messages get to the right place."></i></label>
                                <select class="form-control" id="ecarrier">
                                    <option>N/A</option>
                                    <option>Telus</option>
                                    <option>Bell Mobility</option>
                                    <option>Rogers</option>
                                    <option>Freedom Mobile</option>
                                    <option>Fido</option>
                                    <option>Microcell</option>
                                    <option>PC Mobile</option>
                                    <option>Solo Mobile</option>
                                    <option>Virgin Mobile</option>
                                    <option>Koodo</option>
                                    <option>Chatr</option>
                                    <option>Sasktel</option>
                                </select>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger mr-auto" data-dismiss="modal" id="erecipient-cancel">Cancel</button>
                        <button type="button" class="btn btn-success" id="erecipient-button">Edit</button>
                        <div class="toast text-white bg-success float-right" id="erecipient-success">
                            <div class="toast-body">Recipient details updated.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal" id="changeUserPasswordModal">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Change Password</h4>
                    </div>
                    <div class="modal-body">
                        <form id="change-user-password-form" action="https://ec2-35-183-181-30.ca-central-1.compute.amazonaws.com/controller.php" method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>">
                            <div class="form-group">
                                <label for="cnguserpass-old">Current Password:</label>
                                <input type="password" class="form-control" id="cnguserpass-old" name="cnguserpass-old" placeholder="">
                                <div class="invalid-feedback" id="cnguserpass-old-error"></div>
                            </div>
                            <div class="form-group">
                                <label for="cnguserpass-new">New Password:</label>
                                <input type="password" class="form-control" id="cnguserpass-new" name="cnguserpass-new" placeholder="" required>
                                <div class="invalid-feedback" id="cnguserpass-new-error"></div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger mr-auto" data-dismiss="modal" id="cnguserpass-cancel">Cancel</button>
                        <button type="button" class="btn btn-success" id="cnguserpass-button">Submit</button>
                        <div class="toast text-white bg-success float-right fade hide" id="cnguserpass-success">
                            <div class="toast-body" id="cnguserpass-success-toast">
                                <!--<div class="toast-header">Success</div>-->
                                <div class="toast-body">Password successfully changed.</div>
                            </div>
                        </div>
                        <div class="toast text-white bg-danger float-right fade hide" id="cnguserpass-fail">
                            <div class="toast-body" id="cnguserpass-fail-toast">
                                <div class="toast-header">An error occured</div>
                                <div class="toast-body">Please try retyping your old password.</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>