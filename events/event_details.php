<?php
session_start();
include '../components/header.php';
include '../includes/db.php';
include '../config.php'; // For Stripe keys

$event_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Get event from DB
$stmt = $conn->prepare("SELECT e.*, u.name AS organizer_name FROM events e JOIN users u ON e.organizer_id = u.id WHERE e.id = ?");
$stmt->bind_param("i", $event_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows < 1) {
  echo "<main class='p-10 text-center'><h2 class='text-xl text-red-600'>Event not found</h2></main>";
  include '../components/footer.php';
  exit();
}

$event = $result->fetch_assoc();
?>

<main class="p-10 max-w-4xl mx-auto bg-white rounded shadow">
  <!--  Event Image -->
  <?php if (!empty($event['image_path'])): ?>
    <img src="../<?= htmlspecialchars($event['image_path']) ?>" class="w-full h-64 object-cover rounded mb-6" alt="Event Image" />
  <?php endif; ?>

  <!--  Event Title and Organizer -->
  <h1 class="text-3xl font-bold text-blue-700 mb-2"><?= htmlspecialchars($event['title']) ?></h1>
  <p class="text-gray-600 mb-4">Organized by: <strong><?= htmlspecialchars($event['organizer_name']) ?></strong></p>

  <!--  Event Description -->
  <p class="text-gray-800 mb-4 whitespace-pre-line"><?= nl2br(htmlspecialchars($event['description'])) ?></p>

  <!--  Event Details -->
  <ul class="mb-6 space-y-1 text-gray-700">
    <li><strong>Date:</strong> <?= $event['event_date'] ?> at <?= $event['event_time'] ?></li>
    <li><strong>Location:</strong> <?= htmlspecialchars($event['location']) ?></li>
    <li><strong>Category:</strong> <?= htmlspecialchars($event['category']) ?></li>
    <li><strong>Ticket Type:</strong> <?= htmlspecialchars($event['ticket_type']) ?> — ₹<?= number_format($event['ticket_price'], 2) ?></li>
  </ul>

  <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'user'): ?>
    <!--  Stripe Checkout Button -->
    <script src="https://js.stripe.com/v3/"></script>

    <button id="payBtn" class="bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700">
      Pay with Stripe (₹<?= number_format($event['ticket_price'], 2) ?>)
    </button>

    <script>
      const stripe = Stripe("<?= $stripePublishableKey ?>");

      document.getElementById("payBtn").onclick = function () {
        fetch("/event-system/payment/create_checkout_session.php", {
          method: "POST",
          headers: {
            "Content-Type": "application/x-www-form-urlencoded"
          },
          body: new URLSearchParams({
            event_id: <?= $event['id'] ?>,
            user_id: <?= $_SESSION['user_id'] ?>,
            price: <?= $event['ticket_price'] ?>
          })
        })
        .then(res => res.json())
        .then(data => {
          if (data.id) {
            stripe.redirectToCheckout({ sessionId: data.id });
          } else {
            alert("Payment Failed: " + (data.error || "Unknown error"));
          }
        })
        .catch(err => alert("Payment Failed: " + err.message));
      };
    </script>

    <!--  Engagement Section -->
    <div class="bg-blue-50 p-4 rounded-lg space-y-2 mt-6">
      <h3 class="text-lg font-semibold text-blue-700 mb-2">Get Involved</h3>
      <ul class="space-y-2">
        <li>
          <a href="/event-system/engagement/forum.php?event_id=<?= $event['id'] ?>"
             class="block text-blue-600 hover:bg-blue-700 hover:text-white px-4 py-2 rounded transition">
            🗨️ Join the Discussion
          </a>
        </li>
        <li>
          <a href="/event-system/engagement/poll.php?event_id=<?= $event['id'] ?>"
             class="block text-green-700 hover:bg-blue-700 hover:text-white px-4 py-2 rounded transition">
            📊 Participate in a Poll
          </a>
        </li>
        <li>
          <a href="/event-system/engagement/qna.php?event_id=<?= $event['id'] ?>"
             class="block text-purple-700 hover:bg-blue-700 hover:text-white px-4 py-2 rounded transition">
            ❓ Ask a Question
          </a>
        </li>
        <li>
          <a href="/event-system/engagement/event_qna.php?event_id=<?= $event['id'] ?>"
             class="block text-yellow-700 hover:bg-blue-700 hover:text-white px-4 py-2 rounded transition">
            🔎 View Q&A
          </a>
        </li>
      </ul>
    </div>
  <?php else: ?>
    <p class="text-blue-700 mt-6 font-medium">Please login as a user to buy tickets and participate.</p>
  <?php endif; ?>
</main>

<?php include '../components/footer.php'; ?>
