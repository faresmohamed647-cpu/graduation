(() => {
    const storageKey = 'lang_parent';
    const defaultLang = 'en';

    const phraseMap = {
        'Parent Dashboard - School Bus Tracking': 'لوحة ولي الأمر - تتبع الحافلات المدرسية',
        'Skip to content': 'تخطي إلى المحتوى',
        'Live Tracking': 'التتبع المباشر',
        'Trip History': 'سجل الرحلات',
        'Emergency Alerts': 'تنبيهات الطوارئ',
        'Profile & Settings': 'الملف الشخصي والإعدادات',
        'Parent Requests': 'طلبات أولياء الأمور',
        'تقديم طلب': 'طلب ولي أمر',
        'Bus Status': 'حالة الحافلة',
        'On the Way': 'في الطريق',
        'Estimated Arrival Time': 'وقت الوصول المتوقع',
        'Driver Information': 'معلومات السائق',
        'Call Driver': 'اتصال بالسائق',
        'Total Children': 'إجمالي الأطفال',
        'Today Attendance': 'حضور اليوم',
        'Bus Speed (km/h)': 'سرعة الحافلة (كم/س)',
        'Total Distance': 'إجمالي المسافة',
        'Recent Activity': 'النشاط الأخير',
        'picked up': 'تم الصعود',
        'Bus departed from terminal': 'انطلقت الحافلة من المحطة',
        'Bus is on the way': 'الحافلة في الطريق',
        'Map Loading...': 'جاري تحميل الخريطة...',
        'Last seen: --': 'آخر ظهور: --',
        'Follow': 'متابعة',
        'Locate Child': 'تحديد موقع الطفل',
        'Center Bus': 'توسيط الحافلة',
        'Share': 'مشاركة',
        'Contact Driver': 'تواصل مع السائق',
        'Share Location': 'مشاركة الموقع',
        'Test Notification': 'تنبيه تجريبي',
        'Attendance Records': 'سجلات الحضور',
        'Overall Attendance': 'إجمالي الحضور',
        'Days Present': 'أيام الحضور',
        'Days Absent': 'أيام الغياب',
        'Missed Pickups': 'مواعيد صعود فائتة',
        'Missed Drop-offs': 'مواعيد نزول فائتة',
        'Payment History': 'سجل المدفوعات',
        'Make Payment': 'إجراء الدفع',
        'Total Paid': 'إجمالي المدفوعات',
        'Pending Payment': 'دفعة معلقة',
        'Export Excel': 'تصدير إكسل',
        'Export PDF': 'تصدير PDF',
        'Export': 'تصدير',
        'Prev': 'السابق',
        'Next': 'التالي',
        'Children': 'الأطفال',
        'Attendance': 'الحضور',
        'Notifications': 'الإشعارات',
        'Payments': 'المدفوعات',
        'Support': 'الدعم',
        'Dashboard': 'لوحة التحكم',
        'Logout': 'تسجيل الخروج',
        'Dark': 'داكن',
        'Your application is under review': 'طلبك قيد المراجعة والتدقيق',
        'Welcome to SafeStep. Your account is registered but has not been approved yet. Your account will be activated and you will be notified upon approval.': 'مرحباً بك في SafeStep. حسابك مسجل في النظام ولكن لم يتم اعتماده بعد من قبل الإدارة. سيتم تفعيل حسابك بالكامل وإرسال إشعار لك فور الموافقة على طلب التقديم.',
        'Application Status:': 'حالة الطلب:',
        'Pending': 'قيد الانتظار',
        'Application Rejected': 'تم رفض طلب الانضمام',
        'We apologize, your application to join SafeStep has been rejected by the administration. Please review your details or contact support.': 'نعتذر منك، لقد تم رفض طلبك للانضمام إلى منصة SafeStep من قبل الإدارة. يرجى مراجعة البيانات المدخلة أو التواصل مع الدعم الفني للاستفسار وتحديث الطلب.',
        'Rejected': 'مرفوض',
        'Register Children Details': 'تسجيل بيانات الأطفال',
        'Required step to activate account': 'خطوة ضرورية لتفعيل الحساب',
        'Child': 'الابن',
        'Full Name': 'الاسم الكامل',
        'Age': 'العمر',
        'Grade': 'الصف الدراسي',
        'School': 'المدرسة',
        'Pickup Location (From)': 'موقع الالتقاء (من)',
        'Drop-off Location (To)': 'موقع التوصيل (إلى)',
        'Pickup Time': 'وقت الركوب',
        'Drop-off Time': 'وقت العودة',
        'Has any medical condition or health issue': 'يعاني من أي أمراض أو حالة صحية خاصة',
        'Medical condition details': 'تفاصيل الحالة المرضية',
        'Medication / dosage details': 'العلاج / الأدوية التي يتناولها',
        'Write details here...': 'اكتب التفاصيل هنا...',
        'Write medication details...': 'اكتب تفاصيل العلاج والجرعات...',
        'Send to Admin': 'إرسال البيانات للإدارة',
        'Route and Bus assignment pending': 'جاري تحديد خط السير والحافلة',
        'Children details submitted successfully. The school admin is assigning the proper bus and driver, we will activate live tracking as soon as it\'s completed.': 'تم تسجيل بيانات الأطفال بنجاح. تقوم إدارة المدرسة حاليًا بتعيين الحافلة والسائق المناسبين لأبنائك، وسنقوم بتفعيل التتبع المباشر فور الانتهاء.',
        'Registered Children in the System:': 'الأطفال المسجلين في النظام:',
        'years': 'سنة',
        'Special Health Condition:': 'حالة صحية خاصة:',
        'Medication:': 'العلاج:',
        'None': 'لا يوجد',
        'No children registered.': 'لا يوجد أطفال مسجلون.',
        'No attendance records found.': 'لم يتم العثور على سجلات حضور.',
        'From:': 'من:',
        'To:': 'إلى:',
        'Suggested Time:': 'الوقت المقترح:',
        'Sending...': 'جاري الإرسال...',
        'Submission failed.': 'فشلت عملية الإرسال.',
        'Children details submitted.': 'تم تقديم بيانات الأطفال بنجاح.',
        'Your parent application must be accepted before submitting children details.': 'يجب قبول طلب ولي الأمر قبل إدخال بيانات الأطفال.',
        'Children details were already submitted.': 'تم تقديم تفاصيل الأطفال بالفعل.',
        'Unable to submit children details.': 'تعذر تقديم تفاصيل الأطفال.',
        'School:': 'المدرسة:',
        'No Driver Assigned': 'لم يتم تعيين سائق بعد',
        'No recent activity found.': 'لم يتم العثور على أنشطة مؤخراً.',
        'dropped off': 'تم النزول',
        'marked present': 'حاضر',
        'marked absent': 'غائب'
    };

    const wordMap = {
        'Age': 'العمر',
        'Grade': 'الصف الدراسي',
        'School': 'المدرسة',
        'years': 'سنة',
        'status': 'الحالة',
        'Dashboard': 'لوحة التحكم',
        'Live': 'مباشر',
        'Tracking': 'التتبع',
        'Children': 'الأطفال',
        'Attendance': 'الحضور',
        'Notifications': 'الإشعارات',
        'Payments': 'المدفوعات',
        'Support': 'الدعم',
        'Trip': 'رحلة',
        'History': 'السجل',
        'Export': 'تصدير',
        'Excel': 'إكسل',
        'PDF': 'PDF',
        'Prev': 'السابق',
        'Next': 'التالي',
        'Emergency': 'طوارئ',
        'Alerts': 'تنبيهات',
        'Profile': 'الملف الشخصي',
        'Settings': 'الإعدادات',
        'Parent': 'ولي أمر',
        'Requests': 'طلبات',
        'Bus': 'الحافلة',
        'Status': 'الحالة',
        'On': 'على',
        'Way': 'الطريق',
        'Estimated': 'المقدر',
        'Arrival': 'الوصول',
        'Time': 'الوقت',
        'Driver': 'السائق',
        'Information': 'المعلومات',
        'Call': 'اتصال',
        'Total': 'إجمالي',
        'Today': 'اليوم',
        'Speed': 'السرعة',
        'Distance': 'المسافة',
        'Recent': 'الأخير',
        'Activity': 'النشاط',
        'Last': 'آخر',
        'seen': 'ظهور',
        'Follow': 'متابعة',
        'Locate': 'تحديد موقع',
        'Child': 'الطفل',
        'Center': 'توسيط',
        'Share': 'مشاركة',
        'Contact': 'تواصل',
        'Location': 'الموقع',
        'Test': 'تجريبي',
        'Notification': 'تنبيه',
        'Records': 'السجلات',
        'Overall': 'إجمالي',
        'Days': 'أيام',
        'Present': 'الحضور',
        'Absent': 'الغياب',
        'Missed': 'فائتة',
        'Pickups': 'الصعود',
        'Drop-offs': 'النزول',
        'Payment': 'دفع',
        'Paid': 'مدفوع',
        'Pending': 'معلق',
        'Dark': 'داكن',
        'Logout': 'تسجيل الخروج'
    };

    const attrNames = ['placeholder', 'title', 'aria-label'];
    const originalTextMap = new WeakMap();
    const originalAttrMap = new WeakMap();
    let currentLang = defaultLang;
    let observer = null;

    const patternRules = [
        { regex: /Today at\s+([0-9:]+\s*(?:AM|PM))/i, replace: 'اليوم الساعة $1' },
        { regex: /ETA:\s*([0-9:]+\s*(?:AM|PM|mins|minutes)?)/i, replace: 'وقت الوصول المتوقع: $1' },
        { regex: /(\d+(?:\.\d+)?)\s*km\s*away/i, replace: 'يبعد $1 كم' },
        { regex: /(\d+(?:\.\d+)?)\s*km\/h/i, replace: '$1 كم/س' },
        { regex: /(\d+(?:\.\d+)?)\s*km\b/i, replace: '$1 كم' },
        { regex: /(\d+)\s*mins?/i, replace: '$1 دقيقة' }
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
        toggle.querySelector('span').textContent = label;
        toggle.setAttribute('aria-label', currentLang === 'ar' ? 'Switch language to English' : 'Switch language to Arabic');
    }

    function applyLanguage(lang) {
        currentLang = lang;

        // Update document attributes for RTL support and SEO
        document.documentElement.lang = lang;
        document.documentElement.dir = lang === 'ar' ? 'rtl' : 'ltr';

        if (!document.documentElement.dataset.i18nTitle) {
            document.documentElement.dataset.i18nTitle = document.title;
        }
        document.title = currentLang === 'ar'
            ? translateString(document.documentElement.dataset.i18nTitle, 'ar')
            : document.documentElement.dataset.i18nTitle;
        if (!document.body) return;
        applyTree(document.body);
        updateToggleLabel();
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
        // Only handle added nodes; don't re-translate on every text update.
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
