<?php
// Establish connection to the database
$connect = mysqli_connect("localhost", "root", "", "rfid");

// Check the connection
if (mysqli_connect_errno()) {
    die("Failed to connect to MySQL: " . mysqli_connect_error());
}

// Get current date (Malaysia timezone)
date_default_timezone_set('Asia/Kuala_Lumpur');
$currentDate = new DateTime();
$currentMonth = (int)$currentDate->format('m');
$currentYear = (int)$currentDate->format('Y');

// Fetch all residents and update their payment status based on the latest payment
$query = "SELECT id, paymentmonth FROM resident";
$result = mysqli_query($connect, $query);

while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
    // Parse payment month (assumes 'paymentmonth' is stored as 'Month Year', e.g., 'January 2024')
    $latestPaymentMonth = DateTime::createFromFormat('F Y', $row['paymentmonth']);

    // Calculate month difference between the current date and the latest payment month
    $interval = $currentDate->diff($latestPaymentMonth);
    $monthsPassed = ($interval->y * 12) + $interval->m;

    // Check if the latest payment is in the future or past
    $isFuturePayment = $latestPaymentMonth > $currentDate;
    $paymentYear = (int)$latestPaymentMonth->format('Y');

    // Determine payment status based on the conditions provided
    if ($paymentYear > $currentYear) {
        // Payment is in a future year
        $paymentStatus = 'Paid';
    } elseif ($monthsPassed > 3) {
        // More than 3 months in the past
        $paymentStatus = 'Overdue over 3 months';
    } elseif ($monthsPassed > 1 && !$isFuturePayment) {
        // Within 3 months but more than 1 month in the past
        $paymentStatus = 'Overdue within 3 months';
    } else {
        // Within 1 month in the past or any time in the current or future year
        $paymentStatus = 'Paid';
    }

    // Update the payment status in the database
    $updateQuery = "UPDATE resident SET paymentstatus = '" . mysqli_real_escape_string($connect, $paymentStatus) . "' WHERE id = " . $row['id'];
    mysqli_query($connect, $updateQuery);
}

// Close connection
mysqli_close($connect);
?>
