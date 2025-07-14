<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'organizer') {
  header("Location: ../auth/login.php");
  exit();
}
include '../includes/db.php';
include '../components/header.php';

$organizer_id = $_SESSION['user_id'];

if (isset($_GET['mark']) && isset($_GET['id'])) {
  $id = intval($_GET['id']);
  $conn->query("UPDATE qna SET answered = 1 WHERE id = $id");
  header("Location: manage_qna.php");
  exit();
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['qna_id'], $_POST['answer'])) {
  $id = intval($_POST['qna_id']);
  $answer = trim($_POST['answer']);
  if ($answer) {
    $stmt = $conn->prepare("UPDATE qna SET answer = ?, answered = 1 WHERE id = ?");
    $stmt->bind_param("si", $answer, $id);
    $stmt->execute();
    header("Location: manage_qna.php");
    exit();
  }
}

$qna = $conn->query("
  SELECT q.*, u.name, e.title 
  FROM qna q
  JOIN users u ON q.user_id = u.id
  JOIN events e ON q.event_id = e.id
  WHERE e.organizer_id = $organizer_id
  ORDER BY q.created_at DESC
");
?>

<main class="p-10 max-w-4xl mx-auto">
  <h2 class="text-2xl font-bold text-blue-700 mb-6">❓ Q&A Management</h2>

  <?php while ($row = $qna->fetch_assoc()): ?>
    <div class="border p-4 rounded mb-3 bg-white">
      <p><strong><?= htmlspecialchars($row['name']) ?></strong> (Event: <em><?= htmlspecialchars($row['title']) ?></em>)</p>
      <p class="text-gray-800"><?= htmlspecialchars($row['question']) ?></p>
      <p class="text-sm text-gray-500 mb-1"><?= $row['created_at'] ?></p>
      <?php if (!$row['answered']): ?>
  <form method="POST" class="mt-2">
    <input type="hidden" name="qna_id" value="<?= $row['id'] ?>" />
    <textarea name="answer" rows="2" required class="w-full border p-2 rounded text-sm mb-2" placeholder="Type your answer..."></textarea>
    <button type="submit" class="bg-green-600 text-white px-3 py-1 rounded text-sm">Submit Answer</button>
  </form>
<?php else: ?>
  <p class="text-green-800 text-sm mt-2"><strong>Answer:</strong> <?= nl2br(htmlspecialchars($row['answer'])) ?></p>
<?php endif; ?>
    </div>
  <?php endwhile; ?>
</main>

<?php include '../components/footer.php'; ?>