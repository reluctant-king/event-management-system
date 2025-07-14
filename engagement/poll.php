<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
  header("Location: ../auth/login.php");
  exit();
}
include '../includes/db.php';
include '../components/header.php';

$event_id = isset($_GET['event_id']) ? intval($_GET['event_id']) : 0;

//  Get latest poll for this event
$poll = $conn->query("SELECT * FROM polls WHERE event_id = $event_id ORDER BY created_at DESC LIMIT 1")->fetch_assoc();

if (!$poll) {
  echo "<main class='p-10 text-center'><p>No poll available.</p></main>";
  include '../components/footer.php';
  exit();
}

//  Check if user already voted
$has_voted = false;
$uid = $_SESSION['user_id'];
$pid = $poll['id'];

$vote_check = $conn->query("SELECT id FROM poll_votes WHERE poll_id = $pid AND user_id = $uid");
if ($vote_check && $vote_check->num_rows > 0) {
  $has_voted = true;
}

//  Handle vote submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$has_voted) {
  $option_id = intval($_POST['option']);
  $conn->query("INSERT INTO poll_votes (poll_id, option_id, user_id) VALUES ($pid, $option_id, $uid)");

  //  Redirect to event details (prevent resubmission)
  header("Location: ../events/event_details.php?id=$event_id&voted=1");
  exit();
}

//  Fetch poll options
$options = $conn->query("SELECT * FROM poll_options WHERE poll_id = $pid");
?>

<main class="p-10 max-w-xl mx-auto">
  <h2 class="text-2xl font-bold text-blue-700 mb-4"><?= htmlspecialchars($poll['question']) ?></h2>

  <?php if ($has_voted): ?>
    <p class="text-green-700 font-medium mb-4">✅ You have already voted in this poll.</p>
  <?php else: ?>
    <form method="POST" class="space-y-2">
      <?php while ($opt = $options->fetch_assoc()): ?>
        <label class="block">
          <input type="radio" name="option" value="<?= $opt['id'] ?>" required>
          <?= htmlspecialchars($opt['option_text']) ?>
        </label>
      <?php endwhile; ?>
      <button type="submit" class="mt-4 bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Vote</button>
    </form>
  <?php endif; ?>
</main>

<?php include '../components/footer.php'; ?>
