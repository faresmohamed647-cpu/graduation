(() => {
    const storageKey = 'lang_school_admin';
    const defaultLang = 'en';

    const phraseMap = {
        'School Dashboard - SafeStep Bus': 'لوحة المدرسة - SafeStep Bus',
        'School Portal': 'بوابة المدرسة',
        'School Dashboard': 'لوحة المدرسة',

        // Schools
        'Al-Azhar International School': 'مدرسة الأزهر الدولية',
        'Cairo American College': 'كلية القاهرة الأمريكية',
        'British International School Cairo': 'المدرسة البريطانية الدولية بالقاهرة',

        // Routes
        'Smouha to Sidi Gaber': 'سموحة إلى سيدي جابر',
        'Sidi Bishr to Fleming': 'سيدي بشر إلى فليمنج',
        'Sporting to Stanley': 'سبورتنج إلى ستانلي',
        'Agami to Mansheya': 'العجمي إلى المنشية',

        // Drivers
        'Ahmed Khaled': 'أحمد خالد',
        'Mohamed Samir': 'محمد سمير',
        'Hassan Ibrahim': 'حسن إبراهيم',

        // Parents
        'Sara Ahmed': 'سارة أحمد',
        'Mohamed Hassan': 'محمد حسن',
        'Fatma Ali': 'فاطمة علي',
        'Hana Mostafa': 'هناء مصطفى',
        'Amira Khaled': 'أميرة خالد',

        // Students
        'Youssef Ahmed': 'يوسف أحمد',
        'Malak Ahmed': 'ملاك أحمد',
        'Omar Hassan': 'عمر حسن',
        'Nour Hassan': 'نور حسن',
        'Adam Hassan': 'آدم حسن',
        'Ali Mohamed': 'علي محمد',
        'Farida Mohamed': 'فريدة محمد',
        'Ziad Mostafa': 'زياد مصطفى',
        'Salma Mostafa': 'سلمى مصطفى',
        'Hamza Khaled': 'حمزة خالد',
        'Jana Khaled': 'جنى خالد',
        'Kareem Khaled': 'كريم خالد',

        // Grades
        'Grade 1': 'الصف الأول',
        'Grade 2': 'الصف الثاني',
        'Grade 3': 'الصف الثالث',
        'Grade 4': 'الصف الرابع',
        'Grade 5': 'الصف الخامس',
        'Grade 6': 'الصف السادس',
        'Grade 7': 'الصف السابع',
        'Grade 8': 'الصف الثامن',
        'Grade 9': 'الصف التاسع',
        'Grade 10': 'الصف العاشر',

        // Years
        '8 years': '٨ سنوات',
        '5 years': '٥ سنوات',
        '12 years': '١٢ سنة',
        'Parent Management': 'إدارة أولياء الأمور',
        'Student Management': 'إدارة الطلاب',
        'Bus Management': 'إدارة الحافلات',
        'Driver Management': 'إدارة السائقين',
        'Route Management': 'إدارة المسارات',
        'Trip Monitoring': 'مراقبة الرحلات',
        'Live Bus Tracking': 'تتبع الحافلات المباشر',
        'Attendance Management': 'إدارة الحضور',
        'Parent Communication': 'التواصل مع أولياء الأمور',
        'Emergency Center': 'مركز الطوارئ',
        'Reports & Analytics': 'التقارير والتحليلات',
        'School Settings': 'إعدادات المدرسة',
        'Activity Logs': 'سجل النشاط',
        'Logout': 'تسجيل الخروج',
        'Dashboard': 'لوحة التحكم',
        'Parents': 'أولياء الأمور',
        'Students': 'الطلاب',
        'Buses': 'الحافلات',
        'Drivers': 'السائقين',
        'Routes': 'المسارات',
        'Communication': 'التواصل',
        'Settings': 'الإعدادات',
        'Total Students': 'إجمالي الطلاب',
        'Active Students': 'الطلاب النشطون',
        'Total Drivers': 'إجمالي السائقين',
        'Total Buses': 'إجمالي الحافلات',
        'Active Trips': 'الرحلات النشطة',
        "Today's Attendance": 'حضور اليوم',
        "Today's Absence": 'غياب اليوم',
        'Emergency Alerts': 'تنبيهات الطوارئ',
        'Dark': 'داكن',
        'Light': 'فاتح',
        'Search students, parents, buses...': 'ابحث عن طلاب أو أولياء أمور أو حافلات...',

        // Stat Card Trends
        'Linked to parents': 'مرتبط بأولياء الأمور',
        'On fleet routes': 'على مسارات الأسطول',
        'Assigned to buses': 'معينين لحافلات',
        'Active routes': 'مسارات نشطة',

        // Chart & KPI Headers
        'Student Attendance Trends': 'اتجاهات حضور الطلاب',
        'Weekly Trips Analysis': 'تحليل الرحلات الأسبوعي',
        'Bus Usage Statistics': 'إحصائيات استخدام الحافلات',
        'Monthly Safety Reports': 'تقارير السلامة الشهرية',
        'School Performance KPIs': 'مؤشرات الأداء للمدرسة',
        'At-Risk Students': 'الطلاب المعرضون للغياب',

        // Table Headers
        'Student': 'الطالب',
        'Grade': 'الصف',
        'Absent Rate': 'نسبة الغياب',
        'Risk': 'مستوى الخطورة',
        'Name': 'الاسم',
        'Email': 'البريد الإلكتروني',
        'Phone': 'الهاتف',
        'Children': 'الأبناء',
        'Status': 'الحالة',
        'Actions': 'الإجراءات',
        'Parent': 'ولي الأمر',
        'Bus': 'الحافلة',
        'Route': 'المسار',
        'Bus #': 'رقم الحافلة',
        'Plate': 'اللوحة',
        'Capacity': 'السعة',
        'Driver': 'السائق',
        'Insurance': 'التأمين',
        'Type': 'النوع',
        'Stops': 'المحطات',
        'Duration': 'المدة',
        'Distance': 'المسافة',
        'Date': 'التاريخ',
        'Shift': 'الوردية',
        'Speed': 'السرعة',
        'Last Update': 'آخر تحديث',
        'Pickup': 'الركوب',
        'Drop-off': 'النزول',
        'Severity': 'الخطورة',
        'Message': 'الرسالة',
        'Action': 'النشاط',
        'User': 'المستخدم',
        'Entity': 'الكيان',

        // Buttons & Actions
        'Add Student': 'إضافة طالب',
        'Add Bus': 'إضافة حافلة',
        'Create Route': 'إنشاء مسار',
        'Schedule Trip': 'جدولة رحلة',
        'Broadcast to Parents': 'إرسال تعميم لأولياء الأمور',
        'Report Emergency': 'الإبلاغ عن حالة طوارئ',
        'Load': 'تحميل',
        'Export CSV': 'تصدير CSV',
        'Export Excel': 'تصدير Excel',
        'Export PDF': 'تصدير PDF',
        'Save School Profile': 'حفظ ملف المدرسة',
        'Save Profile': 'حفظ الملف الشخصي',
        'Profile': 'الملف الشخصي',
        'View': 'عرض',
        'QR': 'كود QR',
        'Delete': 'حذف',
        'Edit': 'تعديل',
        'Map': 'الخريطة',
        'Resolve': 'حل المشكلة',
        'Report': 'إبلاغ',
        'Save': 'حفظ',
        'Cancel': 'إلغاء',
        'Broadcast': 'تعميم',

        // Settings Labels
        'School Profile': 'الملف التعريفي للمدرسة',
        'School Name': 'اسم المدرسة',
        'Principal': 'المدير',
        'Address': 'العنوان',
        'Admin Profile': 'الملف التعريفي للمشرف',

        // Navigation Menu
        'Trip Monitoring': 'مراقبة الرحلات',
        'Live Tracking': 'تتبع مباشر',
        'Attendance': 'الحضور والغياب',
        'Emergency Center': 'مركز الطوارئ',
        'Reports': 'التقارير',
        'Activity Logs': 'سجل النشاط',

        // Parent Communication Card
        'Title': 'العنوان',
        'Announcement title': 'عنوان الإعلان',
        'Announcement': 'إعلان للمدرسة',
        'General': 'عام',
        'Delay Alert': 'تنبيه تأخير',
        'Emergency': 'طوارئ',

        // KPIs
        'Attendance Rate': 'نسبة الحضور',
        'Fleet Utilization': 'استخدام الأسطول',
        'On-Time Trips': 'الرحلات المنضبطة',
        'Safety Score': 'تقييم السلامة',
    };

    const wordMap = {
        'School': 'مدرسة',
        'Dashboard': 'لوحة التحكم',
        'Portal': 'البوابة',
        'Parent': 'ولي الأمر',
        'Parents': 'أولياء الأمور',
        'Student': 'طالب',
        'Students': 'الطلاب',
        'Bus': 'الحافلة',
        'Buses': 'الحافلات',
        'Driver': 'السائق',
        'Drivers': 'السائقين',
        'Route': 'مسار',
        'Routes': 'المسارات',
        'Trip': 'رحلة',
        'Trips': 'الرحلات',
        'Tracking': 'تتبع',
        'Attendance': 'الحضور والغياب',
        'Emergency': 'طوارئ',
        'Logs': 'السجلات',
        'Settings': 'الإعدادات',
        'Logout': 'تسجيل الخروج',
        'Active': 'نشط',
        'Inactive': 'غير نشط',
        'Pending': 'معلق',
        'Approved': 'موافق عليه',
        'Completed': 'مكتمل',
        'On-time': 'في الوقت المحدد',
        'Late': 'متأخر',
        'Absent': 'غائب',
        'Present': 'حاضر',
        'Total': 'إجمالي',
        'Weekly': 'أسبوعي',
        'Monthly': 'شهري',
        'Safety': 'السلامة',
        'Performance': 'الأداء',
        'KPIs': 'مؤشرات الأداء',
        'Risk': 'مستوى الخطورة',
        'Grade': 'الصف',
        'Rate': 'معدل',
        'Name': 'الاسم',
        'Email': 'البريد الإلكتروني',
        'Phone': 'الهاتف',
        'Plate': 'اللوحة',
        'Capacity': 'السعة',
        'Action': 'الإجراء',
        'Actions': 'الإجراءات',
        'Status': 'الحالة',
        'Save': 'حفظ',
        'Cancel': 'إلغاء',
        'Broadcast': 'تعميم',
        'Report': 'إبلاغ',
        'Search': 'بحث',
        'Dark': 'داكن',
        'Light': 'فاتح'
    };

    const attrNames = ['placeholder', 'title', 'aria-label'];
    const originalTextMap = new WeakMap();
    const originalAttrMap = new WeakMap();
    let currentLang = defaultLang;
    let observer = null;

    const patternRules = [
        { regex: /(\d+)\s+minutes?\s+ago/i, replace: 'منذ $1 دقيقة' },
        { regex: /(\d+)\s+hours?\s+ago/i, replace: 'منذ $1 ساعة' },
        { regex: /Today at\s+([0-9:]+\s*(?:AM|PM))/i, replace: 'اليوم الساعة $1' },
        { regex: /(\d+(?:\.\d+)?)\s*km\s*away/i, replace: 'يبعد $1 كم' },
        { regex: /(\d+(?:\.\d+)?)\s*km\/h/i, replace: '$1 كم/س' },
        { regex: /(\d+)%\s*Complete/i, replace: 'مكتمل $1%' }
    ];

    function applyPatterns(text) {
        let out = text;
        patternRules.forEach(rule => {
            out = out.replace(rule.regex, rule.replace);
        });
        return out;
    }

    function translateWords(text) {
        return text.replace(/\b[A-Za-z][A-Za-z\-\/]*\b/g, (match) => {
            const key = match.trim();
            const lower = key.toLowerCase();
            const capitalized = lower.charAt(0).toUpperCase() + lower.slice(1);
            const mapped = wordMap[key] || wordMap[lower] || wordMap[capitalized] || wordMap[key.toUpperCase()];
            return mapped || match;
        });
    }

    function translateString(text, lang) {
        if (lang !== 'ar') return text;
        const trimmed = text.trim();
        if (!trimmed) return text;
        if (phraseMap[trimmed]) {
            return text.replace(trimmed, phraseMap[trimmed]);
        }
        const withPatterns = applyPatterns(text);
        const afterPhrase = phraseMap[withPatterns.trim()] || withPatterns;
        return translateWords(afterPhrase);
    }

    function applyTextNode(node) {
        if (!node || !node.nodeValue) return;
        const parent = node.parentElement;
        if (!parent) return;
        const tag = parent.tagName;
        if (tag === 'SCRIPT' || tag === 'STYLE' || tag === 'NOSCRIPT') return;

        if (!originalTextMap.has(node)) {
            originalTextMap.set(node, node.nodeValue);
        }

        if (currentLang === 'ar') {
            const original = originalTextMap.get(node) || node.nodeValue;
            node.nodeValue = translateString(original, 'ar');
        } else {
            node.nodeValue = originalTextMap.get(node) || node.nodeValue;
        }
    }

    function applyAttributes(el) {
        if (!el || !el.getAttribute) return;
        if (!originalAttrMap.has(el)) originalAttrMap.set(el, {});
        const originalAttrs = originalAttrMap.get(el);

        attrNames.forEach(attr => {
            const value = el.getAttribute(attr);
            if (!value) return;
            if (!(attr in originalAttrs)) originalAttrs[attr] = value;
            if (currentLang === 'ar') {
                el.setAttribute(attr, translateString(originalAttrs[attr], 'ar'));
            } else {
                el.setAttribute(attr, originalAttrs[attr]);
            }
        });
    }

    const NodeFilterRef = window.NodeFilter || { SHOW_TEXT: 4, FILTER_ACCEPT: 1, FILTER_REJECT: 2 };

    function applyTree(root) {
        if (!root) return;
        if (root.nodeType === 3) {
            applyTextNode(root);
            return;
        }
        if (root.nodeType === 1) {
            applyAttributes(root);
        }

        const walker = document.createTreeWalker(root, NodeFilterRef.SHOW_TEXT, {
            acceptNode(node) {
                if (!node.nodeValue || !node.nodeValue.trim()) return NodeFilterRef.FILTER_REJECT;
                return NodeFilterRef.FILTER_ACCEPT;
            }
        });

        let node = walker.nextNode();
        while (node) {
            applyTextNode(node);
            node = walker.nextNode();
        }

        if (root.querySelectorAll) {
            root.querySelectorAll('[placeholder],[title],[aria-label]').forEach(applyAttributes);
        }
    }

    function updateToggleLabel() {
        const toggle = document.getElementById('langToggle');
        if (!toggle) return;
        const label = currentLang === 'ar' ? 'EN' : 'AR';
        const span = toggle.querySelector('span');
        if (span) span.textContent = label;
        toggle.setAttribute('aria-label', currentLang === 'ar' ? 'Switch language to English' : 'Switch language to Arabic');
    }

    function applyLanguage(lang) {
        currentLang = lang;
        document.documentElement.lang = lang;
        // Keep document direction LTR to prevent layout mirroring, matching the Admin Dashboard translation behavior.
        // document.documentElement.dir = lang === 'ar' ? 'rtl' : 'ltr';

        if (!document.documentElement.dataset.i18nTitle) {
            document.documentElement.dataset.i18nTitle = document.title;
        }
        document.title = currentLang === 'ar'
            ? translateString(document.documentElement.dataset.i18nTitle, 'ar')
            : document.documentElement.dataset.i18nTitle;
        if (!document.body) return;
        applyTree(document.body);
        updateToggleLabel();

        // Notify tracking maps to resize/update center if map loaded
        if (window.SchoolAdmin && typeof window.dispatchEvent === 'function') {
            window.dispatchEvent(new Event('resize'));
        }
    }

    function observeChanges() {
        if (observer) return;
        observer = new MutationObserver((mutations) => {
            if (currentLang !== 'ar') return;
            mutations.forEach(mutation => {
                if (mutation.type === 'childList') {
                    mutation.addedNodes.forEach(node => applyTree(node));
                }
            });
        });
        observer.observe(document.body, { childList: true, subtree: true });
    }

    const safeStorage = {
        get(key) {
            try { return localStorage.getItem(key); } catch { return null; }
        },
        set(key, value) {
            try { localStorage.setItem(key, value); } catch { /* ignore */ }
        }
    };

    function toggleLanguage() {
        const nextLang = currentLang === 'ar' ? 'en' : 'ar';
        safeStorage.set(storageKey, nextLang);
        applyLanguage(nextLang);
    }

    function bindToggle() {
        const toggle = document.getElementById('langToggle');
        if (toggle && !toggle.dataset.langBound) {
            toggle.addEventListener('click', toggleLanguage);
            toggle.dataset.langBound = 'true';
        }
    }

    function init() {
        const saved = safeStorage.get(storageKey);
        const initial = saved || defaultLang;
        applyLanguage(initial);
        observeChanges();

        bindToggle();
        setTimeout(bindToggle, 50);
        setTimeout(bindToggle, 200);
    }

    window.toggleLanguage = toggleLanguage;

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
