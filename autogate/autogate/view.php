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

        .update-button, .edit-button, .del-button, .send-button {
            padding: 5px 15px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 4px 2px;
            transition-duration: 0.4s;
            cursor: pointer;
            border-radius: 12px;
        }

        .update-button {
            background-color: #2196F3; /* Blue */
            color: white;
        }

        .update-button:hover {
            background-color: white;
            color: #2196F3;
        }

        .edit-button {
            background-color: #4CAF50; /* Green */
            color: white;
        }

        .edit-button:hover {
            background-color: white;
            color: #4CAF50;
        }

        .del-button {
            background-color: #D2042D;
            color: white;
        }

        .del-button:hover {
            background-color: white;
            color: #D2042D;
        }

        .send-button {
            border: none;
            background-color: #800080; /* Purple */
            color: white;

        }
        .send-button:hover {
            background-color: white;
            color: #800080; /* Purple */
        }

        .btn {
            border: 2px solid black;
            background-color: white;
            color: black;
            padding: 14px 28px;
            font-size: 16px;
            cursor: pointer;
        }

        .next {
            border-color: #2196F3;
            color: dodgerblue;
        }

        .next:hover {
            background: #2196F3;
            color: white;
        }

        .previous {
            border-color: #04AA6D;
            color: green;
        }

        .previous:hover {
            background: #04AA6D;
            color: white;
        }

        .filter {
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

        /* Toggle button styles */
        .switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 34px;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 26px;
            width: 26px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
        }

        input:checked + .slider {
            background-color: #2196F3;
        }

        input:checked + .slider:before {
            transform: translateX(26px);
        }

        /* Rounded sliders */
        .slider.round {
            border-radius: 34px;
        }

        .slider.round:before {
            border-radius: 50%;
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

<h1 align="center">Residents Information</h1>

<div align="center">
    <label>Resident Payment Status:</label>
    <label class="switch">
        <input type="checkbox" id="toggleSwitch">
        <span class="slider round"></span>
    </label>
    <span id="toggleStatus">Auto</span>
</div>

<div class="filter-container" align="center">
    <form method="GET">
        <!-- Filter form fields (same as before) -->
        <label for="resident_name">Resident Name:</label>
        <input type="text" id="resident_name" name="resident_name" value="<?php echo isset($_GET['resident_name']) ? $_GET['resident_name'] : ''; ?>">
        
        <label for="card_id">RFID Number:</label>
        <input type="text" id="card_id" name="card_id" value="<?php echo isset($_GET['card_id']) ? $_GET['card_id'] : ''; ?>">

        <label for="address">Address:</label>
        <input type="text" id="address" name="address" value="<?php echo isset($_GET['address']) ? $_GET['address'] : ''; ?>">
        
        <label for="payment_status">Payment Status:</label>
        <select id="payment_status" name="payment_status">
            <option value="">All</option>
            <option value="Paid" <?php echo isset($_GET['payment_status']) && $_GET['payment_status'] === 'Paid' ? 'selected' : ''; ?>>Paid</option>
            <option value="Overdue within 3 months" <?php echo isset($_GET['payment_status']) && $_GET['payment_status'] === 'Overdue within 3 months' ? 'selected' : ''; ?>>Overdue within 3 months</option>
            <option value="Overdue over 3 months" <?php echo isset($_GET['payment_status']) && $_GET['payment_status'] === 'Overdue over 3 months' ? 'selected' : ''; ?>>Overdue over 3 months</option>
        </select>

        <label for="payment_month">Payment Month:</label>
        <input type="text" id="payment_month" name="payment_month" value="<?php echo isset($_GET['payment_month']) ? $_GET['payment_month'] : ''; ?>">
        
        <button type="submit" class="filter">Filter</button>
    </form>
</div>

<?php
// Conditionally include the update_payment_status.php script based on toggle state
if (!isset($_COOKIE['toggleState']) || $_COOKIE['toggleState'] === 'auto') {
    include 'update_payment_status.php';
}

// Database connection and query logic
$connect = mysqli_connect("localhost", "root", "", "rfid");

// Check connection
if (mysqli_connect_errno()) {
    die("Failed to connect to MySQL: " . mysqli_connect_error());
}

// Fetching data logic
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$limit = 5;
$start = ($page - 1) * $limit;

// Initialize filter variables
$whereClause = "";
$conditions = array();

// Check if Resident Name filter is provided
if (!empty($_GET['resident_name'])) {
    $conditions[] = "residentname LIKE '%" . mysqli_real_escape_string($connect, $_GET['resident_name']) . "%'";
}

// Check if RFID Number filter is provided
if (!empty($_GET['card_id'])) {
    $conditions[] = "cardid LIKE '%" . mysqli_real_escape_string($connect, $_GET['card_id']) . "%'";
}

// Check if Address filter is provided
if (!empty($_GET['address'])) {
    $conditions[] = "address LIKE '%" . mysqli_real_escape_string($connect, $_GET['address']) . "%'";
}

// Check if Payment Status filter is provided
if (!empty($_GET['payment_status'])) {
    $conditions[] = "paymentstatus = '" . mysqli_real_escape_string($connect, $_GET['payment_status']) . "'";
}

// Check if Payment Month filter is provided
if (!empty($_GET['payment_month'])) {
    $conditions[] = "paymentmonth = '" . mysqli_real_escape_string($connect, $_GET['payment_month']) . "'";
}

// Construct the WHERE clause based on filters
if (!empty($conditions)) {
    $whereClause = "WHERE " . implode(" AND ", $conditions);
}

// Query to fetch total number of records
$totalQuery = "SELECT COUNT(*) as total FROM resident $whereClause";
$totalResult = mysqli_query($connect, $totalQuery);
$totalRow = mysqli_fetch_assoc($totalResult);
$totalRecords = $totalRow['total'];
$totalPages = ceil($totalRecords / $limit);

// Query to fetch the filtered data
$query = "SELECT id, residentname, gender, race, phone_no, cardid, platenumber, address, email, paymentmonth, paymentstatus FROM resident $whereClause ORDER BY id LIMIT $start, $limit";
$result = mysqli_query($connect, $query);

if ($result) {
    echo '<div class="table-container">';
    echo '<table border="5" align="center">
    <tr>
        <td><b>No.</b></td>
        <td><b>Resident Name</b></td>
        <td><b>Gender</b></td>
        <td><b>Race</b></td>
        <td><b>Phone Number</b></td>
        <td><b>RFID Number</b></td>
        <td><b>Plate Number</b></td>
        <td><b>Address</b></td>
        <td><b>Email</b></td>
        <td><b>Latest Payment</b></td>
        <td><b>Payment Status</b></td>
        <td><b>Update</b></td>
        <td><b>Edit</b></td>
        <td><b>Delete</b></td>
        <td><b>Send Email</b></td>
    </tr>';

    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        echo '<tr>
        <td>' . $row['id'] . '</td>
        <td>' . $row['residentname'] . '</td>
        <td>' . $row['gender'] . '</td>
        <td>' . $row['race'] . '</td>
        <td>' . $row['phone_no'] . '</td>
        <td>' . $row['cardid'] . '</td>
        <td>' . $row['platenumber'] . '</td>
        <td>' . $row['address'] . '</td>
        <td>' . $row['email'] . '</td>
        <td>' . $row['paymentmonth'] . '</td>
        <td>' . $row['paymentstatus'] . '</td>
        <td><a href="update_resident.php?id=' . $row['id'] . '" class="update-button">Update</a></td>
        <td><a href="edit_resident.php?id=' . $row['id'] . '" class="edit-button edit-btn">Edit</a></td>
        <td><a href="delete_resident.php?id=' . $row['id'] . '" class="del-button">Delete</a></td>
        <td><button class="send-button" onclick="sendEmail(' . $row['id'] . ')">Send</button></td>
        </tr>';
    }
    echo '</table>';
    echo '</div>';
}
{
    // Pagination links
    echo '<div class="pagination">';
    echo '<div align="center">';
    if ($page > 1) {
        echo '<a href="view.php?page=' . ($page - 1) . '" button class="btn previous">Previous</button></a>&nbsp;&nbsp;';
    }
    if ($page < $totalPages) {
        echo '<a href="view.php?page=' . ($page + 1) . '" button class="btn next">Next</button></a>';
    }
    echo '</div>';
    echo '</div>';
}

