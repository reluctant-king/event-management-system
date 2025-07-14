<?php include '../components/header.php'; ?>
<?php include '../includes/db.php'; ?>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $name     = $_POST['name'];
  $email    = $_POST['email'];
  $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
  $role     = $_POST['role'];

  $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
  $stmt->bind_param("ssss", $name, $email, $password, $role);

  if ($stmt->execute()) {
    header("Location: login.php");
  } else {
    echo "<p class='text-red-600'>Error: " . $conn->error . "</p>";
  }
}
?>

<main class="p-10 max-w-xl mx-auto">
  <h2 class="text-3xl font-bold text-center text-blue-700 mb-6">Register</h2>
  <form method="POST" class="space-y-4">
    <input type="text" name="name" required placeholder="Full Name" class="w-full p-3 border rounded" />
    <input type="email" name="email" required placeholder="Email" class="w-full p-3 border rounded" />
    <input type="password" name="password" required placeholder="Password" class="w-full p-3 border rounded" />
    <select name="role" class="w-full p-3 border rounded">
      <option value="user">User</option>
      <option value="organizer">Event Organizer</option>
    </select>
    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Register</button>
  </form>
</main>

<?php include '../components/footer.php'; ?>