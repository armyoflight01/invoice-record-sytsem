<?php
/**
 * Arabic & English Invoice Record System - Main Application
 * نظام إدارة وتسجيل الفواتير باللغتين العربية والإنجليزية
 */
require_once __DIR__ . '/config.php';
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>نظام إدارة وتسجيل الفواتير | Arabic Invoice Record System</title>
    
    <!-- Google Fonts: Cairo & Tajawal -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&family=Tajawal:wght@400;500;700;800&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Custom Green Theme Stylesheet -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<div class="app-container">

    <!-- Mobile Sidebar Backdrop -->
    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

    <!-- App Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="brand-wrapper">
                <div class="brand-logo-icon">
                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <div>
                    <div class="brand-title" data-i18n="brand_title">نظام الفواتير</div>
                    <div class="brand-subtitle" id="sidebarBrandName" data-i18n="brand_subtitle">الواحة للحلول التجارية</div>
                </div>
            </div>
            <button class="sidebar-close-btn" id="closeSidebarBtn" title="إغلاق القائمة">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <nav class="sidebar-nav">
            <a href="#" class="nav-link active" data-view="dashboard">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                <span data-i18n="nav_dashboard">لوحة التحكم</span>
            </a>

            <a href="#" class="nav-link" data-view="invoices">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span data-i18n="nav_invoices">سجل الفواتير</span>
            </a>

            <a href="#" class="nav-link" data-view="clients">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                <span data-i18n="nav_clients">دليل العملاء</span>
            </a>

            <a href="#" class="nav-link" data-view="products">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                <span data-i18n="nav_products">المنتجات والخدمات</span>
            </a>

            <a href="#" class="nav-link" data-view="settings">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span data-i18n="nav_settings">إعدادات المنشأة</span>
            </a>

            <a href="#" class="nav-link" data-view="backup">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                <span data-i18n="nav_backup">النسخ الاحتياطي</span>
            </a>
        </nav>

        <div class="sidebar-footer">
            <span data-i18n="version_text">الإصدار 2.5 الأخضر المطور</span>
            <span style="font-size:10px; background:rgba(255,255,255,0.15); padding:2px 6px; border-radius:4px;">AR / EN</span>
        </div>
    </aside>

    <!-- Main Wrapper -->
    <div class="main-wrapper">
        
        <!-- Top Sticky Navbar -->
        <header class="topbar">
            <div class="topbar-right">
                <button class="mobile-toggle-btn" id="mobileToggleBtn" title="فتح القائمة">
                    <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <div class="page-headline">
                    <h2 id="pageTitle" data-i18n="title_dashboard">لوحة التحكم والإحصائيات</h2>
                    <p data-i18n="page_subtitle">نظام الفواتير الإلكترونية المتكامل للهواتف والكمبيوتر</p>
                </div>
            </div>

            <div class="topbar-actions">
                <!-- Language Toggle Button -->
                <button class="lang-btn" id="langToggleBtn" onclick="App.toggleLanguage()" title="Change Language / تغيير اللغة">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"/></svg>
                    <span id="langToggleText">English</span>
                </button>

                <button class="btn btn-outline btn-sm" onclick="App.exportInvoicesToCSV()" title="تصدير ملف إكسل">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span data-i18n="btn_export_excel">تصدير Excel</span>
                </button>
                <button class="btn btn-primary" onclick="App.openNewInvoiceModal()">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                    <span data-i18n="btn_new_invoice">فاتورة جديدة</span>
                </button>
            </div>
        </header>

        <!-- Main Content Area -->
        <main class="content-area">

            <!-- ==============================================
                 VIEW 1: Dashboard
                 ============================================== -->
            <section class="view-section active-view" id="view-dashboard">
                
                <!-- KPI Statistics Grid -->
                <div class="kpi-grid">
                    <div class="kpi-card total">
                        <div class="kpi-content">
                            <h3 data-i18n="kpi_total_sales">إجمالي المبيعات النشطة</h3>
                            <div class="kpi-value" id="kpiTotalSales">0.00 ر.س</div>
                            <div class="kpi-sub" data-i18n="kpi_total_sub">المجموع الكلي للفواتير المعتمدة</div>
                        </div>
                        <div class="kpi-icon">
                            <svg width="26" height="26" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                    </div>

                    <div class="kpi-card paid">
                        <div class="kpi-content">
                            <h3 data-i18n="kpi_paid_total">المبالغ المسددة (مدفوعة)</h3>
                            <div class="kpi-value" id="kpiPaidTotal">0.00 ر.س</div>
                            <div class="kpi-sub" data-i18n="kpi_paid_sub">تم تحصيلها بنجاح</div>
                        </div>
                        <div class="kpi-icon" style="background:#d1fae5; color:#059669;">
                            <svg width="26" height="26" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                    </div>

                    <div class="kpi-card pending">
                        <div class="kpi-content">
                            <h3 data-i18n="kpi_unpaid_total">المبالغ المستحقة (معلقة/غير مدفوعة)</h3>
                            <div class="kpi-value" id="kpiUnpaidTotal">0.00 ر.س</div>
                            <div class="kpi-sub" data-i18n="kpi_unpaid_sub">بانتظار السداد من العملاء</div>
                        </div>
                        <div class="kpi-icon" style="background:#fef3c7; color:#d97706;">
                            <svg width="26" height="26" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                    </div>

                    <div class="kpi-card count">
                        <div class="kpi-content">
                            <h3 data-i18n="kpi_invoice_count">إجمالي عدد الفواتير</h3>
                            <div class="kpi-value" id="kpiInvoiceCount">0 فاتورة</div>
                            <div class="kpi-sub" data-i18n="kpi_invoice_sub">سجل العمليات الصادرة</div>
                        </div>
                        <div class="kpi-icon" style="background:#e0f2fe; color:#0284c7;">
                            <svg width="26" height="26" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                    </div>
                </div>

                <!-- Recent Invoices Table -->
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">
                            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span data-i18n="recent_invoices_title">أحدث الفواتير المسجلة</span>
                        </div>
                        <button class="btn btn-sm btn-light" onclick="App.switchView('invoices')" data-i18n="btn_view_all_invoices">عرض جميع الفواتير</button>
                    </div>
                    <div class="card-body" style="padding:0;">
                        <div class="table-container" style="border:none; border-radius:0;">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th data-i18n="th_invoice_num">رقم الفاتورة</th>
                                        <th data-i18n="th_client">العميل</th>
                                        <th data-i18n="th_issue_date">تاريخ الإصدار</th>
                                        <th data-i18n="th_total">المبلغ الكلي</th>
                                        <th data-i18n="th_status">الحالة</th>
                                        <th style="width: 80px;" data-i18n="th_actions">إجراء</th>
                                    </tr>
                                </thead>
                                <tbody id="recentInvoicesTableBody">
                                    <!-- Dynamic rows loaded via JS -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Quick Action Banners -->
                <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px;">
                    <div style="background:var(--primary-light); border:1px solid var(--primary-border); padding:20px; border-radius:var(--radius-lg); display:flex; align-items:center; justify-content:space-between;">
                        <div>
                            <h4 style="color:var(--primary-dark); font-size:16px;" data-i18n="client_modal_title_new">إضافة عميل جديد</h4>
                            <p style="font-size:12.5px; color:var(--text-muted); margin-top:3px;" data-i18n="lbl_client_tax">سجل بيانات العملاء وأرقامهم الضريبية</p>
                        </div>
                        <button class="btn btn-primary btn-sm" onclick="App.openNewClientModal()" data-i18n="btn_add_client">إضافة عميل</button>
                    </div>

                    <div style="background:#f0fdf4; border:1px solid #bbf7d0; padding:20px; border-radius:var(--radius-lg); display:flex; align-items:center; justify-content:space-between;">
                        <div>
                            <h4 style="color:#166534; font-size:16px;" data-i18n="product_modal_title_new">إضافة منتج أو خدمة</h4>
                            <p style="font-size:12.5px; color:var(--text-muted); margin-top:3px;" data-i18n="lbl_product_desc">أدرج الخدمات بالأسعار المحددة لتسريع الفوترة</p>
                        </div>
                        <button class="btn btn-success btn-sm" onclick="App.openNewProductModal()" data-i18n="btn_add_product">إضافة منتج</button>
                    </div>
                </div>

            </section>

            <!-- ==============================================
                 VIEW 2: Invoices List & Management
                 ============================================== -->
            <section class="view-section" id="view-invoices">
                
                <!-- Filters & Search Bar -->
                <div class="filter-bar">
                    <div class="search-input-group">
                        <svg class="search-icon" width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <input type="text" id="invoiceSearchInput" class="search-input" data-i18n-placeholder="search_invoice_placeholder" placeholder="ابحث برقم الفاتورة، اسم العميل أو الهاتف...">
                    </div>

                    <select id="statusFilterSelect" class="filter-select">
                        <option value="all" data-i18n="status_all">جميع الحالات</option>
                        <option value="paid" data-i18n="status_paid">مدفوعة</option>
                        <option value="unpaid" data-i18n="status_unpaid">غير مدفوعة (مستحقة)</option>
                        <option value="pending" data-i18n="status_pending">قيد الانتظار</option>
                        <option value="cancelled" data-i18n="status_cancelled">ملغاة</option>
                    </select>

                    <div style="display:flex; align-items:center; gap:8px;">
                        <input type="date" id="dateFromFilter" class="filter-select">
                        <span style="color:var(--text-muted); font-size:12px;" data-i18n="date_to">إلى</span>
                        <input type="date" id="dateToFilter" class="filter-select">
                    </div>

                    <button class="btn btn-outline btn-sm" onclick="document.getElementById('invoiceSearchInput').value=''; document.getElementById('statusFilterSelect').value='all'; document.getElementById('dateFromFilter').value=''; document.getElementById('dateToFilter').value=''; App.loadInvoices();" data-i18n="btn_reset">
                        إعادة تعيين
                    </button>
                </div>

                <!-- Invoices Table -->
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">
                            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            <span data-i18n="title_invoices">سجل الفواتير الصادرة</span>
                        </div>
                        <button class="btn btn-primary btn-sm" onclick="App.openNewInvoiceModal()">
                            <span>+ </span><span data-i18n="btn_new_invoice">إنشاء فاتورة جديدة</span>
                        </button>
                    </div>
                    <div class="card-body" style="padding:0;">
                        <div class="table-container" style="border:none;">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th data-i18n="th_invoice_num">رقم الفاتورة</th>
                                        <th data-i18n="th_client">العميل</th>
                                        <th data-i18n="th_issue_date">تاريخ الإصدار</th>
                                        <th data-i18n="th_due_date">تاريخ الاستحقاق</th>
                                        <th data-i18n="th_total">المجموع الإجمالي</th>
                                        <th data-i18n="th_status">الحالة</th>
                                        <th style="width: 190px;" data-i18n="th_actions">العمليات</th>
                                    </tr>
                                </thead>
                                <tbody id="invoicesTableBody">
                                    <!-- Dynamic rows loaded via JS -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </section>

            <!-- ==============================================
                 VIEW 3: Clients Directory
                 ============================================== -->
            <section class="view-section" id="view-clients">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">
                            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                            <span data-i18n="title_clients">دليل العملاء المسجلين</span>
                        </div>
                        <button class="btn btn-primary btn-sm" onclick="App.openNewClientModal()">
                            <span>+ </span><span data-i18n="btn_add_client">إضافة عميل جديد</span>
                        </button>
                    </div>
                    <div class="card-body" style="padding:0;">
                        <div class="table-container" style="border:none;">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th data-i18n="lbl_client_name">اسم العميل</th>
                                        <th data-i18n="th_phone">رقم الجوال</th>
                                        <th data-i18n="th_email">البريد الإلكتروني</th>
                                        <th data-i18n="th_tax_num">الرقم الضريبي</th>
                                        <th data-i18n="th_invoices_count">عدد الفواتير</th>
                                        <th style="width: 120px;" data-i18n="th_actions">إجراءات</th>
                                    </tr>
                                </thead>
                                <tbody id="clientsTableBody">
                                    <!-- Dynamic rows loaded via JS -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>

            <!-- ==============================================
                 VIEW 4: Products & Services Catalog
                 ============================================== -->
            <section class="view-section" id="view-products">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">
                            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                            <span data-i18n="title_products">قائمة المنتجات والخدمات الجاهزة</span>
                        </div>
                        <button class="btn btn-primary btn-sm" onclick="App.openNewProductModal()">
                            <span>+ </span><span data-i18n="btn_add_product">إضافة منتج أو خدمة</span>
                        </button>
                    </div>
                    <div class="card-body" style="padding:0;">
                        <div class="table-container" style="border:none;">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th data-i18n="th_item_name">اسم الصنف / الخدمة</th>
                                        <th data-i18n="th_description">الوصف</th>
                                        <th data-i18n="th_unit_price">سعر الوحدة</th>
                                        <th data-i18n="th_tax_rate">نسبة الضريبة</th>
                                        <th style="width: 120px;" data-i18n="th_actions">إجراءات</th>
                                    </tr>
                                </thead>
                                <tbody id="productsTableBody">
                                    <!-- Dynamic rows loaded via JS -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>

            <!-- ==============================================
                 VIEW 5: Settings
                 ============================================== -->
            <section class="view-section" id="view-settings">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">
                            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <span data-i18n="settings_title">بيانات المنشأة وإعدادات الفوترة الإلكترونية</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <form id="settingsForm">
                            <div class="form-grid">
                                <div class="form-group">
                                    <label data-i18n="lbl_company_name">اسم المنشأة / الشركة *</label>
                                    <input type="text" name="company_name" id="setting_company_name" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label data-i18n="lbl_company_cr">رقم السجل التجاري (CR)</label>
                                    <input type="text" name="company_cr" id="setting_company_cr" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label data-i18n="lbl_company_vat">الرقم الضريبي (15 رقماً) *</label>
                                    <input type="text" name="company_vat" id="setting_company_vat" class="form-control" placeholder="310123456700003" required>
                                </div>
                                <div class="form-group">
                                    <label data-i18n="lbl_company_phone">رقم الجوال / الهاتف</label>
                                    <input type="text" name="company_phone" id="setting_company_phone" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label data-i18n="lbl_company_email">البريد الإلكتروني للفوترة</label>
                                    <input type="email" name="company_email" id="setting_company_email" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label data-i18n="lbl_currency">رمز العملة</label>
                                    <input type="text" name="currency" id="setting_currency" class="form-control" placeholder="ر.س">
                                </div>
                                <div class="form-group">
                                    <label data-i18n="lbl_default_tax">نسبة الضريبة الافتراضية (%)</label>
                                    <input type="number" step="0.1" name="default_tax_rate" id="setting_default_tax_rate" class="form-control" placeholder="15">
                                </div>
                                <div class="form-group">
                                    <label data-i18n="lbl_invoice_prefix">بادئة رقم الفاتورة</label>
                                    <input type="text" name="invoice_prefix" id="setting_invoice_prefix" class="form-control" placeholder="INV-">
                                </div>
                            </div>

                            <div class="form-group" style="margin-bottom: 20px;">
                                <label data-i18n="lbl_company_address">عنوان المنشأة بالتفصيل</label>
                                <input type="text" name="company_address" id="setting_company_address" class="form-control">
                            </div>

                            <div class="form-group" style="margin-bottom: 24px;">
                                <label data-i18n="lbl_invoice_footer">ملاحظة وتذييل الفاتورة الافتراضي</label>
                                <textarea name="invoice_footer" id="setting_invoice_footer" class="form-control" rows="3"></textarea>
                            </div>

                            <button type="submit" class="btn btn-primary">
                                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span data-i18n="btn_save_settings">حفظ وتحديث الإعدادات</span>
                            </button>
                        </form>
                    </div>
                </div>
            </section>

            <!-- ==============================================
                 VIEW 6: Backup & Restore
                 ============================================== -->
            <section class="view-section" id="view-backup">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">
                            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                            <span data-i18n="title_backup">النسخ الاحتياطي وحماية البيانات</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap:24px;">
                            <div style="background:var(--primary-light); border:1px solid var(--primary-border); padding:24px; border-radius:var(--radius-lg);">
                                <h3 style="color:var(--primary-dark); font-size:17px; margin-bottom:8px;" data-i18n="backup_export_title">تصدير نسخة احتياطية كاملة</h3>
                                <p style="color:var(--text-muted); font-size:13px; margin-bottom:16px;" data-i18n="backup_export_desc">قم بتحميل ملف JSON يحتوي على كامل الفواتير، بيانات العملاء، الخدمات، والإعدادات لحفظها في مكان آمن.</p>
                                <a href="api.php?action=export_data" class="btn btn-primary">
                                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                    <span data-i18n="btn_download_backup">تحميل النسخة الاحتياطية (JSON)</span>
                                </a>
                            </div>

                            <div style="background:var(--bg-muted); border:1px solid var(--border); padding:24px; border-radius:var(--radius-lg);">
                                <h3 style="font-size:17px; margin-bottom:8px;" data-i18n="backup_import_title">استعادة نسخة احتياطية</h3>
                                <p style="color:var(--text-muted); font-size:13px; margin-bottom:16px;" data-i18n="backup_import_desc">حدد ملف النسخة الاحتياطية (JSON) لاسترجاع كافة البيانات السابقة.</p>
                                <form action="api.php" method="POST" enctype="multipart/form-data" onsubmit="return confirm(App.t('confirm_restore'));">
                                    <input type="hidden" name="action" value="import_data">
                                    <input type="file" name="backup_file" accept=".json" class="form-control" style="margin-bottom:12px;" required>
                                    <button type="submit" class="btn btn-outline" style="border-color:var(--primary); color:var(--primary);">
                                        <span data-i18n="btn_restore_now">استرجاع البيانات الآن</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

        </main>
    </div>

