<?php
// Ambil slug dari URL (contoh: /depobos)
$request_path = isset($_GET['slug']) ? trim($_GET['slug'], '/') : '';

// 1. Jika path kosong, muat home.php (Halaman Utama)
if (empty($request_path)) {
    if (file_exists('home.php')) {
        include('home.php');
    } else {
        header("HTTP/1.0 404 Not Found");
        echo "<h1>404 Not Found</h1>";
    }
    exit();
}

$file_brands = "list1.txt"; // File sumber data brand tunggal
$filePelengkap = "pelengkap.txt";

if (!file_exists($file_brands)) {
    header("HTTP/1.0 404 Not Found");
    echo "<h1>404 Not Found</h1>";
    exit();
}

$brands = file($file_brands, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$pelengkap = file_exists($filePelengkap) 
             ? file($filePelengkap, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) 
             : [];

$target_string = strtolower($request_path);
$matched_index = -1;
$current_brand = "";

// Cocokkan slug dengan isi list tunggal
foreach ($brands as $index => $item) {
    $b_slug = strtolower(trim(str_replace(' ', '-', $item)));

    if ($target_string === $b_slug) {
        $matched_index = $index;
        $current_brand = strtoupper(trim($item));
        break;
    }
}

// Fallback jika slug tidak ditemukan persis di list (menggunakan crc32)
if ($matched_index === -1) {
    $fallback_index = abs(crc32($target_string)) % count($brands);
    $matched_index = $fallback_index;
    $current_brand = strtoupper(trim($brands[$fallback_index]));
}

// Pelengkap berputar (modulo) yang di dalamnya sudah mengandung huruf "x" (misal: "Game Online x ...")
$selected_pelengkap = "";
if (!empty($pelengkap)) {
    $p_Index = $matched_index % count($pelengkap);
    $selected_pelengkap = trim($pelengkap[$p_Index]);
}

// Hasil akhir Title: Brand Tunggal + Pelengkap (yang sudah ada huruf x-nya)
$BRANDS = trim($current_brand . " " . $selected_pelengkap);

// URL Pendukung (Canonical / AMP)
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$fullUrl = $protocol . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$slug_brand = strtolower(str_replace(' ', '-', trim($current_brand)));
$ampUrl = "https://dewatempur.site/terbang/" . $slug_brand;

// Logika Generator Meta Keywords Otomatis (Khusus 1 Brand + Variasi Kata Sambung)
$kw = strtolower($current_brand);
$kata_sambung = ["", "login", "link", "daftar", "alternatif", "resmi", "gacor"]; 
$kumpulan_keyword = [];

foreach ($kata_sambung as $kata) {
    if (!empty($kw)) {
        $kumpulan_keyword[] = trim($kw . " " . $kata);
    }
}
$meta_keywords = implode(", ", $kumpulan_keyword);

// Panggil template HTML/Frontend
include('asap.php');
exit();
?>
