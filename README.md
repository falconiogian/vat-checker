# VAT Number Checker (PHP OOP, no framework)

Simple php project to validate and correct Italian VAT numbers using pure PHP (8.3) and MySQL.

## Features
- Upload CSV with VAT numbers
- Validate, correct, or reject numbers
- Store results in MySQL
- Display categorized results (valid, corrected, invalid)
- Form to test a single VAT number

## Setup
1. Clone the repo
2. Import `schema.sql` and `seed.sql` into MySQL
3. Run the project under XAMPP/WAMP (PHP 8.3)
4. Open localhost/vat-checker in the browser (it will automatically load index.php) 

## Database
- DB: `vat_checker`
- Table: `vat_numbers`

## Notes
- No frameworks used, pure PHP (OOP style).