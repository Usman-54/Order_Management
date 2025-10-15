<?php
// db.php
$host = "localhost";  // change if different
$user = "root";       // change if different
$pass = "admin";           // change if different
$dbname = "resturent"; // your DB name from SQL dump

$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
?>
