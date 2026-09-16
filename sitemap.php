<?php
header("Content-Type: application/xml; charset=utf-8");

// Tentukan lokasi file sumber brand
$file_brands = "list.txt"; // Sesuaikan jika nama file txt kamu "list.txt"

if (!file_exists($file_brands)) {
    echo '<?xml version="1.0" encoding="UTF-8"?>';
    echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"></urlset>';
    exit();
}

$brands = file($file_brands, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

// Deteksi protokol dan domain secara dinamis
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];

// Tanggal modifikasi sitemap hari ini
$today = date('Y-m-d');

echo '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;

foreach ($brands as $item) {
    $slug = strtolower(trim(str_replace(' ', '-', $item)));
    if (!empty($slug)) {
        $brandUrl = $protocol . "://" . $host . "/" . $slug;
        
        echo "  <url>\n";
        echo "    <loc>" . htmlspecialchars($brandUrl, ENT_XML1, 'UTF-8') . "</loc>\n";
        echo "    <lastmod>" . $today . "</lastmod>\n";
        echo "    <changefreq>weekly</changefreq>\n";
        echo "    <priority>1.0</priority>\n";
        echo "  </url>\n";
    }
}

echo '</urlset>';
?>
