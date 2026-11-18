<?php
/**
 * YARAMAY Computer Maintenance Services - Invoice Record System
 * Configuration & SQLite Auto-Setup
 */

// Error handling
error_reporting(E_ALL);
ini_set('display_errors', '0');

define('DATA_DIR', __DIR__ . DIRECTORY_SEPARATOR . 'data');
define('SESSION_DIR', DATA_DIR . DIRECTORY_SEPARATOR . 'sessions');
define('DB_FILE', DATA_DIR . DIRECTORY_SEPARATOR . 'invoices.db');

if (!file_exists(DATA_DIR)) {
    @mkdir(DATA_DIR, 0777, true);
}
if (!file_exists(SESSION_DIR)) {
    @mkdir(SESSION_DIR, 0777, true);
}

if (is_dir(SESSION_DIR)) {
    @session_save_path(SESSION_DIR);
}

if (session_status() === PHP_SESSION_NONE) {
    @session_start();
}

function getDatabaseConnection(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = 'sqlite:' . DB_FILE;
        $pdo = new PDO($dsn, null, null, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
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
            contact_person TEXT,
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
            tax_rate REAL DEFAULT 0,
            description TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );",

        "CREATE TABLE IF NOT EXISTS invoices (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            invoice_number TEXT UNIQUE NOT NULL,
            client_id INTEGER,
            issue_date DATE NOT NULL,
            due_date DATE,
            status TEXT DEFAULT 'unpaid', -- paid, partial, unpaid, pending, cancelled
            payment_method TEXT DEFAULT 'gcash', -- gcash, cash, stc_pay, bank_transfer, card, cheque
            payment_term TEXT DEFAULT 'full', -- full, 3_months, 6_months, custom
            installment_months INTEGER DEFAULT 1,
            monthly_payment REAL DEFAULT 0,
            partner_assigned TEXT DEFAULT 'both', -- ibraheem, fawas, both
            ibraheem_share REAL DEFAULT 0,
            fawas_share REAL DEFAULT 0,
            subtotal REAL DEFAULT 0,
            discount REAL DEFAULT 0,
            tax_amount REAL DEFAULT 0,
            grand_total REAL DEFAULT 0,
            paid_amount REAL DEFAULT 0,
            remaining_balance REAL DEFAULT 0,
            price_note TEXT,
            amount_due_note TEXT,
            package_includes TEXT,
            notes TEXT,
            submitted_by TEXT DEFAULT 'admin', -- admin, client
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
            tax_rate REAL DEFAULT 0,
            total REAL DEFAULT 0,
            FOREIGN KEY (invoice_id) REFERENCES invoices (id) ON DELETE CASCADE
        );",

        "CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT UNIQUE NOT NULL,
            password TEXT NOT NULL,
            full_name TEXT NOT NULL,
            role TEXT DEFAULT 'admin', -- superadmin, admin
            email TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );",

        "CREATE TABLE IF NOT EXISTS login_attempts (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            ip_address TEXT NOT NULL,
            username TEXT,
            attempt_time DATETIME DEFAULT CURRENT_TIMESTAMP,
            status TEXT DEFAULT 'failed'
        );",

        "CREATE TABLE IF NOT EXISTS security_logs (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            event_type TEXT NOT NULL,
            ip_address TEXT NOT NULL,
            username TEXT,
            details TEXT,
            user_agent TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );",

        "CREATE TABLE IF NOT EXISTS rate_limits (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            ip_address TEXT NOT NULL,
            endpoint TEXT NOT NULL,
            request_time DATETIME DEFAULT CURRENT_TIMESTAMP
        );"
    ];

    foreach ($queries as $q) {
        $db->exec($q);
    }

    $db->exec("CREATE INDEX IF NOT EXISTS idx_login_attempts_ip ON login_attempts(ip_address, attempt_time);");
    $db->exec("CREATE INDEX IF NOT EXISTS idx_rate_limits_ip ON rate_limits(ip_address, endpoint, request_time);");
    $db->exec("CREATE INDEX IF NOT EXISTS idx_security_logs_created ON security_logs(created_at);");

    // Ensure columns exist if migrated from older version
    ensureColumnExists($db, 'clients', 'contact_person', 'TEXT');
    ensureColumnExists($db, 'invoices', 'paid_amount', 'REAL DEFAULT 0');
    ensureColumnExists($db, 'invoices', 'remaining_balance', 'REAL DEFAULT 0');
    ensureColumnExists($db, 'invoices', 'payment_term', "TEXT DEFAULT 'full'");
    ensureColumnExists($db, 'invoices', 'installment_months', "INTEGER DEFAULT 1");
    ensureColumnExists($db, 'invoices', 'monthly_payment', "REAL DEFAULT 0");
    ensureColumnExists($db, 'invoices', 'partner_assigned', "TEXT DEFAULT 'both'");
    ensureColumnExists($db, 'invoices', 'ibraheem_share', "REAL DEFAULT 0");
    ensureColumnExists($db, 'invoices', 'fawas_share', "REAL DEFAULT 0");
    ensureColumnExists($db, 'invoices', 'price_note', 'TEXT');
    ensureColumnExists($db, 'invoices', 'amount_due_note', 'TEXT');
    ensureColumnExists($db, 'invoices', 'package_includes', 'TEXT');
    ensureColumnExists($db, 'invoices', 'submitted_by', "TEXT DEFAULT 'admin'");

    // Default Settings for YARAMAY Computer Maintenance Services
    $defaultSettings = [
        'company_name'       => 'YARAMAY Computer Maintenance Services',
        'company_cr'         => 'Cambridge C5 -1G',
        'company_vat'        => 'VAT-PH-09617190588',
        'company_phone'      => '+639075251376 (SMS) / +966508624264',
        'company_email'      => 'billing@yaramay.online',
        'company_address'    => '#19 Street Brgy. San Andres Cainta Rizal Cambridge C5 -1G',
        'company_manager'    => 'Ferdenand (Ibraheem) Sabado',
        'company_title'      => 'General Manager',
        'company_cert'       => 'MCP, MCSE, MCSA and CISCO Certified',
        'company_website'    => 'www.yaramay.online',
        'company_gcash'      => '09617190588',
        'gcash_account_name' => 'Ferdenand Sabado',
        'partner_1_name'     => 'Mr. Ibraheem',
        'partner_2_name'     => 'Mr. Fawas',
        'company_stc'        => 'STC Pay / GCash',
        'currency'           => 'PHP',
        'currency_symbol'    => '₱',
        'default_tax_rate'   => '0',
        'invoice_prefix'     => 'INV-',
        'invoice_footer'     => "We are open for your suggestions on improving our services with you.\nPayment can be transferred through STC Pay or GCash (Account Name: Ferdenand Sabado, GCash #: 09617190588).\nThanking you for doing Business with us!"
    ];

    $checkStmt = $db->prepare("SELECT COUNT(*) as count FROM settings WHERE key = ?");
    $insertStmt = $db->prepare("INSERT INTO settings (key, value) VALUES (?, ?)");

    foreach ($defaultSettings as $k => $v) {
        $checkStmt->execute([$k]);
        if ($checkStmt->fetchColumn() == 0) {
            $insertStmt->execute([$k, $v]);
        }
    }

    // Check if partner shares need calculation on existing invoices
    $db->exec("
        UPDATE invoices SET 
            partner_assigned = 'both',
            ibraheem_share = ROUND(paid_amount / 2, 2),
            fawas_share = ROUND(paid_amount / 2, 2)
        WHERE (partner_assigned = 'both' OR partner_assigned IS NULL OR partner_assigned = '') 
          AND id != 3 
          AND (ibraheem_share = 0 OR ibraheem_share IS NULL);
    ");
    $db->exec("
        UPDATE invoices SET 
            partner_assigned = 'ibraheem',
            ibraheem_share = paid_amount,
            fawas_share = 0
        WHERE id = 3;
    ");

    // Check installment fields on existing invoices
    $db->exec("
        UPDATE invoices SET 
            payment_term = 'full',
            installment_months = 1,
            monthly_payment = grand_total
        WHERE payment_term IS NULL OR payment_term = ''
    ");
    $db->exec("
        UPDATE invoices SET 
            payment_term = 'custom',
            installment_months = 12,
            monthly_payment = 7500.00
        WHERE id = 1
    ");

    // Seed default superadmin ibraheem01 if no users exist
    $userCount = (int)$db->query("SELECT COUNT(*) FROM users")->fetchColumn();
    if ($userCount === 0) {
        $hashed = password_hash('123456789', PASSWORD_DEFAULT);
        $insertUser = $db->prepare("
            INSERT INTO users (username, password, full_name, role, email)
            VALUES (?, ?, ?, ?, ?)
        ");
        $insertUser->execute([
            'ibraheem01',
            $hashed,
            'Mr. Ibraheem (Superadmin)',
            'superadmin',
            'ibraheem@yaramay.online'
        ]);
    }
}

function ensureColumnExists(PDO $db, string $table, string $column, string $definition): void {
    try {
        $stmt = $db->query("PRAGMA table_info($table)");
        $cols = $stmt->fetchAll(PDO::FETCH_COLUMN, 1);
        if (!in_array($column, $cols)) {
            $db->exec("ALTER TABLE $table ADD COLUMN $column $definition;");
        }
    } catch (Exception $e) {
        // Table or column check error
    }
}

// Auto initialize schema
initDatabaseSchema();

// Load Web Application Firewall & Advanced Security Shield
require_once __DIR__ . '/security.php';

// Authentication Helpers
function isLoggedIn(): bool {
    return !empty($_SESSION['admin_user']) && !empty($_SESSION['admin_user']['id']);
}

function getCurrentUser(): ?array {
    return $_SESSION['admin_user'] ?? null;
}

function requireAuth(): void {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

function isSuperAdmin(): bool {
    return isset($_SESSION['admin_user']['role']) && $_SESSION['admin_user']['role'] === 'superadmin';
}

function jsonSuccess($data = [], $message = 'Success') {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'success' => true,
        'message' => $message,
        'data'    => $data
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

function jsonError($message = 'An error occurred while processing request', $code = 400) {
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'success' => false,
        'message' => $message
    ], JSON_UNESCAPED_UNICODE);
    exit;
}
