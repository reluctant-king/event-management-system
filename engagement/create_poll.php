<?php
session_start();
include '../includes/db.php';
include '../components/header.php';

//  Organizer access only
if ($_SESSION['role'] !== 'organizer') {
  header("Location: ../auth/login.php");
  exit();
}

//  Get and validate event ID
$event_id = isset($_GET['event_id']) ? intval($_GET['event_id']) : 0;
if ($event_id <= 0) {
  echo "<main class='p-10 text-red-600 text-center'>Invalid Event ID.</main>";
  include '../components/footer.php';
  exit();
}

// Optional: Fetch event title (for display context)
$event_title = '';
$event_result = $conn->query("SELECT title FROM events WHERE id = $event_id AND organizer_id = {$_SESSION['user_id']}");
if ($event_result && $event_result->num_rows > 0) {
  $event_title = $event_result->fetch_assoc()['title'];
} else {
  echo "<main class='p-10 text-red-600 text-center'>Unauthorized access to event.</main>";
  include '../components/footer.php';
  exit();
}

//  Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $question = trim($_POST['question']);
  $options = array_filter($_POST['options'], function ($o) {
    return trim($o) !== '';
  });

  if (!empty($question) && count($options) >= 2) {
    $stmt = $conn->prepare("INSERT INTO polls (event_id, question) VALUES (?, ?)");
    $stmt->bind_param("is", $event_id, $question);
    $stmt->execute();
    $poll_id = $conn->insert_id;

    $opt_stmt = $conn->prepare("INSERT INTO poll_options (poll_id, option_text) VALUES (?, ?)");
    foreach ($options as $opt_text) {
      $text = trim($opt_text);
      $opt_stmt->bind_param("is", $poll_id, $text);
      $opt_stmt->execute();
    }

    //  Redirect to poll results (not user voting page)
    header("Location: manage_poll.php");
    exit();
  } else {
    $error = "Please enter a question and at least two poll options.";
  }
}
?>

<main class="p-10 max-w-3xl mx-auto">
  <h2 class="text-2xl font-bold text-blue-700 mb-4">Create Poll for: <span
      class="text-gray-800"><?= htmlspecialchars($event_title) ?></span></h2>

  <?php if (!empty($error)): ?>
    <p class="text-red-600 mb-4 text-center"><?= htmlspecialchars($error) ?></p>
  <?php endif; ?>

  <form method="POST" class="space-y-4">
    <input type="hidden" name="event_id" value="<?= $event_id ?>" />

    <input type="text" name="question" required class="w-full p-3 border rounded" placeholder="Enter your poll question"
      value="<?= isset($question) ? htmlspecialchars($question) : '' ?>" />

    <div class="space-y-2">
      <?php for ($i = 0; $i < 4; $i++): ?>
        <input type="text" name="options[]" class="w-full p-3 border rounded" placeholder="Option <?= $i + 1 ?>"
          value="<?= isset($options[$i]) ? htmlspecialchars($options[$i]) : '' ?>" />
      <?php endfor; ?>
    </div>

    <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700 transition">
       Create Poll
    </button>
  </form>
</main>

<?php include '../components/footer.php'; ?>