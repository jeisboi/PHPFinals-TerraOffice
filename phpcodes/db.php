<?php
$conn = new mysqli("localhost","root","","terraoffice_db");
if($conn->connect_error){ die("DB Error: ".$conn->connect_error); }
session_start();
?>