// Close database connection
mysqli_close($connect);
?>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        let toggleSwitch = document.getElementById('toggleSwitch');
        let toggleStatus = document.getElementById('toggleStatus');
        let editButtons = document.querySelectorAll('.edit-btn');

        // Retrieve saved toggle state from localStorage or fallback to cookie
        let savedState = localStorage.getItem('toggleState') || getCookie('toggleState');

        // Set the toggle and button state based on saved state
        if (savedState === 'manual') {
            toggleSwitch.checked = true;
            toggleStatus.innerHTML = 'Manual';
            enableEditButtons();
            document.cookie = "toggleState=manual; path=/";
        } else {
            toggleSwitch.checked = false;
            toggleStatus.innerHTML = 'Auto';
            disableEditButtons();
            document.cookie = "toggleState=auto; path=/";
        }

        // Event listener for toggle switch changes
        toggleSwitch.addEventListener('change', function () {
            if (this.checked) {
                toggleStatus.innerHTML = 'Manual';
                enableEditButtons();
                localStorage.setItem('toggleState', 'manual'); // Save state in localStorage
                document.cookie = "toggleState=manual; path=/"; // Also update cookie
            } else {
                toggleStatus.innerHTML = 'Auto';
                disableEditButtons();
                localStorage.setItem('toggleState', 'auto'); // Save state in localStorage
                document.cookie = "toggleState=auto; path=/"; // Also update cookie
                // Optionally, you could reload the page here if necessary:
                // location.reload();
            }
        });

        // Enable the "Edit" buttons
        function enableEditButtons() {
            editButtons.forEach(function(button) {
                button.disabled = false;
                button.style.opacity = "1";
                button.style.pointerEvents = "auto";
            });
        }

        // Disable the "Edit" buttons
        function disableEditButtons() {
            editButtons.forEach(function(button) {
                button.disabled = true;
                button.style.opacity = "0.5";
                button.style.pointerEvents = "none";
            });
        }

        // Utility function to get cookies
        function getCookie(name) {
            let cookieArr = document.cookie.split(";");

            for (let i = 0; i < cookieArr.length; i++) {
                let cookiePair = cookieArr[i].split("=");

                if (name === cookiePair[0].trim()) {
                    return decodeURIComponent(cookiePair[1]);
                }
            }

            return null;
        }
    });

    function sendEmail(id) {
        let xhr = new XMLHttpRequest();
        xhr.open("GET", "send_email.php?id=" + id, true);
        xhr.onreadystatechange = function () {
            if (xhr.readyState == 4 && xhr.status == 200) {
                alert("Email sent successfully!");
            }
        };
        xhr.send();
    }
</script>
</body>
</html>
