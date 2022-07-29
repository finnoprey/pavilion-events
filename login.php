<?php
    /**
     * Software Development SAT - Pavilion Events Management System (PEMS)
     *
     * The login page provides a simple form input for users to log in
     * to PEMS, by posting to a login handler.
     *
     * @author Finn Scicluna-O'Prey <finn@oprey.co>
     *
     */
    include("./classes/Account.php");

    session_start();
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
    <?php include('./handlers/modal_renderer.php'); ?>
    <div class="container">
        <div class="header">
            <h1 class="title">Login</h1>
            <div class="login-form-wrapper">
                <div class="login-form-backing">
                    <form class="login-form" action="/handlers/login_handler.php" method="post">
                        <label for="email">Email</label>
                        <input type="email" name="email" required>
                        <label for="password">Password</label>
                        <input type="password" name="password" required>
                        <input type="submit" value="Submit">
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>