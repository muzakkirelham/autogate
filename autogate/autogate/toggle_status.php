<?php
// Establish connection to the database
$connect = mysqli_connect("localhost", "root", "", "rfid");

// Check the connection
if (mysqli_connect_errno()) {
    die("Failed to connect to MySQL: " . mysqli_connect_error());
}

// Get the resident ID
$id = isset($_GET['id']) ? $_GET['id'] : 0;

// Fetch the current status
$query = "SELECT status FROM resident WHERE id = $id";
$result = mysqli_query($connect, $query);
$row = mysqli_fetch_assoc($result);

$newStatus = $row['status'] == 'Block' ? 'Unblock' : 'Block';

// Update the status
$updateQuery = "UPDATE resident SET status = '$newStatus' WHERE id = $id";
mysqli_query($connect, $updateQuery);

// Close the database connection
mysqli_close($connect);

echo "Status updated";
?>
