<?php

function getAllKategori(PDO $pdo): array
{
    $stmt = $pdo->query('SELECT id, nama_kategori FROM kategori ORDER BY nama_kategori ASC');
    return $stmt->fetchAll();
}

function getAllSupplier(PDO $pdo): array
{
    $stmt = $pdo->query('SELECT id, nama_supplier FROM supplier ORDER BY nama_supplier ASC');
    return $stmt->fetchAll();
}

function getKategoriById(PDO $pdo, int $id): ?array
{
    $stmt = $pdo->prepare('SELECT id, nama_kategori FROM kategori WHERE id = :id');
    $stmt->execute(['id' => $id]);
    $row = $stmt->fetch();
    return $row ?: null;
}

function getSupplierById(PDO $pdo, int $id): ?array
{
    $stmt = $pdo->prepare('SELECT id, nama_supplier FROM supplier WHERE id = :id');
    $stmt->execute(['id' => $id]);
    $row = $stmt->fetch();
    return $row ?: null;
}

function countProduk(PDO $pdo, string $keyword = ''): int
{
    if ($keyword !== '') {
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM produk WHERE nama_produk LIKE :kw');
        $stmt->execute(['kw' => '%' . $keyword . '%']);
    } else {
        $stmt = $pdo->query('SELECT COUNT(*) FROM produk');
    }
    return (int) $stmt->fetchColumn();
}

function getProdukList(PDO $pdo, string $keyword = '', int $limit = 5, int $offset = 0): array
{
    $sql = 'SELECT
                p.id, p.nama_produk, p.harga, p.stok, p.created_at,
                k.nama_kategori,
                s.nama_supplier
            FROM produk p
            JOIN kategori k ON k.id = p.kategori_id
            JOIN supplier s ON s.id = p.supplier_id';

    if ($keyword !== '') {
        $sql .= ' WHERE p.nama_produk LIKE :kw';
    }

    $sql .= ' ORDER BY p.id DESC LIMIT :limit OFFSET :offset';

    $stmt = $pdo->prepare($sql);
    if ($keyword !== '') {
        $stmt->bindValue(':kw', '%' . $keyword . '%', PDO::PARAM_STR);
    }
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll();
}

function getProdukById(PDO $pdo, int $id): ?array
{
    $stmt = $pdo->prepare('SELECT * FROM produk WHERE id = :id');
    $stmt->execute(['id' => $id]);
    $row = $stmt->fetch();
    return $row ?: null;
}

function createProduk(PDO $pdo, array $data): void
{
    $stmt = $pdo->prepare(
        'INSERT INTO produk (nama_produk, kategori_id, supplier_id, harga, stok)
         VALUES (:nama_produk, :kategori_id, :supplier_id, :harga, :stok)'
    );
    $stmt->execute([
        'nama_produk'  => $data['nama_produk'],
        'kategori_id'  => $data['kategori_id'],
        'supplier_id'  => $data['supplier_id'],
        'harga'        => $data['harga'],
        'stok'         => $data['stok'],
    ]);
}

function updateProduk(PDO $pdo, int $id, array $data): void
{
    $stmt = $pdo->prepare(
        'UPDATE produk
         SET nama_produk = :nama_produk,
             kategori_id = :kategori_id,
             supplier_id = :supplier_id,
             harga = :harga,
             stok = :stok
         WHERE id = :id'
    );
    $stmt->execute([
        'nama_produk'  => $data['nama_produk'],
        'kategori_id'  => $data['kategori_id'],
        'supplier_id'  => $data['supplier_id'],
        'harga'        => $data['harga'],
        'stok'         => $data['stok'],
        'id'           => $id,
    ]);
}

function deleteProdukWithLog(PDO $pdo, int $id, string $namaProduk): void
{
    $pdo->beginTransaction();
    try {
        $stmt = $pdo->prepare('DELETE FROM produk WHERE id = :id');
        $stmt->execute(['id' => $id]);

        $stmtLog = $pdo->prepare(
            'INSERT INTO log_aktivitas (aksi, keterangan) VALUES (:aksi, :keterangan)'
        );
        $stmtLog->execute([
            'aksi'       => 'DELETE',
            'keterangan' => 'Produk "' . $namaProduk . '" (ID ' . $id . ') dihapus',
        ]);

        $pdo->commit();
    } catch (Throwable $e) {
        $pdo->rollBack();
        throw $e;
    }
}