<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<header class="bg-white shadow p-4 flex justify-between items-center">
    <!-- Tailwind CSS CDN -->
<script src="https://cdn.tailwindcss.com"></script>

  <a href="/event-system/index.php"  class="text-2xl font-bold text-blue-600 ml-5">Eventified</a>
  
  <nav class="space-x-4 text-sm md:text-base">
    <a href="/event-system/index.php" class="text-gray-600 hover:text-blue-600">Home</a>

    <?php if (isset($_SESSION['user_id'])): ?>
      <?php if ($_SESSION['role'] === 'organizer'): ?>
        <a href="/event-system/dashboard/organizer_dashboard.php" class="text-gray-600 hover:text-blue-600">My Events</a>
      <?php else: ?>
        <a href="/event-system/dashboard/user_dashboard.php" class="text-gray-600 hover:text-blue-600">Dashboard</a>
      <?php endif; ?>
      <a href="/event-system/auth/logout.php" class="text-red-500 hover:text-red-600">Logout</a>
    <?php else: ?>
      <a href="/event-system/auth/login.php" class="text-gray-600 hover:text-blue-600">Login</a>
      <a href="/event-system/auth/register.php" class="text-gray-600 hover:text-blue-600">Register</a>
    <?php endif; ?>
  </nav>
</header>
