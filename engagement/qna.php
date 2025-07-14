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
  $question = trim($_POST['question']);
  if ($question) {
    $stmt = $conn->prepare("INSERT INTO qna (event_id, user_id, question) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $event_id, $_SESSION['user_id'], $question);
    $stmt->execute();
  }
}
?>

<main class="p-10 max-w-2xl mx-auto">
  <h2 class="text-2xl font-bold text-purple-700 mb-4">❓ Ask a Question</h2>
  <form method="POST" class="space-y-3">
    <textarea name="question" required class="w-full p-3 border rounded" placeholder="Type your question..."></textarea>
    <button type="submit" class="bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700">Submit</button>
  </form>
</main>

<?php include '../components/footer.php'; ?>