</div>

<!-- ==============================================
     MODAL 1: Create / Edit Invoice Modal
     ============================================== -->
<div class="modal-overlay" id="invoiceModal">
    <div class="modal-dialog" style="max-width: 960px;">
        <div class="modal-header">
            <h3 id="invoiceFormTitle" data-i18n="btn_new_invoice">إنشاء فاتورة جديدة</h3>
            <button class="modal-close" onclick="App.closeModal('invoiceModal')">
                <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form id="invoiceForm">
            <div class="modal-body">
                <input type="hidden" id="invoiceIdInput">

                <!-- Row 1: Invoice metadata -->
                <div class="form-grid">
                    <div class="form-group">
                        <label data-i18n="lbl_invoice_num">رقم الفاتورة *</label>
                        <input type="text" id="invoiceNumberInput" class="form-control" style="font-family: monospace; font-weight:700;" required>
                    </div>

                    <div class="form-group">
                        <label data-i18n="lbl_client">العميل *</label>
                        <select id="invoiceClientSelect" class="form-control">
                            <!-- Populated by JS -->
                        </select>
                    </div>

                    <div class="form-group">
                        <label data-i18n="lbl_issue_date">تاريخ الإصدار *</label>
                        <input type="date" id="invoiceIssueDate" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label data-i18n="lbl_due_date">تاريخ الاستحقاق</label>
                        <input type="date" id="invoiceDueDate" class="form-control">
                    </div>
                </div>

                <!-- Row 2: Status & Method -->
                <div class="form-grid">
                    <div class="form-group">
                        <label data-i18n="lbl_status">حالة الفاتورة *</label>
                        <select id="invoiceStatusSelect" class="form-control">
                            <option value="unpaid" data-i18n="status_unpaid">غير مدفوعة (مستحقة)</option>
                            <option value="paid" data-i18n="status_paid">مدفوعة بالكامل</option>
                            <option value="pending" data-i18n="status_pending">قيد الانتظار</option>
                            <option value="cancelled" data-i18n="status_cancelled">ملغاة</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label data-i18n="lbl_payment_method">طريقة الدفع *</label>
                        <select id="invoicePaymentMethod" class="form-control">
                            <option value="bank_transfer" data-i18n="method_bank">تحويل بنكي</option>
                            <option value="card" data-i18n="method_card">بطاقة مدى / ائتمان</option>
                            <option value="cash" data-i18n="method_cash">نقداً (كاش)</option>
                            <option value="cheque" data-i18n="method_cheque">شيك مصرفي</option>
                        </select>
                    </div>
                </div>

                <!-- Invoice Line Items Table -->
                <div style="display:flex; justify-content:space-between; align-items:center; margin-top:20px; margin-bottom:8px;">
                    <h4 style="color:var(--primary-dark); font-size:15px; font-weight:800;" data-i18n="lbl_invoice_items">بنود الفاتورة والخدمات</h4>
                    <button type="button" class="btn btn-sm btn-light" id="addItemBtn">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span data-i18n="btn_add_item">إضافة بند جديد</span>
                    </button>
                </div>

                <div class="items-table-wrapper">
                    <table class="items-builder-table">
                        <thead>
                            <tr>
                                <th style="width: 35%;" data-i18n="th_item_name">البند والوصف</th>
                                <th style="width: 12%; text-align:center;" data-i18n="th_quantity">الكمية</th>
                                <th style="width: 15%;" data-i18n="th_unit_price">سعر الوحدة</th>
                                <th style="width: 12%;" data-i18n="th_discount">الخصم</th>
                                <th style="width: 12%; text-align:center;" data-i18n="th_tax_rate">الضريبة %</th>
                                <th style="width: 14%; text-align:center;" data-i18n="th_total">الإجمالي</th>
                                <th style="width: 40px;"></th>
                            </tr>
                        </thead>
                        <tbody id="invoiceItemsTableBody">
                            <!-- Dynamic Item Rows -->
                        </tbody>
                    </table>
                </div>

                <!-- Calculation Summary & Notes -->
                <div style="display:flex; justify-content:space-between; flex-wrap:wrap; gap:20px;">
                    <div style="flex:1; min-width:260px;">
                        <label style="font-size:13px; font-weight:700; color:var(--text-heading); margin-bottom:6px; display:block;" data-i18n="lbl_notes_terms">شروط الدفع وملاحظات</label>
                        <textarea id="invoiceNotesInput" class="form-control" rows="3"></textarea>
                    </div>

                    <div class="invoice-calc-box">
                        <div class="calc-row">
                            <span data-i18n="calc_subtotal">المجموع الفرعي:</span>
                            <span id="calcSubtotal">0.00 ر.س</span>
                        </div>
                        <div class="calc-row">
                            <span data-i18n="calc_discount">إجمالي الخصم:</span>
                            <span id="calcDiscount" style="color:#dc2626;">0.00 ر.س</span>
                        </div>
                        <div class="calc-row">
                            <span data-i18n="calc_tax">ضريبة القيمة المضافة:</span>
                            <span id="calcTax">0.00 ر.س</span>
                        </div>
                        <div class="calc-row grand">
                            <span data-i18n="calc_grand_total">المجموع الكلي:</span>
                            <span id="calcGrandTotal">0.00 ر.س</span>
                        </div>
                    </div>
                </div>

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="App.closeModal('invoiceModal')" data-i18n="btn_cancel">إلغاء</button>
                <button type="submit" class="btn btn-primary">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span data-i18n="btn_save_invoice">حفظ وتأكيد الفاتورة</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ==============================================
     MODAL 2: View & Share Invoice Modal
     ============================================== -->
