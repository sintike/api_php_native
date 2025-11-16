<?php
include "../database.php";

header('Content-Type: application/json');

// Ambil JSON body
$data = json_decode(file_get_contents("php://input"), true);

// Validasi data
if (empty($data['username']) || empty($data['email']) || empty($data['password'])) {
    echo json_encode([
        "status" => false,
        "message" => "Data tidak lengkap, penambahan gagal!"
    ], JSON_PRETTY_PRINT);
    exit;
}

$username = $data['username'];
$email = $data['email'];
$password = password_hash($data['password'], PASSWORD_DEFAULT);

// Gunakan tabel yang benar: users
$stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $username, $email, $password);

if ($stmt->execute()) {
    $id = $stmt->insert_id;
    $stmt->close();

    // Ambil data terbaru
    $result = $conn->query("SELECT id, username, email FROM users WHERE id = $id");
    $newUser = $result->fetch_assoc();

    echo json_encode([
        "status" => true,
        "message" => "User berhasil ditambahkan",
        "data" => $newUser
    ], JSON_PRETTY_PRINT);

} else {
    echo json_encode([
        "status" => false,
        "message" => "Penambahan gagal, terjadi kesalahan!"
    ], JSON_PRETTY_PRINT);
}

$conn->close();
?>
