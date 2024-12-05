<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "rfid_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Handle GET request to fetch name based on RFID tag
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['rfid_tag'])) {
  $rfid_tag = $_GET['rfid_tag'];

  $sql = "SELECT name FROM rfid_tags WHERE rfid_tag = '$rfid_tag'";
  $result = $conn->query($sql);

  if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo json_encode(["name" => $row["name"]]);
  } else {
    echo json_encode(["name" => "Unknown"]);
  }

  $conn->close();
}

// Handle POST request to store RFID tag, name, date, time, and status
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['rfid_tag']) && isset($_POST['name']) && isset($_POST['date']) && isset($_POST['time'])) {
  $rfid_tag = $_POST['rfid_tag'];
  $name = $_POST['name'];
  $date = $_POST['date'];
  $time = $_POST['time'];
  $status = $_POST['status'];

  $sql_insert = "INSERT INTO rfid_logs (rfid_tag, name, scan_date, scan_time, status) VALUES ('$rfid_tag', '$name', '$date', '$time', '$status')";

  if ($conn->query($sql_insert) === TRUE) {
    echo "New record created successfully";
  } else {
    echo "Error: " . $sql_insert . "<br>" . $conn->error;
  }

  $conn->close();
}
?>
