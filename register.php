<?php

include 'config.php';
session_start();
if(isset($_POST['submit'])){

   $fullname = mysqli_real_escape_string($connection, $_POST['fullname']);
   $username = mysqli_real_escape_string($connection, $_POST['username']);
   $email = mysqli_real_escape_string($connection, $_POST['email']);
   $phoneno = mysqli_real_escape_string($connection, $_POST['phoneno']);
   $pass = mysqli_real_escape_string($connection, md5($_POST['password']));
   $cpass = mysqli_real_escape_string($connection, md5($_POST['cpassword']));
   $role = "user";

   if($_SESSION["user_role"]!=""&& $_SESSION["user_role"]=="super_admin"){
      $role=" admin";
   }

   $select = mysqli_query($connection, "SELECT * FROM `user_info` WHERE email = '$email' AND password = '$pass'") or die('query failed');

  


   if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $errors[] = 'Invalid email format';
   }

   // Validate phone number (assuming Sri Lankan phone number format)
   if (!preg_match('/^(?:\+94|0)[1-9]\d{8}$/', $phoneno)) {
      $errors[] = 'Invalid phone number format (e.g., +94123456789 or 0712345678)';
   }

   if (empty($errors)) {
      $select = mysqli_query($connection, "SELECT * FROM user_info WHERE email = '$email' AND password = '$pass'") or die('query failed');

      if(mysqli_num_rows($select) > 0){
         $errors[] = 'User already exists!';
      } else {
         mysqli_query($connection, "INSERT INTO user_info (fullname,username, email, phonenumber, password, role) VALUES('$fullname', '$username', '$email','$phoneno', '$pass','$role')") or die('query failed');
         header('location: login.php');
         exit();
      }
   }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>register</title>

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>

<?php
if(isset($message)){
   foreach($message as $message){
      echo '<div class="message" onclick="this.remove();">'.$message.'</div>';
   }
}
?>
   
<div class="form-container">

   <form action="" method="post">
      <h3>Register Now</h3>
    
      <input type="text" name="fullname" required placeholder="enter fullname" class="box" >
      <input type="text" name="username" required placeholder="enter username" class="box">
      <input type="email" name="email" required placeholder="enter email" class="box">
      <input type="tell" name="phoneno" required placeholder="enter phone number" class="box">
      <input type="password" name="password" required placeholder="enter password" class="box">
      <input type="password" name="cpassword" required placeholder="confirm password" class="box">
     
      <input type="submit" name="submit" class="btn" value="register">
      <?php

      if (!isset($_SESSION["user_role"])) {
         echo '<p>already have an account? <a href="login.php">login here</a></p>';
      }
   ?>
      
   </form>

</div>

</body>
</html>