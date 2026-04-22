<?php
header("Content-Type: application/json");

error_reporting(0);
ini_set('display_errors', 0);

$conn = new mysqli("localhost", "oluo", "vz9Kh6Qj", "oluo_1");

$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    http_response_code(400);
    echo json_encode(["error" => "Invalid JSON"]);
    exit;
}

$stmt = $conn->prepare("
  INSERT INTO ROOM
  (room_num, department_id, room_type, beds_count, occupied, last_cleaned)
  VALUES (?, ?, ?, ?, ?, ?)
");

if (!$stmt) {
    http_response_code(500);
    echo json_encode(["error" => $conn->error]);
    exit;
}

$stmt->bind_param(
    "sssiss",
    $data["room_num"],
    $data["department_id"],
    $data["room_type"],
    $data["beds_count"],
    $data["is_filled"],
    $data["last_cleaned"]
);

if ($stmt->execute()) {

    $update = $conn->prepare("
        UPDATE DEPARTMENT
        SET beds_total = beds_total + ?
        WHERE department_id = ?
    ");

    $update->bind_param(
        "is",
        $data["beds_count"],
        $data["department_id"]
    );

    $update->execute();
    $update->close();

    echo json_encode(["success" => true]);

} else {
    http_response_code(500);
    echo json_encode(["error" => $stmt->error]);
}

$stmt->close();
$conn->close();
?>