<?php
$error=''; //Variable to Store error message;
if (isset($_POST['submit'])) {
    if (empty($_POST['email']) || empty($_POST['password'])) {
        $error = "Username or Password is Invalid";
    } else {
        //Define $user and $pass
        $email=$_POST['email'];
        $password=$_POST['password'];
        //Establishing Connection with server by passing server_name, user_id and pass as a patameter
        $conn = mysqli_connect("localhost", "root", "");
        //Selecting Database
        $db = mysqli_select_db($conn, "cyberbullying");
        //sql query to fetch information of registerd user and finds user match.
        $query = mysqli_query($conn, "SELECT * FROM users WHERE password='$password' AND email='$email'");
        $rows = mysqli_num_rows($query);
        if ($rows == 1) {
            echo 1;
            header("Location: analyze.php"); // Redirecting to other page
        } else {
           header("Location: login.php?error=Password_invalid");
        }
        mysqli_close($conn); // Closing connection
    }
}
