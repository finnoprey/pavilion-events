<?php
    /**
     * Software Development SAT - Pavilion Events Management System (PEMS)
     *
     * The main page that displays all events in the current and 
     * upcoming weeks.
     *
     * @author Finn Scicluna-O'Prey <finn@oprey.co>
     *
     */

    session_start();

    include('./utils/helpers.php');
    include('./utils/helpers_sql.php');
    include('./utils/helpers_validation.php');
    $config = include('config.php');

    // Check if user is trying to post, or in this case, trying to filter/search the upcoming list.
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $conn = mysqli_connect($config->db_address, $config->db_user, $config->db_password, $config->db_schema);
        if (!$conn) {
            $_SESSION['error_message'] = 'Could not connect to database. Please notify administrators.';
            redirect('/login.php');
            exit();
        }

        // Set the search term to the input they entered
        if (exists('search_upcoming', $_POST)) {
            $search_term = $_POST['search_upcoming'];
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pavilion Events</title>
    <link rel="stylesheet" href="assets/css/globals.css">
    <link rel="stylesheet" href="assets/css/dashboard.css">

    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
</head>
<body>
  <?php include('./handlers/modal_renderer.php'); ?>
    <div class="container">
        <div class="header">
            <h1 class="title">Events</h1>
            <a href="/login.php" class="login-button">
                <span class="material-symbols-outlined">
                    person
                </span>
            </a>
        </div>
        <div class="events">
            <h2 class="events-row-heading heading-thisweek">This Week</h2>
            <div class="events-row">
                <?php
                $conn = mysqli_connect($config->db_address, $config->db_user, $config->db_password, $config->db_schema);
                if ($conn->connect_error) {
                    exit('The database could not be reached. Please contact operators.');
                }

                $events = basic_query($conn, 'SELECT name, image, description, organizers, place, date FROM events ORDER BY date');
                while($event = $events->fetch_assoc()) {
                    $name = $event['name'];
                    $description = $event['description'];
                    $image = $event['image'];
                    $organizers = $event['organizers'];
                    $place = $event['place'];
                    $date = date('Y-m-d H:i', strtotime($event['date']));
                    // Convert date into many parts for it's display in the event details
                    $formatted_time = date('g:ia', strtotime($event['date']));
                    $formatted_date_day = date('D', strtotime($event['date']));
                    $formatted_date_date = date('jS', strtotime($event['date']));
                    $formatted_date_month = date('M', strtotime($event['date']));

                    $first_day = date('Y-m-d', strtotime('sunday last week'));  
                    $last_day = date('Y-m-d', strtotime('monday next week'));  
                    // Only display events that are in the current week
                    if($date > $first_day && $date < $last_day) {
                        echo <<<HTML
                        <div class="event box-outlined">
                            <div class="flex-column">
                                <img src="/uploads/event-images/$image" class="event-image">
                                <div class="flex-row">
                                    <div class="event-content-left">
                                        <h3 class="event-title">$name</h3>
                                        <h4 class="event-description">$description</h4>
                                    </div>
                                    <div class="event-details">
                                        <div class="event-detail">
                                            <span class="material-symbols-outlined">schedule</span>
                                            <p>$formatted_time</p>
                                        </div>
                                        <div class="event-detail">
                                            <span class="material-symbols-outlined">event</span>
                                            <p>$formatted_date_day, $formatted_date_date of $formatted_date_month</p>
                                        </div>
                                        <div class="event-detail">
                                            <span class="material-symbols-outlined">near_me</span>
                                            <p>$place</p>
                                        </div>
                                        <div class="event-detail">
                                            <span class="material-symbols-outlined">group</span>
                                            <p>$organizers</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    HTML;
                    } 
                }
                ?>
            </div>
            <div class="flex-row">
                <h2 class="events-row-heading heading-upcoming">Upcoming</h2>
                <form action="dashboard.php" method="POST">
                <input type="text" name="search_upcoming" class="search-upcoming">
                </form>
            </div>
            <div class="events-row upcoming-row">
            <?php
                $conn = mysqli_connect($config->db_address, $config->db_user, $config->db_password, $config->db_schema);
                if ($conn->connect_error) {
                    exit('The database could not be reached. Please contact operators.');
                }

                $events = basic_query($conn, 'SELECT name, image, description, organizers, place, date FROM events ORDER BY date');
                while ($event = $events->fetch_assoc()) {
                    $name = $event['name'];
                    $description = $event['description'];
                    $image = $event['image'];
                    $organizers = $event['organizers'];
                    $place = $event['place'];
                    $date = date('Y-m-d H:i', strtotime($event['date']));
                    // Convert date into many parts for it's display in the event details
                    $formatted_time = date('g:ia', strtotime($event['date']));
                    $formatted_date_day = date('D', strtotime($event['date']));
                    $formatted_date_date = date('jS', strtotime($event['date']));
                    $formatted_date_month = date('M', strtotime($event['date']));

                    // Check if search term is inside any of the event details
                    if (isset($search_term)) {
                        $search_term_lower = strtolower($search_term);
                        if (!(str_contains(strtolower($name), $search_term_lower) 
                        || str_contains(strtolower($description), $search_term_lower) 
                        || str_contains(strtolower($organizers), $search_term_lower) 
                        || str_contains(strtolower($place), $search_term_lower) 
                        || str_contains(strtolower($date), $search_term_lower))) {
                            // Skip displaying this event if it doesn't include any of the search terms
                            continue;
                        }
                    }

                    $last_sunday = date('Y-m-d', strtotime('sunday last week'));  
                    $next_monday = date('Y-m-d', strtotime('monday next week'));  

                    // Only display events that aren't in the current week
                    if(!($date > $last_sunday && $date < $next_monday)) {
                        echo <<<HTML
                        <div class="event box-outlined">
                            <div class="flex-column">
                                <img src="/uploads/event-images/$image" class="event-image filter-grayscale">
                                <div class="flex-row">
                                    <div class="event-content-left">
                                        <h3 class="event-title">$name</h3>
                                        <h4 class="event-description">$description</h4>
                                    </div>
                                    <div class="event-details">
                                        <div class="event-detail">
                                            <span class="material-symbols-outlined">schedule</span>
                                            <p>$formatted_time</p>
                                        </div>
                                        <div class="event-detail">
                                            <span class="material-symbols-outlined">event</span>
                                            <p>$formatted_date_day, $formatted_date_date of $formatted_date_month</p>
                                        </div>
                                        <div class="event-detail">
                                            <span class="material-symbols-outlined">near_me</span>
                                            <p>$place</p>
                                        </div>
                                        <div class="event-detail">
                                            <span class="material-symbols-outlined">group</span>
                                            <p>$organizers</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    HTML;
                    } 
                }
                ?>
            </div>
        </div>
    </div>
</body>
</html>