<?php
require_once __DIR__ . '/includes/config.php';

$flash   = getFlash();
$keyword = trim($_GET['q'] ?? '');

$perPage     = 5;
$currentPage = max(1, (int) ($_GET['page'] ?? 1));
$totalRows   = countProduk($pdo, $keyword);
$totalPages  = max(1, (int) ceil($totalRows / $perPage));
$currentPage = min($currentPage, $totalPages);
$offset      = ($currentPage - 1) * $perPage;

$produkList = getProdukList($pdo, $keyword, $perPage, $offset);

function qs(array $extra = [], string $keyword = '', int $page = 1): string
{
    $params = array_merge($keyword !== '' ? ['q' => $keyword] : [], ['page' => $page], $extra);
    return '?' . http_build_query($params);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>CRUD Inventaris</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Special+Elite&family=Work+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/style.css">
</head>
<body>

<div class="board">
  <div class="paper paper--wide">
    <div class="pin"></div>

    <h1 class="page-title">Inventaris Barang</h1>
    <p class="page-subtitle">CRUD Produk — PHP Native + PDO + MySQL</p>

    <?php if ($flash): ?>
      <div class="alert alert-<?= htmlspecialchars($flash['type'], ENT_QUOTES, 'UTF-8') ?>">
        <?= htmlspecialchars($flash['message'], ENT_QUOTES, 'UTF-8') ?>
      </div>
    <?php endif; ?>

    <div class="toolbar">
      <form method="GET" class="search-form">
        <input type="text" name="q" placeholder="Cari nama produk..." value="<?= htmlspecialchars($keyword, ENT_QUOTES, 'UTF-8') ?>">
        <button type="submit" class="stamp-btn stamp-btn--sm">Cari</button>
        <?php if ($keyword !== ''): ?>
          <a href="index.php" class="stamp-btn stamp-btn--sm stamp-btn--ink">Reset</a>
        <?php endif; ?>
      </form>
      <a href="create.php" class="stamp-btn stamp-btn--sm">+ Tambah Produk</a>
    </div>

    <?php if (empty($produkList)): ?>
      <p class="empty-note">
        <?= $keyword !== '' ? 'Nggak ada produk yang cocok dengan pencarian itu.' : 'Belum ada produk. Yuk tambah dulu.' ?>
      </p>
    <?php else: ?>
      <div class="table-wrap">
        <table class="data-table">
          <thead>
            <tr>
              <th>Nama Produk</th>
              <th>Kategori</th>
              <th>Supplier</th>
              <th>Harga</th>
              <th>Stok</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($produkList as $p): ?>
              <tr>
                <td><?= htmlspecialchars($p['nama_produk'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($p['nama_kategori'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($p['nama_supplier'], ENT_QUOTES, 'UTF-8') ?></td>
                <td>Rp<?= number_format((float) $p['harga'], 0, ',', '.') ?></td>
                <td><?= (int) $p['stok'] ?></td>
                <td>
                  <div class="action-cell">
                    <a href="update.php?id=<?= (int) $p['id'] ?>" class="stamp-btn stamp-btn--xs stamp-btn--ink">Edit</a>
                    <form method="POST" action="delete.php" onsubmit="return confirm('Yakin mau hapus produk &quot;<?= htmlspecialchars(addslashes($p['nama_produk']), ENT_QUOTES, 'UTF-8') ?>&quot;? Aksi ini nggak bisa dibatalkan.');">
                      <input type="hidden" name="id" value="<?= (int) $p['id'] ?>">
                      <button type="submit" class="stamp-btn stamp-btn--xs stamp-btn--danger">Hapus</button>
                    </form>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <?php if ($totalPages > 1): ?>
        <div class="pagination">
          <?php if ($currentPage > 1): ?>
            <a href="<?= qs([], $keyword, $currentPage - 1) ?>" class="page-link">&laquo; Sebelumnya</a>
          <?php endif; ?>
          <span class="page-info">Halaman <?= $currentPage ?> dari <?= $totalPages ?></span>
          <?php if ($currentPage < $totalPages): ?>
            <a href="<?= qs([], $keyword, $currentPage + 1) ?>" class="page-link">Berikutnya &raquo;</a>
          <?php endif; ?>
        </div>
      <?php endif; ?>
    <?php endif; ?>
  </div>
</div>

</body>
</html>