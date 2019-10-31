<?php 
session_start();
$config = parse_ini_file("config.ini");
require_once('database_conn.php');

if( count($_POST) > 0 && isset($_POST['username'] ) && isset($_POST['password'] ) ){
    unset($_SESSION['SUCCESS']);
    $sql_query = "SELECT * FROM user WHERE username='".$_POST['username']."' AND password='".$_POST['password']."'";
    $result = $conn->query($sql_query);
    if ($result->num_rows > 0) { 
        unset($_SESSION['ERROR']);    
        $_SESSION['logged_in'] = TRUE;
        $_SESSION['user'] = $_POST['username'];
    }else{
        $_SESSION['ERROR'] = "Incorrect Username or Password";
    }
}
if( isset($_SESSION['logged_in']) ){
    if( $_SESSION['logged_in'] ){
        header("Location: ".$config["HOST_URL"]."/dashboard.php"); 
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Pay2All</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css">
    <!-- Bootstrap core CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <!-- Material Design Bootstrap -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdbootstrap/4.8.10/css/mdb.min.css" rel="stylesheet">
    
    <!-- JQuery -->
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <!-- Bootstrap tooltips -->
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.4/umd/popper.min.js"></script>
    <!-- Bootstrap core JavaScript -->
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <!-- MDB core JavaScript -->
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/mdbootstrap/4.8.10/js/mdb.min.js"></script>

    <title>Sign In</title>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-3"></div>
            <div class="col-md-6">
            <!-- Default form login -->
        <form class="text-center border border-light p-5 signinform" action="#" method="POST">

        <p class="h4 mb-4">Sign in</p>
        <?php
            if(isset($_SESSION['ERROR'])){
                echo "<h4>".$_SESSION['ERROR']."</h4>";
            }
            if(isset($_SESSION['SUCCESS'])){
                echo "<h4>".$_SESSION['SUCCESS']."</h4>";
            }
        ?>
        <!-- Username -->
        <input name="username" type="text" id="defaultLoginFormEmail" class="form-control mb-4" placeholder="Username" required>

        <!-- Password -->
        <input name="password" type="password" id="defaultLoginFormPassword" class="form-control mb-4" placeholder="Password" required>

        <div class="d-flex justify-content-around">
            <div>
                <!-- Remember me -->
                <div class="custom-control custom-checkbox">
                    <!-- <input type="checkbox" class="custom-control-input" id="defaultLoginFormRemember"> -->
                    <!-- <label class="custom-control-label" for="defaultLoginFormRemember">Remember me</label> -->
                </div>
            </div>
            <div>
                <!-- Forgot password -->
                <!-- <a href="">Forgot password?</a> -->
            </div>
        </div>

        <!-- Sign in button -->
        <button class="btn btn-info btn-block my-4" type="submit">Sign in</button>

        <!-- Register -->
        <p>Not a member?
            <a href="register.php">Register</a>
        </p>

        </form>
        <!-- Default form login -->    
        </div>
    </div>

    </div>
</body>
</html>