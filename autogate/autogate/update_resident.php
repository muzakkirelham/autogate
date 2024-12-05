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

    // Retrieve resident information
    $resident_query = "SELECT * FROM resident WHERE id = $resident_id";
    $resident_result = mysqli_query($connect, $resident_query);

    if ($resident_result && mysqli_num_rows($resident_result) > 0) {
        $resident = mysqli_fetch_assoc($resident_result);

        // Retrieve payment information based on resident name
        $resident_name = $resident['residentname'];
        $payment_query = "SELECT paymentdate, paymentmonth FROM payment WHERE residentname = '$resident_name'";
        $payment_result = mysqli_query($connect, $payment_query);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Resident Payment Information</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .main-container {
            display: flex;
            gap: 20px;
            width: 90%;
            max-width: 1200px;
        }
        .container, .info-container, .payment-container {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            width: 100%;
        }
        h2 {
            color: #333;
            text-align: center;
        }
        label {
            display: block;
            margin-bottom: 5px;
            color: #555;
        }
        input[type="date"],
        select {
            width: 100%;
            padding: 8px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        input[type="submit"], button {
            width: 48%;
            padding: 10px;
            margin-top: 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
        }
        button {
            background-color: #f44336;
            color: white;
        }
        button:hover {
            background-color: #d32f2f;
        }
        .info, .payment-info {
            margin-bottom: 10px;
            color: #333;
        }
        .info span, .payment-info span {
            font-weight: bold;
            color: #555;
        }
        .latest-payment {
            margin-bottom: 15px;
            font-weight: bold;
            color: #333;
            text-align: center;
        }
        .payment-container table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .payment-container th, .payment-container td {
            padding: 8px;
            border: 1px solid #ddd;
            text-align: left;
        }
        .payment-container th {
            background-color: #4CAF50;
            color: white;
        }
    </style>
</head>
<body>
    <div class="main-container">
        <!-- Resident Information Container -->
        <div class="info-container">
            <h2>Resident Information</h2>
            <div class="info"><span>Name:</span> <?php echo $resident['residentname'] ?? 'N/A'; ?></div>
            <div class="info"><span>Gender:</span> <?php echo $resident['gender'] ?? 'N/A'; ?></div>
            <div class="info"><span>Race:</span> <?php echo $resident['race'] ?? 'N/A'; ?></div>
            <div class="info"><span>Phone Number:</span> <?php echo $resident['phone_no'] ?? 'N/A'; ?></div>
            <div class="info"><span>Card ID:</span> <?php echo $resident['cardid'] ?? 'N/A'; ?></div>
            <div class="info"><span>Plate Number:</span> <?php echo $resident['platenumber'] ?? 'N/A'; ?></div>
            <div class="info"><span>Address:</span> <?php echo $resident['address'] ?? 'N/A'; ?></div>
            <div class="info"><span>Email:</span> <?php echo $resident['email'] ?? 'N/A'; ?></div>
            <div class="info"><span>Latest Payment Month:</span> <?php echo $resident['paymentmonth'] ?? 'N/A'; ?></div>
            <div class="info"><span>Payment Status:</span> <?php echo $resident['paymentstatus'] ?? 'N/A'; ?></div>
        </div>

        <!-- Update Form Container -->
        <div class="container">
            <h2>Update Resident Payment Information</h2>
            <div class="latest-payment">
                Latest Payment Month: <?php echo $resident['paymentmonth'] ?? 'Not Available'; ?>
            </div>
            <form action="update_payment_info.php" method="post">
                <input type="hidden" name="resident_id" value="<?php echo $resident['id']; ?>">
                
                <label for="payment_date">Payment Date:</label>
                <input type="date" name="payment_date" id="payment_date" value="<?php echo date('Y-m-d'); ?>">
                
                <label for="payment_year">Payment Year:</label>
                <select name="payment_year" id="payment_year">
                    <?php
                    for ($year = 2024; $year <= 2034; $year++) {
                        echo "<option value=\"$year\">$year</option>";
                    }
                    ?>
                </select>

                <label for="payment_month">Payment Month:</label>
                <select name="payment_month" id="payment_month">
                    <?php
                    $months = [
                        "January", "February", "March", "April", "May", "June",
                        "July", "August", "September", "October", "November", "December"
                    ];
                    foreach ($months as $month) {
                        echo "<option value=\"$month\">$month</option>";
                    }
                    ?>
                </select>

                <input type="submit" value="Update Payment">
                <button type="button" onclick="cancelUpdate()">Cancel</button>
            </form>
        </div>

        <!-- Payment History Container -->
        <div class="payment-container">
            <h2>Payment History</h2>
            <table>
                <tr>
                    <th>Payment Date</th>
                    <th>Payment Month</th>
                </tr>
                <?php
                if ($payment_result && mysqli_num_rows($payment_result) > 0) {
                    while ($payment = mysqli_fetch_assoc($payment_result)) {
                        echo "<tr><td>" . $payment['paymentdate'] . "</td><td>" . $payment['paymentmonth'] . "</td></tr>";
                    }
                } else {
                    echo "<tr><td colspan='2'>No payment records found.</td></tr>";
                }
                ?>
            </table>
        </div>
    </div>

    <!-- JavaScript function to handle cancellation -->
    <script>
        function cancelUpdate() {
            // Redirect the user to the previous page
            window.history.back();
        }
    </script>
</body>
</html>
<?php
    } else {
        echo "Resident not found.";
    }
} else {
    echo "Invalid request.";
}

// Close the database connection
mysqli_close($connect);
?>