<div class="modal-overlay" id="viewInvoiceModal">
    <div class="modal-dialog" style="max-width: 780px;">
        <div class="modal-header">
            <h3 data-i18n="btn_view">معاينة الفاتورة</h3>
            <button class="modal-close" onclick="App.closeModal('viewInvoiceModal')">
                <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="modal-body" id="viewInvoiceModalContent">
            <!-- Dynamic view injected via JS -->
        </div>
    </div>
</div>

<!-- ==============================================
     MODAL 3: Client Form Modal
     ============================================== -->
<div class="modal-overlay" id="clientModal">
    <div class="modal-dialog modal-sm">
        <div class="modal-header">
            <h3 id="clientModalTitle" data-i18n="client_modal_title_new">إضافة عميل جديد</h3>
            <button class="modal-close" onclick="App.closeModal('clientModal')">
                <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form id="clientForm">
            <div class="modal-body">
                <input type="hidden" id="clientIdInput" name="id">
                <div class="form-group" style="margin-bottom:14px;">
                    <label data-i18n="lbl_client_name">اسم العميل / المؤسسة *</label>
                    <input type="text" id="clientNameInput" name="name" class="form-control" required>
                </div>
                <div class="form-group" style="margin-bottom:14px;">
                    <label data-i18n="lbl_client_phone">رقم الجوال / الهاتف</label>
                    <input type="text" id="clientPhoneInput" name="phone" class="form-control" placeholder="+966 50 000 0000">
                </div>
                <div class="form-group" style="margin-bottom:14px;">
                    <label data-i18n="lbl_client_email">البريد الإلكتروني</label>
                    <input type="email" id="clientEmailInput" name="email" class="form-control">
                </div>
                <div class="form-group" style="margin-bottom:14px;">
                    <label data-i18n="lbl_client_tax">الرقم الضريبي للعميل (إن وجد)</label>
                    <input type="text" id="clientTaxInput" name="tax_number" class="form-control">
                </div>
                <div class="form-group">
                    <label data-i18n="lbl_client_address">العنوان</label>
                    <input type="text" id="clientAddressInput" name="address" class="form-control">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="App.closeModal('clientModal')" data-i18n="btn_cancel">إلغاء</button>
                <button type="submit" class="btn btn-primary" data-i18n="btn_save_client">حفظ العميل</button>
            </div>
        </form>
    </div>
