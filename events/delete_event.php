<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'organizer') {
  header("Location: ../auth/login.php");
  exit();
}

$id = intval($_GET['id']);
$stmt = $conn->prepare("DELETE FROM events WHERE id = ? AND organizer_id = ?");
$stmt->bind_param("ii", $id, $_SESSION['user_id']);
$stmt->execute();

header("Location: list_events.php?my=1&deleted=1");
exit();
