<?php

class Database
{
    private PDO $pdo;

    public function __construct(
        string $host = 'localhost',
        string $db = 'vat_checker',
        string $user = 'root',
        string $pass = '',
        string $charset = 'utf8mb4'
    ) {
        $dsn = "mysql:host=$host;dbname=$db;charset=$charset";

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        $this->pdo = new PDO($dsn, $user, $pass, $options);
    }

    public function insertVatNumber(string $original, string $status, ?string $corrected, string $notes): void
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO vat_numbers (original_input, status, corrected_value, notes)
            VALUES (:original, :status, :corrected, :notes)
        ");
        $stmt->execute([
            ':original'  => $original,
            ':status'    => $status,
            ':corrected' => $corrected,
            ':notes'     => $notes,
        ]);
    }

    public function getVatNumbersByStatus(string $status): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM vat_numbers WHERE status = :status ORDER BY created_at DESC");
        $stmt->execute([':status' => $status]);
        return $stmt->fetchAll();
    }

    public function getAllVatNumbers(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM vat_numbers ORDER BY created_at DESC");
        return $stmt->fetchAll();
    }
}
