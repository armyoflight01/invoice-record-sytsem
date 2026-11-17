<?php
/**
 * نظام تسجيل وإدارة الفواتير باللغة العربية
 * Arabic Invoice Record System
 * Configuration & SQLite Auto-Setup
 */

// Enable error logging while keeping API clean
error_reporting(E_ALL);
ini_set('display_errors', '0');

define('DATA_DIR', __DIR__ . DIRECTORY_SEPARATOR . 'data');
define('DB_FILE', DATA_DIR . DIRECTORY_SEPARATOR . 'invoices.db');

if (!file_exists(DATA_DIR)) {
    @mkdir(DATA_DIR, 0777, true);
}

function getDatabaseConnection(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = 'sqlite:' . DB_FILE;
        $pdo = new PDO($dsn, null, null, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
        // Enable foreign key support in SQLite
        $pdo->exec('PRAGMA foreign_keys = ON;');
    }
    return $pdo;
}

function initDatabaseSchema(): void {
    $db = getDatabaseConnection();

    // Tables schema
    $queries = [
        "CREATE TABLE IF NOT EXISTS settings (
            key TEXT PRIMARY KEY,
            value TEXT
        );",

        "CREATE TABLE IF NOT EXISTS clients (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            phone TEXT,
            email TEXT,
            address TEXT,
            tax_number TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );",

        "CREATE TABLE IF NOT EXISTS products (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            price REAL DEFAULT 0,
            tax_rate REAL DEFAULT 15,
            description TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );",

        "CREATE TABLE IF NOT EXISTS invoices (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            invoice_number TEXT UNIQUE NOT NULL,
            client_id INTEGER,
            issue_date DATE NOT NULL,
            due_date DATE,
            status TEXT DEFAULT 'unpaid', -- paid, unpaid, pending, cancelled
            payment_method TEXT DEFAULT 'bank_transfer', -- cash, bank_transfer, card, cheque
            subtotal REAL DEFAULT 0,
            discount REAL DEFAULT 0,
            tax_amount REAL DEFAULT 0,
            grand_total REAL DEFAULT 0,
            notes TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (client_id) REFERENCES clients (id) ON DELETE SET NULL
        );",

        "CREATE TABLE IF NOT EXISTS invoice_items (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            invoice_id INTEGER NOT NULL,
            item_name TEXT NOT NULL,
            description TEXT,
            quantity REAL DEFAULT 1,
            unit_price REAL DEFAULT 0,
            discount REAL DEFAULT 0,
            tax_rate REAL DEFAULT 15,
            total REAL DEFAULT 0,
            FOREIGN KEY (invoice_id) REFERENCES invoices (id) ON DELETE CASCADE
        );"
    ];

    foreach ($queries as $q) {
        $db->exec($q);
    }

    // Default Settings
    $defaultSettings = [
        'company_name'     => 'مؤسسة الواحة للحلول التجارية',
        'company_cr'       => '1010789456',
        'company_vat'      => '310123456700003',
        'company_phone'    => '+966 54 321 0987',
        'company_email'    => 'billing@alwaha-solutions.com',
        'company_address'  => 'طريق الملك فهد، حي الصحافة، الرياض، المملكة العربية السعودية',
        'company_logo'     => '',
        'currency'         => 'ر.س',
        'default_tax_rate' => '15',
        'invoice_prefix'   => 'INV-',
        'invoice_footer'   => 'شكراً لتعاملكم معنا. تسري هذه الفاتورة كإشعار رسمي للدفع وفق الشروط المتفق عليها.'
    ];

    $checkStmt = $db->prepare("SELECT COUNT(*) as count FROM settings WHERE key = ?");
    $insertStmt = $db->prepare("INSERT INTO settings (key, value) VALUES (?, ?)");

    foreach ($defaultSettings as $k => $v) {
        $checkStmt->execute([$k]);
        if ($checkStmt->fetchColumn() == 0) {
            $insertStmt->execute([$k, $v]);
        }
    }

    // Seed sample data if empty
    $clientCount = $db->query("SELECT COUNT(*) FROM clients")->fetchColumn();
    if ($clientCount == 0) {
        $db->exec("INSERT INTO clients (name, phone, email, address, tax_number) VALUES
            ('شركة الأفق الرقمية المحدودة', '+966 50 111 2233', 'info@alofoq-tech.sa', 'شارع التحلية، الرياض', '300987654300003'),
            ('مؤسسة البناء الحديث للمقاولات', '+966 55 444 7788', 'contact@modernbuild.sa', 'حي الزهراء، جدة', '311223344500003'),
            ('مركز الرواد للاستشارات والتدريب', '+966 53 888 9900', 'finance@alrowad.edu.sa', 'حي العليا، الخبر', '302334455600003')
        ");

        $db->exec("INSERT INTO products (name, price, tax_rate, description) VALUES
            ('تصميم وبرمجة موقع إلكتروني', 3500.00, 15, 'تصميم واجهة مستخدم متجاوبة مع لوحة تحكم'),
            ('استضافة سحابية سنوية (VPS)', 1200.00, 15, 'خادم افتراضي 4 أنوية، 8 جيجابايت رام'),
            ('خدمات دعم فني وصيانة شهرية', 800.00, 15, 'صيانة دورية وتحديثات أمان وحفظ نسخ احتياطية'),
            ('حملة تسويق وإعلانات رقمية', 2200.00, 15, 'إدارة حملات جوجل ومنصات التواصل الاجتماعي')
        ");

        // Insert sample invoices
        $db->exec("INSERT INTO invoices (invoice_number, client_id, issue_date, due_date, status, payment_method, subtotal, discount, tax_amount, grand_total, notes) VALUES
            ('INV-2026-0001', 1, date('now', '-5 days'), date('now', '+10 days'), 'paid', 'bank_transfer', 4700.00, 200.00, 675.00, 5175.00, 'تم استلام المبلغ عبر التحويل البنكي لحساب مصرف الراجحي.'),
            ('INV-2026-0002', 2, date('now', '-2 days'), date('now', '+12 days'), 'pending', 'card', 2200.00, 0.00, 330.00, 2530.00, 'بانتظار تأكيد السداد بواسطة بطاقة مدى.'),
            ('INV-2026-0003', 3, date('now'), date('now', '+15 days'), 'unpaid', 'cash', 800.00, 50.00, 112.50, 862.50, 'الرجاء سداد المبلغ نقداً عند الاستلام.')
        ");

        // Insert sample invoice items
        $db->exec("INSERT INTO invoice_items (invoice_id, item_name, description, quantity, unit_price, discount, tax_rate, total) VALUES
            (1, 'تصميم وبرمجة موقع إلكتروني', 'واجهة عربية متطورة وسريعة متجاوبة مع الهواتف', 1, 3500.00, 200.00, 15, 3795.00),
            (1, 'استضافة سحابية سنوية (VPS)', 'باقة الأعمال 8 جيجابايت', 1, 1200.00, 0, 15, 1380.00),
            (2, 'حملة تسويق وإعلانات رقمية', 'خطة تسويق شاملة لمدة شهر', 1, 2200.00, 0, 15, 2530.00),
            (3, 'خدمات دعم فني وصيانة شهرية', 'صيانة شهر نوفمبر 2026', 1, 800.00, 50.00, 15, 862.50)
        ");
    }
}

// Auto initialize
initDatabaseSchema();

function jsonSuccess($data = [], $message = 'تمت العملية بنجاح') {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'success' => true,
        'message' => $message,
        'data'    => $data
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

function jsonError($message = 'حدث خطأ أثناء معالجة الطلب', $code = 400) {
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'success' => false,
        'message' => $message
    ], JSON_UNESCAPED_UNICODE);
    exit;
}
