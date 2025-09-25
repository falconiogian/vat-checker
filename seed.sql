USE vat_checker;

INSERT INTO vat_numbers (original_input, status, corrected_value, notes) VALUES
('IT12345678901', 'valid', NULL, 'Already valid'),
('98765432158', 'corrected', 'IT98765432158', 'Added IT prefix'),
('IT12345', 'invalid', NULL, 'Wrong number of digits after IT'),
('123-hello', 'invalid', NULL, 'Invalid format');
