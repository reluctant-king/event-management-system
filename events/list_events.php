<?php
session_start();
include '../components/header.php';
include '../includes/db.php';

// Determine if showing only organizer's events
$isOrganizer = isset($_SESSION['user_id']) && $_SESSION['role'] === 'organizer';
$my = isset($_GET['my']) && $_GET['my'] == 1 && $isOrganizer;

// Filters
$search = isset($_GET['search']) ? $_GET['search'] : '';
$category = isset($_GET['category']) ? $_GET['category'] : '';
$location = isset($_GET['location']) ? $_GET['location'] : '';
$date = isset($_GET['date']) ? $_GET['date'] : '';

$conditions = [];
$params = [];
$types = '';

if ($my) {
  $conditions[] = "organizer_id = ?";
  $params[] = $_SESSION['user_id'];
  $types .= 'i';
} else {
  $conditions[] = "is_private = 0";
  if (!empty($search)) {
    $conditions[] = "(title LIKE ? OR description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $types .= 'ss';
  }
  if (!empty($category)) {
    $conditions[] = "category = ?";
    $params[] = $category;
    $types .= 's';
  }
  if (!empty($location)) {
    $conditions[] = "location LIKE ?";
    $params[] = "%$location%";
    $types .= 's';
  }
  if (!empty($date)) {
    $conditions[] = "event_date = ?";
    $params[] = $date;
    $types .= 's';
  }
}

$whereClause = implode(" AND ", $conditions);
$query = "SELECT * FROM events WHERE $whereClause ORDER BY event_date ASC";

$stmt = $conn->prepare($query);
if ($types && $params) {
  $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<main class="p-10 max-w-6xl mx-auto">
  <h1 class="text-3xl font-bold text-blue-700 mb-6 text-center"><?= $my ? 'My Events' : 'Upcoming Events' ?></h1>

  <?php if (!$my): ?>
    <!-- Filter Form -->
    <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-8">
      <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search..."
        class="p-2 border rounded" />
      <input type="text" name="category" value="<?= htmlspecialchars($category) ?>" placeholder="Category"
        class="p-2 border rounded" />
      <input type="text" name="location" value="<?= htmlspecialchars($location) ?>" placeholder="Location"
        class="p-2 border rounded" />
      <input type="date" name="date" value="<?= htmlspecialchars($date) ?>" min="<?= date('Y-m-d') ?>"
        class="p-2 border rounded" />
      <button type="submit" class="bg-blue-600 text-white p-2 rounded hover:bg-blue-700 col-span-1 md:col-auto">Apply Filters</button>
    </form>
  <?php endif; ?>

  <?php if ($result->num_rows > 0): ?>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <?php while ($event = $result->fetch_assoc()): ?>
        <div class="border p-4 rounded shadow bg-white hover:shadow-lg transition-all duration-200">
          
          <!-- Event Image -->
          <?php if (!empty($event['image_path'])): ?>
            <img src="../<?= htmlspecialchars($event['image_path']) ?>" alt="Event Image"
                 class="w-full h-40 object-cover rounded mb-3" />
          <?php endif; ?>

          <!-- Event Info -->
          <h3 class="text-xl font-bold text-blue-600"><?= htmlspecialchars($event['title']) ?></h3>
          <p class="text-sm text-gray-600 mt-1"><?= htmlspecialchars($event['description']) ?></p>
          <p class="mt-2 text-gray-700">
            📍 <?= htmlspecialchars($event['location']) ?> <br>
            📅 <?= $event['event_date'] ?> at <?= $event['event_time'] ?>
          </p>
          <p class="text-sm text-gray-500 mt-1">🎟️ <?= htmlspecialchars($event['ticket_type']) ?> — ₹<?= htmlspecialchars($event['ticket_price']) ?></p>
          <p class="text-sm text-gray-500">Category: <?= htmlspecialchars($event['category']) ?></p>

          <!-- Action Buttons -->
          <div class="mt-4 flex flex-wrap gap-3">
            <a href="event_details.php?id=<?= $event['id'] ?>" class="text-blue-700 hover:bg-blue-700 hover:text-white px-4 py-2 rounded transition">View Details</a>

            <?php if ($my): ?>
              <a href="edit_event.php?id=<?= $event['id'] ?>" class="text-yellow-600 hover:bg-yellow-700 hover:text-white px-4 py-2 rounded transition">Edit</a>
              <a href="delete_event.php?id=<?= $event['id'] ?>" onclick="return confirm('Delete this event?');" class="text-red-600 hover:bg-red-700 hover:underline hover:text-white px-4 py-2 rounded transition">Delete</a>
              <a href="attendees.php?id=<?= $event['id'] ?>" class="text-green-600 hover:bg-green-700 hover:underline hover:text-white px-4 py-2 rounded transition">Attendees</a>
              <a href="../engagement/create_poll.php?event_id=<?= $event['id'] ?>" class="text-indigo-600 hover:bg-indigo-700 hover:underline hover:text-white px-4 py-2 rounded transition">Create Poll</a>
            <?php endif; ?>
          </div>
        </div>
      <?php endwhile; ?>
    </div>
  <?php else: ?>
    <p class="text-center text-gray-600 mt-10">No events found.</p>
  <?php endif; ?>
</main>

<?php include '../components/footer.php'; ?>
