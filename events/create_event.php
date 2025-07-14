<?php
session_start();
if (!isset($_SESSION['user_id'])) {
  header("Location: ../auth/login.php");
  exit();
}
include '../components/header.php';
include '../includes/db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $title = $_POST['title'];
  $description = $_POST['description'];
  $date = $_POST['event_date'];
  $time = $_POST['event_time'];
  $location = $_POST['location'];
  $category = $_POST['category'];
  $is_private = isset($_POST['is_private']) ? 1 : 0;
  $ticket_type = $_POST['ticket_type'];
  $ticket_price = $_POST['ticket_price'];
  $organizer_id = $_SESSION['user_id'];

  // Image upload handling
  $image_path = null;
  if (isset($_FILES['event_image']) && $_FILES['event_image']['error'] === UPLOAD_ERR_OK) {
    $image_name = basename($_FILES['event_image']['name']);
    $target_dir = '../assets/images/';
    $target_path = $target_dir . time() . '_' . $image_name;

    if (move_uploaded_file($_FILES['event_image']['tmp_name'], $target_path)) {
      $image_path = str_replace('../', '', $target_path); // Save relative path like assets/images/...
    } else {
      echo "<p class='text-red-600 text-center'>Image upload failed.</p>";
    }
  }

  $stmt = $conn->prepare("INSERT INTO events (organizer_id, title, description, event_date, event_time, location, category, is_private, ticket_type, ticket_price, image_path) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
  $stmt->bind_param("issssssisds", $organizer_id, $title, $description, $date, $time, $location, $category, $is_private, $ticket_type, $ticket_price, $image_path);

  if ($stmt->execute()) {
    header("Location: ../dashboard/organizer_dashboard.php?success=event_created");
  } else {
    echo "<p class='text-red-600 text-center'>Error: " . $conn->error . "</p>";
  }
}
?>

<main class="p-10 max-w-3xl mx-auto">
  <h2 class="text-3xl font-bold text-blue-700 mb-6 text-center">Create New Event</h2>

  <form method="POST" enctype="multipart/form-data" class="space-y-4">
    <input type="text" name="title" required placeholder="Event Title" class="w-full p-3 border rounded" />

    <textarea name="description" required placeholder="Event Description" class="w-full p-3 border rounded"></textarea>

    <div class="grid grid-cols-2 gap-4">
      <input type="date" name="event_date" id="event_date" required class="p-3 border rounded" min="<?= date('Y-m-d') ?>" />
      <input type="time" name="event_time" id="event_time" required class="p-3 border rounded" />
    </div>

    <input type="text" name="location" required placeholder="Location" class="w-full p-3 border rounded" />

    <select name="category" required class="w-full p-3 border rounded">
      <option value="">Select Category</option>
      <option value="Music">Music</option>
      <option value="Tech">Tech</option>
      <option value="Sports">Sports</option>
      <option value="Art">Art</option>
      <option value="Education">Education</option>
      <option value="Other">Other</option>
    </select>

    <div class="flex items-center gap-2">
      <input type="checkbox" name="is_private" class="h-5 w-5" />
      <label>Private Event</label>
    </div>

    <div class="grid grid-cols-2 gap-4">
      <input type="text" name="ticket_type" required placeholder="Ticket Type (e.g., VIP, General)" class="p-3 border rounded" />
      <input type="number" name="ticket_price" required step="0.01" min="0" placeholder="Ticket Price" class="p-3 border rounded" />
    </div>

    <div>
      <input type="file" name="event_image" accept="image/*" onchange="previewImage(event)" class="w-full p-3 border rounded" />
      <img id="imagePreview" class="mt-3 h-40 w-40 rounded hidden object-cover" />
    </div>

    <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700">Create Event</button>
  </form>
</main>

<script>
  function previewImage(event) {
    const img = document.getElementById('imagePreview');
    img.src = URL.createObjectURL(event.target.files[0]);
    img.classList.remove('hidden');
  }

  // Optional: Auto restrict time to current time if today is selected
  document.getElementById('event_date').addEventListener('change', function () {
    const selectedDate = this.value;
    const timeInput = document.getElementById('event_time');
    const now = new Date();

    if (selectedDate === new Date().toISOString().split('T')[0]) {
      let hours = now.getHours().toString().padStart(2, '0');
      let minutes = now.getMinutes().toString().padStart(2, '0');
      timeInput.min = `${hours}:${minutes}`;
    } else {
      timeInput.removeAttribute('min');
    }
  });
</script>

<?php include '../components/footer.php'; ?>
