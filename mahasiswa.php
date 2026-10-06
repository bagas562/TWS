<?php
header("Content-Type: application/json");
require "data.php";

$method = $_SERVER['REQUEST_METHOD'];

// GET: Ambil semua data
if ($method === "GET") {
    http_response_code(200);
    echo json_encode($students);
    exit;
}

// POST: Tambah data baru
if ($method === "POST") {
    $input = json_decode(file_get_contents("php://input"), true);

    if (!isset($input['nim']) || !isset($input['name']) || !isset($input['major'])) {
        http_response_code(400);
        echo json_encode(["error" => "Field nim, name, dan major wajib diisi"]);
        exit;
    }

    $newId = count($students) + 1;
    $newStudent = [
        "id" => $newId,
        "nim" => $input['nim'],
        "name" => $input['name'],
        "major" => $input['major'],
    ];

    http_response_code(201);
    header("Location: /mahasiswa.php?id=$newId");
    echo json_encode($newStudent);
    exit;
}

// PUT: Ubah data mahasiswa berdasarkan ID
if ($method === "PUT") {
    $id = $_GET['id'] ?? null;
    $studentIndex = null;

    foreach ($students as $index => $s) {
        if ($s['id'] == $id) {
            $studentIndex = $index;
            break;
        }
    }

    if ($studentIndex === null) {
        http_response_code(404);
        echo json_encode(["error" => "Mahasiswa dengan ID $id tidak ditemukan"]);
        exit;
    }

    $input = json_decode(file_get_contents("php://input"), true);
    if (!isset($input['nim']) || !isset($input['name']) || !isset($input['major'])) {
        http_response_code(400);
        echo json_encode(["error" => "Field nim, name, dan major wajib diisi"]);
        exit;
    }

    $students[$studentIndex]['nim'] = $input['nim'];
    $students[$studentIndex]['name'] = $input['name'];
    $students[$studentIndex]['major'] = $input['major'];

    http_response_code(200);
    echo json_encode([
        "message" => "Data mahasiswa berhasil diperbarui",
        "data" => $students[$studentIndex]
    ]);
    exit;
}

// DELETE: Hapus data mahasiswa berdasarkan ID
if ($method === "DELETE") {
    $id = $_GET['id'] ?? null;
    $found = false;

    foreach ($students as $index => $s) {
        if ($s['id'] == $id) {
            $found = true;
            unset($students[$index]);
            break;
        }
    }

    if (!$found) {
        http_response_code(404);
        echo json_encode(["error" => "Mahasiswa dengan ID $id tidak ditemukan"]);
        exit;
    }

    http_response_code(200);
    echo json_encode(["message" => "Data mahasiswa dengan ID $id berhasil dihapus"]);
    exit;
}

// Method tidak didukung
http_response_code(405);
echo json_encode(["error" => "Method tidak didukung"]);