<?php

class VatValidator
{
    /**
     * Validate and possibly correct a VAT number.
     *
     * @param string $input
     * @return array {
     *     status: string (valid|corrected|invalid),
     *     corrected: ?string,
     *     notes: string
     * }
     */
    public function validate(string $input): array
    {
        $original = trim($input);

        // Normalize input
        $normalized = strtoupper($original);
        $normalized = preg_replace('/\s+/', '', $normalized);

        // Case 1: Already valid (starts with IT + 11 digits)
        if (preg_match('/^IT\d{11}$/', $normalized)) {
            return [
                'status' => 'valid',
                'corrected' => null,
                'notes' => 'Already valid'
            ];
        }

        // Case 2: Missing IT prefix but has 11 digits
        if (preg_match('/^\d{11}$/', $normalized)) {
            return [
                'status' => 'corrected',
                'corrected' => 'IT' . $normalized,
                'notes' => 'Added IT prefix'
            ];
        }

        // Case 3: Starts with IT but digits count wrong
        if (preg_match('/^IT\d+$/', $normalized)) {
            return [
                'status' => 'invalid',
                'corrected' => null,
                'notes' => 'Wrong number of digits after IT'
            ];
        }

        // Case 4: Anything else (letters, symbols, too short/long, etc.)
        return [
            'status' => 'invalid',
            'corrected' => null,
            'notes' => 'Invalid format'
        ];
    }
}
