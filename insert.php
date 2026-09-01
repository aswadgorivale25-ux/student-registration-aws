<?php
// Loads DB credentials from config.php (not committed to git).
// See config.sample.php for the expected format.
require __DIR__ . '/config.php';

$conn = new mysqli($host, $user, $password, $db);
if ($conn->connect_error) {
    die("Connection Failed: " . $conn->connect_error);
}

$firstname = $_POST['firstname'];
$lastname  = $_POST['lastname'];
$email     = $_POST['email'];
$mobile    = $_POST['mobile'];
$gender    = $_POST['gender'];
$dob       = $_POST['dob'];
$course    = $_POST['course'];
$address   = $_POST['address'];
$pass      = password_hash($_POST['password'], PASSWORD_DEFAULT);

$stmt = $conn->prepare(
    "INSERT INTO students
    (firstname,lastname,email,mobile,gender,dob,course,address,password)
    VALUES (?,?,?,?,?,?,?,?,?)"
);
$stmt->bind_param(
    "sssssssss",
    $firstname,
    $lastname,
    $email,
    $mobile,
    $gender,
    $dob,
    $course,
    $address,
    $pass
);

if ($stmt->execute()) {
    echo "<h2>Registration Successful!</h2>";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
