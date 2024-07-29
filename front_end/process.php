<?php
ini_set("pcre.jit", 0);
$firstname = "";
$lastname = "";
$email = "";
$password = "";
$db = mysqli_connect('localhost', 'root', '', 'cyberbullying');
if($db == false){
  echo 'Not Connected';
  exit();
}
$firstname = ($_POST['firstname']);
$lastname = ($_POST['lastname']);
$email = ($_POST['email']);
$email_array= explode(".", $email);
$count= sizeof($email_array);
$password = ($_POST['password']);
$cPassword = ($_POST['cPassword']);
if($count==1){
  echo "<script> window.location.replace('register.php?error=11') </script>";
       // header("Location: register.php?error=11");
           //There is no email domain
}
    if($count>1){
      $repeat=0;
      for ($x=0; $x<$count; $x++) {
        # code...
        if($email_array[$x]=="com"){
          $repeat=$repeat+1;
        }
      }
      if($repeat>1 ){
  echo "<script> window.location.replace('register.php?error=12') </script>";

           // header("Location: register.php?error=12");
           //Email is invalid
      }
    }
    if (!preg_match('/^[a-z]*$/i', $firstname)) {
  echo "<script> window.location.replace('register.php?error=1') </script>";
           // header("Location: register.php?error=1");
    // array_push($errors, "First Name should only contain letters");
    }
    if(strrpos($firstname, ' ') !== false){

  echo "<script> window.location.replace('register.php?error=2') </script>";
           // header("Location: register.php?error=2");
    // array_push($errors, "First Name should only contain letters");
    }

    if (!preg_match('/^[a-z]*$/i', $lastname)) {
  echo "<script> window.location.replace('register.php?error=9') </script>";
           // header("Location: register.php?error=9");
    // array_push($errors, "First Name should only contain letters");
    }
    if(strrpos($lastname, ' ') !== false){

  echo "<script> window.location.replace('register.php?error=10') </script>";
           // header("Location: register.php?error=10");
    // array_push($errors, "Last Name should only contain 1 word");
    }

    
    if($password == $cPassword){
    if (strlen($password) <= '8') {

  echo "<script> window.location.replace('register.php?error=3') </script>";
           // header("Location: register.php?error=3");
        // $passwordErr = "Your Password Must Contain At Least 8 Characters!";
    }
    if(!preg_match("#[0-9]+#",$password)) {
  echo "<script> window.location.replace('register.php?error=4') </script>";

           // header("Location: register.php?error=4");
        // $passwordErr = "Your Password Must Contain At Least 1 Number!";
    }
    if(!preg_match("#[A-Z]+#",$password)) {
  echo "<script> window.location.replace('register.php?error=5') </script>";
           // header("Location: register.php?error=5");
        // $passwordErr = "Your Password Must Contain At Least 1 Capital Letter!";
    }
    if(!preg_match("#[a-z]+#",$password)) {

  echo "<script> window.location.replace('register.php?error=6') </script>";
           // header("Location: register.php?error=6");
        // $passwordErr = "Your Password Must Contain At Least 1 Lowercase Letter!";
    }
    if(!preg_match("@[^\w]@", $password)){
  echo "<script> window.location.replace('register.php?error=7') </script>";
            // header("Location: register.php?error=7");
        // $passwordErr = "Your Password Must Contain At Least 1 Special Letter!";
    
    }
   if($password != $cPassword){
  echo "<script> window.location.replace('register.php?error=8') </script>";
            // header("Location: register.php?error=8");
    
  }
    $query = mysqli_query($db, "SELECT * FROM users WHERE email='$email'");
        $rows = mysqli_num_rows($query);
        if ($rows> 0) {

  echo "<script> window.location.replace('register.php?error=13') </script>";
            // header("Location: register.php?error=13"); // Redirecting to other page
        }else{
            $sql = "INSERT INTO users (firstname, lastname, email, password) VALUES ('$firstname', '$lastname', '$email', '$password')";
            mysqli_query($db, $sql);
  echo "<script> window.location.replace('login.php') </script>";
  
             // header("Location: login.php");
        }  }
