<?php
/**
 * Arabic & English Invoice Record System - Printable Invoice
 * طباعة الفاتورة الضريبية / التجارية باللغتين العربية والإنجليزية
 */

require_once __DIR__ . '/config.php';

$id = (int)($_GET['id'] ?? 0);
if (!$id) {
    die('خطأ: رقم الفاتورة غير محدد | Error: Invoice ID is required.');
}

$lang = strtolower($_GET['lang'] ?? 'ar');
if ($lang !== 'en') {
    $lang = 'ar';
}
$isEn = ($lang === 'en');
$dir = $isEn ? 'ltr' : 'rtl';

$db = getDatabaseConnection();

// Fetch invoice
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

if (!$invoice) {
    die('خطأ: الفاتورة غير موجودة | Error: Invoice not found.');
}

// Fetch items
$itemStmt = $db->prepare("SELECT * FROM invoice_items WHERE invoice_id = ? ORDER BY id ASC");
$itemStmt->execute([$id]);
$items = $itemStmt->fetchAll();

// Fetch settings
$settings = $db->query("SELECT key, value FROM settings")->fetchAll(PDO::FETCH_KEY_PAIR);

$companyName = $settings['company_name'] ?? ($isEn ? 'Al-Waha Business Solutions' : 'مؤسسة الواحة للحلول التجارية');
$companyCr = $settings['company_cr'] ?? '';
$companyVat = $settings['company_vat'] ?? '';
$companyPhone = $settings['company_phone'] ?? '';
$companyEmail = $settings['company_email'] ?? '';
$companyAddress = $settings['company_address'] ?? '';
$currency = $settings['currency'] ?? ($isEn ? 'SAR' : 'ر.س');
$footerNote = $settings['invoice_footer'] ?? ($isEn ? 'Thank you for your business. Payment is due within agreed terms.' : 'شكراً لتعاملكم معنا. تسري هذه الفاتورة كإشعار رسمي للدفع.');

// Status translation
$statusLabels = [
    'paid' => [
        'text' => $isEn ? 'Paid in Full' : 'مدفوعة بالكامل',
        'class' => 'status-paid'
    ],
    'unpaid' => [
        'text' => $isEn ? 'Unpaid (Due)' : 'غير مدفوعة (مستحقة)',
        'class' => 'status-unpaid'
    ],
    'pending' => [
        'text' => $isEn ? 'Pending' : 'قيد الانتظار',
        'class' => 'status-pending'
    ],
    'cancelled' => [
        'text' => $isEn ? 'Cancelled' : 'ملغاة',
        'class' => 'status-cancelled'
    ],
];
$statusInfo = $statusLabels[$invoice['status']] ?? ['text' => $invoice['status'], 'class' => ''];

// Payment method translation
$paymentMethods = [
    'cash' => $isEn ? 'Cash' : 'نقداً (كاش)',
    'bank_transfer' => $isEn ? 'Bank Transfer' : 'تحويل بنكي',
    'card' => $isEn ? 'Credit / Mada Card' : 'بطاقة مدى / ائتمان',
    'cheque' => $isEn ? 'Bank Cheque' : 'شيك مصرفي'
];
$paymentMethodText = $paymentMethods[$invoice['payment_method']] ?? $invoice['payment_method'];

// Simple TLV Encoder for Saudi E-Invoicing (ZATCA Phase 1 QR format)
function getZatcaQrBase64($seller, $vatNumber, $timestamp, $total, $tax) {
    $tlv = '';
    $tags = [
        1 => $seller,
        2 => $vatNumber,
        3 => $timestamp,
        4 => number_format((float)$total, 2, '.', ''),
        5 => number_format((float)$tax, 2, '.', '')
    ];
    foreach ($tags as $tag => $value) {
        $valStr = (string)$value;
        $len = strlen($valStr);
        $tlv .= chr($tag) . chr($len) . $valStr;
    }
    return base64_encode($tlv);
}

