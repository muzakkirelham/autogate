<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "rfid";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Handle GET request to fetch residentname, platenumber, address, and paymentstatus based on cardid
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['cardid'])) {
  $cardid = $_GET['cardid'];

  $sql = "SELECT residentname, platenumber, address, paymentstatus FROM resident WHERE cardid = '$cardid'";
  $result = $conn->query($sql);

  if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();

    // Check if paymentstatus is "Overdue over 3 months"
    if ($row["paymentstatus"] != "Overdue over 3 months") {
      echo json_encode([
        "residentname" => $row["residentname"], 
        "platenumber" => $row["platenumber"], 
        "address" => $row["address"]
      ]);
    } else {
      echo json_encode(["residentname" => "Unknown"]);
    }
  } else {
    echo json_encode(["residentname" => "Unknown"]);
  }

  $conn->close();
}

// Handle POST request to store cardid, residentname, platenumber, address, date, time, and status
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['cardid']) && isset($_POST['residentname']) && isset($_POST['platenumber']) && isset($_POST['address']) && isset($_POST['date']) && isset($_POST['time']) && isset($_POST['status'])) {
  $cardid = $_POST['cardid'];
  $residentname = $_POST['residentname'];
  $platenumber = $_POST['platenumber'];
  $address = $_POST['address'];
  $date = $_POST['date'];
  $time = $_POST['time'];
  $status = $_POST['status'];

  $sql_insert = "INSERT INTO monitor (cardid, residentname, platenumber, address, scan_date, scan_time, status) VALUES ('$cardid', '$residentname', '$platenumber', '$address', '$date', '$time', '$status')";

  if ($conn->query($sql_insert) === TRUE) {
    echo "New record created successfully";
  } else {
    echo "Error: " . $sql_insert . "<br>" . $conn->error;
  }

  $conn->close();
}
?>
