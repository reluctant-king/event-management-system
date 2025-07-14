<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
  header("Location: ../auth/login.php");
  exit();
}

include '../includes/db.php';
include '../components/header.php';

$event_id = isset($_GET['event_id']) ? intval($_GET['event_id']) : 0;
if ($event_id <= 0) {
  echo "<main class='p-10 text-red-600 text-center'>Invalid event ID.</main>";
  include '../components/footer.php';
  exit();
}

$event = $conn->query("SELECT title FROM events WHERE id = $event_id")->fetch_assoc();
$qa = $conn->query("
  SELECT q.question, q.answer, u.name AS asked_by, q.created_at
  FROM qna q
  JOIN users u ON q.user_id = u.id
  WHERE q.event_id = $event_id AND q.answered = 1
  ORDER BY q.created_at DESC
");
?>

<main class="p-10 max-w-4xl mx-auto">
  <h2 class="text-2xl font-bold text-blue-700 mb-6">❓ Q&A for: <?= htmlspecialchars($event['title']) ?></h2>

  <?php if ($qa->num_rows > 0): ?>
    <?php while ($row = $qa->fetch_assoc()): ?>
      <div class="bg-white border p-4 rounded mb-4 shadow">
        <p class="font-semibold text-blue-800"><?= htmlspecialchars($row['asked_by']) ?> asked:</p>
        <p class="mb-2 text-gray-800"><?= nl2br(htmlspecialchars($row['question'])) ?></p>
        <p class="text-green-700"><strong>Answer:</strong> <?= nl2br(htmlspecialchars($row['answer'])) ?></p>
        <p class="text-sm text-gray-500 mt-1"><?= date('M d, Y h:i A', strtotime($row['created_at'])) ?></p>
      </div>
    <?php endwhile; ?>
  <?php else: ?>
    <p class="text-gray-600">No answered questions yet for this event.</p>
  <?php endif; ?>
</main>

<?php include '../components/footer.php'; ?>