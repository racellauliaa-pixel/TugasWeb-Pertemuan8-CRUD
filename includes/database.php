<?php
class Database
{
    private static ?Database $instance = null;
    private PDO $pdo;

    private const HOST    = '127.0.0.1';
    private const DBNAME  = 'inventaris_db';
    private const USER    = 'root';
    private const PASS    = 'najaemin17.';
    private const CHARSET = 'utf8mb4';

    private function __construct()
    {
        $dsn = sprintf(
            'mysql:host=%s;dbname=%s;charset=%s',
            self::HOST,
            self::DBNAME,
            self::CHARSET
        );

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $this->pdo = new PDO($dsn, self::USER, self::PASS, $options);
        } catch (PDOException $e) {
            
            throw new PDOException('Koneksi database gagal: ' . $e->getMessage());
        }
    }

    private function __clone() {}

    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection(): PDO
    {
        return $this->pdo;
    }
}