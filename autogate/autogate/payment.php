<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" type="text/css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .navbar {
            font-family: Arial, sans-serif;
            overflow: hidden;
            background-color: #333;
            padding: 10px 20px;
        }

        .navbar a {
            float: left;
            display: block;
            color: #f2f2f2;
            text-align: center;
            padding: 14px 16px;
            text-decoration: none;
        }

        .navbar a:hover {
            background-color: #ddd;
            color: black;
        }

        .navbar a.active {
            background-color: #04AA6D;
            color: white;
        }

        .table-container {
            width: 100%;
            margin: 20px auto;
            border-collapse: collapse;
        }

        .table-container th, .table-container td {
            padding: 8px;
            border: 1px solid #ddd;
            text-align: left;
        }

        .table-container th {
            background-color: #f2f2f2;
        }

        .table-container tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .table-container tr:hover {
            background-color: #ddd;
        }
        .btn {
            border: 2px solid black;
            background-color: white;
            color: black;
            padding: 14px 28px;
            font-size: 16px;
            cursor: pointer;
        }
        .next{
            border-color: #2196F3;
            color: dodgerblue;
        }
        .next:hover {
            background: #2196F3;
            color: white;
        }
        .previous{
            border-color: #04AA6D;
            color: green;
        }
        .previous:hover {
            background: #04AA6D;
            color: white;
        }
        .filter{
            border: 2px solid black;
            border-color: #AEC6CF;
            color: dodgerblue;
            padding: 12px 17px;
            font-size: 12px;
            cursor: pointer;
        }
        .filter:hover {
            background: #2196F3;
            color: white;
        }
    </style>
</head>
<body>
<div class="navbar">
    <a href="admin.php">Admin</a>
    <a href="view.php">View</a>
    <a href="monitor.php">Monitor</a>
    <a href="statistics.php">Statistics</a>
    <a href="payment.php">Payment</a>
</div>
<h1 align="center">Payment History</h1>

<div class="filter-container" align="center">
    <form method="GET">
        <label for="resident_name">Resident Name:</label>
        <input type="text" id="resident_name" name="resident_name" value="<?php echo isset($_GET['resident_name']) ? $_GET['resident_name'] : ''; ?>">

        <label for="address">Address:</label>
        <input type="text" id="address" name="address" value="<?php echo isset($_GET['address']) ? $_GET['address'] : ''; ?>">

        <label for="paymentdate">Payment Date:</label>
        <input type="text" id="paymentdate" name="paymentdate" value="<?php echo isset($_GET['paymentdate']) ? $_GET['paymentdate'] : ''; ?>">

        <label for="paymentmonth">Payment Month:</label>
        <input type="text" id="paymentmonth" name="paymentmonth" value="<?php echo isset($_GET['paymentmonth']) ? $_GET['paymentmonth'] : ''; ?>">

        <button type="submit" class="filter">Filter</button>
    </form>
</div>

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

// Check if Address filter is provided
if (!empty($_GET['address'])) {
    $conditions[] = "address LIKE '%" . mysqli_real_escape_string($connect, $_GET['address']) . "%'";
}

// Check if Payment Date filter is provided
if (!empty($_GET['paymentdate'])) {
    $conditions[] = "paymentdate LIKE '%" . mysqli_real_escape_string($connect, $_GET['paymentdate']) . "%'";
}

// Check if Payment Month filter is provided
if (!empty($_GET['paymentmonth'])) {
    $conditions[] = "paymentmonth LIKE '%" . mysqli_real_escape_string($connect, $_GET['paymentmonth']) . "%'";
}

// Construct the WHERE clause
if (!empty($conditions)) {
    $whereClause = "WHERE " . implode(" AND ", $conditions);
}

// Query to fetch total number of records
$totalQuery = "SELECT COUNT(*) as total FROM payment $whereClause";
$totalResult = mysqli_query($connect, $totalQuery);
$totalRow = mysqli_fetch_assoc($totalResult);
$totalRecords = $totalRow['total'];
$totalPages = ceil($totalRecords / $limit);

// Query to fetch filtered payment data with pagination in reverse order
$query = "SELECT id, residentname, address, paymentdate, paymentmonth FROM payment $whereClause ORDER BY id DESC LIMIT $start, $limit";
$result = mysqli_query($connect, $query);

if ($result) {
    echo '<div class="table-container">';
    echo '<div align="center">';
    echo '<table border="5">
    <tr>
        <td><b>No.</b></td>
        <td><b>Resident Name</b></td>
        <td><b>Address</b></td>
        <td><b>Payment Date</b></td>
        <td><b>Payment Month</b></td>
    </tr>';

    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        echo '<tr>
        <td>' . $row['id'] . '</td>
        <td>' . $row['residentname'] . '</td>
        <td>' . $row['address'] . '</td>
        <td>' . $row['paymentdate'] . '</td>
        <td>' . $row['paymentmonth'] . '</td>
        </tr>';
    }

    echo '</table>';
    echo '</div>';
    echo '</div>';

// Pagination links
echo '<div class="pagination">';
echo '<div align="center">';
$filterParams = "";
if (!empty($_GET['resident_name'])) {
    $filterParams .= "&resident_name=" . urlencode($_GET['resident_name']);
}
if (!empty($_GET['address'])) {
    $filterParams .= "&address=" . urlencode($_GET['address']);
}
if (!empty($_GET['paymentdate'])) {
    $filterParams .= "&paymentdate=" . urlencode($_GET['paymentdate']);
}
if (!empty($_GET['paymentmonth'])) {
    $filterParams .= "&paymentmonth=" . urlencode($_GET['paymentmonth']);
}

if ($page > 1) {
    echo '<a href="payment.php?page=' . ($page - 1) . $filterParams . '" class="btn previous">Previous</a>&nbsp;&nbsp;';
}
if ($page < $totalPages) {
    echo '<a href="payment.php?page=' . ($page + 1) . $filterParams . '" class="btn next">Next</a>';
}
echo '</div>';
echo '</div>';
}

// Close the database connection
mysqli_close($connect);
?>
</body>
</html>
