<?php
session_start();
include '../includes/db.php';
include '../components/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'organizer') {
  echo "<main class='p-10 text-red-600 text-center'>Access denied.</main>";
  include '../components/footer.php';
  exit();
}

$organizer_id = $_SESSION['user_id'];
$polls = $conn->query("
  SELECT p.*, e.title 
  FROM polls p 
  JOIN events e ON p.event_id = e.id 
  WHERE e.organizer_id = $organizer_id 
  ORDER BY p.created_at DESC
");
?>

<main class="p-10 max-w-4xl mx-auto">
  <h2 class="text-2xl font-bold text-blue-700 mb-6">📊 Poll Results</h2>

  <?php while ($poll = $polls->fetch_assoc()): ?>
    <div class="mb-6 bg-white p-4 border rounded shadow">
      <h3 class="text-xl font-semibold text-blue-600"><?= htmlspecialchars($poll['question']) ?></h3>
      <p class="text-sm text-gray-500 mb-2">Event: <?= htmlspecialchars($poll['title']) ?></p>

      <?php
        $options = $conn->query("
          SELECT o.option_text, COUNT(v.id) as votes 
          FROM poll_options o 
          LEFT JOIN poll_votes v ON o.id = v.option_id 
          WHERE o.poll_id = {$poll['id']} 
          GROUP BY o.id
        ");
      ?>

      <?php while ($opt = $options->fetch_assoc()): ?>
        <div class="border px-3 py-2 rounded mb-2">
          <p class="text-gray-800"><?= htmlspecialchars($opt['option_text']) ?></p>
          <p class="text-sm text-gray-500"><?= $opt['votes'] ?> vote(s)</p>
        </div>
      <?php endwhile; ?>
    </div>
  <?php endwhile; ?>
</main>

<?php include '../components/footer.php'; ?>
