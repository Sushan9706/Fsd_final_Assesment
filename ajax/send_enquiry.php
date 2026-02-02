<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $property_id = $_POST['property_id'] ?? 0;
    $agent_id = $_POST['agent_id'] ?? 0;
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $message = $_POST['message'] ?? '';

    if ($property_id && $agent_id && $name && $email && $message) {
        try {
            $stmt = $pdo->prepare("INSERT INTO enquiries (property_id, agent_id, viewer_name, viewer_email, viewer_phone, message) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$property_id, $agent_id, $name, $email, $phone, $message]);
            echo json_encode(['status' => 'success', 'message' => 'Your enquiry has been sent successfully.']);
        } catch (PDOException $e) {
            echo json_encode(['status' => 'error', 'message' => 'Database error. Please try again.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Please fill in all required fields.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}