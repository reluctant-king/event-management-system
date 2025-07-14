<?php
session_start();
require '../includes/db.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/PHPMailer/src/PHPMailer.php';
require '../vendor/PHPMailer/src/SMTP.php';
require '../vendor/PHPMailer/src/Exception.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
  $event_id    = intval($_POST['event_id']);
  $ticket_type = $_POST['ticket_type'];
  $price       = floatval($_POST['price']);
  $user_id     = $_SESSION['user_id'];

  // Get user email
  $user = $conn->query("SELECT email FROM users WHERE id = $user_id")->fetch_assoc();
  $email = $user['email'];
  $ticket_id = uniqid("TKT_");

  // Save ticket
  $stmt = $conn->prepare("INSERT INTO tickets (event_id, user_id, ticket_type, price, ticket_id) VALUES (?, ?, ?, ?, ?)");
  $stmt->bind_param("iisd", $event_id, $user_id, $ticket_type, $price, $ticket_id);
  $stmt->execute();

  //  Generate QR code
  include '../phpqrcode/qrlib.php';
  $qrPath = "../tickets/$ticket_id.png";
  QRcode::png("https://yourdomain.com/verify_ticket.php?id=$ticket_id", $qrPath);

  //  Send email with QR code
  $mail = new PHPMailer(true);
  try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'youremail@gmail.com'; // use app password
    $mail->Password = 'your_app_password';
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    $mail->setFrom('no-reply@yourdomain.com', 'Eventify');
    $mail->addAddress($email);
    $mail->addAttachment($qrPath);

    $mail->isHTML(true);
    $mail->Subject = '🎫 Your Event Ticket';
    $mail->Body    = "Thank you for purchasing a ticket! Your ticket ID is <b>$ticket_id</b>. Your QR code is attached.";

    $mail->send();
  } catch (Exception $e) {
    error_log("Mail error: {$mail->ErrorInfo}");
  }

  header("Location: ../dashboard/user_dashboard.php?mock_payment=success");
  exit();
} else {
  echo "Invalid request.";
}
