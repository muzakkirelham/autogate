<?php
// Establish connection to the database
$connect = mysqli_connect("localhost", "root", "", "rfid");

// Check the connection
if (mysqli_connect_errno()) {
    die("Failed to connect to MySQL: " . mysqli_connect_error());
}

// Initialize pagination variables
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$limit = 5; // Number of records per page
$start = ($page - 1) * $limit;

// Initialize filter variables
$whereClause = "";
$conditions = array();

// Check if Resident Name filter is provided
if (!empty($_GET['resident_name'])) {
    $conditions[] = "residentname LIKE '%" . mysqli_real_escape_string($connect, $_GET['resident_name']) . "%'";
}

// Check if Card ID filter is provided
if (!empty($_GET['card_id'])) {
    $conditions[] = "cardid LIKE '%" . mysqli_real_escape_string($connect, $_GET['card_id']) . "%'";
}

// Check if Plate Number filter is provided
if (!empty($_GET['plate_number'])) {
    $conditions[] = "platenumber LIKE '%" . mysqli_real_escape_string($connect, $_GET['plate_number']) . "%'";
}

// Check if Address filter is provided
if (!empty($_GET['address'])) {
    $conditions[] = "address LIKE '%" . mysqli_real_escape_string($connect, $_GET['address']) . "%'";
}

// Check if Status filter is provided
if (!empty($_GET['status'])) {
    $conditions[] = "status = '" . mysqli_real_escape_string($connect, $_GET['status']) . "'";
}

// Construct the WHERE clause
if (!empty($conditions)) {
    $whereClause = "WHERE " . implode(" AND ", $conditions);
}

// Query to fetch filtered monitor data with pagination in reverse order
$query = "SELECT id, residentname, cardid, platenumber, address, scan_date, scan_time, status FROM monitor $whereClause ORDER BY id DESC LIMIT $start, $limit";
$result = mysqli_query($connect, $query);

// Display the table
echo '<table class="table-container">';
echo '<tr>
        <th>ID</th>
        <th>Resident Name</th>
        <th>RFID Number</th>
        <th>Plate Number</th>
        <th>Address</th>
        <th>Scan Date</th>
        <th>Scan Time</th>
        <th>Status</th>
    </tr>';

while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
    echo '<tr>
        <td>' . $row['id'] . '</td>
        <td>' . $row['residentname'] . '</td>
        <td>' . $row['cardid'] . '</td>
        <td>' . $row['platenumber'] . '</td>
        <td>' . $row['address'] . '</td>
        <td>' . $row['scan_date'] . '</td>
        <td>' . $row['scan_time'] . '</td>
        <td>' . $row['status'] . '</td>
        </tr>';
}

echo '</table>';

// Close the database connection
mysqli_close($connect);
?>
