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
            width: 80%;
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
            margin: 5px;
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
        .filter-container {
            text-align: center;
            margin-bottom: 20px;
        }
        .table-wrapper {
            text-align: center;
        }
    </style>
    <script>
        // Function to auto-refresh the table content every 5 seconds
        function autoRefresh() {
            setInterval(function() {
                var xhr = new XMLHttpRequest();
                var resident_name = document.getElementById('resident_name').value;
                var card_id = document.getElementById('card_id').value;
                var plate_number = document.getElementById('plate_number').value;
                var address = document.getElementById('address').value;
                var status = document.getElementById('status').value;
                var page = <?php echo isset($_GET['page']) ? $_GET['page'] : 1; ?>;
                var queryString = `fetch_table_data.php?page=${page}&resident_name=${resident_name}&card_id=${card_id}&plate_number=${plate_number}&address=${address}&status=${status}`;
                xhr.open('GET', queryString, true);
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        document.getElementById('table-content').innerHTML = xhr.responseText;
                    }
                };
                xhr.send();
            }, 5000); // 5000 milliseconds = 5 seconds
        }

        // Function to update the Next and Previous button links with filter values
        function updatePaginationLinks() {
            var resident_name = document.getElementById('resident_name').value;
            var card_id = document.getElementById('card_id').value;
            var plate_number = document.getElementById('plate_number').value;
            var address = document.getElementById('address').value;
            var status = document.getElementById('status').value;
            var page = <?php echo isset($_GET['page']) ? $_GET['page'] : 1; ?>;

            var nextPage = page + 1;
            var prevPage = page - 1;

            var queryString = `resident_name=${resident_name}&card_id=${card_id}&plate_number=${plate_number}&address=${address}&status=${status}`;

            var nextButton = document.getElementById('next-button');
            var prevButton = document.getElementById('prev-button');

            if (nextButton) {
                nextButton.href = `monitor.php?page=${nextPage}&${queryString}`;
            }

            if (prevButton) {
                prevButton.href = `monitor.php?page=${prevPage}&${queryString}`;
            }
        }

        window.onload = function() {
            autoRefresh();
            updatePaginationLinks();
        };
    </script>
</head>
<body>
<div class="navbar">
    <a href="admin.php">Admin</a>
    <a href="view.php">View</a>
    <a href="monitor.php">Monitor</a>
    <a href="statistics.php">Statistics</a>
    <a href="payment.php">Payment</a>
</div>
<h1 align="center">Residents Entrance And Exit Monitoring</h1>

<div class="filter-container" align="center">
    <form method="GET">
        <label for="resident_name">Resident Name:</label>
        <input type="text" id="resident_name" name="resident_name" value="<?php echo isset($_GET['resident_name']) ? $_GET['resident_name'] : ''; ?>">
        
        <label for="card_id">RFID Number:</label>
        <input type="text" id="card_id" name="card_id" value="<?php echo isset($_GET['card_id']) ? $_GET['card_id'] : ''; ?>">

        <label for="plate_number">Plate Number:</label>
        <input type="text" id="plate_number" name="plate_number" value="<?php echo isset($_GET['plate_number']) ? $_GET['plate_number'] : ''; ?>">

        <label for="address">Address:</label>
        <input type="text" id="address" name="address" value="<?php echo isset($_GET['address']) ? $_GET['address'] : ''; ?>">
        
        <label for="status">Status:</label>
        <select id="status" name="status">
            <option value="">All</option>
            <option value="IN" <?php echo isset($_GET['status']) && $_GET['status'] === 'IN' ? 'selected' : ''; ?>>IN</option>
            <option value="OUT" <?php echo isset($_GET['status']) && $_GET['status'] === 'OUT' ? 'selected' : ''; ?>>OUT</option>
        </select>

        <button type="submit" class="filter">Filter</button>
    </form>
</div>

<div class="table-wrapper">
    <div class="table-container" id="table-content">
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
    </div>
</div>

<div align="center">
    <?php if ($page > 1): ?>
        <a id="prev-button" href="monitor.php?page=<?php echo $page - 1; ?>" class="btn previous">Previous</a>
    <?php endif; ?>

    <?php if (mysqli_num_rows($result) == $limit): ?>
        <a id="next-button" href="monitor.php?page=<?php echo $page + 1; ?>" class="btn next">Next</a>
    <?php endif; ?>
</div>

</body>
</html>
