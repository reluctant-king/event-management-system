<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
  header("Location: ../auth/login.php");
  exit();
}
include '../includes/db.php';
include '../components/header.php';

$event_id = isset($_GET['event_id']) ? intval($_GET['event_id']) : 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $message = trim($_POST['message']);
  if ($message) {
    $stmt = $conn->prepare("INSERT INTO discussions (event_id, user_id, message) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $event_id, $_SESSION['user_id'], $message);
    $stmt->execute();
  }
}

$messages = $conn->query("SELECT d.*, u.name FROM discussions d JOIN users u ON d.user_id = u.id WHERE event_id = $event_id ORDER BY posted_at DESC");
?>

<main class="p-10 max-w-3xl mx-auto">
  <h2 class="text-2xl font-bold text-blue-700 mb-4">💬 Join the Discussion</h2>
  <form method="POST" class="mb-6">
    <textarea name="message" required class="w-full p-3 border rounded mb-2" placeholder="Share your thoughts..."></textarea>
    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Post</button>
  </form>

  <?php while ($m = $messages->fetch_assoc()): ?>
    <div class="border p-3 rounded mb-3">
      <p class="font-semibold"><?= htmlspecialchars($m['name']) ?>:</p>
      <p><?= nl2br(htmlspecialchars($m['message'])) ?></p>
      <p class="text-xs text-gray-500"><?= $m['posted_at'] ?></p>
    </div>
  <?php endwhile; ?>
</main>

<?php include '../components/footer.php'; ?>