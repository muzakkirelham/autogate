<?php
// Establish connection to the database
$connect = mysqli_connect("localhost", "root", "", "rfid");

// Check the connection
if (mysqli_connect_errno()) {
    die("Failed to connect to MySQL: " . mysqli_connect_error());
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $resident_id = mysqli_real_escape_string($connect, $_POST['resident_id']);
    $payment_date = mysqli_real_escape_string($connect, $_POST['payment_date']);
    $payment_year = mysqli_real_escape_string($connect, $_POST['payment_year']);
    $payment_month = mysqli_real_escape_string($connect, $_POST['payment_month']);
    $payment_month = $payment_month . ' ' . $payment_year;

    // Update resident payment information
    $update_query = "UPDATE resident SET paymentdate = '$payment_date', paymentmonth = '$payment_month' WHERE id = $resident_id";
    
    if (mysqli_query($connect, $update_query)) {
        // Fetch the updated resident information
        $fetch_query = "SELECT residentname, address FROM resident WHERE id = $resident_id";
        $fetch_result = mysqli_query($connect, $fetch_query);
        if ($fetch_result && mysqli_num_rows($fetch_result) > 0) {
            $resident = mysqli_fetch_assoc($fetch_result);
            $residentname = $resident['residentname'];
            $address = $resident['address'];

            // Insert into payment table
            $insert_query = "INSERT INTO payment (residentname, address, paymentdate, paymentmonth) VALUES ('$residentname', '$address', '$payment_date', '$payment_month')";
            mysqli_query($connect, $insert_query);
        }
        
        echo "Payment information updated successfully.";
        echo '<br><a href="view.php">Go Back</a>';
    } else {
        echo "Error updating payment information: " . mysqli_error($connect);
    }
}

// Close the database connection
mysqli_close($connect);
?>
