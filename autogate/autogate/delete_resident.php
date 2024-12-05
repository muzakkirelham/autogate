<?php
// Establish connection to the database
$connect = mysqli_connect("localhost", "root", "", "rfid");

// Check the connection
if (mysqli_connect_errno()) {
    die("Failed to connect to MySQL: " . mysqli_connect_error());
}

// Check if resident ID is provided in the URL
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $resident_id = $_GET['id'];

    // Display a confirmation dialog
    echo '<script>
            if (confirm("Are you sure you want to delete this resident?")) {
                window.location.href = "delete_resident.php?id=' . $resident_id . '&confirmed=1"; // Proceed with deletion
            } else {
                window.location.href = "view.php"; // Cancel deletion and go back to view.php
            }
          </script>';
} else {
    echo "Invalid request.";
}

// Check if the deletion is confirmed
if (isset($_GET['confirmed']) && $_GET['confirmed'] == 1) {
    // Delete resident from the database
    $query = "DELETE FROM resident WHERE id = $resident_id";

    if (mysqli_query($connect, $query)) {
        // Redirect back to view.php after successful deletion
        header("Location: view.php");
        exit; // Stop further execution
    } else {
        echo "Error deleting resident: " . mysqli_error($connect);
    }
}

// Close the database connection
mysqli_close($connect);
?>