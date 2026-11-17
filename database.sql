-- ========================================================
-- Arabic Invoice Record System (نظام تسجيل وإدارة الفواتير)
-- MySQL / MariaDB Database Dump
-- ========================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for settings
-- ----------------------------
CREATE TABLE IF NOT EXISTS `settings` (
  `key` varchar(100) NOT NULL,
  `value` text DEFAULT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `settings` (`key`, `value`) VALUES
('company_name', 'مؤسسة الواحة للحلول التجارية'),
('company_cr', '1010789456'),
('company_vat', '310123456700003'),
('company_phone', '+966 54 321 0987'),
('company_email', 'billing@alwaha-solutions.com'),
('company_address', 'طريق الملك فهد، حي الصحافة، الرياض، المملكة العربية السعودية'),
('company_logo', ''),
('currency', 'ر.س'),
('default_tax_rate', '15'),
('invoice_prefix', 'INV-'),
('invoice_footer', 'شكراً لتعاملكم معنا. تسري هذه الفاتورة كإشعار رسمي للدفع وفق الشروط المتفق عليها.')
ON DUPLICATE KEY UPDATE `value`=VALUES(`value`);

-- ----------------------------
-- Table structure for clients
-- ----------------------------
CREATE TABLE IF NOT EXISTS `clients` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `tax_number` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `clients` (`id`, `name`, `phone`, `email`, `address`, `tax_number`, `created_at`) VALUES
(1, 'شركة الأفق الرقمية المحدودة', '+966 50 111 2233', 'info@alofoq-tech.sa', 'شارع التحلية، الرياض', '300987654300003', CURRENT_TIMESTAMP),
(2, 'مؤسسة البناء الحديث للمقاولات', '+966 55 444 7788', 'contact@modernbuild.sa', 'حي الزهراء، جدة', '311223344500003', CURRENT_TIMESTAMP),
(3, 'مركز الرواد للاستشارات والتدريب', '+966 53 888 9900', 'finance@alrowad.edu.sa', 'حي العليا، الخبر', '302334455600003', CURRENT_TIMESTAMP);

-- ----------------------------
-- Table structure for products
-- ----------------------------
CREATE TABLE IF NOT EXISTS `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `tax_rate` decimal(5,2) NOT NULL DEFAULT 15.00,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `products` (`id`, `name`, `price`, `tax_rate`, `description`, `created_at`) VALUES
(1, 'تصميم وبرمجة موقع إلكتروني', 3500.00, 15.00, 'تصميم واجهة مستخدم متجاوبة مع لوحة تحكم', CURRENT_TIMESTAMP),
(2, 'استضافة سحابية سنوية (VPS)', 1200.00, 15.00, 'خادم افتراضي 4 أنوية، 8 جيجابايت رام', CURRENT_TIMESTAMP),
(3, 'خدمات دعم فني وصيانة شهرية', 800.00, 15.00, 'صيانة دورية وتحديثات أمان وحفظ نسخ احتياطية', CURRENT_TIMESTAMP),
(4, 'حملة تسويق وإعلانات رقمية', 2200.00, 15.00, 'إدارة حملات جوجل ومنصات التواصل الاجتماعي', CURRENT_TIMESTAMP);

-- ----------------------------
-- Table structure for invoices
-- ----------------------------
CREATE TABLE IF NOT EXISTS `invoices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `invoice_number` varchar(50) NOT NULL,
  `client_id` int(11) DEFAULT NULL,
  `issue_date` date NOT NULL,
  `due_date` date DEFAULT NULL,
  `status` enum('paid','unpaid','pending','cancelled') NOT NULL DEFAULT 'unpaid',
  `payment_method` varchar(50) NOT NULL DEFAULT 'bank_transfer',
  `subtotal` decimal(10,2) NOT NULL DEFAULT 0.00,
  `discount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `tax_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `grand_total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_invoice_number` (`invoice_number`),
  KEY `idx_client_id` (`client_id`),
  CONSTRAINT `fk_invoices_client` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `invoices` (`id`, `invoice_number`, `client_id`, `issue_date`, `due_date`, `status`, `payment_method`, `subtotal`, `discount`, `tax_amount`, `grand_total`, `notes`, `created_at`) VALUES
(1, 'INV-2026-0001', 1, DATE_SUB(CURDATE(), INTERVAL 5 DAY), DATE_ADD(CURDATE(), INTERVAL 10 DAY), 'paid', 'bank_transfer', 4700.00, 200.00, 675.00, 5175.00, 'تم استلام المبلغ عبر التحويل البنكي لحساب مصرف الراجحي.', CURRENT_TIMESTAMP),
(2, 'INV-2026-0002', 2, DATE_SUB(CURDATE(), INTERVAL 2 DAY), DATE_ADD(CURDATE(), INTERVAL 12 DAY), 'pending', 'card', 2200.00, 0.00, 330.00, 2530.00, 'بانتظار تأكيد السداد بواسطة بطاقة مدى.', CURRENT_TIMESTAMP),
(3, 'INV-2026-0003', 3, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 15 DAY), 'unpaid', 'cash', 800.00, 50.00, 112.50, 862.50, 'الرجاء سداد المبلغ نقداً عند الاستلام.', CURRENT_TIMESTAMP);

-- ----------------------------
-- Table structure for invoice_items
-- ----------------------------
CREATE TABLE IF NOT EXISTS `invoice_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `invoice_id` int(11) NOT NULL,
  `item_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `quantity` decimal(10,2) NOT NULL DEFAULT 1.00,
  `unit_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `discount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `tax_rate` decimal(5,2) NOT NULL DEFAULT 15.00,
  `total` decimal(10,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`id`),
  KEY `idx_invoice_id` (`invoice_id`),
  CONSTRAINT `fk_items_invoice` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `invoice_items` (`id`, `invoice_id`, `item_name`, `description`, `quantity`, `unit_price`, `discount`, `tax_rate`, `total`) VALUES
(1, 1, 'تصميم وبرمجة موقع إلكتروني', 'واجهة عربية متطورة وسريعة متجاوبة مع الهواتف', 1.00, 3500.00, 200.00, 15.00, 3795.00),
(2, 1, 'استضافة سحابية سنوية (VPS)', 'باقة الأعمال 8 جيجابايت', 1.00, 1200.00, 0.00, 15.00, 1380.00),
(3, 2, 'حملة تسويق وإعلانات رقمية', 'خطة تسويق شاملة لمدة شهر', 1.00, 2200.00, 0.00, 15.00, 2530.00),
(4, 3, 'خدمات دعم فني وصيانة شهرية', 'صيانة شهر نوفمبر 2026', 1.00, 800.00, 50.00, 15.00, 862.50);

SET FOREIGN_KEY_CHECKS = 1;
