<?php
    include_once("./classes/Account.php");

    session_start();
    
    // render the error message sent by another page
    if (isset($_SESSION['error_message'])) {
        $message = $_SESSION['error_message'];
        echo('<h1>' . $message . '</h1>');
    }

    session_unset();
    session_destroy();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pavilion Events - Login</title>
    <link rel="stylesheet" href="assets/css/globals.css">
    <link rel="stylesheet" href="assets/css/login.css">
    <style>
        body {
            /* fix to remove live reload white flash */
            background-color: black;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1 class="title">Login</h1>
        <div class="login-form-wrapper">
            <div class="login-form-backing">
                <form class="login-form" action="/handlers/login_handler.php" method="post">
                    <label for="email">Email</label>
                    <input type="text" name="email">
                    <label for="password">Password</label>
                    <input type="password" name="password">
                    <input type="submit" value="Submit">
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>