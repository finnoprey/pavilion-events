<?php
    include_once("../classes/Account.php");

    // this function will take the user object, start the session 
    // and redirect to the relevant page based on account type
    function redirect_to_relevant_page(Account $account) {
        // create session and add account to it
        session_start();
        $_SESSION['account'] = serialize($account);

        if ($account->type == 'ORGANIZER') {
            header("Location: /organizer.php");
        } else if ($account->type == 'ADMINISTRATOR') {
            header("Location: /admin.php");
        }

        exit();
    }

    function is_valid_login($username, $password) {
        return true;
    }

    // check that the request method is post
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        // if it's not, send an error message and terminate execution
        echo('Invalid request method.');
        exit();
    }
    
    // get inputs from form
    $username = $_POST['username'];
    $password = $_POST['password'];

    if (is_valid_login($username, $password)) {
        // should get other data from database, but this is temporary test
        $account = new Account($username, 'ORGANIZER');
        redirect_to_relevant_page($account);
    }
?>