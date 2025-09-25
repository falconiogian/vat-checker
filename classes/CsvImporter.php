<?php

require_once __DIR__ . '/VatValidator.php';
require_once __DIR__ . '/Database.php';

class CsvImporter
{
    private VatValidator $validator;
    private Database $db;

    public function __construct(Database $db)
    {
        $this->validator = new VatValidator();
        $this->db = $db;
    }

    /**
     * Process a 2-column CSV file (id, vat_number) and store results in the DB.
     *
     * @param string $filePath
     * @return array Summary of processing
     */
    public function import(string $filePath): array
    {
        if (!file_exists($filePath) || !is_readable($filePath)) {
            throw new RuntimeException("CSV file not found or not readable.");
        }

        $handle = fopen($filePath, 'r');
        if ($handle === false) {
            throw new RuntimeException("Unable to open file: $filePath");
        }

        $processed = 0;
        $valid = 0;
        $corrected = 0;
        $invalid = 0;

        $isHeader = true;

        while (($row = fgetcsv($handle, 0, ",")) !== false) {
            // Skip header
            if ($isHeader) {
                $isHeader = false;
                continue;
            }

            // Expecting at least 2 columns: id, vat_number
            if (count($row) < 2) {
                continue;
            }

            $input = trim($row[1] ?? '');
            if ($input === '') {
                continue; // skip blanks
            }

            // Remove BOM if present
            $input = preg_replace('/^\xEF\xBB\xBF/', '', $input);

            $result = $this->validator->validate($input);

            $this->db->insertVatNumber(
                $input,
                $result['status'],
                $result['corrected'],
                $result['notes']
            );

            $processed++;
            switch ($result['status']) {
                case 'valid':
                    $valid++;
                    break;
                case 'corrected':
                    $corrected++;
                    break;
                case 'invalid':
                    $invalid++;
                    break;
            }
        }

        fclose($handle);

        return [
            'processed' => $processed,
            'valid' => $valid,
            'corrected' => $corrected,
            'invalid' => $invalid,
        ];
    }
}
