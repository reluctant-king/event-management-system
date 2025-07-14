<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'organizer') {
  header("Location: ../auth/login.php");
  exit();
}
include '../components/header.php';
include '../includes/db.php';

$organizer_id = $_SESSION['user_id'];

// Total Events
$total_events = $conn->query("SELECT COUNT(*) as total FROM events WHERE organizer_id = $organizer_id")->fetch_assoc()['total'];

// Total Tickets Sold + Revenue
$tickets = $conn->query("SELECT COUNT(*) as total, SUM(price) as revenue FROM tickets WHERE event_id IN (SELECT id FROM events WHERE organizer_id = $organizer_id)")->fetch_assoc();
?>

<main class="p-10">
  <h1 class="text-3xl font-bold text-green-700 mb-6">Organizer Dashboard</h1>
  <p class="mb-6">Manage your events, track performance, and view attendees.</p>

  <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-10">
    <div class="bg-white border p-4 rounded shadow">
      <h3 class="text-xl font-bold">My Events</h3>
      <p class="text-3xl text-blue-600"><?= $total_events ?></p>
    </div>
    <div class="bg-white border p-4 rounded shadow">
      <h3 class="text-xl font-bold">Tickets Sold</h3>
      <p class="text-3xl text-green-600">
        <?= isset($tickets['total']) ? $tickets['total'] : 0 ?>
      </p>
    </div>
    <div class="bg-white border p-4 rounded shadow">
      <h3 class="text-xl font-bold">Total Revenue</h3>
      <p class="text-3xl text-purple-600">
        ₹ <?= number_format(isset($tickets['revenue']) ? $tickets['revenue'] : 0, 2) ?>
      </p>
    </div>
  </div>

  <a href="../events/create_event.php"
    class="inline-block bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">+ Create New Event</a>
  <a href="../events/list_events.php?my=1"
    class="ml-4 inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">My Events</a>

  <div class="mt-8">
    <h3 class="text-xl font-semibold mb-2">Engagement Tools</h3>

    <a href="../events/list_events.php?my=1"
      class="text-blue-600 hover:bg-pink-700 hover:text-white px-4 py-2 rounded transition">
      Create Poll (select an event)
    </a>

    <a href="../engagement/manage_qna.php"
      class="text-blue-600 hover:bg-red-700 hover:text-white px-4 py-2 rounded transition">
      Moderate Q&A
    </a>

    <a href="../engagement/manage_poll.php"
      class="text-blue-600 hover:bg-purple-700 hover:text-white px-4 py-2 rounded transition">
      View Poll Results
    </a>

    <a href="../engagement/manage_forum.php"
      class="text-blue-600 hover:bg-green-700 hover:text-white px-4 py-2 rounded transition">
      Review Forum Posts
    </a>
  </div>
</main>

<?php include '../components/footer.php'; ?>