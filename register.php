<?php 
session_start();
$_SESSION['logged_in'] = FALSE;
$config = parse_ini_file("config.ini");
require_once('database_conn.php');
if( count($_POST) > 0 && !empty($_POST['name']) && !empty($_POST['username'])
 && !empty($_POST['password']) && !empty($_POST['email']) && !empty($_POST['phone'])){
    $sql_query = "SELECT * FROM user where username='".$_POST['username']."' OR email='".$_POST['email']."'";
    $result = $conn->query($sql_query);
    if ($result->num_rows > 0) { 
        $_SESSION['ERROR'] = "Already a user is registered with that email or username. ";
    }else{
        $sql_query = "INSERT INTO user (`name`, `email`, `username`, `password`, `phone`) VALUES ('".$_POST['name']."', '".$_POST['email']."', '".$_POST['username']."', '".$_POST['password']."', '".$_POST['phone']."')";
        if( !mysqli_query($conn , $sql_query) ){
            $_SESSION['ERROR'] = mysqli_error($conn);
        }else{
            unset($_SESSION['ERROR']);
            $_SESSION['SUCCESS'] = "Registration is successful";
            header("Location: http://".$config["HOST_URL"]."/"); 

        }    
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
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

    <title>Register</title>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-3"></div>
            <div class="col-md-6">
                <!-- Default form register -->
<form class="text-center border border-light p-5 signupform" action="#" method="POST">

<p class="h4 mb-4">Sign up</p>

<?php
    if(isset($_SESSION['ERROR'])){
        echo "<h4>".$_SESSION['ERROR']."</h4>";
    }
?>
<!-- Full name -->
<input name="name" type="text" id="defaultRegisterFormFullName" class="form-control mb-4" placeholder="Full Name" required>
<!-- E-mail -->
<input name="email" type="email" id="defaultRegisterFormEmail" class="form-control mb-4" placeholder="E-mail" required>
<!-- Username -->
<input name="username" type="text" id="defaultRegisterFormFullName" class="form-control mb-4" placeholder="Username" required>
<!-- Password -->
<input name="password" type="password" id="defaultRegisterFormPassword" class="form-control" placeholder="Password" aria-describedby="defaultRegisterFormPasswordHelpBlock" required>
<small id="defaultRegisterFormPasswordHelpBlock" class="form-text text-muted mb-4">
    At least 8 characters and 1 digit
</small>
<!-- Phone number -->
<input name="phone" type="text" id="defaultRegisterPhonePassword" class="form-control" placeholder="Phone number" aria-describedby="defaultRegisterFormPhoneHelpBlock" required>
<small id="defaultRegisterFormPhoneHelpBlock" class="form-text text-muted mb-4">
</small>

<!-- Sign up button -->
<button class="btn btn-info my-4 btn-block" type="submit">Sign Up</button>


<hr>

<!-- Terms of service -->

</form>
<!-- Default form register -->
            </div>
        </div>
    </div>    
</body>
</html>