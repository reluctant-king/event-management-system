<?php
require '../vendor/stripe/init.php';//  use Stripe’s manual SDK path
require '../config.php'; //  Your config with Stripe keys



header('Content-Type: application/json');

\Stripe\Stripe::setApiKey($stripeSecretKey);

try {
  $session = \Stripe\Checkout\Session::create([
    'payment_method_types' => ['card'],
    'line_items' => [[
      'price_data' => [
        'currency' => 'inr',
        'product_data' => ['name' => 'Event Ticket'],
        'unit_amount' => $_POST['price'] * 100,
      ],
      'quantity' => 1,
    ]],
    'mode' => 'payment',
'success_url' => 'http://localhost/event-system/payment/success.php?session_id={CHECKOUT_SESSION_ID}&event_id=' . $_POST['event_id'] . '&user_id=' . $_POST['user_id'],
    'cancel_url'  => 'http://localhost/event-system/index.php?cancel=1',
  ]);

  echo json_encode(['id' => $session->id]);
} catch (Exception $e) {
  http_response_code(500);
  echo json_encode(['error' => $e->getMessage()]);
}
