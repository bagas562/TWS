<?php
header("Content-Type: application/json");

// Data awal (Dummy array in-memory)
$books = [
    ["id" => 1, "judul" => "Clean Code: A Handbook of Agile Software Craftsmanship", "penulis" => "Robert C. Martin", "harga" => 450000],
    ["id" => 2, "judul" => "Design Patterns: Elements of Reusable Object-Oriented Software", "penulis" => "Erich Gamma, Richard Helm, Ralph Johnson, John Vlissides", "harga" => 520000],
];

$method = $_SERVER['REQUEST_METHOD'];
$id = $_GET['id'] ?? null;

// Routing request berdasarkan HTTP Method
match ($method) {
    'GET'    => $id ? ambilSatuBuku($books, $id) : ambilSemuaBuku($books),
    'POST'   => tambahBuku($books),
    'PATCH'  => ubahBuku($books, $id),
    'DELETE' => hapusBuku($books, $id),
    default  => kirimResponse(405, ["error" => "Method tidak didukung"]),
};

// --- FUNGSIONALITAS CRUD ---

function ambilSemuaBuku($books) {
    kirimResponse(200, $books);
}

function ambilSatuBuku($books, $id) {
    foreach ($books as $b) {
        if ($b["id"] == $id) {
            kirimResponse(200, $b);
            return;
        }
    }
    kirimResponse(404, ["error" => "Buku tidak ditemukan"]);
}

function tambahBuku($books) {
    $input = json_decode(file_get_contents("php://input"), true);

    // Validasi input
    if (empty($input['judul']) || empty($input['penulis']) || empty($input['harga'])) {
        kirimResponse(400, ["error" => "Field judul, penulis, dan harga wajib diisi"]);
        return;
    }

    $newId = count($books) + 1;
    $newBook = [
        "id"      => $newId,
        "judul"   => $input['judul'],
        "penulis" => $input['penulis'],
        "harga"   => (int)$input['harga'],
    ];

    header("Location: /buku.php?id=$newId");
    kirimResponse(201, $newBook);
}

function ubahBuku($books, $id) {
    if (!$id) {
        kirimResponse(400, ["error" => "Parameter ID wajib disertakan"]);
        return;
    }

    $input = json_decode(file_get_contents("php://input"), true);

    foreach ($books as $i => $b) {
        if ($b["id"] == $id) {
            $books[$i] = array_merge($b, $input);
            kirimResponse(200, $books[$i]);
            return;
        }
    }

    kirimResponse(404, ["error" => "Buku tidak ditemukan"]);
}

function hapusBuku($books, $id) {
    if (!$id) {
        kirimResponse(400, ["error" => "Parameter ID wajib disertakan"]);
        return;
    }

    foreach ($books as $i => $b) {
        if ($b["id"] == $id) {
            unset($books[$i]);
            kirimResponse(200, ["message" => "Buku berhasil dihapus"]);
            return;
        }
    }

    kirimResponse(404, ["error" => "Buku tidak ditemukan"]);
}

function kirimResponse($code, $data = null) {
    http_response_code($code);
    if ($data !== null) {
        echo json_encode($data);
    }
    exit;
}