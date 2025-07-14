<?php
session_start();
include 'includes/db.php';
include 'components/header.php';

// Pull public upcoming events
$events = $conn->query("SELECT * FROM events WHERE is_private = 0 ORDER BY event_date ASC LIMIT 4");

// Pull basic stats
$event_count = $conn->query("SELECT COUNT(*) as total FROM events WHERE is_private = 0")->fetch_assoc()['total'];
$ticket_count = $conn->query("SELECT COUNT(*) as total FROM tickets")->fetch_assoc()['total'];
?>

<main class="bg-blue-50 min-h-screen p-8">

  <!-- Hero Section -->
  <section class="text-center max-w-4xl mx-auto mt-20 mb-16">
    <h1 class="text-5xl font-bold text-blue-700 mb-4">Discover and Host Incredible Events</h1>
    <p class="text-lg text-gray-600 mb-6">Eventify helps users explore and attend events, while organizers manage them
      with ease.</p>

    <?php if (!isset($_SESSION['user_id'])): ?>
      <div class="space-x-4">
        <a href="/event-system/auth/register.php?role=user"
          class="bg-blue-600 text-white px-6 py-3 rounded hover:bg-blue-700 transition">Join as User</a>
        <a href="/event-system/auth/register.php?role=organizer"
          class="bg-green-600 text-white px-6 py-3 rounded hover:bg-green-700 transition">Join as Organizer</a>
      </div>
    <?php endif; ?>
  </section>

  <!-- Search Filters -->
  <section class="bg-white p-6 rounded shadow max-w-4xl mx-auto mb-12">
    <form method="GET" action="/event-system/events/list_events.php" class="grid grid-cols-1 md:grid-cols-4 gap-4">
      <input type="text" name="search" placeholder="Search by keyword" class="p-2 border rounded" />
      <input type="text" name="category" placeholder="Category" class="p-2 border rounded" />
      <input type="text" name="location" placeholder="Location" class="p-2 border rounded" />
      <input type="date" name="date" class="p-2 border rounded" />
      <button type="submit" class="col-span-full bg-blue-600 text-white p-2 rounded hover:bg-blue-700 transition">Find
        Events</button>
    </form>
  </section>

  <!-- Upcoming Events Preview -->
  <section class="max-w-6xl mx-auto mb-16">
    <h2 class="text-3xl font-bold text-blue-700 mb-6 text-center">Upcoming Events</h2>
    <div class="grid grid-cols- md:grid-cols-3 gap-6">
      <?php if ($events->num_rows > 0): ?>
        <?php while ($e = $events->fetch_assoc()): ?>
          <div class="bg-white p-4 rounded shadow hover:shadow-lg transition-all duration-200">
            <?php if (!empty($e['image_path'])): ?>
              <img src="<?= htmlspecialchars($e['image_path']) ?>" alt="Event Image"
                class="w-full h-40 object-cover rounded mb-3" />
            <?php endif; ?>

            <h3 class="text-xl font-bold text-blue-600"><?= htmlspecialchars($e['title']) ?></h3>
            <p class="text-gray-600"><?= htmlspecialchars($e['description']) ?></p>
            <p class="text-sm text-gray-500 mt-2"><?= htmlspecialchars($e['location']) ?> — <?= $e['event_date'] ?>
              <?= $e['event_time'] ?></p>
            <a href="/event-system/events/event_details.php?id=<?= $e['id'] ?>"
              class="text-blue-700 hover:bg-blue-700 hover:text-white px-2 py-2 rounded transition mt-2 inline-block">View Details</a>
          </div>
        <?php endwhile; ?>
      <?php else: ?>
        <p class="text-center col-span-full text-gray-600">No events found.</p>
      <?php endif; ?>
    </div>
  </section>

  <!-- Stats Section -->
  <section class="bg-white p-8 rounded shadow max-w-6xl mx-auto mb-16">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-center">
      <div>
        <p class="text-4xl font-bold text-blue-700"><?= $event_count ?></p>
        <p class="text-gray-600 mt-1">Events Listed</p>
      </div>
      <div>
        <p class="text-4xl font-bold text-green-700"><?= $ticket_count ?></p>
        <p class="text-gray-600 mt-1">Tickets Sold</p>
      </div>
      <div>
        <p class="text-4xl font-bold text-purple-700">100+</p>
        <p class="text-gray-600 mt-1">Organizers Onboarded</p>
      </div>
    </div>
  </section>

  <!-- How It Works Section -->
  <section class="max-w-4xl mx-auto text-center mb-16">
    <h2 class="text-3xl font-bold text-blue-700 mb-6">How It Works</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <div class="p-4">
        <div class="text-4xl text-blue-500 mb-2">📝</div>
        <h3 class="font-bold">Sign Up</h3>
        <p class="text-gray-600">Register as a user or an event organizer to get started.</p>
      </div>
      <div class="p-4">
        <div class="text-4xl text-green-500 mb-2">🎉</div>
        <h3 class="font-bold">Create or Attend</h3>
        <p class="text-gray-600">Organizers host events. Users explore and attend with one click.</p>
      </div>
      <div class="p-4">
        <div class="text-4xl text-purple-500 mb-2">📊</div>
        <h3 class="font-bold">Engage & Track</h3>
        <p class="text-gray-600">Join live Q&A, vote in polls, and track ticket sales in real-time.</p>
      </div>
    </div>
  </section>

</main>

<?php include 'components/footer.php'; ?>