</div>

<!-- ==============================================
     MODAL 4: Product / Service Form Modal
     ============================================== -->
<div class="modal-overlay" id="productModal">
    <div class="modal-dialog modal-sm">
        <div class="modal-header">
            <h3 id="productModalTitle" data-i18n="product_modal_title_new">إضافة صنف أو خدمة</h3>
            <button class="modal-close" onclick="App.closeModal('productModal')">
                <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form id="productForm">
            <div class="modal-body">
                <input type="hidden" id="productIdInput" name="id">
                <div class="form-group" style="margin-bottom:14px;">
                    <label data-i18n="lbl_product_name">اسم المنتج أو الخدمة *</label>
                    <input type="text" id="productNameInput" name="name" class="form-control" required>
                </div>
                <div class="form-group" style="margin-bottom:14px;">
                    <label data-i18n="lbl_product_price">سعر الوحدة الافتراضي *</label>
                    <input type="number" step="0.01" min="0" id="productPriceInput" name="price" class="form-control" required>
                </div>
                <div class="form-group" style="margin-bottom:14px;">
                    <label data-i18n="lbl_product_tax">نسبة الضريبة (%)</label>
                    <input type="number" step="0.01" min="0" id="productTaxInput" name="tax_rate" class="form-control" value="15">
                </div>
                <div class="form-group">
                    <label data-i18n="lbl_product_desc">وصف الصنف</label>
                    <textarea id="productDescInput" name="description" class="form-control" rows="2"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="App.closeModal('productModal')" data-i18n="btn_cancel">إلغاء</button>
                <button type="submit" class="btn btn-primary" data-i18n="btn_save_product">حفظ الصنف</button>
            </div>
        </form>
    </div>
</div>

<!-- Toast Container -->
<div class="toast-container" id="toastContainer"></div>

<!-- JavaScript Core -->
<script src="assets/js/app.js"></script>

</body>
</html>
