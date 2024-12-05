<?php
// Connect to the database
$connect = mysqli_connect("localhost", "root", "", "rfid");

// Check the connection
if (mysqli_connect_errno()) {
    die("Failed to connect to MySQL: " . mysqli_connect_error());
}

// Get the date from the AJAX request
$selected_date = isset($_GET['scan_date']) ? $_GET['scan_date'] : date('Y-m-d');

// Query to count "IN/OUT" status for the selected date
$statusQuery = "SELECT status, COUNT(*) AS count 
                FROM monitor 
                WHERE scan_date = '$selected_date'
                GROUP BY status";
$statusResult = mysqli_query($connect, $statusQuery);
$statusData = [];

while ($row = mysqli_fetch_assoc($statusResult)) {
    $statusData[] = [
        'name' => $row['status'],
        'y' => (int)$row['count']
    ];
}

// Query to count race distribution (no date filter needed)
$raceQuery = "SELECT race, COUNT(*) AS count FROM resident GROUP BY race";
$raceResult = mysqli_query($connect, $raceQuery);
$raceData = [];

while ($row = mysqli_fetch_assoc($raceResult)) {
    $raceData[] = [
        'name' => $row['race'],
        'y' => (int)$row['count']
    ];
}

// Query to count payment distribution by paymentmonth
$paymentQuery = "SELECT paymentmonth, COUNT(*) AS count FROM payment GROUP BY paymentmonth";
$paymentResult = mysqli_query($connect, $paymentQuery);
$paymentData = [];

// Define an array to map each month to a numeric index for sorting
$monthOrder = [
    'January 2024' => 1, 'February 2024' => 2, 'March 2024' => 3, 'April 2024' => 4,
    'May 2024' => 5, 'June 2024' => 6, 'July 2024' => 7, 'August 2024' => 8,
    'September 2024' => 9, 'October 2024' => 10, 'November 2024' => 11, 'December 2024' => 12
];

while ($row = mysqli_fetch_assoc($paymentResult)) {
    $paymentData[] = [
        'name' => $row['paymentmonth'],
        'y' => (int)$row['count'],
        'order' => $monthOrder[$row['paymentmonth']] ?? 0 // Assign the month order for sorting
    ];
}

// Sort the payment data by the assigned month order
usort($paymentData, function($a, $b) {
    return $a['order'] <=> $b['order'];
});

// Remove the 'order' key after sorting as it is no longer needed
foreach ($paymentData as &$data) {
    unset($data['order']);
}

// Output the result as JSON
echo json_encode([
    'statusData' => $statusData,
    'raceData' => $raceData,
    'paymentData' => $paymentData
]);

// Close the connection
mysqli_close($connect);
?>
