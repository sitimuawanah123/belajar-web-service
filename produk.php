<?php

header("Content-Type: application/json");

session_start();

require "data.php";

// Menyimpan data dalam session agar perubahan tetap terbaca
// pada request berikutnya
if (!isset($_SESSION['alatTulis'])) {
    $_SESSION['alatTulis'] = $alatTulis;
}

$alatTulis = $_SESSION['alatTulis'];

// CREATE - POST
function tambahData(&$alatTulis)
{
    $input = json_decode(file_get_contents("php://input"), true);

    if (
        !isset($input['name']) ||
        !isset($input['category']) ||
        !isset($input['price']) ||
        !isset($input['stock'])
    ) {
        http_response_code(400);

        echo json_encode([
            "error" => "Field name, category, price, dan stock wajib diisi"
        ]);

        return;
    }

    $newId = count($alatTulis) + 1;

    $newAlatTulis = [
        "id" => $newId,
        "name" => $input['name'],
        "category" => $input['category'],
        "price" => $input['price'],
        "stock" => $input['stock']
    ];

    $alatTulis[] = $newAlatTulis;

    $_SESSION['alatTulis'] = $alatTulis;

    http_response_code(201);

    header("Location: /produk.php?id=$newId");

    echo json_encode($newAlatTulis);
}


// READ - GET SEMUA DATA
function ambilSemuaData($alatTulis)
{
    http_response_code(200);

    echo json_encode($alatTulis);
}


// READ - GET BERDASARKAN ID
function ambilSatuData($alatTulis, $id)
{
    foreach ($alatTulis as $alat) {

        if ($alat["id"] == $id) {

            http_response_code(200);

            echo json_encode($alat);

            return;
        }
    }

    http_response_code(404);

    echo json_encode([
        "error" => "Alat tulis tidak ditemukan"
    ]);
}


// UPDATE - PATCH
function ubahData(&$alatTulis, $id)
{
    $input = json_decode(file_get_contents("php://input"), true);

    foreach ($alatTulis as $i => $alat) {

        if ($alat["id"] == $id) {

            $alatTulis[$i] = array_merge($alat, $input);

            $_SESSION['alatTulis'] = $alatTulis;

            http_response_code(200);

            echo json_encode($alatTulis[$i]);

            return;
        }
    }

    http_response_code(404);

    echo json_encode([
        "error" => "Alat tulis tidak ditemukan"
    ]);
}


// DELETE
function hapusData(&$alatTulis, $id)
{
    foreach ($alatTulis as $i => $alat) {

        if ($alat["id"] == $id) {

            unset($alatTulis[$i]);

            $alatTulis = array_values($alatTulis);

            $_SESSION['alatTulis'] = $alatTulis;

            http_response_code(204);

            return;
        }
    }

    http_response_code(404);

    echo json_encode([
        "error" => "Alat tulis tidak ditemukan"
    ]);
}


// ROUTING
$method = $_SERVER['REQUEST_METHOD'];

$id = $_GET['id'] ?? null;

match ($method) {

    'GET' =>
        $id
        ? ambilSatuData($alatTulis, $id)
        : ambilSemuaData($alatTulis),

    'POST' =>
        tambahData($alatTulis),

    'PATCH' =>
        ubahData($alatTulis, $id),

    'DELETE' =>
        hapusData($alatTulis, $id),

    default =>
        http_response_code(405)
};