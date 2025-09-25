# VAT Number Checker (PHP OOP, no framework)

A backend project to validate and correct Italian VAT numbers using pure PHP (8.3) and MySQL.  
No frameworks, built with Object Oriented Programming principles.

---

## 🚀 Features
- Upload CSV with VAT numbers.
- Validate, correct, or reject numbers.
- Automatically fix missing "IT" prefix when possible.
- Store results in MySQL.
- Display categorized results:
  - ✅ Valid VAT numbers
  - 🛠 Corrected VAT numbers (with explanation)
  - ❌ Invalid VAT numbers
- Test a single VAT number via a web form.
- Simple and clean UI using Bootstrap 5 (CDN).

---

## 📂 Project Structure
```
vat-checker/
│── classes/
│   ├── Database.php       # PDO wrapper for MySQL
│   ├── VatValidator.php   # Core validation logic
│   ├── CsvImporter.php    # CSV processor
│── index.php              # Upload CSV + results page
│── check.php              # Single VAT validator form
│── schema.sql             # Database schema
│── seed.sql               # Example data for testing
│── uploads/               # Uploaded CSV files (gitignored)
│── README.md
```

---

## ⚙️ Setup (XAMPP / WAMP)

### 1) Install & start services
- XAMPP: Start Apache + MySQL from the XAMPP Control Panel.
- WAMP: Start all services (Apache + MySQL). The tray icon should be green.

### 2) Place the project in the web root
- XAMPP (Windows): `C:\xampp\htdocs\vat-checker`
- WAMP (Windows): `C:\wamp64\www\vat-checker`

Or clone directly:
```bash
# XAMPP
cd C:\xampp\htdocs
git clone https://github.com/falconiogian/vat-checker

# WAMP
cd C:\wamp64\www
git clone https://github.com/falconiogian/vat-checker
```

### 3) Create the database
Open phpMyAdmin:
- XAMPP: http://localhost/phpmyadmin
- WAMP:  http://localhost/phpmyadmin

Run these scripts in order:
1. `schema.sql` (creates DB and tables)
2. `seed.sql` (adds example rows)

Or from terminal:
```bash
mysql -u root -p < schema.sql
mysql -u root -p < seed.sql
```

> The database name is `vat_checker` by default (created by `schema.sql`).

### 4) Configure the database connection (IMPORTANT)
Open `classes/Database.php`. The constructor has defaults that match typical local setups:

```php
public function __construct(
    string $host = 'localhost',
    string $db = 'vat_checker',
    string $user = 'root',
    string $pass = '',
    string $charset = 'utf8mb4'
) {
    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    // ...
}
```

- XAMPP default: user `root`, empty password (`''`)
- WAMP default: user `root`, empty password (`''`)
- If your MySQL uses a non-default port (e.g., 3307), either:
  - Change `$dsn` to:  
    `mysql:host=127.0.0.1;port=3307;dbname=vat_checker;charset=utf8mb4`  
  - or set `$host = '127.0.0.1:3307'`.

If you changed the DB name in `schema.sql`, update `$db` accordingly.

> Tip (optional hardening): Create a dedicated MySQL user instead of `root`:
> ```sql
> CREATE USER 'vat_user'@'localhost' IDENTIFIED BY 'strongpassword';
> GRANT ALL PRIVILEGES ON vat_checker.* TO 'vat_user'@'localhost';
> FLUSH PRIVILEGES;
> ```
> Then set `$user = 'vat_user'`, `$pass = 'strongpassword'` in `Database.php`.

### 5) Open the app
- Main page:  http://localhost/vat-checker  
- Single check: http://localhost/vat-checker/check.php

---

## 🗄 Database Details

Database: `vat_checker`  
Table: `vat_numbers`

| Column          | Type                                | Description                                   |
|-----------------|-------------------------------------|-----------------------------------------------|
| id              | INT AUTO_INCREMENT PRIMARY KEY      | Unique ID                                     |
| original_input  | VARCHAR(50)                         | Raw VAT number as provided                    |
| status          | ENUM('valid','corrected','invalid') | Classification of the VAT number              |
| corrected_value | VARCHAR(50) NULL                    | Corrected VAT number if applicable            |
| notes           | TEXT NULL                           | Explanation of validation/correction          |
| created_at      | TIMESTAMP DEFAULT CURRENT_TIMESTAMP | When the record was created                   |

---

## 📖 Usage

### Upload CSV
- Use the upload form on the main page to process a CSV file of VAT numbers.
- The app saves each result and shows categorized tables.

### Check a single VAT
- Go to `/check.php`, enter a VAT number, and see instant feedback.
- The result is saved to the database as well.

---

## 📝 Example Validations

| Input         | Result       | Notes                         |
|---------------|--------------|-------------------------------|
| IT12345678901 | ✅ Valid     | Already valid                 |
| 98765432158   | 🛠 Corrected | Added IT prefix               |
| IT12345       | ❌ Invalid   | Wrong number of digits        |
| 123-hello     | ❌ Invalid   | Invalid format (non-numeric)  |

---

## 🛠 Troubleshooting

- SQLSTATE[HY000] [1049] Unknown database 'vat_checker'  
  You haven’t run `schema.sql` yet, or the DB name in `Database.php` doesn’t match the actual DB.

- SQLSTATE[HY000] [1045] Access denied for user  
  Wrong MySQL username/password. Update `Database.php` to correct credentials.

- SQLSTATE[HY000] [2002] No such file or directory / Connection refused  
  MySQL isn’t running or the host/port is wrong. Start MySQL and verify host/port in `$dsn`.

- Class 'PDO' not found  
  Enable/ext install `pdo_mysql` (it’s enabled by default in modern XAMPP/WAMP).
  Check your `php.ini` for `extension=pdo_mysql`.

---

## ⚡ Notes
- Pure PHP (8.3), no frameworks or external dependencies.
- OOP design; data persisted via PDO.
- UI is Bootstrap via CDN only.
