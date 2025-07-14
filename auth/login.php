<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
  if ($_SESSION['role'] === 'user') {
    header("Location: ../dashboard/user_dashboard.php");
  } else {
    header("Location: ../dashboard/organizer_dashboard.php");
  }
  exit();
}

include '../includes/db.php';
include '../components/header.php';

$error = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $email    = trim($_POST['email']);
  $password = $_POST['password'];

  $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    if (password_verify($password, $user['password'])) {
      $_SESSION['user_id'] = $user['id'];
      $_SESSION['role'] = $user['role'];

      if ($user['role'] === 'user') {
        header("Location: ../dashboard/user_dashboard.php");
      } else {
        header("Location: ../dashboard/organizer_dashboard.php");
      }
      exit();
    } else {
      $error = "Incorrect password";
    }
  } else {
    $error = "User not found";
  }
}
?>

<main class="p-10 max-w-xl mx-auto">
  <h2 class="text-3xl font-bold text-center text-blue-700 mb-6">Login</h2>

  <?php if (!empty($error)): ?>
    <p class="text-red-600 text-center mb-4"><?= htmlspecialchars($error) ?></p>
  <?php endif; ?>

  <form method="POST" class="space-y-4">
    <input type="email" name="email" required placeholder="Email" class="w-full p-3 border rounded" />
    <input type="password" name="password" required placeholder="Password" class="w-full p-3 border rounded" />
    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Login</button>
  </form>
</main>

<?php include '../components/footer.php'; ?>
