<?php
    session_start();

    include("./classes/Account.php");
    include('./utils/helpers.php');
    $config = include('config.php');
    
    // if the user is not logged in, redirect back to login
    if (!isset($_SESSION['account'])) {
        $_SESSION['error_message'] = 'You need to be logged in to view this page.';
        redirect('/login.php');
        exit();
    }

    $account = unserialize($_SESSION['account']);

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $conn = mysqli_connect($config->db_address, $config->db_user, $config->db_password, $config->db_schema);
        if (!$conn) {
            $_SESSION['error_message'] = 'Could not connect to database. Please notify administrators.';
            redirect('/login.php');
            exit();
        }

        if (isset($_POST['delete'])) {
            exit('Delete');
        } elseif (isset($_POST['submit'])) {
            exit('Create');
        }
    }
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
  <?php include('./handlers/modal_renderer.php'); ?>
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
        <h1 class="heading">Add</h1>
          <div class="box-outlined box-form box-create">
            <form action="organizer.php" method="POST">
              <div class="flex-column">
                <label for="name">Name</label>
                <input name="name" type="text">
              </div>
              <div class="flex-column">
                <label for="description">Description</label>
                <textarea cols="20" rows="5" name="description" class="description-input"></textarea>
              </div>
              <div class="flex-row">
                <div class="flex-column">
                  <label for="organizers">Organizers</label>
                  <input name="organizers" type="text" style="width: 120px; margin-right: 10px;">
                </div>
                <div class="flex-column">
                  <label for="place">Place</label>
                  <input name="place" type="text" style="width: 130px;">
                </div>
              </div>
              <div class="flex-row">
                <div class="flex-column">
                  <label for="time">Time</label>
                  <input name="time" type="time" style="width: 100px; margin-right: 10px;">
                </div>
                <div class="flex-column">
                  <label for="date">Date</label>
                  <input name="date" type="date" style="width: 120px;">
                </div>
              </div>
              <input type="submit" name="submit" value="Create">
            </form>
          </div>
          <h2 class="subheading">Delete</h2>
          <div class="box-outlined box-form box-delete">
            <form action="organizer.php" method="POST">
              <div class="flex-row flex-centered">
                <div class="flex-column"> 
                  <label for="name">Name</label>
                  <input name="name" type="text" style="width: 200px;">
                </div>
                <input type="submit" name="delete" value="Delete">
              </div>
            </form>
          </div>
        </div>
      </div>
  </div>
</body>
</html>