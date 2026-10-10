<?php
require_once __DIR__ . '/includes/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$id = (int) ($_POST['id'] ?? 0);
$produk = getProdukById($pdo, $id);

if (!$produk) {
    setFlash('error', 'Produk tidak ditemukan atau sudah dihapus.');
    header('Location: index.php');
    exit;
}

try {
    deleteProdukWithLog($pdo, $id, $produk['nama_produk']);
    setFlash('success', 'Produk "' . htmlspecialchars($produk['nama_produk'], ENT_QUOTES, 'UTF-8') . '" berhasil dihapus.');
} catch (Throwable $e) {
    setFlash('error', 'Gagal menghapus produk. Coba lagi.');
}

header('Location: index.php');
exit;