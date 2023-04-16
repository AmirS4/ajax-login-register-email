<?php
    function redirect() {
        header('location: register.php');
        exit();
    }

    if (!isset($_GET['email']) || !isset($_GET['token'])) {
        redirect();
    }else {
        $con = new mysqli('localhost', 'login-form', 'login-form', 'login-form');
        $email = $con->real_escape_string($_GET['email']);
        $token = $con->real_escape_string($_GET['token']);
        $sql = $con->query("SELECT id FROM users WHERE email='$email' AND token='$token' AND isEmailConfirmed=0");
        if ($sql->num_rows > 0) {
            $con->query("UPDATE users SET isEmailConfirmed=1, token='' WHERE email='$email'");
            echo 'Your Email has benn verified! You can log in now!';
        } else {
            redirect();
        }
    }
?>