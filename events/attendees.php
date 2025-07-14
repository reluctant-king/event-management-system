<?php
session_start();
include '../components/header.php';
include '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'organizer') {
  echo "<p class='text-red-600'>Access denied</p>";
  include '../components/footer.php'; exit();
}

$event_id = intval($_GET['id']);
$stmt = $conn->prepare("SELECT title FROM events WHERE id = ? AND organizer_id = ?");
$stmt->bind_param("ii", $event_id, $_SESSION['user_id']);
$stmt->execute();
$event = $stmt->get_result()->fetch_assoc();

if (!$event) {
  echo "<p class='text-red-600'>Invalid or unauthorized event</p>";
  include '../components/footer.php'; exit();
}

$tickets = $conn->query("SELECT t.*, u.name, u.email FROM tickets t JOIN users u ON u.id = t.user_id WHERE t.event_id = $event_id");
?>

<main class="p-10 max-w-4xl mx-auto">
  <h2 class="text-3xl font-bold text-blue-700 mb-6">Attendees for: <?= $event['title'] ?></h2>

  <?php if ($tickets->num_rows > 0): ?>
    <table class="w-full border">
      <thead class="bg-gray-100">
        <tr>
          <th class="p-2 text-left">Name</th>
          <th class="p-2 text-left">Email</th>
          <th class="p-2 text-left">Ticket</th>
          <th class="p-2 text-left">Price</th>
          <th class="p-2 text-left">Purchased</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = $tickets->fetch_assoc()): ?>
          <tr class="border-t">
            <td class="p-2"><?= $row['name'] ?></td>
            <td class="p-2"><?= $row['email'] ?></td>
            <td class="p-2"><?= $row['ticket_type'] ?></td>
            <td class="p-2">₹<?= $row['price'] ?></td>
            <td class="p-2"><?= $row['purchase_time'] ?></td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  <?php else: ?>
    <p class="text-gray-500">No attendees yet.</p>
  <?php endif; ?>
</main>

<?php include '../components/footer.php'; ?>
