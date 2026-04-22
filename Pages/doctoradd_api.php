<?php
header("Content-Type: application/json");

$conn = new mysqli("localhost", "oluo", "vz9Kh6Qj", "oluo_1");

$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    http_response_code(400);
    echo json_encode(["error" => "Invalid JSON"]);
    exit;
}

$stmt = $conn->prepare("
    INSERT INTO DOCTOR
    (doctor_id, department_id, first_name, last_name, contact_num,
     shift_start, shift_end, is_on_shift, license_num)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
");

if (!$stmt) {
    http_response_code(500);
    echo json_encode(["error" => $conn->error]);
    exit;
}

$stmt->bind_param(
    "sssssssis",
    $data["doctor_id"],
    $data["department_id"],
    $data["first_name"],
    $data["last_name"],
    $data["contact_num"],
    $data["shift_start"],
    $data["shift_end"],
    $data["is_on_shift"],
    $data["license_num"]
);

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    http_response_code(500);
    echo json_encode(["error" => $stmt->error]);
}

$stmt->close();
$conn->close();
?>