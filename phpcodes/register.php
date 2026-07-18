<?php
include "header.php";
require_once "functions.php";

if($_SERVER['REQUEST_METHOD']=="POST"){
  $name=$_POST['full_name'];
  $email=$_POST['email'];
  $pass=$_POST['password'];
  $cpass=$_POST['confirm_password'];
  $addr=$_POST['address'];
  $contact=$_POST['contact_number'];

  if(!filter_var($email,FILTER_VALIDATE_EMAIL)){
    echo "<div class='card'>Invalid email.</div>";
  }
  elseif($pass!==$cpass){
    echo "<div class='card'>Passwords do not match.</div>";
  }
  else{
    $hash=password_hash($pass,PASSWORD_DEFAULT);
    $verified = 1; // auto-verified
    $st=$conn->prepare("INSERT INTO users(full_name,email,password_hash,address,contact_number,is_verified) VALUES(?,?,?,?,?,?)");
    $st->bind_param("sssssi",$name,$email,$hash,$addr,$contact,$verified);

    if($st->execute()){
      echo "<div class='card'>Registered successfully. You can now login.</div>";
    }else{
      echo "<div class='card'>Email already exists.</div>";
    }
  }
}
?>
<div class="card">
<h2>Register</h2>
<form method="post">
  <input name="full_name" placeholder="Complete name" required>
  <input type="email" name="email" placeholder="E-mail address" required>
  <input type="password" name="password" placeholder="Password" required>
  <input type="password" name="confirm_password" placeholder="Confirm Password" required>
  <textarea name="address" placeholder="Complete address" required></textarea>
  <input name="contact_number" placeholder="Contact number" required>
  <button type="submit">Register</button>
</form>
</div>
<?php include "footer.php"; ?>