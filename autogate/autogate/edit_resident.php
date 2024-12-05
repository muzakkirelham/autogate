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
    $query = "SELECT * FROM resident WHERE id = $resident_id";
    $result = mysqli_query($connect, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $resident = mysqli_fetch_assoc($result);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Resident Payment Status</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: #f4f4f9;
        }
        .container {
            width: 100%;
            max-width: 600px;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            color: #333;
        }
        form {
            margin-top: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #555;
        }
        select, input[type="submit"], button {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
        }
        select {
            background: #f9f9f9;
        }
        input[type="submit"], button {
            background-color: #007bff;
            color: white;
            cursor: pointer;
        }
        button {
            background-color: #6c757d;
        }
        input[type="submit"]:hover {
            background-color: #0056b3;
        }
        button:hover {
            background-color: #5a6268;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Edit Resident Payment Status</h2>
        <form action="edit_payment_status.php" method="post">
            <input type="hidden" name="resident_id" value="<?php echo $resident['id']; ?>">
            <label for="payment_status">New Payment Status:</label>
            <select name="payment_status" id="payment_status">
                <option value="Paid" <?php if ($resident['paymentstatus'] == 'Paid') echo 'selected'; ?>>Paid</option>
                <option value="Overdue within 3 months" <?php if ($resident['paymentstatus'] == 'Overdue within 3 months') echo 'selected'; ?>>Overdue within 3 months</option>
                <option value="Overdue over 3 months" <?php if ($resident['paymentstatus'] == 'Overdue over 3 months') echo 'selected'; ?>>Overdue over 3 months</option>
            </select>
            <input type="submit" value="Edit Payment Status">
            <button type="button" onclick="cancelEdit()">Cancel</button>
        </form>
    </div>
    <script>
        function cancelEdit() {
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
