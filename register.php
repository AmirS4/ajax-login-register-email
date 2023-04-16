<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

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

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;


if (isset($_POST['submit'])) {
    $con = new mysqli('localhost', 'login-form', 'login-form', 'login-form');
    $name = $con->real_escape_string($_POST['name']);
    $email = $con->real_escape_string($_POST['email']);
    $password = $con->real_escape_string($_POST['password']);
    $cPassword = $con->real_escape_string($_POST['cPassword']);

    if ($name == "" || $email == "" || $password != $cPassword)
        $msg = "Please check your inputs!";
    if (!preg_match("/^[a-zA-z]*$/", $name)) {
        $msg = "Only alphabets and whitespace are allowed";
    } else {
        $sql = $con->query("SELECT id FROM users WHERE email = '$email'");
        if ($sql->num_rows > 0) {
            $msg = "Email already exists in the database!";
        } else {
            $token = 'qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM123456789!$/()*';
            $token = str_shuffle($token);
            $token = substr($token, 0, 10);
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            $con->query("INSERT INTO users (name,email,password,isEmailConfirmed,token)
                VALUES ('$name', '$email', '$hashedPassword', '0', '$token')
        ");
            include_once "PHPMailer/PHPMailer.php";
            include_once "PHPMailer/Exception.php";
            include_once "PHPMailer/SMTP.php";
            $mail = new PHPMailer();
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'farsadroohs4@gmail.com';
            $mail->Password = 'phzdjshtssrqqfib';
            $mail->SMTPSecure = 'ssl';
            $mail->Port = 465;
            $mail->setFrom('farsadroohs4@gmail.com');
            $mail->addAddress($email, $name);
            $mail->Subject = "Please verify your email";
            $mail->isHTML(true);
            $mail->Body = "
                 Please click on the link below:<br><br>
                 <a href='http://localhost/registeration-form/confirm.php?email=$email&token=$token'>Click Here</a>
            ";
            if ($mail->send())
                $msg = "You have been registered! Please verify your email!";
            else
                $msg = "Something went wrong! Please try again!";
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
    <title>register</title>
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
                <a href="http://localhost/registeration-form/login.php">All ready a user? Login here!</a><br><br>
                <?php if ($msg != "") echo $msg . "<br><br>" ?>
                <form id="form" method="post" action="register.php">
                    <div class="form-group col-md-12">
                        <input class="form-control" name="name" id="name" placeholder="Name..."><br>
                    </div>
                    <div class="form-group col-md-12">
                        <input class="form-control" name="email" id="email" type="email" placeholder="Email..."><br>
                    </div>
                    <div class="form-group col-md-12">
                        <input class="form-control" name="password" id="password" type="password" placeholder="Password..."><br>
                    </div>
                    <div class="form-group col-md-12">
                        <input class="form-control" name="cPassword" id="cPassword" type="password" placeholder="Confirm Password..."><br>
                    </div>
                    <div class="form-group col-md-12">
                        <input class="btn btn-primary" name="submit" id="submit" type="submit" value="Register" disabled="disabled">
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
                                cPassword: {
                                    required: true,
                                    equalTo: "#password"
                                },
                                email: {
                                    required: true,
                                    email: true
                                },
                                messages: {
                                    name: {
                                        required: "Please enter a name!",
                                        minlength: "Your name must consist of at least 2 characters!"
                                    },
                                    password: {
                                        required: "Please provide a password!",
                                        minlength: "Your password must be at least 5 characters!"
                                    },
                                    cPassword: {
                                        required: "Please provide a password!",
                                        equalTo: "Passwords does not match!"
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
                                name: $('#name').val(),
                                email: $('#email').val(),
                                password: $('#password').val(),
                                cPassword: $('#cPassword').val(),
                                submit: $('#submit').val(),
                            };
                            $.ajax({
                                type: "post",
                                url: "register.php",
                                data: formData,
                                // cache: false,
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