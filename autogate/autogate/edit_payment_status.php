<?php
// Establish connection to the database
$connect = mysqli_connect("localhost", "root", "", "rfid");

// Check the connection
if (mysqli_connect_errno()) {
    die("Failed to connect to MySQL: " . mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if resident ID and payment status are set
    if (isset($_POST['resident_id']) && isset($_POST['payment_status'])) {
        $resident_id = $_POST['resident_id'];
        $payment_status = $_POST['payment_status'];

        // Update payment status in the database
        $query = "UPDATE resident SET paymentstatus = '$payment_status' WHERE id = $resident_id";

        if (mysqli_query($connect, $query)) {
        // Redirect to view.php after successful update
        header("Location: view.php");
        exit; // Stop further execution
        } else {
            echo "Error updating payment status: " . mysqli_error($connect);
        }
    } else {
        echo "Invalid request.";
    }
} else {
    echo "Method not allowed.";
}

// Close the database connection
mysqli_close($connect);
?>