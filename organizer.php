<?php
    /**
     * Software Development SAT - Pavilion Events Management System (PEMS)
     *
     * The organizer page that allows for the creation and deletion of
     * events including a compact view of all events.
     *
     * @author Finn Scicluna-O'Prey <finn@oprey.co>
     *
     */

    session_start();

    include("./classes/Account.php");
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

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $conn = mysqli_connect($config->db_address, $config->db_user, $config->db_password, $config->db_schema);
        if (!$conn) {
            $_SESSION['error_message'] = 'Could not connect to database. Please notify administrators.';
            redirect('/login.php');
            exit();
        }

        if (isset($_POST['delete'])) {
            if (!exists('name', $_POST)) {
                $_SESSION['error_message'] = 'Please enter the full name of the event you wish to delete.';
                redirect('/organizer.php');
                exit();
            }
            
            $input_name = $_POST['name'];
            $sql = 'SELECT name, image FROM events WHERE name=?';
            $event = prepared_select_single($conn, $sql, [$input_name]);

            if (is_null($event)) {
                $_SESSION['error_message'] = 'An event with that name does not exist.';
                redirect('/organizer.php');
                exit();
            }

            $sql = 'DELETE FROM events WHERE name=?';
            prepared_query($conn, $sql, [$input_name]);
            unlink('uploads/event-images/' . $event['image']);
            $_SESSION['success_message'] = 'That event has been deleted.';
            redirect('/organizer.php');
            exit();
        } elseif (isset($_POST['submit'])) {
            $errors = [];

            if (!exists('name', $_POST)) {
                array_push($errors, 'Please enter a name.');
            }

            if (!exists('description', $_POST)) {
                array_push($errors, 'Please enter a description.');
            }

            if (!exists('organizers', $_POST)) {
                array_push($errors, 'Please enter at least one organizer.');
            }

            if (!exists('place', $_POST)) {
                array_push($errors, 'Please enter a place.');
            }

            if (!exists('time', $_POST)) {
                array_push($errors, 'Please enter a time.');
            }

            if (!exists('date', $_POST)) {
                array_push($errors, 'Please enter a date.');
            }

            if (!exists('image', $_FILES)) {
                array_push($errors, 'Please include an image.');
            }

            if (sizeof($errors) != 0) {
                $_SESSION['error_message'] = generate_multiline_string($errors);
                redirect('/organizer.php');
                exit();
            }

            $date_time = $_POST['date'] . ' ' . $_POST['time'];

            $name = $_POST['name'];
            $description = $_POST['description'];
            $organizers = $_POST['organizers'];
            $place = $_POST['place'];
            $date = date('Y-m-d H:i', strtotime($date_time));

            $errors = [];

            if (strlen($name) > 512) {
                array_push($errors, 'Please enter a name under 512 characters.');
            }

            if (strlen($description) > 1024) {
                array_push($errors, 'Please enter a description under 1024 characters.');
            }

            if (strlen($organizers) > 512) {
                array_push($errors, 'Please a list of organizers under 512 characters.');
            }

            if (strlen($place) > 512) {
                array_push($errors, 'Please enter a place name under 512 characters.');
            }

            if (sizeof($errors) != 0) {
                $_SESSION['error_message'] = generate_multiline_string($errors);
                redirect('/organizer.php');
                exit();
            }

            $file_name = str_replace(' ', '_', strtolower($name));
            $existing_images = glob('./uploads/event-images/' . $file_name . '.*');
            
            if (sizeof($existing_images) > 0) {
              $_SESSION['error_message'] = 'An event with that name already exists.';
                redirect('/organizer.php');
                exit();
            }

            $image_dir = 'uploads/event-images/';
            $image_name = $_FILES['image']['name'];
            $image_size = $_FILES['image']['size'];
            $image_tmp = $_FILES['image']['tmp_name'];
            $image_type = $_FILES['image']['type'];
            $image_type = strtolower(pathinfo($image_dir . $image_name, PATHINFO_EXTENSION));

            $extensions = ['jpeg', 'jpg', 'png'];

            if (!in_array($image_type, $extensions)) {
                $_SESSION['error_message'] = 'Please upload a jpeg, jpg or png file!';
                redirect('/organizer.php');
                exit();
            }

            if ($image_size > 10485760){
                $_SESSION['error_message'] = 'The image needs to be under 10mb.';
                redirect('/organizer.php');
                exit();
            }

            $file_name_with_extension = $file_name . '.' . $image_type;

            move_uploaded_file($image_tmp, $image_dir . $file_name_with_extension);

            $sql = 'INSERT INTO events (name, image, description, organizers, place, date) VALUES (?,?,?,?,?,?)';
            prepared_query($conn, $sql, [$name, $file_name_with_extension, $description, $organizers, $place, $date]);

            $_SESSION['success_message'] = 'The event has been created.';
            redirect('/organizer.php');
            exit();
        }
        $conn->close();
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
            <a href="/" class="dashboard-button">
                <span class="material-symbols-outlined">
                    dashboard
                </span>
            </a>
      </div>
        <div class="content flex-row">
            <div class="events-section flex-column">
            <h1 class="heading">Events</h1>
            <div class="flex-row events-grid">
                <?php
                $conn = mysqli_connect($config->db_address, $config->db_user, $config->db_password, $config->db_schema);
                if ($conn->connect_error) {
                    exit('The database could not be reached. Please contact operators.');
                }

                $events = basic_query($conn, 'SELECT name, image, description FROM events ORDER BY date');
                    while($event = $events->fetch_assoc()) {
                        $name = $event['name'];
                        $description = $event['description'];
                        $image = $event['image'];
                        echo <<<HTML
                            <div class="box-outlined events-section-box">
                                <img src="uploads/event-images/$image">
                                <div class="events-section-box-text">
                                    <h2>$name</h2>
                                    <p>$description</p>
                                </div>
                            </div>
                        HTML;
                    }
                ?>
            </div>
            </div>
            <div class="modify-section flex-column">
            <h1 class="heading">Add</h1>
            <div class="box-outlined box-form box-create">
                <form action="organizer.php" method="POST" enctype="multipart/form-data">
                <div class="flex-column">
                    <label for="name">Name</label>
                    <input name="name" type="text" style="width: 80%;">
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
                <div class="flex-column">
                    <label for="image">Image</label>
                    <input name="image" type="file">
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