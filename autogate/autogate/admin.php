<?php
// Start session
session_start();
// Check if the admin is logged in
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    // Display logout button
    echo '<form action="logout.php" method="post" class="logout-form">';
    echo '<button class="button" type="submit">Logout</button>';
    echo '</form>';
}

if (
    isset($_POST['resident_name']) &&
    isset($_POST['gender']) &&  // Add gender check
    isset($_POST['race']) &&    // Add race check
    isset($_POST['phone_no']) && // Add phone number check
    isset($_POST['card_id']) &&
    isset($_POST['address']) &&
    isset($_POST['platenumber']) &&
    isset($_POST['email']) &&
    isset($_POST['payment_month']) &&  // Check if the month is set
    isset($_POST['payment_year'])  // Check if the year is set
) {
    // Combine month and year for paymentmonth
    $paymentmonth = $_POST['payment_month'] . ' ' . $_POST['payment_year'];

    // Establish connection to the database
    $conn = new mysqli("localhost", "root", "", "rfid");

    // Prepare SQL statement to insert data into the database
    $sql = $conn->prepare("INSERT INTO resident (id, residentname, gender, race, phone_no, cardid, address, platenumber, email, paymentmonth) VALUES (NULL, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    // Bind parameters to the prepared statement
    $sql->bind_param("sssssssss", $_POST['resident_name'], $_POST['gender'], $_POST['race'], $_POST['phone_no'], $_POST['card_id'], $_POST['address'], $_POST['platenumber'], $_POST['email'], $paymentmonth);

    // Execute the prepared statement
    $sql->execute();

    // Notify the user that insertion was successful
    echo "<script>alert('Insert successfully')</script>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Autogate System Model For Residents Entrance And Exit Monitoring</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
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

        .logout-form {
            float: right;
            padding: 14px;
        }

        .button {
            background-color: #04AA6D;
            color: white;
            border: none;
            padding: 10px 15px;
            cursor: pointer;
            font-size: 14px;
        }

        .button:hover {
            background-color: #45a049;
        }

        .container {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background-color: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        h1, h2 {
            text-align: center;
            color: #333;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            color: #555;
        }

        input, select {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        input[type="submit"] {
            background-color: #04AA6D;
            color: white;
            cursor: pointer;
            font-size: 16px;
            border: none;
            margin-top: 20px;
            transition: background-color 0.3s;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }

        .highlighted {
            color: #04AA6D;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <a href="#">Admin</a>
        <a href="view.php">View</a>
        <a href="monitor.php">Monitor</a>
        <a href="statistics.php">Statistics</a>
        <a href="payment.php">Payment</a>
    </div>

    <div class="container">
        <h1>Residents Management System</h1>
        <h2><span class="highlighted">Resident Registration</span></h2>

        <form action="" method="post">
            <div class="form-group">
                <label for="resident_name">Resident Name:</label>
                <input type="text" id="resident_name" name="resident_name" required>
            </div>

            <!-- New field: Gender -->
            <div class="form-group">
                <label for="gender">Gender:</label>
                <select id="gender" name="gender" required>
                    <option value="">Select Gender</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                </select>
            </div>

            <!-- New field: Race -->
            <div class="form-group">
                <label for="race">Race:</label>
                <select id="race" name="race" required>
                    <option value="">Select Race</option>
                    <option value="Malays">Malays</option>
                    <option value="Chinese">Chinese</option>
                    <option value="Indian">Indian</option>
                    <option value="Other">Other</option>
                </select>
            </div>

            <!-- New field: Phone Number -->
            <div class="form-group">
                <label for="phone_no">Phone Number:</label>
                <input type="text" id="phone_no" name="phone_no" required>
            </div>

            <div class="form-group">
                <label for="card_id">Card Number:</label>
                <input type="text" id="card_id" name="card_id" required>
            </div>

            <div class="form-group">
                <label for="address">Address:</label>
                <input type="text" id="address" name="address" required>
            </div>

            <div class="form-group">
                <label for="platenumber">Plate Number:</label>
                <input type="text" id="platenumber" name="platenumber" required>
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>

            <!-- Month and Year selection -->
            <div class="form-group">
                <label for="payment_month">Month of Registration:</label>
                <select id="payment_month" name="payment_month" required>
                    <option value="">Select Month</option>
                    <option value="January">January</option>
                    <option value="February">February</option>
                    <option value="March">March</option>
                    <option value="April">April</option>
                    <option value="May">May</option>
                    <option value="June">June</option>
                    <option value="July">July</option>
                    <option value="August">August</option>
                    <option value="September">September</option>
                    <option value="October">October</option>
                    <option value="November">November</option>
                    <option value="December">December</option>
                </select>
            </div>

            <div class="form-group">
                <label for="payment_year">Year of Registration:</label>
                <select id="payment_year" name="payment_year" required>
                    <option value="">Select Year</option>
                    <?php
                    // Generate year options from 2020 to the current year + 5
                    for ($i = 2020; $i <= date("Y") + 5; $i++) {
                        echo "<option value='$i'>$i</option>";
                    }
                    ?>
                </select>
            </div>

            <input type="submit" value="Submit">
        </form>
    </div>
</body>
</html>
