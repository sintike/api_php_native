<?php
include "../database.php";

header('Content-Type: application/json');

$sql = "SELECT id, username, email FROM Users"; // pastikan nama tabel sesuai
$result = $conn->query($sql);

$user = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $user[] = $row;
    }
}

echo json_encode([
    "message" => "Semua user berhasil diambil",
    "data" => $user
]);

$conn->close();
?>