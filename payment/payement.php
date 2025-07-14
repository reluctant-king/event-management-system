<?php
session_start();
include '../includes/db.php';
require '../phpqrcode/qrlib.php';



if (!isset($_SESSION['user_id'])) {
  header("Location: ../auth/login.php");
  exit();
}

$event_id = intval($_POST['event_id']);
$ticket_type = $_POST['ticket_type'];
$price = floatval($_POST['price']);
$user_id = $_SESSION['user_id'];
$ticket_id = uniqid('TKT_'); //  Unique ticket ID

// Optional: Prevent double booking
$check = $conn->query("SELECT * FROM tickets WHERE event_id = $event_id AND user_id = $user_id");
if ($check->num_rows > 0) {
  header("Location: ../events/event_details.php?id=$event_id&already_purchased=1");
  exit();
}

//  Generate QR Code
$qrPath = "../tickets/$ticket_id.png";
QRcode::png("http://localhost/event-system/verify_ticket.php?id=$ticket_id", $qrPath);

//  Save Ticket
$stmt = $conn->prepare("INSERT INTO tickets (event_id, user_id, ticket_type, price, ticket_id, qr_path) VALUES (?, ?, ?, ?, ?, ?)");
if (!$stmt) {
  die("❌ Prepare failed: " . $conn->error);
}
$stmt->bind_param("iisdss", $event_id, $user_id, $ticket_type, $price, $ticket_id, $qrPath);
$stmt->execute();

//  Redirect to dashboard
header("Location: ../dashboard/user_dashboard.php?mock_payment=success");
exit();
?>
