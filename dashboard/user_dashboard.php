<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
  header("Location: ../auth/login.php");
  exit();
}

include '../includes/db.php';
include '../components/header.php';
$user_id = $_SESSION['user_id'];
?>

<main class="p-10 max-w-4xl mx-auto">
  <h1 class="text-3xl font-bold text-blue-700 mb-6">User Dashboard</h1>
  <p>Welcome! Browse and register for upcoming events.</p>

  <a href="/event-system/events/list_events.php"
    class="mt-4 inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">🎟️ Browse Events</a>

  <!--  Show success message if redirected from payment -->
  <?php if (isset($_GET['mock_payment']) && $_GET['mock_payment'] === 'success'): ?>
    <div class="mt-6 p-4 bg-green-100 text-green-700 rounded border border-green-300">
      ✅ Your ticket purchase was successful! You can download it below.
    </div>
  <?php endif; ?>

  <h2 class="text-2xl font-bold mt-10 text-blue-700">My Tickets</h2>

  <?php
  $tickets = $conn->query("
    SELECT t.*, e.title
    FROM tickets t
    JOIN events e ON t.event_id = e.id
    WHERE t.user_id = $user_id
    ORDER BY t.purchase_time DESC
  ");
  ?>

  <?php if ($tickets->num_rows > 0): ?>
    <ul class="mt-4 space-y-3">
      <?php while ($t = $tickets->fetch_assoc()): ?>
        <li class="border p-4 rounded bg-white shadow">
          <strong><?= htmlspecialchars($t['title']) ?></strong><br>
          <?= htmlspecialchars($t['ticket_type']) ?> — ₹<?= number_format($t['price'], 2) ?><br>
          <span class="text-sm text-gray-500">Purchased on <?= date("M d, Y H:i", strtotime($t['purchase_time'])) ?></span>

          <?php
            $ticketImg = "/event-system/tickets/{$t['ticket_id']}.png";
            $ticketPath = __DIR__ . '/../tickets/' . $t['ticket_id'] . '.png';
          ?>

          <?php if (!empty($t['ticket_id']) && file_exists($ticketPath)): ?>
            <br>
            <a href="<?= $ticketImg ?>" download
              class="inline-block text-blue-700 hover:bg-blue-700 hover:text-white px-4 py-2 rounded transition mt-2">
              📥 Download Ticket
            </a>
          <?php else: ?>
            <br><span class="text-red-500 text-sm">Ticket not generated.</span>
          <?php endif; ?>
        </li>
      <?php endwhile; ?>
    </ul>
  <?php else: ?>
    <p class="text-gray-500 mt-4">No tickets purchased yet.</p>
  <?php endif; ?>
</main>

<?php include '../components/footer.php'; ?>
