CREATE DATABASE IF NOT EXISTS inventaris_db
  CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

USE inventaris_db;

DROP TABLE IF EXISTS produk;
DROP TABLE IF EXISTS kategori;
DROP TABLE IF EXISTS supplier;

CREATE TABLE kategori (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_kategori VARCHAR(100) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE supplier (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_supplier VARCHAR(100) NOT NULL,
    kontak VARCHAR(50) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE produk (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_produk VARCHAR(150) NOT NULL,
    kategori_id INT NOT NULL,
    supplier_id INT NOT NULL,
    harga DECIMAL(12,2) NOT NULL DEFAULT 0,
    stok INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_produk_kategori FOREIGN KEY (kategori_id)
        REFERENCES kategori(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_produk_supplier FOREIGN KEY (supplier_id)
        REFERENCES supplier(id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

DROP TABLE IF EXISTS log_aktivitas;
CREATE TABLE log_aktivitas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    aksi VARCHAR(50) NOT NULL,
    keterangan VARCHAR(255) NOT NULL,
    waktu TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO kategori (nama_kategori) VALUES
    ('Cleanser'),
    ('Toner & Essence'),
    ('Serum'),
    ('Moisturizer'),
    ('Eye Care');

INSERT INTO supplier (nama_supplier, kontak) VALUES
    ('PT Luxe Beauty Indonesia', '0812-1111-2222'),
    ('CV Dermacare Premium', '0813-3333-4444'),
    ('PT Golden Radiance', '0821-5555-6666'),
    ('UD Elite Skincare', '0822-7777-8888'),
    ('PT Prestige Cosmetics', '0852-9999-0000');

INSERT INTO produk (nama_produk, kategori_id, supplier_id, harga, stok) VALUES
    ('Gentle Cleansing Balm Premium', 1, 1, 185000.00, 35),
    ('Double Cleanse Oil to Foam', 1, 1, 165000.00, 40),
    ('Hydrating Essence Toner', 2, 2, 220000.00, 30),
    ('24K Gold Radiance Essence', 2, 3, 350000.00, 20),
    ('Hyaluronic Acid Serum Concentrate', 3, 2, 275000.00, 45),
    ('Retinol Night Repair Serum', 3, 4, 320000.00, 25),
    ('Snail Mucin Repair Serum', 3, 4, 245000.00, 38),
    ('Ceramide Barrier Repair Cream', 4, 5, 295000.00, 28),
    ('Gold Collagen Night Cream', 4, 3, 380000.00, 18),
    ('Caviar Eye Cream', 5, 5, 410000.00, 15);