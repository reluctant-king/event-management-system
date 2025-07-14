<?php
session_start();
include '../includes/db.php';
include '../components/header.php';

// Ensure only organizers can access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'organizer') {
  header("Location: ../auth/login.php");
  exit();
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$organizer_id = $_SESSION['user_id'];

// Fetch the event
$stmt = $conn->prepare("SELECT * FROM events WHERE id = ? AND organizer_id = ?");
$stmt->bind_param("ii", $id, $organizer_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows < 1) {
  echo "<main class='p-10 text-center text-red-600'>Event not found or unauthorized access.</main>";
  include '../components/footer.php';
  exit();
}

$event = $result->fetch_assoc();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $title        = trim($_POST['title']);
  $description  = trim($_POST['description']);
  $date         = $_POST['event_date'];
  $time         = $_POST['event_time'];
  $location     = trim($_POST['location']);
  $category     = trim($_POST['category']);
  $is_private   = isset($_POST['is_private']) ? 1 : 0;
  $ticket_type  = trim($_POST['ticket_type']);
  $ticket_price = floatval($_POST['ticket_price']);
  $image_path   = $event['image_path']; // keep old by default

  // Handle new image upload if present
  if (isset($_FILES['event_image']) && $_FILES['event_image']['error'] === UPLOAD_ERR_OK) {
    $image_name = basename($_FILES['event_image']['name']);
    $target_dir = '../assets/images/';
    $new_path = $target_dir . time() . '_' . $image_name;

    if (move_uploaded_file($_FILES['event_image']['tmp_name'], $new_path)) {
      $image_path = str_replace('../', '', $new_path); // assets/images/...
    } else {
      echo "<p class='text-red-600 text-center'>Image upload failed. Retaining old image.</p>";
    }
  }

  // Update event
  $update = $conn->prepare("UPDATE events 
    SET title=?, description=?, event_date=?, event_time=?, location=?, category=?, is_private=?, ticket_type=?, ticket_price=?, image_path=? 
    WHERE id=? AND organizer_id=?");

  $update->bind_param("ssssssisdsii",
    $title, $description, $date, $time, $location, $category, $is_private,
    $ticket_type, $ticket_price, $image_path, $id, $organizer_id
  );

  if ($update->execute()) {
    header("Location: list_events.php?my=1&success=edited");
    exit();
  } else {
    echo "<p class='text-red-600 text-center'>Error updating event: " . $conn->error . "</p>";
  }
}

// For date/time validation
$today = date('Y-m-d');
$now = date('H:i');
$isToday = $event['event_date'] === $today;
?>

<main class="p-10 max-w-3xl mx-auto">
  <h2 class="text-3xl font-bold text-blue-700 mb-6 text-center">Edit Event</h2>

  <?php if (!empty($event['image_path'])): ?>
    <div class="mb-4">
      <p class="font-medium mb-1">Current Image:</p>
      <img src="../<?= htmlspecialchars($event['image_path']) ?>" class="w-60 h-36 object-cover rounded shadow" />
    </div>
  <?php endif; ?>

  <form method="POST" enctype="multipart/form-data" class="space-y-4">
    <input type="text" name="title" value="<?= htmlspecialchars($event['title']) ?>" required class="w-full p-3 border rounded" />
    
    <textarea name="description" required class="w-full p-3 border rounded"><?= htmlspecialchars($event['description']) ?></textarea>
    
    <div class="grid grid-cols-2 gap-4">
      <input type="date" name="event_date" value="<?= $event['event_date'] ?>" min="<?= $today ?>" required class="p-3 border rounded" />
      <input type="time" name="event_time" value="<?= $event['event_time'] ?>" <?= $isToday ? "min=\"$now\"" : "" ?> required class="p-3 border rounded" />
    </div>

    <input type="text" name="location" value="<?= htmlspecialchars($event['location']) ?>" required class="w-full p-3 border rounded" />
    <input type="text" name="category" value="<?= htmlspecialchars($event['category']) ?>" required class="w-full p-3 border rounded" />

    <div class="flex items-center gap-2">
      <input type="checkbox" name="is_private" class="h-5 w-5" <?= $event['is_private'] ? 'checked' : '' ?> />
      <label class="text-gray-700">Private Event</label>
    </div>

    <div class="grid grid-cols-2 gap-4">
      <input type="text" name="ticket_type" value="<?= htmlspecialchars($event['ticket_type']) ?>" required class="p-3 border rounded" />
      <input type="number" name="ticket_price" step="0.01" value="<?= $event['ticket_price'] ?>" required class="p-3 border rounded" />
    </div>

    <div>
      <label class="block mb-1 font-medium">Upload New Image (optional):</label>
      <input type="file" name="event_image" accept="image/*" class="w-full p-3 border rounded" />
    </div>

    <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700">Update Event</button>
  </form>
</main>

<?php include '../components/footer.php'; ?>
