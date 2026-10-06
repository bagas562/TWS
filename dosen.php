<?php
header("Content-Type: application/json");

// Data dummy Dosen
$dosen = [
    ["id" => 1, "nip" => "19850101", "nama" => "Saeful Fahmi", "matkul" => "Teknologi Web Service"],
    ["id" => 2, "nip" => "19900215", "nama" => "Ramadan Renaldy, M.T.", "matkul" => "Pemrograman Berbasis Framework"]
];

$method = $_SERVER['REQUEST_METHOD'];

// GET: Ambil semua data dosen
if ($method === "GET") {
    http_response_code(200);
    echo json_encode($dosen);
    exit;
}

// POST: Tambah data dosen baru
if ($method === "POST") {
    $input = json_decode(file_get_contents("php://input"), true);

    if (empty($input['nip']) || empty($input['nama']) || empty($input['matkul'])) {
        http_response_code(400);
        echo json_encode(["error" => "Field nip, nama, dan matkul wajib diisi"]);
        exit;
    }

    $newDosen = [
        "id" => count($dosen) + 1,
        "nip" => $input['nip'],
        "nama" => $input['nama'],
        "matkul" => $input['matkul']
    ];

    http_response_code(201);
    echo json_encode($newDosen);
    exit;
}

// Method selain GET dan POST tidak diizinkan
http_response_code(405);
echo json_encode(["error" => "Method tidak didukung"]);