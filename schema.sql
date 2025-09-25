CREATE DATABASE IF NOT EXISTS vat_checker;
USE vat_checker;

DROP TABLE IF EXISTS vat_numbers;

CREATE TABLE vat_numbers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    original_input VARCHAR(50) NOT NULL,
    status ENUM('valid', 'corrected', 'invalid') NOT NULL,
    corrected_value VARCHAR(50) NULL,
    notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
