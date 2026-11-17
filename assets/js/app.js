/**
 * Arabic & English Invoice Record System
 * JavaScript Application Core with Bilingual Support
 */

const App = {
    // Current Language: 'ar' or 'en'
    lang: localStorage.getItem('app_lang') || 'ar',

    currentView: 'dashboard',
    currency: 'ر.س',
    taxRate: 15,
    settings: {},
    clients: [],
    products: [],
    invoices: [],
    editingInvoiceId: null,

    // Bilingual Dictionary
    i18n: {
        ar: {
            brand_title: 'نظام الفواتير',
            brand_subtitle: 'الواحة للحلول التجارية',
            version_text: 'الإصدار 2.5 الأخضر المطور',
            page_subtitle: 'نظام الفواتير الإلكترونية المتكامل للهواتف والكمبيوتر',
            
            nav_dashboard: 'لوحة التحكم',
            nav_invoices: 'سجل الفواتير',
            nav_clients: 'دليل العملاء',
            nav_products: 'المنتجات والخدمات',
            nav_settings: 'إعدادات المنشأة',
            nav_backup: 'النسخ الاحتياطي',

            title_dashboard: 'لوحة التحكم والإحصائيات',
            title_invoices: 'سجل وإدارة الفواتير',
            title_clients: 'دليل العملاء',
            title_products: 'المنتجات والخدمات',
            title_settings: 'إعدادات المنشأة والفواتير',
            title_backup: 'النسخ الاحتياطي واستعادة البيانات',

            btn_new_invoice: 'فاتورة جديدة',
            btn_export_excel: 'تصدير Excel',
            btn_view_all_invoices: 'عرض جميع الفواتير',
            btn_add_client: 'إضافة عميل',
            btn_add_product: 'إضافة منتج',
            btn_save_invoice: 'حفظ وتأكيد الفاتورة',
            btn_save_client: 'حفظ العميل',
            btn_save_product: 'حفظ الصنف',
            btn_save_settings: 'حفظ وتحديث الإعدادات',
            btn_cancel: 'إلغاء',
            btn_close: 'إغلاق',
            btn_reset: 'إعادة تعيين',
            btn_add_item: 'إضافة بند جديد',
            btn_edit: 'تعديل',
            btn_delete: 'حذف',
            btn_view: 'عرض',
            btn_print: 'طباعة',
            btn_print_official: 'طباعة الفاتورة الرسمية',
            btn_whatsapp: 'إرسال عبر واتساب',
            btn_download_backup: 'تحميل النسخة الاحتياطية (JSON)',
            btn_restore_now: 'استرجاع البيانات الآن',

            kpi_total_sales: 'إجمالي المبيعات النشطة',
            kpi_total_sub: 'المجموع الكلي للفواتير المعتمدة',
            kpi_paid_total: 'المبالغ المسددة (مدفوعة)',
            kpi_paid_sub: 'تم تحصيلها بنجاح',
            kpi_unpaid_total: 'المبالغ المستحقة (معلقة/غير مدفوعة)',
            kpi_unpaid_sub: 'بانتظار السداد من العملاء',
            kpi_invoice_count: 'إجمالي عدد الفواتير',
            kpi_invoice_sub: 'سجل العمليات الصادرة',
            recent_invoices_title: 'أحدث الفواتير المسجلة',

            search_invoice_placeholder: 'ابحث برقم الفاتورة، اسم العميل أو الهاتف...',
            status_all: 'جميع الحالات',
            status_paid: 'مدفوعة',
            status_unpaid: 'غير مدفوعة (مستحقة)',
            status_pending: 'قيد الانتظار',
            status_cancelled: 'ملغاة',
            date_to: 'إلى',

            th_invoice_num: 'رقم الفاتورة',
            th_client: 'العميل',
            th_issue_date: 'تاريخ الإصدار',
            th_due_date: 'تاريخ الاستحقاق',
            th_total: 'المجموع الإجمالي',
            th_status: 'الحالة',
            th_actions: 'العمليات',
            th_phone: 'رقم الجوال',
            th_email: 'البريد الإلكتروني',
            th_tax_num: 'الرقم الضريبي',
            th_invoices_count: 'عدد الفواتير',
            th_item_name: 'اسم الصنف / الخدمة',
            th_description: 'الوصف',
            th_unit_price: 'سعر الوحدة',
            th_tax_rate: 'نسبة الضريبة',
            th_quantity: 'الكمية',
            th_discount: 'الخصم',

            lbl_invoice_num: 'رقم الفاتورة *',
            lbl_client: 'العميل *',
            lbl_issue_date: 'تاريخ الإصدار *',
            lbl_due_date: 'تاريخ الاستحقاق',
            lbl_status: 'حالة الفاتورة *',
            lbl_payment_method: 'طريقة الدفع *',
            lbl_invoice_items: 'بنود الفاتورة والخدمات',
            lbl_notes_terms: 'شروط الدفع وملاحظات',
            calc_subtotal: 'المجموع الفرعي:',
            calc_discount: 'إجمالي الخصم:',
            calc_tax: 'ضريبة القيمة المضافة:',
            calc_grand_total: 'المجموع الكلي:',

            method_cash: 'نقداً (كاش)',
            method_bank: 'تحويل بنكي',
            method_card: 'بطاقة مدى / ائتمان',
            method_cheque: 'شيك مصرفي',

            client_modal_title_new: 'إضافة عميل جديد',
            client_modal_title_edit: 'تعديل بيانات العميل',
            lbl_client_name: 'اسم العميل / المؤسسة *',
            lbl_client_phone: 'رقم الجوال / الهاتف',
            lbl_client_email: 'البريد الإلكتروني',
            lbl_client_tax: 'الرقم الضريبي للعميل (إن وجد)',
            lbl_client_address: 'العنوان',

            product_modal_title_new: 'إضافة منتج أو خدمة جديدة',
            product_modal_title_edit: 'تعديل المنتج / الخدمة',
            lbl_product_name: 'اسم المنتج أو الخدمة *',
            lbl_product_price: 'سعر الوحدة الافتراضي *',
            lbl_product_tax: 'نسبة الضريبة (%)',
            lbl_product_desc: 'وصف الصنف',

            settings_title: 'بيانات المنشأة وإعدادات الفوترة الإلكترونية',
            lbl_company_name: 'اسم المنشأة / الشركة *',
            lbl_company_cr: 'رقم السجل التجاري (CR)',
            lbl_company_vat: 'الرقم الضريبي (15 رقماً) *',
            lbl_company_phone: 'رقم الجوال / الهاتف',
            lbl_company_email: 'البريد الإلكتروني للفوترة',
            lbl_currency: 'رمز العملة',
            lbl_default_tax: 'نسبة الضريبة الافتراضية (%)',
            lbl_invoice_prefix: 'بادئة رقم الفاتورة',
            lbl_company_address: 'عنوان المنشأة بالتفصيل',
            lbl_invoice_footer: 'ملاحظة وتذييل الفاتورة الافتراضي',

            backup_export_title: 'تصدير نسخة احتياطية كاملة',
            backup_export_desc: 'قم بتحميل ملف JSON يحتوي على كامل الفواتير، بيانات العملاء، الخدمات، والإعدادات لحفظها في مكان آمن.',
            backup_import_title: 'استعادة نسخة احتياطية',
            backup_import_desc: 'حدد ملف النسخة الاحتياطية (JSON) لاسترجاع كافة البيانات السابقة.',
            confirm_restore: 'تنبيه: ستؤدي الاستعادة إلى استبدال البيانات الحالية بمحتويات الملف. هل تريد المتابعة؟',

            toast_invoice_saved: 'تم حفظ الفاتورة بنجاح',
            toast_invoice_deleted: 'تم حذف الفاتورة بنجاح',
            toast_status_updated: 'تم تحديث حالة الفاتورة',
            toast_client_saved: 'تم حفظ بيانات العميل بنجاح',
            toast_client_deleted: 'تم حذف العميل بنجاح',
            toast_product_saved: 'تم حفظ الصنف بنجاح',
            toast_product_deleted: 'تم حذف الصنف بنجاح',
            toast_settings_saved: 'تم حفظ الإعدادات بنجاح',
            toast_item_required: 'يجب أن تحتوي الفاتورة على بند واحد على الأقل',
            confirm_delete_invoice: 'هل أنت متأكد من حذف هذه الفاتورة نهائياً؟',
            confirm_delete_client: 'هل أنت متأكد من حذف هذا العميل؟ لن يتم حذف فواتيره السابقة.',
            confirm_delete_product: 'هل أنت متأكد من حذف هذا الصنف من الدليل؟',
            cash_client: 'عميل نقدي',
            invoice_word: 'فاتورة',
            no_records: 'لا توجد نتائج مطابقة',
            no_clients: 'لا يوجد عملاء مسجلين',
            no_products: 'لا توجد أصناف مسجلة'
        },
        en: {
            brand_title: 'Invoice System',
            brand_subtitle: 'Al-Waha Business Solutions',
            version_text: 'v2.5 Green Edition',
            page_subtitle: 'Complete E-Invoicing System for Mobile & Desktop',

            nav_dashboard: 'Dashboard',
            nav_invoices: 'Invoices Record',
            nav_clients: 'Clients Directory',
            nav_products: 'Products & Services',
            nav_settings: 'Company Settings',
            nav_backup: 'Backup & Restore',

            title_dashboard: 'Dashboard & Statistics',
            title_invoices: 'Invoices Management',
            title_clients: 'Clients Directory',
            title_products: 'Products & Services',
            title_settings: 'Company & Tax Settings',
            title_backup: 'Backup & Data Recovery',

            btn_new_invoice: 'New Invoice',
            btn_export_excel: 'Export Excel',
            btn_view_all_invoices: 'View All Invoices',
            btn_add_client: 'Add Client',
            btn_add_product: 'Add Product',
            btn_save_invoice: 'Save & Confirm Invoice',
            btn_save_client: 'Save Client',
            btn_save_product: 'Save Item',
            btn_save_settings: 'Save Settings',
            btn_cancel: 'Cancel',
            btn_close: 'Close',
            btn_reset: 'Reset',
            btn_add_item: 'Add New Item',
            btn_edit: 'Edit',
            btn_delete: 'Delete',
            btn_view: 'View',
            btn_print: 'Print',
            btn_print_official: 'Print Official Invoice',
            btn_whatsapp: 'Send via WhatsApp',
            btn_download_backup: 'Download Backup (JSON)',
            btn_restore_now: 'Restore Data Now',

            kpi_total_sales: 'Total Active Sales',
            kpi_total_sub: 'Grand total of valid invoices',
            kpi_paid_total: 'Paid Amount',
            kpi_paid_sub: 'Successfully collected',
            kpi_unpaid_total: 'Due Amount (Pending/Unpaid)',
            kpi_unpaid_sub: 'Awaiting client settlement',
            kpi_invoice_count: 'Total Invoices Count',
            kpi_invoice_sub: 'Issued transaction record',
            recent_invoices_title: 'Recent Invoices',

            search_invoice_placeholder: 'Search by invoice #, client name or phone...',
            status_all: 'All Statuses',
            status_paid: 'Paid',
            status_unpaid: 'Unpaid (Due)',
            status_pending: 'Pending',
            status_cancelled: 'Cancelled',
            date_to: 'To',

            th_invoice_num: 'Invoice #',
            th_client: 'Client',
            th_issue_date: 'Issue Date',
            th_due_date: 'Due Date',
            th_total: 'Total Amount',
            th_status: 'Status',
            th_actions: 'Actions',
            th_phone: 'Phone',
            th_email: 'Email',
            th_tax_num: 'Tax / VAT Number',
            th_invoices_count: 'Invoices Count',
            th_item_name: 'Item / Service Name',
            th_description: 'Description',
            th_unit_price: 'Unit Price',
            th_tax_rate: 'Tax Rate',
            th_quantity: 'Qty',
            th_discount: 'Discount',

            lbl_invoice_num: 'Invoice Number *',
            lbl_client: 'Client *',
            lbl_issue_date: 'Issue Date *',
            lbl_due_date: 'Due Date',
            lbl_status: 'Invoice Status *',
            lbl_payment_method: 'Payment Method *',
            lbl_invoice_items: 'Invoice Items & Services',
            lbl_notes_terms: 'Payment Terms & Notes',
            calc_subtotal: 'Subtotal:',
            calc_discount: 'Total Discount:',
            calc_tax: 'VAT (Tax):',
            calc_grand_total: 'Grand Total:',

            method_cash: 'Cash',
            method_bank: 'Bank Transfer',
            method_card: 'Debit / Credit Card (Mada)',
            method_cheque: 'Bank Cheque',

            client_modal_title_new: 'Add New Client',
            client_modal_title_edit: 'Edit Client Details',
            lbl_client_name: 'Client / Business Name *',
            lbl_client_phone: 'Mobile / Phone Number',
            lbl_client_email: 'Email Address',
            lbl_client_tax: 'Client Tax Number (If available)',
            lbl_client_address: 'Address',

            product_modal_title_new: 'Add Product / Service',
            product_modal_title_edit: 'Edit Product / Service',
            lbl_product_name: 'Product or Service Name *',
            lbl_product_price: 'Default Unit Price *',
            lbl_product_tax: 'Tax Rate (%)',
            lbl_product_desc: 'Item Description',

            settings_title: 'Company Information & E-Invoicing Settings',
            lbl_company_name: 'Company / Business Name *',
            lbl_company_cr: 'Commercial Register (CR)',
            lbl_company_vat: 'Tax / VAT Number (15 digits) *',
            lbl_company_phone: 'Phone Number',
            lbl_company_email: 'Billing Email',
            lbl_currency: 'Currency Symbol',
            lbl_default_tax: 'Default Tax Rate (%)',
            lbl_invoice_prefix: 'Invoice Prefix',
            lbl_company_address: 'Detailed Business Address',
            lbl_invoice_footer: 'Default Invoice Note & Footer',

            backup_export_title: 'Export Full Backup',
            backup_export_desc: 'Download a complete JSON file containing all invoices, clients, products, and settings for safe storage.',
            backup_import_title: 'Restore Backup',
            backup_import_desc: 'Select your backup JSON file to recover all previous records.',
            confirm_restore: 'Warning: Restoring will overwrite existing data with the backup file. Proceed?',

            toast_invoice_saved: 'Invoice saved successfully',
            toast_invoice_deleted: 'Invoice deleted successfully',
            toast_status_updated: 'Invoice status updated',
            toast_client_saved: 'Client saved successfully',
            toast_client_deleted: 'Client deleted successfully',
            toast_product_saved: 'Product saved successfully',
            toast_product_deleted: 'Product deleted successfully',
            toast_settings_saved: 'Settings saved successfully',
            toast_item_required: 'Invoice must contain at least one item',
            confirm_delete_invoice: 'Are you sure you want to permanently delete this invoice?',
            confirm_delete_client: 'Are you sure you want to delete this client? Invoices will not be removed.',
            confirm_delete_product: 'Are you sure you want to delete this product from the catalog?',
            cash_client: 'Cash Customer',
            invoice_word: 'Invoice',
            no_records: 'No matching records found',
            no_clients: 'No registered clients found',
            no_products: 'No products or services found'
        }
    },

    // Translation lookup helper
    t(key) {
        return (this.i18n[this.lang] && this.i18n[this.lang][key]) 
            || (this.i18n['ar'] && this.i18n['ar'][key]) 
            || key;
    },

    // Toggle Language between 'ar' and 'en'
    toggleLanguage() {
        const newLang = this.lang === 'ar' ? 'en' : 'ar';
        this.setLanguage(newLang);
    },

    setLanguage(lang) {
        this.lang = lang;
        localStorage.setItem('app_lang', lang);

        const html = document.documentElement;
        html.lang = lang;
        html.dir = (lang === 'ar' ? 'rtl' : 'ltr');

        // Update Language Switcher Button Text
        const langBtnText = document.getElementById('langToggleText');
        if (langBtnText) {
            langBtnText.innerText = (lang === 'ar' ? 'English' : 'العربية');
        }

        // Apply translations to all data-i18n elements
        document.querySelectorAll('[data-i18n]').forEach(el => {
            const key = el.getAttribute('data-i18n');
            const translation = this.t(key);
            if (translation) {
                el.innerText = translation;
            }
        });

        // Apply translations to placeholder elements
        document.querySelectorAll('[data-i18n-placeholder]').forEach(el => {
            const key = el.getAttribute('data-i18n-placeholder');
            const translation = this.t(key);
            if (translation) {
                el.placeholder = translation;
            }
        });

        // Re-render active view
        this.switchView(this.currentView);
    },

    // Init
    init() {
        this.bindEvents();
        this.setLanguage(this.lang);
        this.loadSettings();
        this.loadClients();
        this.loadProducts();
        this.loadDashboard();
        this.loadInvoices();
    },

    // Bind UI Events
    bindEvents() {
        // Navigation links
        document.querySelectorAll('[data-view]').forEach(el => {
            el.addEventListener('click', (e) => {
                e.preventDefault();
                const view = el.getAttribute('data-view');
                this.switchView(view);
                this.closeSidebar();
            });
        });

        // Mobile drawer toggle
        const toggleBtn = document.getElementById('mobileToggleBtn');
        const sidebar = document.getElementById('sidebar');
        const backdrop = document.getElementById('sidebarBackdrop');
        const closeSidebarBtn = document.getElementById('closeSidebarBtn');

        if (toggleBtn) {
            toggleBtn.addEventListener('click', () => {
                sidebar.classList.add('open');
                backdrop.classList.add('active');
            });
        }

        if (closeSidebarBtn) {
            closeSidebarBtn.addEventListener('click', () => this.closeSidebar());
        }

        if (backdrop) {
            backdrop.addEventListener('click', () => this.closeSidebar());
        }

        // Global Modal Esc key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                this.closeAllModals();
            }
        });

        // Search & Filters for Invoices
        const invoiceSearch = document.getElementById('invoiceSearchInput');
        const statusFilter = document.getElementById('statusFilterSelect');
        const dateFromFilter = document.getElementById('dateFromFilter');
        const dateToFilter = document.getElementById('dateToFilter');

        if (invoiceSearch) {
            let timeout = null;
            invoiceSearch.addEventListener('input', () => {
                clearTimeout(timeout);
                timeout = setTimeout(() => this.loadInvoices(), 300);
            });
        }

        if (statusFilter) {
            statusFilter.addEventListener('change', () => this.loadInvoices());
        }

        if (dateFromFilter) {
            dateFromFilter.addEventListener('change', () => this.loadInvoices());
        }

        if (dateToFilter) {
            dateToFilter.addEventListener('change', () => this.loadInvoices());
        }

        // Settings Form Submit
        const settingsForm = document.getElementById('settingsForm');
        if (settingsForm) {
            settingsForm.addEventListener('submit', (e) => {
                e.preventDefault();
                this.saveSettings();
            });
        }

        // Client Form Submit
        const clientForm = document.getElementById('clientForm');
        if (clientForm) {
            clientForm.addEventListener('submit', (e) => {
                e.preventDefault();
                this.saveClient();
            });
        }

        // Product Form Submit
        const productForm = document.getElementById('productForm');
        if (productForm) {
            productForm.addEventListener('submit', (e) => {
                e.preventDefault();
                this.saveProduct();
            });
        }

        // Invoice Form Submit
        const invoiceForm = document.getElementById('invoiceForm');
        if (invoiceForm) {
            invoiceForm.addEventListener('submit', (e) => {
                e.preventDefault();
                this.saveInvoice();
            });
        }

        // Add Line Item Button
        const addItemBtn = document.getElementById('addItemBtn');
        if (addItemBtn) {
            addItemBtn.addEventListener('click', () => {
                this.addInvoiceItemRow();
            });
        }
    },

    closeSidebar() {
        const sidebar = document.getElementById('sidebar');
        const backdrop = document.getElementById('sidebarBackdrop');
        if (sidebar) sidebar.classList.remove('open');
        if (backdrop) backdrop.classList.remove('active');
    },

    // View Navigation
    switchView(viewName) {
        this.currentView = viewName;

        // Update Nav Links
        document.querySelectorAll('.nav-link').forEach(link => {
            if (link.getAttribute('data-view') === viewName) {
                link.classList.add('active');
            } else {
                link.classList.remove('active');
            }
        });

        // Hide all views
        document.querySelectorAll('.view-section').forEach(sec => {
            sec.classList.remove('active-view');
        });

        // Show target view
        const targetView = document.getElementById(`view-${viewName}`);
        if (targetView) {
            targetView.classList.add('active-view');
        }

        // Update Title
        const titleEl = document.getElementById('pageTitle');
        if (titleEl) {
            titleEl.innerText = this.t(`title_${viewName}`);
        }

        // Refresh Data on Tab Open
        if (viewName === 'dashboard') this.loadDashboard();
        if (viewName === 'invoices') this.loadInvoices();
        if (viewName === 'clients') this.loadClients();
        if (viewName === 'products') this.loadProducts();
    },

    // Toast Notification
    showToast(message, type = 'success') {
        const container = document.getElementById('toastContainer');
        if (!container) return;

        const toast = document.createElement('div');
        toast.className = `toast ${type === 'error' ? 'toast-error' : ''}`;
        toast.innerHTML = `
            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                ${type === 'error' 
                    ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>' 
                    : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>'
                }
            </svg>
            <span>${message}</span>
        `;
        container.appendChild(toast);

        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(10px)';
            setTimeout(() => toast.remove(), 300);
        }, 3500);
    },

    // Modal helpers
    openModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) modal.classList.add('active');
    },

    closeModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) modal.classList.remove('active');
    },

    closeAllModals() {
        document.querySelectorAll('.modal-overlay').forEach(m => m.classList.remove('active'));
    },

    // ==========================================
    // API & Data Loaders
    // ==========================================
    async loadSettings() {
        try {
            const res = await fetch('api.php?action=get_settings');
            const result = await res.json();
            if (result.success) {
                this.settings = result.data;
                this.currency = this.settings.currency || (this.lang === 'ar' ? 'ر.س' : 'SAR');
                this.taxRate = parseFloat(this.settings.default_tax_rate) || 15;

                // Populate Settings Form Fields
                for (const [key, val] of Object.entries(this.settings)) {
                    const input = document.getElementById(`setting_${key}`);
                    if (input) input.value = val;
                }

                // Update branding elements
                const brandNameEl = document.getElementById('sidebarBrandName');
                if (brandNameEl && this.settings.company_name) {
                    brandNameEl.innerText = this.settings.company_name;
                }
            }
        } catch (err) {
            console.error('Error loading settings', err);
        }
    },

    async saveSettings() {
        const form = document.getElementById('settingsForm');
        const formData = new FormData(form);
        formData.append('action', 'save_settings');

        try {
            const res = await fetch('api.php', {
                method: 'POST',
                body: formData
            });
            const result = await res.json();
            if (result.success) {
                this.showToast(this.t('toast_settings_saved'));
                this.loadSettings();
            } else {
                this.showToast(result.message || 'Error', 'error');
            }
        } catch (err) {
            this.showToast('Connection error', 'error');
        }
    },

    async loadDashboard() {
        try {
            const res = await fetch('api.php?action=get_dashboard_stats');
            const result = await res.json();
            if (result.success) {
                const d = result.data;
                document.getElementById('kpiTotalSales').innerText = `${this.formatMoney(d.total_sales)} ${this.currency}`;
                document.getElementById('kpiPaidTotal').innerText = `${this.formatMoney(d.paid_total)} ${this.currency}`;
                document.getElementById('kpiUnpaidTotal').innerText = `${this.formatMoney(d.unpaid_total)} ${this.currency}`;
                document.getElementById('kpiInvoiceCount').innerText = `${d.invoice_count} ${this.t('invoice_word')}`;

                // Render Recent Invoices Table
                const tbody = document.getElementById('recentInvoicesTableBody');
                if (tbody) {
                    if (!d.recent_invoices || d.recent_invoices.length === 0) {
                        tbody.innerHTML = `<tr><td colspan="6" style="text-align:center; padding:20px; color:#94a3b8;">${this.t('no_records')}</td></tr>`;
                    } else {
                        tbody.innerHTML = d.recent_invoices.map(inv => `
                            <tr>
                                <td><strong style="color:var(--primary); font-family:monospace;">${this.escapeHtml(inv.invoice_number)}</strong></td>
                                <td>${this.escapeHtml(inv.client_name || this.t('cash_client'))}</td>
                                <td>${inv.issue_date}</td>
                                <td><strong>${this.formatMoney(inv.grand_total)} ${this.currency}</strong></td>
                                <td>${this.renderStatusBadge(inv.status)}</td>
                                <td>
                                    <button class="btn btn-sm btn-light" onclick="App.viewInvoice(${inv.id})">${this.t('btn_view')}</button>
                                </td>
                            </tr>
                        `).join('');
                    }
                }
            }
        } catch (err) {
            console.error('Error loading dashboard', err);
        }
    },

    async loadInvoices() {
        const search = document.getElementById('invoiceSearchInput')?.value || '';
        const status = document.getElementById('statusFilterSelect')?.value || '';
        const fromDate = document.getElementById('dateFromFilter')?.value || '';
        const toDate = document.getElementById('dateToFilter')?.value || '';

        const params = new URLSearchParams({
            action: 'get_invoices',
            search: search,
            status: status,
            from_date: fromDate,
            to_date: toDate
        });

        try {
            const res = await fetch(`api.php?${params.toString()}`);
            const result = await res.json();
            if (result.success) {
                this.invoices = result.data;
                this.renderInvoicesTable();
            }
        } catch (err) {
            console.error('Error loading invoices', err);
        }
    },

    renderInvoicesTable() {
        const tbody = document.getElementById('invoicesTableBody');
        if (!tbody) return;

        if (this.invoices.length === 0) {
            tbody.innerHTML = `<tr><td colspan="8" style="text-align:center; padding:30px; color:#94a3b8;">${this.t('no_records')}</td></tr>`;
            return;
        }

        tbody.innerHTML = this.invoices.map(inv => `
            <tr>
                <td><strong style="color:var(--primary); font-family:monospace; font-size:14px;">${this.escapeHtml(inv.invoice_number)}</strong></td>
                <td>
                    <div style="font-weight:700;">${this.escapeHtml(inv.client_name || this.t('cash_client'))}</div>
                    ${inv.client_phone ? `<div style="font-size:12px; color:var(--text-muted);">${this.escapeHtml(inv.client_phone)}</div>` : ''}
                </td>
                <td>${inv.issue_date}</td>
                <td>${inv.due_date || '-'}</td>
                <td><strong style="color:var(--primary-dark);">${this.formatMoney(inv.grand_total)} ${this.currency}</strong></td>
                <td>
                    <div style="display:inline-block;" onclick="App.showQuickStatusMenu(event, ${inv.id}, '${inv.status}')">
                        ${this.renderStatusBadge(inv.status)}
                    </div>
                </td>
                <td>
                    <div class="actions-cell">
                        <button class="btn btn-sm btn-light" title="${this.t('btn_view')}" onclick="App.viewInvoice(${inv.id})">
                            <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <span>${this.t('btn_view')}</span>
                        </button>
                        <a href="print.php?id=${inv.id}&lang=${this.lang}" target="_blank" class="btn btn-sm btn-outline" title="${this.t('btn_print')}">
                            <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        </a>
                        <button class="btn btn-sm btn-outline" title="${this.t('btn_edit')}" onclick="App.editInvoice(${inv.id})">
                            <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </button>
                        <button class="btn btn-sm btn-outline" style="color:#ef4444;" title="${this.t('btn_delete')}" onclick="App.deleteInvoice(${inv.id})">
                            <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </div>
                </td>
            </tr>
        `).join('');
    },

    // ==========================================
    // Clients Directory
    // ==========================================
    async loadClients() {
        try {
            const res = await fetch('api.php?action=get_clients');
            const result = await res.json();
            if (result.success) {
                this.clients = result.data;
                this.renderClientsTable();
                this.populateClientsDropdown();
            }
        } catch (err) {
            console.error('Error loading clients', err);
        }
    },

    renderClientsTable() {
        const tbody = document.getElementById('clientsTableBody');
        if (!tbody) return;

        if (this.clients.length === 0) {
            tbody.innerHTML = `<tr><td colspan="6" style="text-align:center; padding:20px; color:#94a3b8;">${this.t('no_clients')}</td></tr>`;
            return;
        }

        tbody.innerHTML = this.clients.map(c => `
            <tr>
                <td><strong>${this.escapeHtml(c.name)}</strong></td>
                <td>${this.escapeHtml(c.phone || '-')}</td>
                <td>${this.escapeHtml(c.email || '-')}</td>
                <td>${this.escapeHtml(c.tax_number || '-')}</td>
                <td><span class="badge badge-paid">${c.invoice_count} ${this.t('invoice_word')}</span></td>
                <td>
                    <div class="actions-cell">
                        <button class="btn btn-sm btn-outline" onclick="App.editClient(${c.id})">${this.t('btn_edit')}</button>
                        <button class="btn btn-sm btn-outline" style="color:#ef4444;" onclick="App.deleteClient(${c.id})">${this.t('btn_delete')}</button>
                    </div>
                </td>
            </tr>
        `).join('');
    },

    populateClientsDropdown() {
        const select = document.getElementById('invoiceClientSelect');
        if (!select) return;
        const currentVal = select.value;
        const promptText = (this.lang === 'ar' ? '-- اختر عميلاً مسجلاً --' : '-- Select Registered Client --');
        select.innerHTML = `<option value="">${promptText}</option>` + 
            this.clients.map(c => `<option value="${c.id}">${this.escapeHtml(c.name)} (${c.phone || '-'})</option>`).join('');
        if (currentVal) select.value = currentVal;
    },

    openNewClientModal() {
        document.getElementById('clientIdInput').value = '';
        document.getElementById('clientNameInput').value = '';
        document.getElementById('clientPhoneInput').value = '';
        document.getElementById('clientEmailInput').value = '';
        document.getElementById('clientAddressInput').value = '';
        document.getElementById('clientTaxInput').value = '';
        document.getElementById('clientModalTitle').innerText = this.t('client_modal_title_new');
        this.openModal('clientModal');
    },

    editClient(id) {
        const client = this.clients.find(c => c.id == id);
        if (!client) return;
        document.getElementById('clientIdInput').value = client.id;
        document.getElementById('clientNameInput').value = client.name;
        document.getElementById('clientPhoneInput').value = client.phone || '';
        document.getElementById('clientEmailInput').value = client.email || '';
        document.getElementById('clientAddressInput').value = client.address || '';
        document.getElementById('clientTaxInput').value = client.tax_number || '';
        document.getElementById('clientModalTitle').innerText = this.t('client_modal_title_edit');
        this.openModal('clientModal');
    },

    async saveClient() {
        const form = document.getElementById('clientForm');
        const formData = new FormData(form);
        formData.append('action', 'save_client');

        try {
            const res = await fetch('api.php', {
                method: 'POST',
                body: formData
            });
            const result = await res.json();
            if (result.success) {
                this.showToast(this.t('toast_client_saved'));
                this.closeModal('clientModal');
                this.loadClients();
            } else {
                this.showToast(result.message || 'Error', 'error');
            }
        } catch (err) {
            this.showToast('Failed to save client', 'error');
        }
    },

    async deleteClient(id) {
        if (!confirm(this.t('confirm_delete_client'))) return;
        try {
            const res = await fetch('api.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'delete_client', id: id })
            });
            const result = await res.json();
            if (result.success) {
                this.showToast(this.t('toast_client_deleted'));
                this.loadClients();
            }
        } catch (err) {
            this.showToast('Failed to delete client', 'error');
        }
    },

    // ==========================================
    // Products & Services Catalog
    // ==========================================
    async loadProducts() {
        try {
            const res = await fetch('api.php?action=get_products');
            const result = await res.json();
            if (result.success) {
                this.products = result.data;
                this.renderProductsTable();
            }
        } catch (err) {
            console.error('Error loading products', err);
        }
    },

    renderProductsTable() {
        const tbody = document.getElementById('productsTableBody');
        if (!tbody) return;

        if (this.products.length === 0) {
            tbody.innerHTML = `<tr><td colspan="5" style="text-align:center; padding:20px; color:#94a3b8;">${this.t('no_products')}</td></tr>`;
            return;
        }

        tbody.innerHTML = this.products.map(p => `
            <tr>
                <td><strong>${this.escapeHtml(p.name)}</strong></td>
                <td>${this.escapeHtml(p.description || '-')}</td>
                <td><strong>${this.formatMoney(p.price)} ${this.currency}</strong></td>
                <td>${p.tax_rate}%</td>
                <td>
                    <div class="actions-cell">
                        <button class="btn btn-sm btn-outline" onclick="App.editProduct(${p.id})">${this.t('btn_edit')}</button>
                        <button class="btn btn-sm btn-outline" style="color:#ef4444;" onclick="App.deleteProduct(${p.id})">${this.t('btn_delete')}</button>
                    </div>
                </td>
            </tr>
        `).join('');
    },

    openNewProductModal() {
        document.getElementById('productIdInput').value = '';
        document.getElementById('productNameInput').value = '';
        document.getElementById('productPriceInput').value = '0';
        document.getElementById('productTaxInput').value = this.taxRate;
        document.getElementById('productDescInput').value = '';
        document.getElementById('productModalTitle').innerText = this.t('product_modal_title_new');
        this.openModal('productModal');
    },

    editProduct(id) {
        const prod = this.products.find(p => p.id == id);
        if (!prod) return;
        document.getElementById('productIdInput').value = prod.id;
        document.getElementById('productNameInput').value = prod.name;
        document.getElementById('productPriceInput').value = prod.price;
        document.getElementById('productTaxInput').value = prod.tax_rate;
        document.getElementById('productDescInput').value = prod.description || '';
        document.getElementById('productModalTitle').innerText = this.t('product_modal_title_edit');
        this.openModal('productModal');
    },

    async saveProduct() {
        const form = document.getElementById('productForm');
        const formData = new FormData(form);
        formData.append('action', 'save_product');

        try {
            const res = await fetch('api.php', {
                method: 'POST',
                body: formData
            });
            const result = await res.json();
            if (result.success) {
                this.showToast(this.t('toast_product_saved'));
                this.closeModal('productModal');
                this.loadProducts();
            } else {
                this.showToast(result.message || 'Error', 'error');
            }
        } catch (err) {
            this.showToast('Failed to save product', 'error');
        }
    },

    async deleteProduct(id) {
        if (!confirm(this.t('confirm_delete_product'))) return;
        try {
            const res = await fetch('api.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'delete_product', id: id })
            });
            const result = await res.json();
            if (result.success) {
                this.showToast(this.t('toast_product_deleted'));
                this.loadProducts();
            }
        } catch (err) {
            this.showToast('Failed to delete item', 'error');
        }
    },

    // ==========================================
    // Invoice Creation & Editing Form
    // ==========================================
    async openNewInvoiceModal() {
        this.editingInvoiceId = null;
        document.getElementById('invoiceFormTitle').innerText = (this.lang === 'ar' ? 'إنشاء فاتورة جديدة' : 'Create New Invoice');
        document.getElementById('invoiceIdInput').value = '';
        
        // Fetch next sequential number
        try {
            const res = await fetch('api.php?action=get_next_number');
            const result = await res.json();
            if (result.success) {
                document.getElementById('invoiceNumberInput').value = result.data.next_number;
            }
        } catch (e) {
            document.getElementById('invoiceNumberInput').value = 'INV-' + Date.now().toString().slice(-6);
        }

        // Set default dates
        const today = new Date().toISOString().split('T')[0];
        const next15Days = new Date(Date.now() + 15 * 86400000).toISOString().split('T')[0];
        document.getElementById('invoiceIssueDate').value = today;
        document.getElementById('invoiceDueDate').value = next15Days;

        // Reset selects
        document.getElementById('invoiceClientSelect').value = '';
        document.getElementById('invoiceStatusSelect').value = 'unpaid';
        document.getElementById('invoicePaymentMethod').value = 'bank_transfer';
        document.getElementById('invoiceNotesInput').value = this.settings.invoice_footer || (this.lang === 'ar' ? 'شكراً لتعاملكم معنا.' : 'Thank you for your business.');

        // Clear items table and add initial empty row
        const itemsBody = document.getElementById('invoiceItemsTableBody');
        itemsBody.innerHTML = '';
        this.addInvoiceItemRow();

        this.openModal('invoiceModal');
    },

    async editInvoice(id) {
        this.editingInvoiceId = id;
        document.getElementById('invoiceFormTitle').innerText = (this.lang === 'ar' ? 'تعديل الفاتورة' : 'Edit Invoice');

        try {
            const res = await fetch(`api.php?action=get_invoice&id=${id}`);
            const result = await res.json();
            if (result.success) {
                const inv = result.data;
                document.getElementById('invoiceIdInput').value = inv.id;
                document.getElementById('invoiceNumberInput').value = inv.invoice_number;
                document.getElementById('invoiceIssueDate').value = inv.issue_date;
                document.getElementById('invoiceDueDate').value = inv.due_date || '';
                document.getElementById('invoiceClientSelect').value = inv.client_id || '';
                document.getElementById('invoiceStatusSelect').value = inv.status;
                document.getElementById('invoicePaymentMethod').value = inv.payment_method;
                document.getElementById('invoiceNotesInput').value = inv.notes || '';

                // Load line items
                const itemsBody = document.getElementById('invoiceItemsTableBody');
                itemsBody.innerHTML = '';

                if (inv.items && inv.items.length > 0) {
                    inv.items.forEach(it => {
                        this.addInvoiceItemRow(it);
                    });
                } else {
                    this.addInvoiceItemRow();
                }

                this.calculateInvoiceTotals();
                this.openModal('invoiceModal');
            }
        } catch (err) {
            this.showToast('Failed to fetch invoice details', 'error');
        }
    },

    addInvoiceItemRow(data = null) {
        const tbody = document.getElementById('invoiceItemsTableBody');
        if (!tbody) return;

        const row = document.createElement('tr');
        const defaultTax = data ? data.tax_rate : this.taxRate;
        const defaultQty = data ? data.quantity : 1;
        const defaultPrice = data ? data.unit_price : 0;
        const defaultDiscount = data ? data.discount : 0;
        const defaultName = data ? data.item_name : '';
        const defaultDesc = data ? data.description : '';

        const presetPrompt = (this.lang === 'ar' ? '-- اختر من الدليل أو اكتب يدوياً --' : '-- Choose from catalog or type --');
        const namePlaceholder = (this.lang === 'ar' ? 'اسم البند / الخدمة *' : 'Item / Service name *');
        const descPlaceholder = (this.lang === 'ar' ? 'وصف إضافي (اختياري)' : 'Description (optional)');

        // Product presets options
        const productOptions = `<option value="">${presetPrompt}</option>` + 
            this.products.map(p => `<option value="${p.id}" data-name="${this.escapeHtml(p.name)}" data-price="${p.price}" data-tax="${p.tax_rate}" data-desc="${this.escapeHtml(p.description || '')}">${this.escapeHtml(p.name)} (${p.price} ${this.currency})</option>`).join('');

        row.innerHTML = `
            <td>
                <select class="form-control item-preset-select" style="margin-bottom: 4px; font-size:12px;">
                    ${productOptions}
                </select>
                <input type="text" class="form-control item-name-input" placeholder="${namePlaceholder}" value="${this.escapeHtml(defaultName)}" required>
                <input type="text" class="form-control item-desc-input" placeholder="${descPlaceholder}" value="${this.escapeHtml(defaultDesc)}" style="margin-top: 4px; font-size: 11.5px;">
            </td>
            <td>
                <input type="number" step="0.01" min="0.01" class="form-control item-qty-input" value="${defaultQty}" style="text-align:center;">
            </td>
            <td>
                <input type="number" step="0.01" min="0" class="form-control item-price-input" value="${defaultPrice}">
            </td>
            <td>
                <input type="number" step="0.01" min="0" class="form-control item-disc-input" value="${defaultDiscount}">
            </td>
            <td>
                <input type="number" step="0.01" min="0" class="form-control item-tax-input" value="${defaultTax}" style="text-align:center;">
            </td>
            <td style="font-weight: 700; color: var(--primary-dark); text-align: center;">
                <span class="item-line-total">0.00</span>
            </td>
            <td style="text-align: center;">
                <button type="button" class="btn btn-sm btn-outline remove-row-btn" style="color:#ef4444;" title="${this.t('btn_delete')}">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </td>
        `;

        tbody.appendChild(row);

        // Bind Row Events
        const presetSelect = row.querySelector('.item-preset-select');
        const nameInput = row.querySelector('.item-name-input');
        const descInput = row.querySelector('.item-desc-input');
        const qtyInput = row.querySelector('.item-qty-input');
        const priceInput = row.querySelector('.item-price-input');
        const discInput = row.querySelector('.item-disc-input');
        const taxInput = row.querySelector('.item-tax-input');
        const removeBtn = row.querySelector('.remove-row-btn');

        // Preset select autofill
        presetSelect.addEventListener('change', () => {
            const opt = presetSelect.selectedOptions[0];
            if (opt && opt.value) {
                nameInput.value = opt.getAttribute('data-name');
                priceInput.value = opt.getAttribute('data-price');
                taxInput.value = opt.getAttribute('data-tax');
                descInput.value = opt.getAttribute('data-desc');
                this.calculateInvoiceTotals();
            }
        });

        // Numeric change listeners
        [qtyInput, priceInput, discInput, taxInput].forEach(inp => {
            inp.addEventListener('input', () => this.calculateInvoiceTotals());
        });

        // Remove row
        removeBtn.addEventListener('click', () => {
            if (tbody.querySelectorAll('tr').length > 1) {
                row.remove();
                this.calculateInvoiceTotals();
            } else {
                this.showToast(this.t('toast_item_required'), 'error');
            }
        });

        this.calculateInvoiceTotals();
    },

    calculateInvoiceTotals() {
        const tbody = document.getElementById('invoiceItemsTableBody');
        if (!tbody) return;

        let grandSubtotal = 0;
        let grandDiscount = 0;
        let grandTax = 0;

        tbody.querySelectorAll('tr').forEach(row => {
            const qty = parseFloat(row.querySelector('.item-qty-input')?.value) || 0;
            const price = parseFloat(row.querySelector('.item-price-input')?.value) || 0;
            const discount = parseFloat(row.querySelector('.item-disc-input')?.value) || 0;
            const taxRate = parseFloat(row.querySelector('.item-tax-input')?.value) || 0;

            const lineSub = qty * price;
            const lineDisc = Math.min(lineSub, discount);
            const taxable = Math.max(0, lineSub - lineDisc);
            const lineTax = (taxable * taxRate) / 100;
            const lineTotal = taxable + lineTax;

            const totalSpan = row.querySelector('.item-line-total');
            if (totalSpan) totalSpan.innerText = lineTotal.toFixed(2);

            grandSubtotal += lineSub;
            grandDiscount += lineDisc;
            grandTax += lineTax;
        });

        const grandFinal = grandSubtotal - grandDiscount + grandTax;

        // Update Summary Display
        document.getElementById('calcSubtotal').innerText = `${grandSubtotal.toFixed(2)} ${this.currency}`;
        document.getElementById('calcDiscount').innerText = `${grandDiscount.toFixed(2)} ${this.currency}`;
        document.getElementById('calcTax').innerText = `${grandTax.toFixed(2)} ${this.currency}`;
        document.getElementById('calcGrandTotal').innerText = `${grandFinal.toFixed(2)} ${this.currency}`;
    },

    async saveInvoice() {
        const id = document.getElementById('invoiceIdInput').value;
        const invoiceNumber = document.getElementById('invoiceNumberInput').value;
        const clientId = document.getElementById('invoiceClientSelect').value;
        const issueDate = document.getElementById('invoiceIssueDate').value;
        const dueDate = document.getElementById('invoiceDueDate').value;
        const status = document.getElementById('invoiceStatusSelect').value;
        const paymentMethod = document.getElementById('invoicePaymentMethod').value;
        const notes = document.getElementById('invoiceNotesInput').value;

        // Collect Items
        const items = [];
        const tbody = document.getElementById('invoiceItemsTableBody');
        tbody.querySelectorAll('tr').forEach(row => {
            const name = row.querySelector('.item-name-input')?.value.trim();
            if (name) {
                items.push({
                    item_name: name,
                    description: row.querySelector('.item-desc-input')?.value.trim() || '',
                    quantity: parseFloat(row.querySelector('.item-qty-input')?.value) || 1,
                    unit_price: parseFloat(row.querySelector('.item-price-input')?.value) || 0,
                    discount: parseFloat(row.querySelector('.item-disc-input')?.value) || 0,
                    tax_rate: parseFloat(row.querySelector('.item-tax-input')?.value) || 15,
                });
            }
        });

        if (items.length === 0) {
            this.showToast(this.t('toast_item_required'), 'error');
            return;
        }

        const payload = {
            action: 'save_invoice',
            id: id,
            invoice_number: invoiceNumber,
            client_id: clientId,
            issue_date: issueDate,
            due_date: dueDate,
            status: status,
            payment_method: paymentMethod,
            notes: notes,
            items: items
        };

        try {
            const res = await fetch('api.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });
            const result = await res.json();
            if (result.success) {
                this.showToast(this.t('toast_invoice_saved'));
                this.closeModal('invoiceModal');
                this.loadInvoices();
                this.loadDashboard();
            } else {
                this.showToast(result.message || 'Save error', 'error');
            }
        } catch (err) {
            this.showToast('Connection error', 'error');
        }
    },

    async deleteInvoice(id) {
        if (!confirm(this.t('confirm_delete_invoice'))) return;
        try {
            const res = await fetch('api.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'delete_invoice', id: id })
            });
            const result = await res.json();
            if (result.success) {
                this.showToast(this.t('toast_invoice_deleted'));
                this.loadInvoices();
                this.loadDashboard();
            }
        } catch (err) {
            this.showToast('Failed to delete invoice', 'error');
        }
    },

    // ==========================================
    // Quick View Modal & WhatsApp Share
    // ==========================================
    async viewInvoice(id) {
        try {
            const res = await fetch(`api.php?action=get_invoice&id=${id}`);
            const result = await res.json();
            if (result.success) {
                const inv = result.data;
                const container = document.getElementById('viewInvoiceModalContent');
                
                // WhatsApp share message text
                const greeting = (this.lang === 'ar' ? 'مرحباً' : 'Hello');
                const invoiceMsg = (this.lang === 'ar' ? 'نرفق لكم تفاصيل الفاتورة رقم' : 'Here are the details for invoice #');
                const totalMsg = (this.lang === 'ar' ? 'المبلغ الإجمالي' : 'Total Amount');
                const statusMsg = (this.lang === 'ar' ? 'الحالة' : 'Status');
                const thanksMsg = (this.lang === 'ar' ? 'شكراً لتعاملكم معنا.' : 'Thank you for your business.');

                const waText = encodeURIComponent(
                    `${greeting} ${inv.client_name || this.t('cash_client')},\n` +
                    `${invoiceMsg}: ${inv.invoice_number}\n` +
                    `${totalMsg}: ${this.formatMoney(inv.grand_total)} ${this.currency}\n` +
                    `${statusMsg}: ${this.getStatusLabel(inv.status)}\n` +
                    `${thanksMsg}`
                );

                container.innerHTML = `
                    <div style="border-bottom: 2px solid var(--primary-border); padding-bottom: 16px; margin-bottom: 20px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
                        <div>
                            <h2 style="color:var(--primary-dark); font-size:20px; font-weight:800;">${this.t('invoice_word')} #${this.escapeHtml(inv.invoice_number)}</h2>
                            <p style="color:var(--text-muted); font-size:12.5px;">${this.t('th_issue_date')}: ${inv.issue_date} ${inv.due_date ? `| ${this.t('th_due_date')}: ${inv.due_date}` : ''}</p>
                        </div>
                        <div>
                            ${this.renderStatusBadge(inv.status)}
                        </div>
                    </div>

                    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap:16px; margin-bottom:20px; background:var(--bg-muted); padding:16px; border-radius:8px;">
                        <div>
                            <span style="font-size:12px; color:var(--text-muted);">${this.t('th_client')}:</span>
                            <div style="font-weight:700;">${this.escapeHtml(inv.client_name || this.t('cash_client'))}</div>
                            <div style="font-size:12px;">${this.escapeHtml(inv.client_phone || '')}</div>
                        </div>
                        <div>
                            <span style="font-size:12px; color:var(--text-muted);">${this.t('lbl_payment_method')}:</span>
                            <div style="font-weight:700;">${this.getPaymentMethodLabel(inv.payment_method)}</div>
                        </div>
                        <div>
                            <span style="font-size:12px; color:var(--text-muted);">${this.t('th_total')}:</span>
                            <div style="font-size:18px; font-weight:800; color:var(--primary);">${this.formatMoney(inv.grand_total)} ${this.currency}</div>
                        </div>
                    </div>

                    <div class="table-container" style="margin-bottom:20px;">
                        <table class="data-table" style="min-width:100%;">
                            <thead>
                                <tr>
                                    <th>${this.t('th_item_name')}</th>
                                    <th style="text-align:center;">${this.t('th_quantity')}</th>
                                    <th>${this.t('th_unit_price')}</th>
                                    <th>${this.t('th_discount')}</th>
                                    <th>${this.t('th_total')}</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${(inv.items || []).map(it => `
                                    <tr>
                                        <td>
                                            <strong>${this.escapeHtml(it.item_name)}</strong>
                                            ${it.description ? `<div style="font-size:11.5px; color:var(--text-muted);">${this.escapeHtml(it.description)}</div>` : ''}
                                        </td>
                                        <td style="text-align:center;">${it.quantity}</td>
                                        <td>${this.formatMoney(it.unit_price)} ${this.currency}</td>
                                        <td>${this.formatMoney(it.discount)} ${this.currency}</td>
                                        <td><strong>${this.formatMoney(it.total)} ${this.currency}</strong></td>
                                    </tr>
                                `).join('')}
                            </tbody>
                        </table>
                    </div>

                    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; border-top:1px solid var(--border); padding-top:16px;">
                        <div style="display:flex; gap:8px;">
                            <a href="print.php?id=${inv.id}&lang=${this.lang}" target="_blank" class="btn btn-primary">
                                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                ${this.t('btn_print_official')}
                            </a>
                            <a href="https://api.whatsapp.com/send?text=${waText}${inv.client_phone ? `&phone=${encodeURIComponent(inv.client_phone.replace(/[^0-9]/g, ''))}` : ''}" target="_blank" class="btn btn-outline" style="color:#059669; border-color:#059669;">
                                <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.711 2.598 2.669-.699c.969.529 1.834.814 2.791.814 3.179 0 5.767-2.587 5.768-5.766 0-3.18-2.587-5.768-5.768-5.768zm0 10.375c-.868 0-1.688-.242-2.392-.682l-.171-.107-1.776.466.474-1.73-.112-.178c-.482-.767-.736-1.558-.735-2.378 0-2.534 2.062-4.596 4.601-4.596 2.536 0 4.598 2.062 4.598 4.596 0 2.535-2.062 4.597-4.601 4.597z"/></svg>
                                ${this.t('btn_whatsapp')}
                            </a>
                        </div>
                        <button class="btn btn-outline" onclick="App.closeModal('viewInvoiceModal')">${this.t('btn_close')}</button>
                    </div>
                `;

                this.openModal('viewInvoiceModal');
            }
        } catch (err) {
            this.showToast('Failed to view invoice', 'error');
        }
    },

    // Quick Status Change Popup
    showQuickStatusMenu(event, id, currentStatus) {
        event.stopPropagation();
        const promptMsg = (this.lang === 'ar' 
            ? 'تغيير حالة الفاتورة:\nاكتب أحد الخيارات: (paid / unpaid / pending / cancelled)' 
            : 'Change invoice status:\nType one: (paid / unpaid / pending / cancelled)');
        const newStatus = prompt(promptMsg, currentStatus);
        if (newStatus && ['paid', 'unpaid', 'pending', 'cancelled'].includes(newStatus.toLowerCase())) {
            this.updateInvoiceStatus(id, newStatus.toLowerCase());
        }
    },

    async updateInvoiceStatus(id, status) {
        try {
            const res = await fetch('api.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'update_invoice_status', id: id, status: status })
            });
            const result = await res.json();
            if (result.success) {
                this.showToast(this.t('toast_status_updated'));
                this.loadInvoices();
                this.loadDashboard();
            }
        } catch (err) {
            this.showToast('Failed to update status', 'error');
        }
    },

    // Export Invoices to CSV (with UTF-8 BOM for Arabic support)
    exportInvoicesToCSV() {
        if (!this.invoices || this.invoices.length === 0) {
            this.showToast(this.t('no_records'), 'error');
            return;
        }

        let csv = '\uFEFF'; // UTF-8 BOM
        const header = (this.lang === 'ar' 
            ? 'رقم الفاتورة,العميل,الهاتف,تاريخ الإصدار,تاريخ الاستحقاق,المجموع الفرعي,الخصم,الضريبة,المجموع الكلي,الحالة\n' 
            : 'Invoice Number,Client,Phone,Issue Date,Due Date,Subtotal,Discount,Tax,Grand Total,Status\n');
        csv += header;

        this.invoices.forEach(i => {
            const row = [
                `"${i.invoice_number}"`,
                `"${i.client_name || this.t('cash_client')}"`,
                `"${i.client_phone || ''}"`,
                `"${i.issue_date}"`,
                `"${i.due_date || ''}"`,
                i.subtotal,
                i.discount,
                i.tax_amount,
                i.grand_total,
                `"${this.getStatusLabel(i.status)}"`
            ];
            csv += row.join(',') + '\n';
        });

        const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        const filenamePrefix = (this.lang === 'ar' ? 'سجل_الفواتير' : 'Invoices_Record');
        a.download = `${filenamePrefix}_${new Date().toISOString().split('T')[0]}.csv`;
        a.click();
        URL.revokeObjectURL(url);
    },

    // Helper functions
    formatMoney(val) {
        return parseFloat(val || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    },

    escapeHtml(str) {
        if (!str) return '';
        const div = document.createElement('div');
        div.innerText = str;
        return div.innerHTML;
    },

    renderStatusBadge(status) {
        const labels = {
            paid: { text: this.t('status_paid'), class: 'badge-paid' },
            unpaid: { text: this.t('status_unpaid'), class: 'badge-unpaid' },
            pending: { text: this.t('status_pending'), class: 'badge-pending' },
            cancelled: { text: this.t('status_cancelled'), class: 'badge-cancelled' },
        };
        const st = labels[status] || { text: status, class: '' };
        return `<span class="badge ${st.class}">${st.text}</span>`;
    },

    getStatusLabel(status) {
        return this.t(`status_${status}`) || status;
    },

    getPaymentMethodLabel(method) {
        return this.t(`method_${method === 'bank_transfer' ? 'bank' : method}`) || method;
    }
};

// Initialize on DOM Ready
document.addEventListener('DOMContentLoaded', () => {
    App.init();
});
