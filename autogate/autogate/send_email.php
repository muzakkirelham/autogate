<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

$mail = new PHPMailer(true);

// Database connection
$connect = mysqli_connect("localhost", "root", "", "rfid");

// Check the connection
if (mysqli_connect_errno()) {
    die("Failed to connect to MySQL: " . mysqli_connect_error());
}

$id = isset($_GET['id']) ? $_GET['id'] : '';

if (!empty($id)) {
    $query = "SELECT residentname, email, paymentmonth FROM resident WHERE id = '$id'";
    $result = mysqli_query($connect, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $residentname = $row['residentname'];
        $email = $row['email'];
        $paymentmonth = $row['paymentmonth'];

        // Setup PHPMailer
        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; // Use Gmail SMTP server
            $mail->SMTPAuth = true;
            $mail->Username = 'muzakkirfyp@gmail.com'; // Your Gmail address
            $mail->Password = 'xptfbikapfheayam'; // Your Gmail password or App Password if 2FA is enabled
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Recipients
            $mail->setFrom('muzakkirfyp@gmail.com', 'Admin of Resident');
            $mail->addAddress($email); // Resident email

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Payment Reminder';
            $mail->Body = "Dear $residentname,<br><br>This is a reminder to complete your resident fees, your last payment is on $paymentmonth.<br><br>Your RFID card has been blocked due to inclompleted payment for 3 months.<br><br>Please complete your payment immediately.<br><br>Thank you!";

            $mail->send();
            echo "Email sent successfully!";
        } catch (Exception $e) {
            echo "Error: {$mail->ErrorInfo}";
        }
    } else {
        echo "Resident not found.";
    }
} else {
    echo "Invalid resident ID.";
}

mysqli_close($connect);
?>
