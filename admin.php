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
          <div class="box-outlined organizers-section-box">
            <img src="assets/img/accounts/dana.png">
            <h2>Dana</h2>
          </div>
          <div class="box-outlined organizers-section-box">
            <img src="assets/img/accounts/alex.png">
            <h2>Alex</h2>
          </div>
          <div class="box-outlined organizers-section-box">
            <img src="assets/img/accounts/tristan.png">
            <h2>Tristan</h2>
          </div>
          <div class="box-outlined organizers-section-box">
            <img src="assets/img/accounts/luka.png">
            <h2>Luka</h2>
          </div>
        </div>
      </div>
      <div class="modify-section flex-column">
        <h1 class="heading">Create Or Edit</h1>
        <div class="box-outlined">
          <form>
            <label for="username">Username</label>
            <input name="username" type="text">
            <br>
            <label for="password">Password</label>
            <input name="password" type="password">
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