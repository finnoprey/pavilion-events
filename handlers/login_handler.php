<?php
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo('Invalid request method.');
        exit();
    }

    include_once("../classes/Account.php");
    include_once("../utils/helpers.php");
    include_once("../utils/helpers-sql.php");
    $config = include('../config.php');

    function redirect_to_relevant_page(Array $session) {
        if (!isset($session) || !isset($_SESSION['account'])) redirect("/login.php");
        $account = unserialize($session['account']);
        if ($account->type == 'ORGANIZER') redirect("/organizer.php");
        if ($account->type == 'ADMINISTRATOR') redirect("/admin.php");
        exit();
    }

    $input_email = $_POST['email'];
    $input_password = $_POST['password'];

    $conn = mysqli_connect($config->db_address, $config->db_user, $config->db_password, $config->db_schema);
    if ($conn->connect_error) {
        die("The database could not be reached. Please contact operators.");
    }

    $user = prepared_select_single($conn, "SELECT name, email, password, type FROM accounts WHERE email=?", [$input_email]);
    $conn->close();

    $db_user_email = $user['email'];
    $db_user_password = $user['password'];
    $db_user_type = $user['type'];

    session_start();
    if (password_verify($input_password, $db_user_password)) {
        $account = new Account($input_email, $db_user_type);
        $_SESSION['account'] = serialize($account);
        redirect_to_relevant_page($_SESSION);
    } else {
        session_start();
        $_SESSION['error_message'] = 'Incorrect account password or username.';
        header("Location: /login.php");
    }
?>