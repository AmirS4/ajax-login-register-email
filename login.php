<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);


//if (isset($_POST['name'])) {
//    $name = $_POST['name'];
//    echo $name;
//    exit;
//}


$servername = "localhost";
$database = "login-form";
$username = "login-form";
$password = "login-form";

//// Create connection
//$conn = new mysqli($servername, $username, $password, $database);
//// Check connection
//if ($conn->connect_error) {
//    die("Connection failed: " . $conn->connect_error);
//}
//echo "Connected successfully";


$msg = '';

if (isset($_POST['submit'])) {
    $con = new mysqli('localhost', 'login-form', 'login-form', 'login-form');
    $email = $con->real_escape_string($_POST['email']);
    $password = $con->real_escape_string($_POST['password']);

    if ($email == "" || $password == "")
        $msg = "Please check your inputs!";
    else {
        $sql = $con->query("SELECT id, password, isEmailConfirmed FROM users WHERE email = '$email'");
        if ($sql->num_rows > 0) {
            $data = $sql->fetch_array();
            if (password_verify($password, $data['password'])) {
                if ($data['isEmailConfirmed'] == 0)
                    $msg = "Please verify your Email";
                else {
                    $msg = "You have been logged in";
                }
            } else {
                $msg = "Please check your inputs!";
            }
        } else {
            $msg = "Please check your inputs!";
        }
    }
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Log In</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.13.2/jquery-ui.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.1/jquery.validate.min.js"></script>
</head>

<body id="response">
    <div class="container" style="margin-top: 100px;">
        <div class="row justify-content-center">
            <div class="col-md-6 col-md-offset-3" align="center">
                <img src="./images/logo.webp" style="height: 120px;" alt="logo"><br><br>
                <a href="http://localhost/registeration-form/register.php">Register here!</a><br><br>
                <?php if ($msg != "") echo $msg . "<br><br>" ?>
                <form id="form" method="post" action="login.php">
                    <div class="form-group col-md-12">
                        <input class="form-control" name="email" id="email" type="email" placeholder="Email..."><br>
                    </div>
                    <div class="form-group col-md-12">
                        <input class="form-control" name="password" id="password" type="password" placeholder="Password..."><br>
                    </div>
                    <div class="form-group col-md-12">
                        <input class="btn btn-primary" name="submit" id="submit" type="submit" value="Log In" disabled="disabled">
                    </div>
                </form>
                <style>
                    .has-error {
                        width: 100%;
                        padding: 12px 20px;
                        margin: 8px 0;
                        box-sizing: border-box;
                        border: 2px solid red;
                        -webkit-transition: 0.5s;
                        transition: 0.5s;
                        outline: none;
                    }
                </style>
                <script>
                    $(document).ready(function() {

                        // form validation
                        $.validator.setDefaults({
                            errorClass: 'text-danger',

                            highlight: function(element) {
                                $(element)
                                    .closest('.form-control')
                                    .addClass('has-error');
                            },
                            unhighlight: function(element) {
                                $(element)
                                    .closest('.form-control')
                                    .removeClass('has-error');
                            }
                        });
                        $.validator.addMethod('strongPassword', function(value, element) {
                            return this.optional(element) || value.length >= 6 && /\d/.test(value) && /[a-z]/i.test(value);
                        }, "Your password must be at least 6 characters long and contain at least one number and one char\'.")
                        $('#form').validate({
                            rules: {
                                name: {
                                    required: true,
                                    minlength: 2
                                },
                                password: {
                                    required: true,
                                    strongPassword: true,
                                },
                                email: {
                                    required: true,
                                    email: true
                                },
                                messages: {
                                    password: {
                                        required: "Please provide a password!",
                                        minlength: "Your password must be at least 5 characters!"
                                    },
                                    email: {
                                        required: "Please enter an email address!",
                                        email: "Please enter a <em>valid</em> email address!"
                                    }
                                }
                            }
                        });

                        // activate submit after validation
                        $('input').on('blur', function() {
                            if ($("#form").valid()) {
                                $('#submit').prop('disabled', false);
                            } else {
                                $('#submit').prop('disabled', 'disabled');
                            }
                        });
                        //submit form
                        $("#form").submit(function(event) {
                            event.preventDefault();
                            let formData = {
                                email: $('#email').val(),
                                password: $('#password').val(),
                                submit: $('#submit').val(),
                            };
                            $.ajax({
                                type: "post",
                                url: "https://pezhmanbirack.ir/AmirS4/registeration-form/login.php",
                                data: formData,
                                cache: false,
                                // processData: false,
                                // dataType: "json",
                                // encode: true,
                                success: function(data) {
                                    console.log(data);
                                    // $('.msg').html(data);
                                    $('#response').html(data);
                                }
                            });
                        });

                    })
                </script>
            </div>
        </div>
    </div>
</body>

</html>