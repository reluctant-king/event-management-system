<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'organizer') {
  header("Location: ../auth/login.php");
  exit();
}
include '../includes/db.php';
include '../components/header.php';

$organizer_id = $_SESSION['user_id'];

$discussions = $conn->query("
  SELECT d.*, u.name, e.title 
  FROM discussions d
  JOIN users u ON d.user_id = u.id
  JOIN events e ON d.event_id = e.id
  WHERE e.organizer_id = $organizer_id
  ORDER BY d.posted_at DESC
");
?>

<main class="p-10 max-w-4xl mx-auto">
  <h2 class="text-2xl font-bold text-blue-700 mb-6">🗨️ Forum Discussions</h2>

  <?php while ($msg = $discussions->fetch_assoc()): ?>
    <div class="border p-4 rounded mb-3 bg-white">
      <p><strong><?= htmlspecialchars($msg['name']) ?></strong> (Event: <?= htmlspecialchars($msg['title']) ?>)</p>
      <p><?= nl2br(htmlspecialchars($msg['message'])) ?></p>
      <p class="text-sm text-gray-500"><?= $msg['posted_at'] ?></p>
    </div>
  <?php endwhile; ?>
</main>

<?php include '../components/footer.php'; ?>