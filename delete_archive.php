<?php
require_once 'config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['account_id']) || $_SESSION['is_admin'] != 1) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['archive_id'])) {
    $archive_id = $_POST['archive_id'];

    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("SELECT resident_id FROM archived_residents WHERE archive_id = ?");
        $stmt->execute([$archive_id]);
        $resident = $stmt->fetch();

        if ($resident) {
            $resident_id = $resident['resident_id'];

            // Delete from archived_residents table
            $delArchive = $pdo->prepare("DELETE FROM archived_residents WHERE archive_id = ?");
            $delArchive->execute([$archive_id]);

            // HARD DELETE: Delete from the main residents table
            $delResident = $pdo->prepare("DELETE FROM residents WHERE id = ?");
            $delResident->execute([$resident_id]);

            $pdo->commit();
            echo json_encode(['status' => 'success']);
        } else {
            $pdo->rollBack();
            echo json_encode(['status' => 'error', 'message' => 'Archive record not found.']);
        }
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
}