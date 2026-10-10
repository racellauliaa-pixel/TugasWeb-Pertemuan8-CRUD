<?php
require_once __DIR__ . '/includes/config.php';

$errors = [];
$old = ['nama_produk' => '', 'kategori_id' => '', 'supplier_id' => '', 'harga' => '', 'stok' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old['nama_produk'] = trim($_POST['nama_produk'] ?? '');
    $old['kategori_id'] = $_POST['kategori_id'] ?? '';
    $old['supplier_id'] = $_POST['supplier_id'] ?? '';
    $old['harga']       = trim($_POST['harga'] ?? '');
    $old['stok']        = trim($_POST['stok'] ?? '');

    if ($old['nama_produk'] === '') {
        $errors[] = 'Nama produk tidak boleh kosong.';
    }
    if ($old['kategori_id'] === '' || !getKategoriById($pdo, (int) $old['kategori_id'])) {
        $errors[] = 'Kategori wajib dipilih.';
    }
    if ($old['supplier_id'] === '' || !getSupplierById($pdo, (int) $old['supplier_id'])) {
        $errors[] = 'Supplier wajib dipilih.';
    }
    if ($old['harga'] === '' || !is_numeric($old['harga']) || (float) $old['harga'] < 0) {
        $errors[] = 'Harga harus berupa angka dan tidak boleh negatif.';
    }
    if ($old['stok'] === '' || !ctype_digit((string) $old['stok'])) {
        $errors[] = 'Stok harus berupa angka bulat dan tidak boleh negatif.';
    }

    if (empty($errors)) {
        createProduk($pdo, [
            'nama_produk' => htmlspecialchars($old['nama_produk'], ENT_QUOTES, 'UTF-8'),
            'kategori_id' => (int) $old['kategori_id'],
            'supplier_id' => (int) $old['supplier_id'],
            'harga'       => (float) $old['harga'],
            'stok'        => (int) $old['stok'],
        ]);

        setFlash('success', 'Produk "' . $old['nama_produk'] . '" berhasil ditambahkan.');
        header('Location: index.php');
        exit;
    }
}

$kategoriList = getAllKategori($pdo);
$supplierList = getAllSupplier($pdo);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Tambah Produk</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Special+Elite&family=Work+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/style.css">
</head>
<body>

<div class="board">
  <div class="paper">
    <div class="pin"></div>

    <h1 class="page-title">Tambah Produk</h1>
    <p class="page-subtitle">Isi data produk baru</p>

    <?php if (!empty($errors)): ?>
      <ul class="error-list">
        <?php foreach ($errors as $error): ?>
          <li><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>

    <form method="POST" action="create.php" novalidate>
      <label class="field-label">
        Nama Produk
        <input type="text" name="nama_produk" value="<?= htmlspecialchars($old['nama_produk'], ENT_QUOTES, 'UTF-8') ?>" required>
      </label>

      <label class="field-label">
        Kategori
        <select name="kategori_id" required>
          <option value="">-- Pilih Kategori --</option>
          <?php foreach ($kategoriList as $k): ?>
            <option value="<?= (int) $k['id'] ?>" <?= (string) $old['kategori_id'] === (string) $k['id'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($k['nama_kategori'], ENT_QUOTES, 'UTF-8') ?>
            </option>
          <?php endforeach; ?>
        </select>
      </label>

      <label class="field-label">
        Supplier
        <select name="supplier_id" required>
          <option value="">-- Pilih Supplier --</option>
          <?php foreach ($supplierList as $s): ?>
            <option value="<?= (int) $s['id'] ?>" <?= (string) $old['supplier_id'] === (string) $s['id'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($s['nama_supplier'], ENT_QUOTES, 'UTF-8') ?>
            </option>
          <?php endforeach; ?>
        </select>
      </label>

      <label class="field-label">
        Harga (Rp)
        <input type="number" step="0.01" min="0" name="harga" value="<?= htmlspecialchars($old['harga'], ENT_QUOTES, 'UTF-8') ?>" required>
      </label>

      <label class="field-label">
        Stok
        <input type="number" min="0" name="stok" value="<?= htmlspecialchars($old['stok'], ENT_QUOTES, 'UTF-8') ?>" required>
      </label>

      <div class="btn-row">
        <button type="submit" class="stamp-btn">Simpan</button>
        <a href="index.php" class="stamp-btn stamp-btn--ink" style="text-align:center;text-decoration:none;">Batal</a>
      </div>
    </form>
  </div>
</div>

</body>
</html>