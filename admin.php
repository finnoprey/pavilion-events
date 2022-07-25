<?php
    include_once("./classes/Account.php");

    session_start();

    // if the user is not logged in, redirect back to login
    if (!isset($_SESSION['account'])) {
        $_SESSION['error_message'] = 'You need to be logged in to view this page.';
        header("Location: /login.php");
    }

    $account = unserialize($_SESSION['account']);

    // if the user is not an admin, redirect back to login
    if($account->type != 'ADMINISTRATOR') {
        $_SESSION['error_message'] = 'You need to be an admin to view this page.';
        header("Location: /login.php");
    }

    include_once("./utils/helpers.php");
    include_once("./utils/helpers-sql.php");
    $config = include('config.php');

    // run other code if they are posting
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $conn = mysqli_connect($config->db_address, $config->db_user, $config->db_password, $config->db_schema);
        if ($conn->connect_error) {
            exit("The database could not be reached. Please contact operators.");
        }
        
        if (isset($_POST['delete'])) {
            // deleting user
            if (!isset($_POST['email'])) {
                echo("Please provide the email of the account you wish to delete.");
                exit();
            }

            $input_email = $_POST['email'];

            prepared_query($conn, 'DELETE FROM accounts WHERE email=? AND type="ORGANIZER"', [$input_email]);
            // the account either exists, or does not/is the admin
            // should say: An organizer account with that meail does not exist.
        } else {
            // editing existing or creating a new user
            $input_name = $_POST['name'];
            $input_email = $_POST['email'];
            $input_password = $_POST['password'];
            $user = prepared_select_single($conn, 'SELECT email, password, type FROM accounts WHERE email=?', [$input_email]);

            $hashed_password = password_hash($input_password, PASSWORD_DEFAULT);

            if ($user == null) {
                // creating new user
                $sql = 'INSERT INTO accounts (name, email, password, type) VALUES (?,?,?,?)';
                prepared_query($conn, $sql, [$input_name, $input_email, $hashed_password, 'ORGANIZER']);
                echo("Created user " . $input_email);
            } else {
                // editing existing user
                $sql = "UPDATE accounts SET name=?, password=? WHERE email=?";
                prepared_query($conn, $sql, [$input_name, $hashed_password, $input_email]);
                echo("Updated " . $input_email . " account password.");
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
<div class="container">
    <div class="header">
        <h1 class="title">Admin</h1>
        <a href="/dashboard" class="dashboard-button">
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
            while($user = $accounts->fetch_assoc()) {
                $name = $user['name'];
                $email = $user['email'];
                echo <<<HTML
                    <div class="box-outlined organizers-section-box">
                    <img src="assets/img/accounts/alex.png">
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
            <label for="name">Name</label>
            <input name="name" type="text">
            <br>
            <label for="email">email</label>
            <input name="email" type="email">
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