$qrData = getZatcaQrBase64(
    $companyName,
    $companyVat,
    $invoice['issue_date'] . 'T12:00:00Z',
    $invoice['grand_total'],
    $invoice['tax_amount']
);
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>" dir="<?php echo $dir; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $isEn ? "Invoice #{$invoice['invoice_number']} - {$companyName}" : "فاتورة رقم {$invoice['invoice_number']} - {$companyName}"; ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&family=Tajawal:wght@400;500;700;800&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-green: #047857;
            --primary-dark: #064e3b;
            --primary-light: #ecfdf5;
            --primary-border: #a7f3d0;
            --accent-green: #10b981;
            --text-main: #1f2937;
            --text-muted: #6b7280;
            --bg-gray: #f9fafb;
            --border-color: #e5e7eb;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: <?php echo $isEn ? "'Inter', 'Segoe UI', Arial, sans-serif" : "'Tajawal', 'Cairo', 'Segoe UI', Tahoma, Arial, sans-serif"; ?>;
            background-color: #f3f4f6;
            color: var(--text-main);
            direction: <?php echo $dir; ?>;
            text-align: <?php echo $isEn ? 'left' : 'right'; ?>;
            padding: 24px;
            font-size: 14px;
            line-height: 1.6;
        }

        .no-print-bar {
            max-width: 820px;
            margin: 0 auto 16px auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: white;
            padding: 12px 20px;
            border-radius: 10px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.06);
            border: 1px solid var(--border-color);
            flex-wrap: wrap;
            gap: 10px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 9px 18px;
            font-size: 13.5px;
            font-weight: 700;
            font-family: inherit;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s ease;
            border: none;
        }

        .btn-green {
            background-color: var(--primary-green);
            color: white;
        }
        .btn-green:hover {
            background-color: var(--primary-dark);
        }

        .btn-outline {
            background-color: white;
            color: var(--text-main);
            border: 1px solid var(--border-color);
        }
        .btn-outline:hover {
            background-color: var(--bg-gray);
        }

        .btn-lang {
            background: var(--primary-light);
            color: var(--primary-dark);
            border: 1px solid var(--primary-border);
        }

        /* Invoice Container */
        .invoice-box {
            max-width: 820px;
            margin: 0 auto;
            padding: 40px;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            border-top: 8px solid var(--primary-green);
            position: relative;
        }

        /* Header */
        .invoice-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid var(--primary-border);
            padding-bottom: 24px;
            margin-bottom: 24px;
        }

        .company-info h1 {
            font-size: 24px;
            color: var(--primary-dark);
            margin-bottom: 6px;
            font-weight: 800;
        }

        .company-info p {
            color: var(--text-muted);
            font-size: 13px;
            line-height: 1.5;
        }

        .company-info strong {
            color: var(--text-main);
        }

        .invoice-title-badge {
            text-align: <?php echo $isEn ? 'right' : 'left'; ?>;
        }

        .invoice-badge-title {
            display: inline-block;
            background: var(--primary-light);
            color: var(--primary-dark);
            font-size: 20px;
            font-weight: 800;
            padding: 6px 16px;
            border-radius: 8px;
            border: 1px solid var(--primary-border);
            margin-bottom: 8px;
        }

        .invoice-meta-item {
            font-size: 13px;
            color: var(--text-muted);
            margin-bottom: 3px;
        }
        .invoice-meta-item strong {
            color: var(--text-main);
        }

        /* Bill To & Details Grid */
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 28px;
        }

        .info-card {
            background: var(--bg-gray);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 16px;
        }

        .info-card h3 {
            font-size: 14px;
            color: var(--primary-green);
            font-weight: 700;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 6px;
            border-bottom: 1px dashed var(--border-color);
            padding-bottom: 6px;
        }

        .info-card p {
            font-size: 13px;
            margin-bottom: 4px;
            color: var(--text-main);
        }
        .info-card p span {
            color: var(--text-muted);
        }

        /* Status Pills */
        .status-pill {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
        }
        .status-paid {
            background: #d1fae5;
            color: #065f46;
        }
        .status-unpaid {
            background: #fee2e2;
            color: #991b1b;
        }
        .status-pending {
            background: #fef3c7;
            color: #92400e;
        }
        .status-cancelled {
            background: #f3f4f6;
            color: #4b5563;
        }

        /* Items Table */
        .table-responsive {
            width: 100%;
            overflow-x: auto;
            margin-bottom: 24px;
        }

        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        .invoice-table th {
            background: var(--primary-green);
            color: white;
            font-weight: 700;
            padding: 10px 12px;
            text-align: <?php echo $isEn ? 'left' : 'right'; ?>;
        }
        .invoice-table th:first-child {
            border-top-<?php echo $isEn ? 'left' : 'right'; ?>-radius: 6px;
        }
        .invoice-table th:last-child {
            border-top-<?php echo $isEn ? 'right' : 'left'; ?>-radius: 6px;
            text-align: <?php echo $isEn ? 'right' : 'left'; ?>;
        }

        .invoice-table td {
            padding: 12px;
            border-bottom: 1px solid var(--border-color);
        }
        .invoice-table td:last-child {
            text-align: <?php echo $isEn ? 'right' : 'left'; ?>;
        }

        .invoice-table tbody tr:nth-child(even) {
            background: #fafafa;
        }

        .item-name {
            font-weight: 700;
            color: var(--text-main);
            font-size: 13.5px;
        }
        .item-desc {
            font-size: 12px;
            color: var(--text-muted);
            margin-top: 3px;
        }

        /* Summary & QR Section */
        .summary-section {
            display: grid;
            grid-template-columns: 1.2fr 1fr;
            gap: 24px;
            align-items: flex-start;
            margin-bottom: 30px;
        }

        .qr-and-notes {
            display: flex;
            gap: 16px;
            background: var(--primary-light);
            border: 1px solid var(--primary-border);
            padding: 16px;
            border-radius: 8px;
        }

        .qr-placeholder {
            width: 110px;
            height: 110px;
            background: white;
            border: 1px solid var(--primary-border);
            padding: 6px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .qr-placeholder img {
            max-width: 100%;
            max-height: 100%;
        }

        .notes-text {
            font-size: 12.5px;
            color: var(--text-main);
        }
        .notes-text h4 {
            font-size: 13px;
            color: var(--primary-dark);
            margin-bottom: 4px;
        }

        .totals-card {
            background: var(--bg-gray);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            overflow: hidden;
        }

        .totals-row {
            display: flex;
            justify-content: space-between;
            padding: 9px 16px;
            font-size: 13px;
            border-bottom: 1px solid var(--border-color);
        }

        .totals-row.grand-total {
            background: var(--primary-green);
            color: white;
            font-size: 16px;
            font-weight: 800;
            border-bottom: none;
            padding: 12px 16px;
        }

        /* Footer & Signatures */
        .invoice-footer {
            border-top: 1px solid var(--border-color);
            padding-top: 20px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }

        .footer-note {
            font-size: 12px;
            color: var(--text-muted);
            max-width: 60%;
        }

        .signature-box {
            text-align: center;
            width: 180px;
        }
        .signature-line {
            height: 60px;
            border-bottom: 1px dashed var(--text-muted);
            margin-bottom: 6px;
        }
        .signature-title {
            font-size: 12px;
            font-weight: 700;
            color: var(--text-main);
        }

        /* Print Media Styles */
        @media print {
            body {
                background: white;
                padding: 0;
            }
            .no-print-bar {
                display: none !important;
            }
            .invoice-box {
                box-shadow: none;
                border: none;
                border-top: 6px solid var(--primary-green);
                padding: 20px 0;
                max-width: 100%;
            }
            @page {
                size: A4;
                margin: 15mm;
            }
        }

        @media (max-width: 768px) {
            body {
                padding: 12px;
            }
            .invoice-box {
                padding: 20px;
            }
            .invoice-header {
                flex-direction: column;
                gap: 16px;
            }
            .invoice-title-badge {
                text-align: <?php echo $isEn ? 'left' : 'right'; ?>;
            }
            .info-grid {
                grid-template-columns: 1fr;
            }
            .summary-section {
                grid-template-columns: 1fr;
            }
            .invoice-footer {
                flex-direction: column;
                gap: 20px;
                align-items: flex-start;
            }
        }
    </style>
</head>
<body>

    <!-- Top Action Bar (Hidden during Print) -->
    <div class="no-print-bar">
        <div style="display:flex; align-items:center; gap: 10px;">
            <a href="index.php" class="btn btn-outline">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?php echo $isEn ? 'M10 19l-7-7m0 0l7-7m-7 7h18' : 'M14 5l7 7m0 0l-7 7m7-7H3'; ?>"/></svg>
                <?php echo $isEn ? 'Back to Dashboard' : 'العودة للوحة التحكم'; ?>
            </a>
            <span style="font-weight: 700; color: var(--primary-green);">
                <?php echo $isEn ? "Tax Invoice #{$invoice['invoice_number']}" : "فاتورة ضريبية #{$invoice['invoice_number']}"; ?>
            </span>
        </div>
        <div style="display: flex; gap: 8px;">
            <!-- Language Switcher in Print -->
            <a href="print.php?id=<?php echo $id; ?>&lang=<?php echo $isEn ? 'ar' : 'en'; ?>" class="btn btn-lang">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"/></svg>
                <?php echo $isEn ? 'عربي (Arabic)' : 'English (إنجليزي)'; ?>
            </a>

            <button onclick="window.print()" class="btn btn-green">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <?php echo $isEn ? 'Print / Export PDF' : 'طباعة / تصدير PDF'; ?>
            </button>
        </div>
    </div>

    <!-- Printable Invoice Page -->
    <div class="invoice-box" id="printableInvoice">
        
        <!-- Header -->
        <div class="invoice-header">
            <div class="company-info">
                <h1><?php echo htmlspecialchars($companyName); ?></h1>
                <?php if (!empty($companyCr)): ?>
                    <p><strong><?php echo $isEn ? 'C.R. Number:' : 'السجل التجاري:'; ?></strong> <?php echo htmlspecialchars($companyCr); ?></p>
                <?php endif; ?>
                <?php if (!empty($companyVat)): ?>
                    <p><strong><?php echo $isEn ? 'VAT Number:' : 'الرقم الضريبي:'; ?></strong> <?php echo htmlspecialchars($companyVat); ?></p>
                <?php endif; ?>
                <?php if (!empty($companyPhone)): ?>
                    <p><strong><?php echo $isEn ? 'Phone:' : 'الهاتف:'; ?></strong> <?php echo htmlspecialchars($companyPhone); ?></p>
                <?php endif; ?>
                <?php if (!empty($companyAddress)): ?>
                    <p><strong><?php echo $isEn ? 'Address:' : 'العنوان:'; ?></strong> <?php echo htmlspecialchars($companyAddress); ?></p>
                <?php endif; ?>
            </div>

            <div class="invoice-title-badge">
                <div class="invoice-badge-title"><?php echo $isEn ? 'TAX INVOICE' : 'فاتورة ضريبية'; ?></div>
                <div class="invoice-meta-item">
                    <strong><?php echo $isEn ? 'Invoice #:' : 'رقم الفاتورة:'; ?></strong> <span style="font-family: monospace; font-size:14px; font-weight:700; color:var(--primary-green);"><?php echo htmlspecialchars($invoice['invoice_number']); ?></span>
                </div>
                <div class="invoice-meta-item">
                    <strong><?php echo $isEn ? 'Issue Date:' : 'تاريخ الإصدار:'; ?></strong> <?php echo htmlspecialchars($invoice['issue_date']); ?>
                </div>
                <?php if (!empty($invoice['due_date'])): ?>
                <div class="invoice-meta-item">
                    <strong><?php echo $isEn ? 'Due Date:' : 'تاريخ الاستحقاق:'; ?></strong> <?php echo htmlspecialchars($invoice['due_date']); ?>
                </div>
                <?php endif; ?>
                <div class="invoice-meta-item" style="margin-top:6px;">
                    <strong><?php echo $isEn ? 'Payment Status:' : 'حالة الدفع:'; ?></strong> <span class="status-pill <?php echo $statusInfo['class']; ?>"><?php echo $statusInfo['text']; ?></span>
                </div>
            </div>
        </div>

        <!-- Bill To & Details -->
        <div class="info-grid">
            <div class="info-card">
                <h3>
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    <?php echo $isEn ? 'Bill To (Client)' : 'فاتورة إلى (العميل)'; ?>
                </h3>
                <p><strong><?php echo htmlspecialchars($invoice['client_name'] ?? ($isEn ? 'Cash Customer' : 'عميل نقدي')); ?></strong></p>
                <?php if (!empty($invoice['client_phone'])): ?>
                    <p><span><?php echo $isEn ? 'Phone:' : 'الهاتف:'; ?></span> <?php echo htmlspecialchars($invoice['client_phone']); ?></p>
                <?php endif; ?>
                <?php if (!empty($invoice['client_email'])): ?>
                    <p><span><?php echo $isEn ? 'Email:' : 'البريد الإلكتروني:'; ?></span> <?php echo htmlspecialchars($invoice['client_email']); ?></p>
                <?php endif; ?>
                <?php if (!empty($invoice['client_address'])): ?>
                    <p><span><?php echo $isEn ? 'Address:' : 'العنوان:'; ?></span> <?php echo htmlspecialchars($invoice['client_address']); ?></p>
                <?php endif; ?>
                <?php if (!empty($invoice['client_tax'])): ?>
                    <p><span><?php echo $isEn ? 'Client Tax / VAT:' : 'الرقم الضريبي للعميل:'; ?></span> <?php echo htmlspecialchars($invoice['client_tax']); ?></p>
                <?php endif; ?>
            </div>

            <div class="info-card">
                <h3>
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <?php echo $isEn ? 'Payment & Settlement Details' : 'تفاصيل السداد'; ?>
                </h3>
                <p><span><?php echo $isEn ? 'Payment Method:' : 'طريقة الدفع:'; ?></span> <strong><?php echo $paymentMethodText; ?></strong></p>
                <p><span><?php echo $isEn ? 'Currency:' : 'العملة:'; ?></span> <?php echo htmlspecialchars($currency); ?></p>
                <p><span><?php echo $isEn ? 'Registered At:' : 'تاريخ التسجيل:'; ?></span> <?php echo date('Y-m-d H:i', strtotime($invoice['created_at'])); ?></p>
                <?php if (!empty($invoice['notes'])): ?>
                <p style="margin-top: 6px;"><span><?php echo $isEn ? 'Notes:' : 'ملاحظات:'; ?></span> <?php echo nl2br(htmlspecialchars($invoice['notes'])); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Items Table -->
        <div class="table-responsive">
            <table class="invoice-table">
                <thead>
                    <tr>
                        <th style="width: 40px;">#</th>
                        <th><?php echo $isEn ? 'Item & Description' : 'البند والوصف'; ?></th>
                        <th style="width: 70px; text-align:center;"><?php echo $isEn ? 'Qty' : 'الكمية'; ?></th>
                        <th style="width: 100px;"><?php echo $isEn ? 'Unit Price' : 'سعر الوحدة'; ?></th>
                        <th style="width: 80px;"><?php echo $isEn ? 'Discount' : 'الخصم'; ?></th>
                        <th style="width: 70px; text-align:center;"><?php echo $isEn ? 'VAT %' : 'الضريبة'; ?></th>
                        <th style="width: 130px;"><?php echo $isEn ? 'Total (Incl. VAT)' : 'الإجمالي (شامل)'; ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($items)): ?>
                    <tr>
                        <td colspan="7" style="text-align: center; color: var(--text-muted); padding: 20px;">
                            <?php echo $isEn ? 'No items found in this invoice' : 'لا توجد بنود في هذه الفاتورة'; ?>
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($items as $index => $item): ?>
                        <tr>
                            <td><?php echo $index + 1; ?></td>
                            <td>
                                <div class="item-name"><?php echo htmlspecialchars($item['item_name']); ?></div>
                                <?php if (!empty($item['description'])): ?>
                                    <div class="item-desc"><?php echo htmlspecialchars($item['description']); ?></div>
                                <?php endif; ?>
                            </td>
                            <td style="text-align: center;"><?php echo $item['quantity']; ?></td>
                            <td><?php echo number_format($item['unit_price'], 2); ?> <?php echo $currency; ?></td>
                            <td><?php echo number_format($item['discount'], 2); ?> <?php echo $currency; ?></td>
                            <td style="text-align: center;"><?php echo $item['tax_rate']; ?>%</td>
                            <td style="font-weight: 700; color: var(--primary-green);">
                                <?php echo number_format($item['total'], 2); ?> <?php echo $currency; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Summary & QR -->
        <div class="summary-section">
            <div class="qr-and-notes">
                <div class="qr-placeholder" id="qrContainer">
                    <img id="qrImg" src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=<?php echo urlencode($qrData); ?>" alt="QR Code" onerror="this.style.display='none'; document.getElementById('qrFallback').style.display='block';">
                    <div id="qrFallback" style="display:none; text-align:center; font-size:11px; color:var(--text-muted);">
                        <?php echo $isEn ? 'ZATCA E-Invoice QR Code' : 'QR متوافق مع هيئة الزكاة والضريبة'; ?>
                    </div>
                </div>
                <div class="notes-text">
                    <h4><?php echo $isEn ? 'Electronic Verification QR' : 'التحقق الإلكتروني من الفاتورة'; ?></h4>
                    <p><?php echo $isEn ? 'This encrypted QR code contains verified invoice and VAT data in compliance with e-invoicing regulations.' : 'هذا الرمز المشفر يتضمن بيانات الفاتورة المعتمدة وضريبة القيمة المضافة وفق متطلبات الفوترة الإلكترونية.'; ?></p>
                    <p style="margin-top:6px; font-size: 11px; color: var(--text-muted);">
                        <?php echo $isEn ? 'Grand Total:' : 'المجموع الكلي:'; ?> <?php echo number_format($invoice['grand_total'], 2); ?> <?php echo $currency; ?>
                    </p>
                </div>
            </div>

            <div class="totals-card">
                <div class="totals-row">
                    <span><?php echo $isEn ? 'Subtotal (Excl. VAT):' : 'المجموع الفرعي (غير شامل الضريبة):'; ?></span>
                    <strong><?php echo number_format($invoice['subtotal'], 2); ?> <?php echo $currency; ?></strong>
                </div>
                <?php if ($invoice['discount'] > 0): ?>
                <div class="totals-row">
                    <span><?php echo $isEn ? 'Total Discount:' : 'إجمالي الخصم:'; ?></span>
                    <strong style="color: #dc2626;">- <?php echo number_format($invoice['discount'], 2); ?> <?php echo $currency; ?></strong>
                </div>
                <?php endif; ?>
                <div class="totals-row">
                    <span><?php echo $isEn ? 'VAT (15%):' : 'ضريبة القيمة المضافة (15%):'; ?></span>
                    <strong><?php echo number_format($invoice['tax_amount'], 2); ?> <?php echo $currency; ?></strong>
                </div>
                <div class="totals-row grand-total">
                    <span><?php echo $isEn ? 'Grand Total:' : 'المجموع الإجمالي النهائي:'; ?></span>
                    <span><?php echo number_format($invoice['grand_total'], 2); ?> <?php echo $currency; ?></span>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="invoice-footer">
            <div class="footer-note">
                <p><?php echo nl2br(htmlspecialchars($footerNote)); ?></p>
            </div>
            <div class="signature-box">
                <div class="signature-line"></div>
                <div class="signature-title"><?php echo $isEn ? 'Authorized Stamp & Signature' : 'الختم والتوقيع المعتمد'; ?></div>
            </div>
        </div>

    </div>

    <?php if (isset($_GET['autoprint'])): ?>
    <script>
        window.addEventListener('DOMContentLoaded', () => {
            window.print();
        });
    </script>
    <?php endif; ?>

</body>
</html>
