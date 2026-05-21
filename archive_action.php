<?php
require_once 'config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];
    $fname = $_POST['fname'] ?? 'Unknown';
    $lname = $_POST['lname'] ?? 'Unknown';

    try {
        
        $stmt = $pdo->prepare("INSERT INTO archived_residents (resident_id, first_name, last_name) VALUES (?, ?, ?)");
        $stmt->execute([$id, $fname, $lname]);
        echo json_encode(['status' => 'success']);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}