<?php


?>


<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <title>Control Panel</title>

    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">

    <!-- Optional theme -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="http://www.theadriangee.com/aw-cpanel/assets/css/custom.css">

    <!-- jQuery library -->
    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>

    <!-- Latest compiled and minified JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

</head>
<body>


<div style="margin: auto;width:55%; margin-top: 75px;">
    <form action="/aw-cpanel/dashboard.php" method="post">
        <div class="imgcontainer">
            <img src="/aw-cpanel/assets/img/img_avatar2.png" alt="Avatar" class="avatar"
                 width="175" height="175">
        </div>

        <div class="container" style="margin: auto;width:95%;text-align: center; ">
            <input type="text" placeholder="Enter Username" name="username" required>
            <input type="password" placeholder="Enter Password" name="password" required>
            <button type="submit">Login</button>
        </div>

        <!--
        <div class="container" style="background-color:#f1f1f1;margin:auto;width:90%;margin-bottom: 25px;">
            <span class="psw" style="margin-bottom: 15px; ">Forgot <a href="#">password?</a></span>
        </div>
        -->

    </form>
</div>


</body>
</html>
