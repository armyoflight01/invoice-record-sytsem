<?php
/**
 * Arabic Invoice Record System API
 * واجهة برمجة تطبيقات نظام إدارة الفواتير
 */

require_once __DIR__ . '/config.php';

header('Content-Type: application/json; charset=utf-8');

$action = $_GET['action'] ?? $_POST['action'] ?? '';
$db = getDatabaseConnection();

// Read JSON input if sent as application/json
$rawInput = file_get_contents('php://input');
$jsonInput = json_decode($rawInput, true);
if (is_array($jsonInput)) {
    $_POST = array_merge($_POST, $jsonInput);
    if (empty($action) && isset($jsonInput['action'])) {
        $action = $jsonInput['action'];
    }
}

try {
    switch ($action) {

        // ==========================================
        // 1. Dashboard Statistics
        // ==========================================
        case 'get_dashboard_stats':
            $totalSales = $db->query("SELECT COALESCE(SUM(grand_total), 0) FROM invoices WHERE status != 'cancelled'")->fetchColumn();
            $paidTotal = $db->query("SELECT COALESCE(SUM(grand_total), 0) FROM invoices WHERE status = 'paid'")->fetchColumn();
            $unpaidTotal = $db->query("SELECT COALESCE(SUM(grand_total), 0) FROM invoices WHERE status IN ('unpaid', 'pending')")->fetchColumn();
            $invoiceCount = $db->query("SELECT COUNT(*) FROM invoices")->fetchColumn();
            $clientCount = $db->query("SELECT COUNT(*) FROM clients")->fetchColumn();
            $productCount = $db->query("SELECT COUNT(*) FROM products")->fetchColumn();

            // Recent invoices
            $recentStmt = $db->query("
                SELECT i.*, c.name as client_name, c.phone as client_phone 
                FROM invoices i
                LEFT JOIN clients c ON i.client_id = c.id
                ORDER BY i.created_at DESC 
                LIMIT 5
            ");
            $recentInvoices = $recentStmt->fetchAll();

            // Status counts
            $statusCounts = [
                'paid' => (int)$db->query("SELECT COUNT(*) FROM invoices WHERE status = 'paid'")->fetchColumn(),
                'pending' => (int)$db->query("SELECT COUNT(*) FROM invoices WHERE status = 'pending'")->fetchColumn(),
                'unpaid' => (int)$db->query("SELECT COUNT(*) FROM invoices WHERE status = 'unpaid'")->fetchColumn(),
                'cancelled' => (int)$db->query("SELECT COUNT(*) FROM invoices WHERE status = 'cancelled'")->fetchColumn(),
            ];

            jsonSuccess([
                'total_sales'    => (float)$totalSales,
                'paid_total'     => (float)$paidTotal,
                'unpaid_total'   => (float)$unpaidTotal,
                'invoice_count'  => (int)$invoiceCount,
                'client_count'   => (int)$clientCount,
                'product_count'  => (int)$productCount,
                'status_counts'  => $statusCounts,
                'recent_invoices'=> $recentInvoices
            ]);
            break;

        // ==========================================
        // 2. Invoices List with Search & Filter
        // ==========================================
        case 'get_invoices':
            $status = trim($_GET['status'] ?? '');
            $search = trim($_GET['search'] ?? '');
            $fromDate = trim($_GET['from_date'] ?? '');
            $toDate = trim($_GET['to_date'] ?? '');

            $sql = "
                SELECT i.*, 
                       c.name as client_name, 
                       c.phone as client_phone,
                       c.tax_number as client_tax,
                       (SELECT COUNT(*) FROM invoice_items ii WHERE ii.invoice_id = i.id) as items_count
                FROM invoices i
                LEFT JOIN clients c ON i.client_id = c.id
                WHERE 1=1
            ";
            $params = [];

            if (!empty($status) && $status !== 'all') {
                $sql .= " AND i.status = ?";
                $params[] = $status;
            }

            if (!empty($search)) {
                $sql .= " AND (i.invoice_number LIKE ? OR c.name LIKE ? OR c.phone LIKE ?)";
                $wildcard = "%$search%";
                $params[] = $wildcard;
                $params[] = $wildcard;
                $params[] = $wildcard;
            }

            if (!empty($fromDate)) {
                $sql .= " AND i.issue_date >= ?";
                $params[] = $fromDate;
            }

            if (!empty($toDate)) {
                $sql .= " AND i.issue_date <= ?";
                $params[] = $toDate;
            }

            $sql .= " ORDER BY i.id DESC";

            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            $invoices = $stmt->fetchAll();

            jsonSuccess($invoices);
            break;

        // ==========================================
        // 3. Single Invoice Details
        // ==========================================
        case 'get_invoice':
            $id = (int)($_GET['id'] ?? 0);
            if (!$id) jsonError('معرف الفاتورة غير صحيح');

            $stmt = $db->prepare("
                SELECT i.*, 
                       c.name as client_name, 
                       c.phone as client_phone, 
                       c.email as client_email, 
                       c.address as client_address, 
                       c.tax_number as client_tax
                FROM invoices i
                LEFT JOIN clients c ON i.client_id = c.id
                WHERE i.id = ?
            ");
            $stmt->execute([$id]);
            $invoice = $stmt->fetch();

            if (!$invoice) jsonError('الفاتورة غير موجودة', 404);

            // Fetch items
            $itemStmt = $db->prepare("SELECT * FROM invoice_items WHERE invoice_id = ? ORDER BY id ASC");
            $itemStmt->execute([$id]);
            $invoice['items'] = $itemStmt->fetchAll();

            // Settings
            $settingsList = $db->query("SELECT key, value FROM settings")->fetchAll(PDO::FETCH_KEY_PAIR);
            $invoice['settings'] = $settingsList;

            jsonSuccess($invoice);
            break;

        // ==========================================
        // 4. Save Invoice (Create or Update)
        // ==========================================
        case 'save_invoice':
            $id = (int)($_POST['id'] ?? 0);
            $invoiceNumber = trim($_POST['invoice_number'] ?? '');
            $clientId = !empty($_POST['client_id']) ? (int)$_POST['client_id'] : null;
            $newClientName = trim($_POST['new_client_name'] ?? '');
            $newClientPhone = trim($_POST['new_client_phone'] ?? '');
            $issueDate = trim($_POST['issue_date'] ?? date('Y-m-d'));
            $dueDate = trim($_POST['due_date'] ?? date('Y-m-d', strtotime('+15 days')));
            $status = trim($_POST['status'] ?? 'unpaid');
            $paymentMethod = trim($_POST['payment_method'] ?? 'bank_transfer');
            $notes = trim($_POST['notes'] ?? '');
            $items = $_POST['items'] ?? [];

            if (empty($items) || !is_array($items)) {
                jsonError('يجب إضافة بند واحد على الأقل في الفاتورة');
            }

            // Quick create client if requested
            if (!$clientId && !empty($newClientName)) {
                $cStmt = $db->prepare("INSERT INTO clients (name, phone) VALUES (?, ?)");
                $cStmt->execute([$newClientName, $newClientPhone]);
                $clientId = (int)$db->lastInsertId();
            }

            // Auto-generate invoice number if empty
            if (empty($invoiceNumber)) {
                $prefix = $db->query("SELECT value FROM settings WHERE key = 'invoice_prefix'")->fetchColumn() ?: 'INV-';
                $nextSeq = (int)$db->query("SELECT MAX(id) FROM invoices")->fetchColumn() + 1;
                $invoiceNumber = sprintf('%s%s-%04d', $prefix, date('Y'), $nextSeq);
            }

            // Calculate totals accurately on server
            $calculatedSubtotal = 0;
            $calculatedDiscount = 0;
            $calculatedTax = 0;
            $normalizedItems = [];

            foreach ($items as $item) {
                $itemName = trim($item['item_name'] ?? '');
                if (empty($itemName)) continue;

                $desc = trim($item['description'] ?? '');
                $qty = max(0.001, (float)($item['quantity'] ?? 1));
                $price = max(0, (float)($item['unit_price'] ?? 0));
                $discount = max(0, (float)($item['discount'] ?? 0));
                $taxRate = max(0, (float)($item['tax_rate'] ?? 15));

                $lineSubtotal = $qty * $price;
                $lineDiscount = min($lineSubtotal, $discount);
                $taxableAmount = max(0, $lineSubtotal - $lineDiscount);
                $lineTax = ($taxableAmount * $taxRate) / 100;
                $lineTotal = $taxableAmount + $lineTax;

                $calculatedSubtotal += $lineSubtotal;
                $calculatedDiscount += $lineDiscount;
                $calculatedTax += $lineTax;

                $normalizedItems[] = [
                    'item_name'   => $itemName,
                    'description' => $desc,
                    'quantity'    => $qty,
                    'unit_price'  => $price,
                    'discount'    => $lineDiscount,
                    'tax_rate'    => $taxRate,
                    'total'       => round($lineTotal, 2)
                ];
            }

            if (empty($normalizedItems)) {
                jsonError('يرجى التأكد من ملء بيانات البنود بشكل صحيح');
            }

            $calculatedGrandTotal = round($calculatedSubtotal - $calculatedDiscount + $calculatedTax, 2);

            $db->beginTransaction();

            if ($id > 0) {
                // Update existing invoice
                $stmt = $db->prepare("
                    UPDATE invoices SET 
                        invoice_number = ?, client_id = ?, issue_date = ?, due_date = ?, 
                        status = ?, payment_method = ?, subtotal = ?, discount = ?, 
                        tax_amount = ?, grand_total = ?, notes = ?
                    WHERE id = ?
                ");
                $stmt->execute([
                    $invoiceNumber, $clientId, $issueDate, $dueDate,
                    $status, $paymentMethod, $calculatedSubtotal, $calculatedDiscount,
                    $calculatedTax, $calculatedGrandTotal, $notes, $id
                ]);

                // Delete existing items to replace with updated list
                $db->prepare("DELETE FROM invoice_items WHERE invoice_id = ?")->execute([$id]);
                $invoiceId = $id;
            } else {
                // Check uniqueness of invoice number
                $checkUniq = $db->prepare("SELECT id FROM invoices WHERE invoice_number = ?");
                $checkUniq->execute([$invoiceNumber]);
                if ($checkUniq->fetch()) {
                    $invoiceNumber .= '-' . rand(10, 99);
                }

                // Insert new invoice
                $stmt = $db->prepare("
                    INSERT INTO invoices (
                        invoice_number, client_id, issue_date, due_date, status, 
                        payment_method, subtotal, discount, tax_amount, grand_total, notes
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([
                    $invoiceNumber, $clientId, $issueDate, $dueDate, $status,
                    $paymentMethod, $calculatedSubtotal, $calculatedDiscount,
                    $calculatedTax, $calculatedGrandTotal, $notes
                ]);
                $invoiceId = (int)$db->lastInsertId();
            }

            // Insert line items
            $itemInsert = $db->prepare("
                INSERT INTO invoice_items (invoice_id, item_name, description, quantity, unit_price, discount, tax_rate, total)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");

            foreach ($normalizedItems as $it) {
                $itemInsert->execute([
                    $invoiceId,
                    $it['item_name'],
                    $it['description'],
                    $it['quantity'],
                    $it['unit_price'],
                    $it['discount'],
                    $it['tax_rate'],
                    $it['total']
                ]);
            }

            $db->commit();

            jsonSuccess(['invoice_id' => $invoiceId, 'invoice_number' => $invoiceNumber], 'تم حفظ الفاتورة بنجاح');
            break;

        // ==========================================
        // 5. Delete Invoice
        // ==========================================
        case 'delete_invoice':
            $id = (int)($_POST['id'] ?? 0);
            if (!$id) jsonError('معرف الفاتورة مطلوب');

            $db->beginTransaction();
            $db->prepare("DELETE FROM invoice_items WHERE invoice_id = ?")->execute([$id]);
            $db->prepare("DELETE FROM invoices WHERE id = ?")->execute([$id]);
            $db->commit();

            jsonSuccess(null, 'تم حذف الفاتورة بنجاح');
            break;

        // ==========================================
        // 6. Update Invoice Status
        // ==========================================
        case 'update_invoice_status':
            $id = (int)($_POST['id'] ?? 0);
            $status = trim($_POST['status'] ?? '');
            $valid = ['paid', 'unpaid', 'pending', 'cancelled'];
            if (!$id || !in_array($status, $valid)) {
                jsonError('البيانات المدخلة غير صحيحة');
            }

            $stmt = $db->prepare("UPDATE invoices SET status = ? WHERE id = ?");
            $stmt->execute([$status, $id]);
            jsonSuccess(null, 'تم تحديث حالة الفاتورة بنجاح');
            break;

        // ==========================================
        // 7. Clients Management
        // ==========================================
        case 'get_clients':
            $search = trim($_GET['search'] ?? '');
            $sql = "
                SELECT c.*, 
                       COUNT(i.id) as invoice_count,
                       COALESCE(SUM(CASE WHEN i.status != 'cancelled' THEN i.grand_total ELSE 0 END), 0) as total_purchases
                FROM clients c
                LEFT JOIN invoices i ON c.id = i.client_id
            ";
            $params = [];
            if (!empty($search)) {
                $sql .= " WHERE c.name LIKE ? OR c.phone LIKE ? OR c.tax_number LIKE ?";
                $wildcard = "%$search%";
                $params = [$wildcard, $wildcard, $wildcard];
            }
            $sql .= " GROUP BY c.id ORDER BY c.id DESC";

            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            jsonSuccess($stmt->fetchAll());
            break;

        case 'save_client':
            $id = (int)($_POST['id'] ?? 0);
            $name = trim($_POST['name'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $address = trim($_POST['address'] ?? '');
            $taxNumber = trim($_POST['tax_number'] ?? '');

            if (empty($name)) jsonError('اسم العميل مطلوب');

            if ($id > 0) {
                $stmt = $db->prepare("UPDATE clients SET name = ?, phone = ?, email = ?, address = ?, tax_number = ? WHERE id = ?");
                $stmt->execute([$name, $phone, $email, $address, $taxNumber, $id]);
            } else {
                $stmt = $db->prepare("INSERT INTO clients (name, phone, email, address, tax_number) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$name, $phone, $email, $address, $taxNumber]);
                $id = (int)$db->lastInsertId();
            }

            jsonSuccess(['id' => $id], 'تم حفظ بيانات العميل بنجاح');
            break;

        case 'delete_client':
            $id = (int)($_POST['id'] ?? 0);
            if (!$id) jsonError('معرف العميل غير صحيح');

            // Set client_id to null on associated invoices
            $db->prepare("UPDATE invoices SET client_id = NULL WHERE client_id = ?")->execute([$id]);
            $db->prepare("DELETE FROM clients WHERE id = ?")->execute([$id]);

            jsonSuccess(null, 'تم حذف العميل بنجاح');
            break;

        // ==========================================
        // 8. Products & Services Catalog
        // ==========================================
        case 'get_products':
            $stmt = $db->query("SELECT * FROM products ORDER BY name ASC");
            jsonSuccess($stmt->fetchAll());
            break;

        case 'save_product':
            $id = (int)($_POST['id'] ?? 0);
            $name = trim($_POST['name'] ?? '');
            $price = (float)($_POST['price'] ?? 0);
            $taxRate = (float)($_POST['tax_rate'] ?? 15);
            $desc = trim($_POST['description'] ?? '');

            if (empty($name)) jsonError('اسم المنتج أو الخدمة مطلوب');

            if ($id > 0) {
                $stmt = $db->prepare("UPDATE products SET name = ?, price = ?, tax_rate = ?, description = ? WHERE id = ?");
                $stmt->execute([$name, $price, $taxRate, $desc, $id]);
            } else {
                $stmt = $db->prepare("INSERT INTO products (name, price, tax_rate, description) VALUES (?, ?, ?, ?)");
                $stmt->execute([$name, $price, $taxRate, $desc]);
                $id = (int)$db->lastInsertId();
            }

            jsonSuccess(['id' => $id], 'تم حفظ الصنف بنجاح');
            break;

        case 'delete_product':
            $id = (int)($_POST['id'] ?? 0);
            if (!$id) jsonError('معرف الصنف غير صحيح');
            $db->prepare("DELETE FROM products WHERE id = ?")->execute([$id]);
            jsonSuccess(null, 'تم حذف الصنف بنجاح');
            break;

        // ==========================================
        // 9. Settings Management
        // ==========================================
        case 'get_settings':
            $settings = $db->query("SELECT key, value FROM settings")->fetchAll(PDO::FETCH_KEY_PAIR);
            jsonSuccess($settings);
            break;

        case 'save_settings':
            $allowedKeys = [
                'company_name', 'company_cr', 'company_vat', 'company_phone',
                'company_email', 'company_address', 'company_logo', 'currency',
                'default_tax_rate', 'invoice_prefix', 'invoice_footer'
            ];

            $stmt = $db->prepare("INSERT INTO settings (key, value) VALUES (?, ?) ON CONFLICT(key) DO UPDATE SET value = excluded.value");
            foreach ($allowedKeys as $k) {
                if (isset($_POST[$k])) {
                    $stmt->execute([$k, trim($_POST[$k])]);
                }
            }

            jsonSuccess(null, 'تم حفظ الإعدادات بنجاح');
            break;

        // ==========================================
        // 10. Generate Next Invoice Number
        // ==========================================
        case 'get_next_number':
            $prefix = $db->query("SELECT value FROM settings WHERE key = 'invoice_prefix'")->fetchColumn() ?: 'INV-';
            $maxId = (int)$db->query("SELECT MAX(id) FROM invoices")->fetchColumn() + 1;
            $nextNumber = sprintf('%s%s-%04d', $prefix, date('Y'), $maxId);
            jsonSuccess(['next_number' => $nextNumber]);
            break;

        // ==========================================
        // 11. Backup / Export Database Data
        // ==========================================
        case 'export_data':
            $data = [
                'exported_at' => date('Y-m-d H:i:s'),
                'settings'    => $db->query("SELECT * FROM settings")->fetchAll(),
                'clients'     => $db->query("SELECT * FROM clients")->fetchAll(),
                'products'    => $db->query("SELECT * FROM products")->fetchAll(),
                'invoices'    => $db->query("SELECT * FROM invoices")->fetchAll(),
                'invoice_items' => $db->query("SELECT * FROM invoice_items")->fetchAll(),
            ];

            header('Content-Type: application/json; charset=utf-8');
            header('Content-Disposition: attachment; filename="invoices_backup_' . date('Y_m_d_His') . '.json"');
            echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            exit;

        // ==========================================
        // 12. Restore / Import Database Data
        // ==========================================
        case 'import_data':
            if (empty($_FILES['backup_file']['tmp_name'])) {
                jsonError('يرجى اختيار ملف النسخة الاحتياطية (JSON)');
            }

            $content = file_get_contents($_FILES['backup_file']['tmp_name']);
            $backup = json_decode($content, true);

            if (!is_array($backup) || !isset($backup['invoices'])) {
                jsonError('ملف النسخة الاحتياطية غير صالح أو تالف');
            }

            $db->beginTransaction();

            // Clear existing tables
            $db->exec("DELETE FROM invoice_items");
            $db->exec("DELETE FROM invoices");
            $db->exec("DELETE FROM products");
            $db->exec("DELETE FROM clients");
            $db->exec("DELETE FROM settings");

            // Restore settings
            if (!empty($backup['settings'])) {
                $st = $db->prepare("INSERT INTO settings (key, value) VALUES (?, ?)");
                foreach ($backup['settings'] as $s) {
                    $st->execute([$s['key'], $s['value']]);
                }
            }

            // Restore clients
            if (!empty($backup['clients'])) {
                $st = $db->prepare("INSERT INTO clients (id, name, phone, email, address, tax_number, created_at) VALUES (?, ?, ?, ?, ?, ?, ?)");
                foreach ($backup['clients'] as $c) {
                    $st->execute([$c['id'], $c['name'], $c['phone'], $c['email'], $c['address'], $c['tax_number'], $c['created_at']]);
                }
            }

            // Restore products
            if (!empty($backup['products'])) {
                $st = $db->prepare("INSERT INTO products (id, name, price, tax_rate, description, created_at) VALUES (?, ?, ?, ?, ?, ?)");
                foreach ($backup['products'] as $p) {
                    $st->execute([$p['id'], $p['name'], $p['price'], $p['tax_rate'], $p['description'], $p['created_at']]);
                }
            }

            // Restore invoices
            if (!empty($backup['invoices'])) {
                $st = $db->prepare("INSERT INTO invoices (id, invoice_number, client_id, issue_date, due_date, status, payment_method, subtotal, discount, tax_amount, grand_total, notes, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                foreach ($backup['invoices'] as $inv) {
                    $st->execute([
                        $inv['id'], $inv['invoice_number'], $inv['client_id'], $inv['issue_date'], $inv['due_date'],
                        $inv['status'], $inv['payment_method'], $inv['subtotal'], $inv['discount'],
                        $inv['tax_amount'], $inv['grand_total'], $inv['notes'], $inv['created_at']
                    ]);
                }
            }

            // Restore items
            if (!empty($backup['invoice_items'])) {
                $st = $db->prepare("INSERT INTO invoice_items (id, invoice_id, item_name, description, quantity, unit_price, discount, tax_rate, total) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                foreach ($backup['invoice_items'] as $it) {
                    $st->execute([
                        $it['id'], $it['invoice_id'], $it['item_name'], $it['description'],
                        $it['quantity'], $it['unit_price'], $it['discount'], $it['tax_rate'], $it['total']
                    ]);
                }
            }

            $db->commit();
            jsonSuccess(null, 'تمت استعادة البيانات بنجاح');
            break;

        default:
            jsonError('الإجراء المطلوب غير معروف');
            break;
    }
} catch (Exception $e) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    jsonError('خطأ بالخادم: ' . $e->getMessage(), 500);
}
