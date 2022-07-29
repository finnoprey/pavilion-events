<?php
    /**
     * Software Development SAT - Pavilion Events Management System (PEMS)
     *
     * The handler that the login page posts data to. This page verifies
     * account credentials and redirects users to the relevant page.
     *
     * @author Finn Scicluna-O'Prey <finn@oprey.co>
     *
     */

    // The user should not be able to access this page without posting.
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo('Invalid request method.');
        exit();
    }

    session_start();

    include('../classes/Account.php');
    include('../utils/helpers.php');
    include('../utils/helpers_sql.php');
    include('../utils/helpers_validation.php');
    $config = include('../config.php');

    /**
     * Redirects the user to the relevant page based on their account type. *
     * @param Array $session
     */
    function redirect_to_relevant_page(Array $session) {
        if (!isset($session) || !isset($_SESSION['account'])) redirect('/login.php');
        $account = unserialize($session['account']);
        if ($account->type == 'ORGANIZER') redirect('/organizer.php');
        if ($account->type == 'ADMINISTRATOR') redirect('/admin.php');
        exit();
    }

    $errors = [];

    if (!exists('email', $_POST)) {
        array_push($errors, 'Please enter an email.');
    }

    if (!exists('password', $_POST)) {
        array_push($errors, 'Please enter a password.');
    }

    // If errors have occured, generate an error message and exit.
    if (sizeof($errors) != 0) {
        $_SESSION['error_message'] = generate_multiline_string($errors);
        redirect('/login.php');
        exit();
    }

    $input_email = $_POST['email'];
    $input_password = $_POST['password'];

    $conn = mysqli_connect($config->db_address, $config->db_user, $config->db_password, $config->db_schema);
    if (!$conn) {
        $_SESSION['error_message'] = 'Could not connect to database. Please notify administrators.';
        redirect('/login.php');
        exit();
    }

    $sql = 'SELECT name, email, password, type FROM accounts WHERE email=?';
    $user = prepared_select_single($conn, $sql, [$input_email]);
    $conn->close();

    if (is_null($user)) {
        $_SESSION['error_message'] = 'An account with that email does not exist.';
        redirect('/login.php');
        exit();
    }

    $db_user_email = $user['email'];
    $db_user_password = $user['password'];
    $db_user_type = $user['type'];

    if (password_verify($input_password, $db_user_password)) {
        // Create, then add the serialized account object to the session
        $account = new Account($input_email, $db_user_type);
        $_SESSION['account'] = serialize($account);
        redirect_to_relevant_page($_SESSION);
    } else {
        $_SESSION['error_message'] = 'That password is incorrect.';
        redirect('/login.php');
        exit();
    }
?>