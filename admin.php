<?php
    session_start();

    include('./classes/Account.php');
    include('./utils/helpers.php');
    include('./utils/helpers_sql.php');
    include('./utils/helpers_validation.php');
    $config = include('config.php');

    // if the user is not logged in, redirect back to login
    if (!isset($_SESSION['account'])) {
        $_SESSION['error_message'] = 'You need to be logged in to view this page.';
        redirect('/login.php');
        exit();
    }

    $account = unserialize($_SESSION['account']);

    // if the user is not an admin, redirect back to login
    if($account->type != 'ADMINISTRATOR') {
        $_SESSION['error_message'] = 'You need to be an admin to view this page.';
        redirect('/login.php');
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $conn = mysqli_connect($config->db_address, $config->db_user, $config->db_password, $config->db_schema);
        if (!$conn) {
            $_SESSION['error_message'] = 'Could not connect to database. Please notify administrators.';
            redirect('/login.php');
            exit();
        }
        
        if (isset($_POST['delete'])) {
            if (!exists('email', $_POST)) {
                $_SESSION['error_message'] = 'Please enter the email of the account you wish to delete.';
                redirect('/admin.php');
                exit();
            }

            $input_email = $_POST['email'];
            $sql = 'SELECT name, email, type FROM accounts WHERE email=?';
            $user = prepared_select_single($conn, $sql, [$input_email]);
            
            if (is_null($user)) {
                $_SESSION['error_message'] = 'An account with that email does not exist.';
                redirect('/admin.php');
                exit();
            }

            $db_user_type = $user['type'];

            if ($db_user_type == 'ADMINISTRATOR') {
                $_SESSION['error_message'] = 'You cannot delete an administrator account.';
                redirect('/admin.php');
                exit();
            }

            $sql = 'DELETE FROM accounts WHERE email=? AND type="ORGANIZER"';
            prepared_query($conn, $sql, [$input_email]);
            $_SESSION['success_message'] = 'That account has been deleted.';
            redirect('/admin.php');
            exit();
        } elseif (isset($_POST['submit'])) {
            if (!exists('email', $_POST)) {
                $_SESSION['error_message'] = 'Please enter an email.';
                redirect('/admin.php');
                exit();
            }

            $input_email = $_POST['email'];
            $sql = 'SELECT email FROM accounts WHERE email=?';
            $account = prepared_select_single($conn, $sql, [$input_email]);
            if ($account == null) {
                // Administrator is creating a new account
                $errors = [];

                if (!exists('name', $_POST)) {
                    array_push($errors, 'Please enter a name.');
                }

                if (!exists('password', $_POST)) {
                    array_push($errors, 'Please enter a password.');
                }

                if (sizeof($errors) != 0) {
                    $_SESSION['error_message'] = generate_multiline_string($errors);
                    redirect('/admin.php');
                    exit();
                }

                $input_name = $_POST['name'];
                $input_password = $_POST['password'];
                $hashed_password = password_hash($input_password, PASSWORD_DEFAULT);

                $sql = 'INSERT INTO accounts (name, email, password, type) VALUES (?,?,?,?)';
                prepared_query($conn, $sql, [$input_name, $input_email, $hashed_password, 'ORGANIZER']);
                
                $_SESSION['success_message'] = 'Account created.';
                redirect('/admin.php');
                exit();
            } else {
                // Administrator is editing an existing account
                $messages = [];
                
                if (exists('name', $_POST)) {
                    $input_name = $_POST['name'];
                    $sql = 'UPDATE accounts SET name=? WHERE email=?';
                    prepared_query($conn, $sql, [$input_name, $input_email]);
                    array_push($messages, 'Updated account name.');
                }

                if (exists('password', $_POST)) {
                    $input_password = $_POST['password'];
                    $hashed_password = password_hash($input_password, PASSWORD_DEFAULT);
                    $sql = 'UPDATE accounts SET password=? WHERE email=?';
                    prepared_query($conn, $sql, [$hashed_password, $input_email]);
                    array_push($messages, 'Updated account password.');
                }

                if (sizeof($messages) != 0) {
                    $_SESSION['success_message'] = generate_multiline_string($messages);
                    redirect('/admin.php');
                    exit();
                }

                $_SESSION['error_message'] = 'Please provide new values for name and/or password.';
                redirect('/admin.php');
            }
        }

        $conn->close();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pavilion Events - Dashboard</title>
    <link rel="stylesheet" href="assets/css/globals.css">
    <link rel="stylesheet" href="assets/css/admin.css">

    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
</head>
<body>
    <?php include('./handlers/modal_renderer.php'); ?>
    <div class="container">
        <div class="header">
            <h1 class="title">Admin</h1>
            <a href="/dashboard.php" class="dashboard-button">
                <span class="material-symbols-outlined">
                    dashboard
                </span>
            </a>
        </div>
        <div class="content flex-row">
        <div class="organizers-section flex-column">
            <h1 class="heading">Organizers</h1>
            <div class="flex-row organizers-grid">
            <?php 
                $conn = mysqli_connect($config->db_address, $config->db_user, $config->db_password, $config->db_schema);
                if ($conn->connect_error) {
                    exit('The database could not be reached. Please contact operators.');
                }

                $accounts = basic_query($conn, 'SELECT name, email FROM accounts WHERE type="ORGANIZER"');
                while($account = $accounts->fetch_assoc()) {
                    $name = $account['name'];
                    $email = $account['email'];
                    echo <<<HTML
                        <div class="box-outlined organizers-section-box">
                            <div class="account-image"> 
                                <p class="account-image-letter">$name[0]</p>
                            </div>
                            <h2>$name</h2>
                            <h4>$email</h4>
                        </div>
                    HTML;
                }
            ?>
            </div>
        </div>
        <div class="modify-section flex-column">
            <h1 class="heading">Create Or Edit</h1>
            <div class="box-outlined">
            <form action="admin.php" method="POST">
                <label for="email">Email</label>
                <input name="email" type="email">
                <br>
                <label for="name">Name</label>
                <input name="name" type="text">
                <br>
                <label for="password">Password</label>
                <input name="password" type="password">
                <br>
                <input type="submit" name="submit" value="Submit">
                <input type="submit" name="delete" value="Delete">
            </form>
            </div>
        </div>
        </div>
    </div>
</body>
</html>