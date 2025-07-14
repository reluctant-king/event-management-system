<?php
session_start();
require '../config.php';
require '../includes/db.php';

//  Stripe SDK (manual)
require '../vendor/stripe/init.php';

//  QR Code Generator
require '../phpqrcode/qrlib.php';

//  Get values from URL
$sessionId = isset($_GET['session_id']) ? $_GET['session_id'] : '';
$event_id = isset($_GET['event_id']) ? intval($_GET['event_id']) : 0;
$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;

if (!$sessionId || !$event_id || !$user_id) {
  die("❌ Invalid request.");
}

try {
  //  Retrieve Stripe session
  \Stripe\Stripe::setApiKey($stripeSecretKey);
  $session = \Stripe\Checkout\Session::retrieve($sessionId);
  $email = $session->customer_details->email;
  $price = $session->amount_total / 100;
  $ticket_id = uniqid('TKT_');

  //  Get event title (optional)
  $event = $conn->query("SELECT title FROM events WHERE id = $event_id")->fetch_assoc();
  $event_title = isset($event['title']) ? $event['title'] : 'Event';

  //  Generate QR Code
  $qrPath = "../tickets/$ticket_id.png";
  QRcode::png("http://localhost/event-system/verify_ticket.php?id=$ticket_id", $qrPath);

  //  Save ticket to DB
  $ticket_type = 'Standard';
  $stmt = $conn->prepare("INSERT INTO tickets (event_id, user_id, ticket_type, price, ticket_id, qr_path) VALUES (?, ?, ?, ?, ?, ?)");
  if (!$stmt) {
    die("❌ Prepare failed: " . $conn->error);
  }
  $stmt->bind_param("iisdss", $event_id, $user_id, $ticket_type, $price, $ticket_id, $qrPath);
  $stmt->execute();

  //  Redirect on success
  header("Location: ../dashboard/user_dashboard.php?payment=success");
  exit;

} catch (Exception $e) {
  echo "<p style='color:red;'>Error: " . $e->getMessage() . "</p>";
}
?>