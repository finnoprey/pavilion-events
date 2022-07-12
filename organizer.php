<?php
    include_once("./classes/Account.php");

    session_start();
    
    // if the user is not logged in, redirect back to login
    if (!isset($_SESSION['account'])) {
        $_SESSION['message'] = 'You need to be logged in to view this page.';
        header("Location: /login.php");
    }

    $account = unserialize($_SESSION['account']);

    echo($account->username);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pavilion Events - Organizer</title>
    <link rel="stylesheet" href="assets/css/globals.css">
    <link rel="stylesheet" href="assets/css/organizer.css">

    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
</head>
<body>
<div class="container">
    <div class="header">
        <h1 class="title">Organizer</h1>
        <a href="/dashboard" class="dashboard-button">
            <span class="material-symbols-outlined">
                dashboard
            </span>
        </a>
    </div>
    <div class="content flex-row">
      <div class="events-section flex-column">
        <h1 class="heading">Events</h1>
        <div class="flex-row events-grid">
          <div class="box-outlined events-section-box">
            <img src="assets/img/gym.png">
            <div class="events-section-box-text">
              <h2>Gym Session</h2>
              <p>Come down to the gym and work your stress off. We’ll be doing a circuit and heaps of activities!</p>
            </div>
          </div>
          <div class="box-outlined events-section-box">
            <img src="assets/img/fun_run.png">
            <div class="events-section-box-text">
              <h2>Fun Run</h2>
              <p>Just take a stroll, or compete to be the best. It’s fun for everyone at this weeks fun run.</p>
            </div>
          </div>
          <div class="box-outlined events-section-box">
            <img src="assets/img/basketball.png">
            <div class="events-section-box-text">
              <h2>Basketball</h2>
              <p>Enjoy a basketball game for all skill levels, for the sake of enjoyment and a good time.</p>
            </div>
          </div>
          <div class="box-outlined events-section-box">
            <img src="assets/img/meditation.png">
            <div class="events-section-box-text">
              <h2>Super Woman</h2>
              <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce at est eu ex sagittis dui.</p>
            </div>
          </div>
        </div>
      </div>
      <div class="modify-section flex-column">
        <h1 class="heading">Create Or Edit</h1>
        <div class="box-outlined">
          <form>
            <label for="name">Name</label>
            <input name="name" type="text">
            <br>
            <label for="description">Description</label>
            <input name="description" type="text">
            <br>
            <label for="organizers">Organizers</label>
            <input name="organizers" type="text">
            <br>
            <label for="place">Place</label>
            <input name="place" type="text">
            <br>
            <label for="time">Time</label>
            <input name="time" type="time">
            <br>
            <label for="date">Date</label>
            <input name="date" type="date">
            <br>
            <input type="button" name="submit" value="Submit">
            <input type="button" name="delete" value="Delete">
          </form>
        </div>
      </div>
    </div>
</div>
</body>
</html>