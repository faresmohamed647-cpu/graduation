(function () {
    const STORAGE_KEY = 'siteLanguage';
    const TEXT_ORIGINALS = new WeakMap();
    const ATTR_ORIGINALS = new WeakMap();

    const translations = {
        ar: {
            'SCHOOL BUS TRACKING': 'تتبع الحافلة المدرسية',
            'Home': 'الرئيسية',
            'About': 'من نحن',
            'Services': 'الخدمات',
            'Pages': 'الصفحات',
            'Pricing Plan': 'خطة الأسعار',
            'Features': 'المزايا',
            'Free Quote': 'عرض مجاني',
            'Our Team': 'فريقنا',
            'Testimonial': 'آراء العملاء',
            '404 Page': 'صفحة 404',
            'Contact': 'اتصل بنا',
            'Contact Us': 'اتصل بنا',
            'Parents': 'أولياء الأمور',
            'Car Owner': 'مالك السيارة',
            'Language': 'اللغة',
            'Loading...': 'جارٍ التحميل...',
            'Read More': 'اقرأ المزيد',
            'Explore More': 'اكتشف المزيد',
            'Our Services': 'خدماتنا',
            'Explore Our Services': 'استكشف خدماتنا',
            'Our Features': 'مميزاتنا',
            'Our Clients Say!': 'ماذا يقول عملاؤنا!',
            'Quick Links': 'روابط سريعة',
            'Support': 'الدعم',
            'Address': 'العنوان',
            'Newsletter': 'النشرة البريدية',
            'Your Name': 'اسمك',
            'Your Email': 'بريدك الإلكتروني',
            'Your Mobile': 'رقم الهاتف',
            'Your email': 'بريدك الإلكتروني',
            'Special Note': 'ملاحظة خاصة',
            'Submit': 'إرسال',
            'Send': 'إرسال',
            'SignUp': 'اشتراك',
            'Call for any query!': 'اتصل لأي استفسار!',
            'Track Your School Bus': 'تابع حافلتك المدرسية',
            'Live Location & Trip Replay!': 'موقع مباشر وإعادة الرحلة!',
            'Select Service Type': 'اختر نوع الخدمة',
            'Live Bus Tracking': 'تتبع الحافلة المباشر',
            'Trip History': 'سجل الرحلات',
            'Trip Replay': 'إعادة الرحلة',
            'Route Replay': 'إعادة المسار',
            'Arrival & Departure Alerts': 'تنبيهات الوصول والانطلاق',
            'School Fleet Monitoring': 'متابعة أسطول المدرسة',
            'Terms & Condition': 'الشروط والأحكام',
            'Type your message...': 'اكتب رسالتك...',
            'Live Support': 'دعم مباشر',
            'About Us': 'من نحن',
            'SAFESTEP BUS': 'سيف ستيب باص'
            ,
            'Not Found': 'غير موجود',
            'Bus Route Not Found': 'مسار الحافلة غير موجود',
            "The page you're looking for doesn't exist.Please return to the home page or track your school bus safely.": 'الصفحة التي تبحث عنها غير موجودة. يرجى العودة للصفحة الرئيسية أو متابعة الحافلة بأمان.',
            'Go Back To Home': 'العودة للرئيسية',
            'Get In Touch': 'تواصل معنا',
            'Contact Us For Any Inquiry': 'تواصل معنا لأي استفسار',
            "Need help tracking your child's school bus or managing alerts?Reach out to us and we'll assist you as quickly as possible": 'تحتاج مساعدة في تتبع حافلة طفلك أو إدارة التنبيهات؟ تواصل معنا وسنساعدك في أسرع وقت.',
            'Download Now': 'حمّل الآن',
            'Subject': 'الموضوع',
            'Leave a message here': 'اكتب رسالتك هنا',
            'Message': 'الرسالة',
            'Send Message': 'إرسال الرسالة',
            'Feature': 'ميزة',
            'Features': 'المميزات',
            'Pricing': 'الأسعار',
            'Simple Pricing for Families': 'أسعار بسيطة للعائلات',
            'Family Offers': 'عروض العائلات',
            '7-day free trial for every family': 'تجربة مجانية لمدة 7 أيام لكل عائلة',
            '20% discount for 3 children or more': 'خصم 20% عند تسجيل 3 أطفال أو أكثر',
            '25% discount on yearly payment': 'خصم 25% عند الدفع السنوي',
            'Family Savings Calculator': 'حاسبة التوفير للعائلة',
            'Estimated Monthly Savings': 'التوفير الشهري المتوقع',
            'Current Monthly Cost': 'التكلفة الشهرية الحالية',
            'School Monthly Cost': 'التكلفة الشهرية مع المدرسة',
            'Dedicated family support': 'دعم مخصص للعائلة',
            'Real-time tracking': 'تتبع لحظي',
            'Cancel anytime within 48 hours': 'إمكانية الإلغاء خلال 48 ساعة',
            'Number of Children': 'عدد الأطفال',
            'Current Cost per Child (Monthly)': 'التكلفة الحالية لكل طفل (شهريًا)',
            'Select Plan': 'اختر الخطة',
            'Basic Plan': 'الخطة الأساسية',
            'VIP Plan': 'خطة كبار الشخصيات',
            'Payment Method': 'طريقة الدفع',
            'Order now': 'اطلب الآن',
            'or pay using credit card': 'أو ادفع باستخدام بطاقة ائتمان',
            'Card holder full name': 'الاسم الكامل لصاحب البطاقة',
            'Enter your full name': 'اكتب اسمك الكامل',
            'Card Number': 'رقم البطاقة',
            'Expiry Date / CVV': 'تاريخ الانتهاء / CVV',
            'Checkout': 'إتمام الدفع',
            'Inpit title': 'عنوان الإدخال',
            'Expiry Date': 'تاريخ الانتهاء',
            'Your full name': 'اسمك الكامل',
            'Your age': 'عمرك',
            'Gender': 'النوع',
            'select': 'اختر',
            'Male': 'ذكر',
            'Female': 'أنثى',
            'Mobile number': 'رقم الهاتف',
            'Car type': 'نوع السيارة',
            'Car model': 'موديل السيارة',
            'Home address': 'عنوان المنزل',
            'Car license plate number': 'رقم لوحة السيارة',
            'Subscribe Now': 'اشترك الآن',
            'Ignorance': 'تجاهل',
            'Join as a Student': 'انضم كطالب',
            'Parents can register here to track their children on the bus': 'يمكن لأولياء الأمور التسجيل هنا لمتابعة أطفالهم داخل الحافلة',
            'Join as a Car Owner': 'انضم كمالك سيارة',
            'Driver and car owners can register to start earning': 'يمكن للسائقين ومالكي السيارات التسجيل لبدء الربح',
            'About Us For Any Inquiry': 'من نحن لأي استفسار',
            'Testimonial': 'آراء العملاء',
            'Client Name': 'اسم العميل',
            'Profession': 'المهنة',
            'Expert Team Members': 'فريق خبراء',
            'Full Name': 'الاسم الكامل',
            'Designation': 'الوظيفة',
            'NEW · Direct for Families': 'جديد · موجه للعائلات',
            'Private vehicle (6-8 children)': 'مركبة خاصة (6-8 أطفال)',
            "Follow the school bus in real time, review past trips, and ensure your child's safety with smart tracking technology": 'تابع الحافلة في الوقت الحقيقي، وراجع الرحلات السابقة، واطمئن على سلامة طفلك بتقنية تتبع ذكية.',
            "Track your child's bus in real-time, receive arrival and departure notifications, and rest assured that every trip is under control.": 'تابع حافلة طفلك لحظيًا واستقبل إشعارات الوصول والانطلاق واطمئن أن كل رحلة تحت السيطرة.',
            'Some Facts': 'بعض الحقائق',
            '#1 Safe and Smart School Transportation - For Families & Schools': 'النقل المدرسي الآمن والذكي رقم 1 للعائلات والمدارس',
            '24/7 Telephone Support': 'دعم هاتفي 24/7',
            'Parents': 'أولياء الأمور',
            'Driver': 'السائق',
            "This is the app that suits the parent's use. SMS notifications or Online App notifications are sent instantly to parents..": 'هذا التطبيق مناسب لاستخدام ولي الأمر. يتم إرسال الإشعارات فورًا عبر الرسائل أو التطبيق.',
            'Our Team': 'فريقنا',
            'Services': 'الخدمات',
            'Contact Us': 'اتصل بنا',
            'Pages': 'الصفحات',
            'About': 'من نحن',
            'Home': 'الرئيسية'
            ,
            'Tempor erat elitr rebum at clita. Diam dolor diam ipsum sit diam amet diam et eos. Clita erat ipsum et lorem et sit.': 'تجربة ممتازة وخدمة موثوقة ساعدتنا كثيرًا في متابعة أبنائنا.',
            '123 Street, New York, USA': '123 شارع، نيويورك، الولايات المتحدة',
            'info@example.com': 'info@example.com',
            'Dolor amet sit justo amet elitr clita ipsum elitr est.': 'اشترك ليصلك كل جديد عن خدماتنا.',
            'Your Site Name': 'اسم موقعك',
            ', All Right Reserved.': '، جميع الحقوق محفوظة.',
            'EGP / month / per childh': 'جنيه / شهر / لكل طفل',
            'Advanced School Bus Tracking for Parents & Schools': 'تتبع متقدم للحافلة المدرسية للأهالي والمدارس',
            'Worldwide Service': 'خدمة عالمية',
            'Multi-language Support - Interface available in multiple languages; more added regularly.': 'دعم متعدد اللغات - الواجهة متاحة بلغات متعددة ويتم إضافة المزيد باستمرار.',
            'On Time Delivery': 'الالتزام بالمواعيد',
            'In-App Chat & Notifications - Secure, real-time messaging and customizable alerts.': 'دردشة وإشعارات داخل التطبيق - رسائل آمنة لحظية وتنبيهات قابلة للتخصيص.',
            'Premium Plan': 'الخطة المميزة',
            'Hello 👋': 'مرحبًا 👋',
            'How can we help you?': 'كيف يمكننا مساعدتك؟',
            'Built for schools and parents. Designed for safety. Powered by simplicity.': 'مصمم للمدارس والأهالي. مبني على الأمان. ومدعوم بالبساطة.',
            'SAFESTEP BUS is a very useful mobile app to track the current whereabouts of the bus and get real-time updates. The app uses vehicle tracking system to create a real time tracking system so that parents and school authorities may locate and receive instant updates and notifications regarding the school bus trips. Friendly, easy to use and enormously helpful, it is a companion in your pocket. Learn More': 'SAFESTEP BUS تطبيق مفيد جدًا لتتبع موقع الحافلة الحالي واستلام تحديثات فورية. يتيح لأولياء الأمور وإدارة المدرسة معرفة الموقع واستلام إشعارات مباشرة عن الرحلات. سهل الاستخدام ومفيد للغاية.',
            'This is the app created for the use of drivers. It is very easy to use and lets the driver instantly update the whereabouts.': 'هذا التطبيق مخصص للسائقين. سهل الاستخدام ويسمح بتحديث الموقع فورًا.',
            'Review any completed trip exactly as it happened. Includes speed data, waiting durations at each stop, stop order, and delays. Crucial for incident resolution and operational analysis.': 'راجع أي رحلة مكتملة كما حدثت تمامًا، مع بيانات السرعة ووقت التوقف وترتيب المحطات والتأخيرات، وهو مهم لتحليل التشغيل وحل المشكلات.',
            'Your ticket to peace of mind!': 'تذكرتك لراحة البال!',
            '#1 Place For Your': 'المكان رقم 1 لـ',
            'SAFTY': 'أمان',
            'of your children': 'أطفالك',
            'Trusted by leading schools across multiple regions': 'موثوق من مدارس رائدة في مناطق متعددة',
            'STUDENT FOLLOW-UP': 'متابعة الطلاب',
            'Free session': 'جلسة مجانية',
            'Transport': 'النقل',
            'Solution': 'الحل',
            'Parents can follow the school bus live, receive alerts, and feel confident that their children arrive safely and on time.': 'يمكن للأهالي متابعة الحافلة مباشرة واستلام التنبيهات والاطمئنان لوصول أطفالهم بأمان وفي الموعد.',
            "This is the app that suits the parent's use. SMS notifications or Online App notifications are sent instantly to parents.": 'هذا التطبيق مناسب لاستخدام ولي الأمر. تصل الإشعارات فورًا عبر الرسائل أو التطبيق.',
            'Pricies real-time location with updates every second on an interactive map': 'موقع لحظي مع تحديثات كل ثانية على خريطة تفاعلية',
            'Pricies real-time location with updates every second on an interactive map.': 'موقع لحظي مع تحديثات كل ثانية على خريطة تفاعلية.',
            'Trip History & Playback': 'سجل الرحلات وإعادة التشغيل',
            'Review any completed trip exactly as it happened.': 'راجع أي رحلة مكتملة كما حدثت.',
            'Includes speed data, waiting durations at each stop, stop order, and delays.': 'تشمل بيانات السرعة ومدة الانتظار عند كل محطة وترتيب المحطات والتأخير.',
            'Crucial for incident resolution and operational analysis.': 'مهم لحل المشكلات والتحليل التشغيلي.',
            'EGP / month / per child': 'جنيه / شهر / لكل طفل',
            'join-student': 'تسجيل طالب',
            'Phone': 'الهاتف',
            'Relationship (with the school)': 'صلة القرابة (بالطالب)',
            'Father': 'الأب',
            'Mother': 'الأم',
            'Number of students': 'عدد الطلاب',
            'Degree': 'المرحلة',
            'Education system': 'النظام التعليمي',
            'School name': 'اسم المدرسة',
            'School address': 'عنوان المدرسة',
            'School starting': 'بداية اليوم الدراسي',
            'Join Now': 'انضم الآن',
            'pay': 'الدفع',
            'See how much you can save monthly by switching to our school plans.': 'شاهد كم يمكنك التوفير شهريًا عند الاشتراك في خططنا المدرسية.',
            '0 EGP': '0 جنيه',
            '450 EGP': '450 جنيه',
            '650 EGP': '650 جنيه',
            '950 EGP': '950 جنيه',
            'Monthly': 'شهري',
            'Quarterly': 'ربع سنوي',
            'Yearly': 'سنوي',
            'monthly': 'شهري',
            'quarterly': 'ربع سنوي',
            'yearly': 'سنوي',
            'State': 'الدولة',
            'Arab Republic of Egypt': 'جمهورية مصر العربية',
            'Kingdom of Saudi Arabia': 'المملكة العربية السعودية',
            'CVV': 'رمز التحقق',
            'EGP': 'جنيه',
            'Quote': 'عرض سعر'
            ,
            'Track your child\'s bus in real-time, receive arrival and departure notifications, and rest assured that every trip is under control.': 'تابع حافلة طفلك لحظيًا واستقبل إشعارات الوصول والانطلاق واطمئن أن كل رحلة تحت السيطرة.',
            'Happy parents': 'أولياء أمور سعداء',
            'Happy driver': 'سائقون سعداء',
            'CReliability for schools': 'موثوقية للمدارس',
            'Advanced Real-time Tracking': 'تتبع متقدم لحظيًا',
            'Smart Notifications': 'إشعارات ذكية',
            'Instant alerts via SMS,email,and,app,for drivers and parents.': 'تنبيهات فورية عبر الرسائل والتطبيق للسائقين والأهالي.',
            '24/7 Surveillance Cameras': 'كاميرات مراقبة 24/7',
            'High-quality recording inside the bus with secure storage and review capability.': 'تسجيل عالي الجودة داخل الحافلة مع تخزين آمن وإمكانية المراجعة.',
            'Driver Background check': 'فحص خلفية السائق',
            'All driver undergo comprehensive background checks and mandatory safety training.': 'كل سائق يخضع لفحص شامل وتدريب أمان إلزامي.',
            'Emergency Alert Sestem': 'نظام تنبيه الطوارئ',
            'SOS button in the app with direct connection to support team and authorities.': 'زر استغاثة داخل التطبيق مع اتصال مباشر بالدعم والجهات المختصة.',
            'SSL/TLS Encryption': 'تشفير SSL/TLS',
            'All data protected with military-gradeencryption for maximum security.': 'كل البيانات محمية بتشفير قوي لأقصى درجات الأمان.',
            'Smart Plan': 'الخطة الذكية',
            'Live GPS tracking on map': 'تتبع GPS مباشر على الخريطة',
            'Pickup & drop-off notifications': 'إشعارات الاستلام والتوصيل',
            'Direct chat with driver': 'دردشة مباشرة مع السائق',
            'Monthly trip reports': 'تقارير رحلات شهرية',
            'Daily school-time support': 'دعم يومي خلال وقت المدرسة',
            'All Smart Plan features': 'كل مزايا الخطة الذكية',
            'Priority customer support': 'دعم عملاء بأولوية',
            'Arrival time sharing with family': 'مشاركة وقت الوصول مع العائلة',
            'Unlimited parent accounts': 'حسابات أولياء أمور غير محدودة',
            'Driver rating & feedback': 'تقييم السائق والملاحظات',
            'Smart route optimization': 'تحسين المسارات الذكي',
            '7 Days Free Trial': 'تجربة مجانية 7 أيام',
            'Private VIP': 'VIP خاص',
            'Dedicated driver': 'سائق مخصص',
            'Door-to-door escort service': 'خدمة مرافقة من الباب إلى الباب',
            'Home to classroom supervision': 'متابعة من المنزل حتى الفصل',
            'Live monitoring all hours': 'مراقبة مباشرة طوال الوقت',
            'Phone & WhatsApp support': 'دعم عبر الهاتف وواتساب',
            'English': 'English',
            'Ahmed Hassan': 'أحمد حسن',
            'Mona Ali': 'منى علي',
            'Omar Khaled': 'عمر خالد',
            'Nour Ibrahim': 'نور إبراهيم',
            'Parent of Grade 4 Student': 'ولي أمر لطالب بالصف الرابع',
            'School Principal': 'مدير مدرسة',
            'Bus Supervisor': 'مشرف حافلات',
            'Operations Manager': 'مدير التشغيل',
            'The live tracking and instant alerts made our morning routine much easier and safer.': 'التتبع المباشر والتنبيهات الفورية جعلوا روتين الصباح أسهل وأكثر أمانًا لنا.',
            'Since using SAFESTEP BUS, pickup delays dropped and parent trust increased significantly.': 'منذ استخدام SAFESTEP BUS انخفضت تأخيرات الاستلام بشكل واضح وارتفعت ثقة أولياء الأمور كثيرًا.',
            'Driver check-ins and route playback help us solve issues quickly and keep trips on schedule.': 'تأكيد حضور السائق وإعادة مسار الرحلة يساعداننا على حل المشكلات بسرعة والحفاظ على الالتزام بالمواعيد.',
            'The platform is simple for families and gives our school full visibility over daily transport.': 'المنصة سهلة للعائلات وتمنح المدرسة رؤية كاملة لحركة النقل اليومية.'
        }
    };

    function normalizeText(text) {
        return text.replace(/\s+/g, ' ').trim();
    }

    function normalizeKey(text) {
        return normalizeText(text)
            .toLowerCase()
            .replace(/[’']/g, "'")
            .replace(/[.,!?;:()[\]{}]/g, '')
            .replace(/\s+/g, ' ')
            .trim();
    }

    function getTranslation(text, lang) {
        if (!text || lang === 'en') {
            return text;
        }

        const dictionary = translations[lang] || {};
        const normalized = normalizeText(text);
        const normalizedKey = normalizeKey(text);

        if (dictionary[text]) return dictionary[text];
        if (dictionary[normalized]) return dictionary[normalized];

        const exactKey = Object.keys(dictionary).find((key) => normalizeKey(key) === normalizedKey);
        if (exactKey) return dictionary[exactKey];

        let translated = text;
        const phraseKeys = Object.keys(dictionary)
            .filter((key) => key.length > 3 && /[a-zA-Z]/.test(key))
            .sort((a, b) => b.length - a.length);

        phraseKeys.forEach((key) => {
            if (translated.includes(key)) {
                translated = translated.split(key).join(dictionary[key]);
            }
        });

        return translated;
    }

    function applyTextTranslations(lang) {
        const walker = document.createTreeWalker(document.body, NodeFilter.SHOW_TEXT, {
            acceptNode(node) {
                if (!node.parentElement) {
                    return NodeFilter.FILTER_REJECT;
                }

                const tag = node.parentElement.tagName;
                if (['SCRIPT', 'STYLE', 'NOSCRIPT'].includes(tag)) {
                    return NodeFilter.FILTER_REJECT;
                }

                if (!node.nodeValue || !node.nodeValue.trim()) {
                    return NodeFilter.FILTER_REJECT;
                }

                return NodeFilter.FILTER_ACCEPT;
            }
        });

        const nodes = [];
        while (walker.nextNode()) {
            nodes.push(walker.currentNode);
        }

        nodes.forEach((node) => {
            if (!TEXT_ORIGINALS.has(node)) {
                TEXT_ORIGINALS.set(node, node.nodeValue);
            }

            const original = TEXT_ORIGINALS.get(node);
            node.nodeValue = lang === 'en' ? original : getTranslation(original, lang);
        });
    }

    function applyAttributeTranslations(lang) {
        const elements = document.querySelectorAll('[placeholder], [title], [value], [aria-label]');

        elements.forEach((el) => {
            if (!ATTR_ORIGINALS.has(el)) {
                ATTR_ORIGINALS.set(el, {
                    placeholder: el.getAttribute('placeholder'),
                    title: el.getAttribute('title'),
                    value: el.getAttribute('value'),
                    ariaLabel: el.getAttribute('aria-label')
                });
            }

            const original = ATTR_ORIGINALS.get(el);

            if (original.placeholder !== null) {
                el.setAttribute('placeholder', lang === 'en' ? original.placeholder : getTranslation(original.placeholder, lang));
            }

            if (original.title !== null) {
                el.setAttribute('title', lang === 'en' ? original.title : getTranslation(original.title, lang));
            }

            if (original.value !== null) {
                el.setAttribute('value', lang === 'en' ? original.value : getTranslation(original.value, lang));
            }

            if (original.ariaLabel !== null) {
                el.setAttribute('aria-label', lang === 'en' ? original.ariaLabel : getTranslation(original.ariaLabel, lang));
            }
        });

        const pageTitle = document.querySelector('title');
        if (pageTitle) {
            if (!pageTitle.dataset.enTitle) {
                pageTitle.dataset.enTitle = pageTitle.textContent;
            }
            const source = pageTitle.dataset.enTitle;
            pageTitle.textContent = lang === 'en' ? source : getTranslation(source, lang);
        }
    }

    function setDirection(lang) {
        document.documentElement.lang = lang;
        document.documentElement.dir = lang === 'ar' ? 'rtl' : 'ltr';
        document.body.classList.toggle('rtl', lang === 'ar');
    }

    function ensureLanguageStyles() {
        if (document.getElementById('lang-inline-style')) {
            return;
        }

        const style = document.createElement('style');
        style.id = 'lang-inline-style';
        style.textContent = '' +
            '.lang-wrapper{position:relative;display:flex;align-items:center;z-index:1000;}' +
            '.lang-wrapper.lang-floating{position:fixed;bottom:18px;right:18px;z-index:2000;}' +
            '.lang-menu{display:none;position:absolute;top:110%;right:0;background:#fff;border:1px solid #ddd;border-radius:8px;min-width:120px;box-shadow:0 8px 20px rgba(0,0,0,.12);padding:6px;}' +
            '.lang-wrapper.active .lang-menu{display:block;}' +
            '.lang-menu button{display:block;width:100%;border:0;background:transparent;padding:8px 10px;text-align:start;border-radius:6px;cursor:pointer;}' +
            '.lang-menu button:hover{background:#f2f5f9;}';

        document.head.appendChild(style);
    }

    function ensureLanguageControl() {
        ensureLanguageStyles();

        if (document.querySelector('.lang-wrapper')) {
            return;
        }

        const target = document.querySelector('.navbar .navbar-collapse') || document.querySelector('.navbar') || document.body;

        const wrapper = document.createElement('div');
        wrapper.className = 'lang-wrapper';
        if (!document.querySelector('.navbar')) {
            wrapper.classList.add('lang-floating');
        }
        wrapper.innerHTML = '' +
            '<a href="javascript:void(0)" class="order-btn lang-toggle">Language</a>' +
            '<div class="lang-menu">' +
            '<button type="button" data-lang="en">English</button>' +
            '<button type="button" data-lang="ar">العربية</button>' +
            '</div>';

        target.appendChild(wrapper);
    }

    function bindLanguageControlEvents() {
        const wrapper = document.querySelector('.lang-wrapper');
        const toggle = document.querySelector('.lang-toggle');

        if (!wrapper || !toggle || wrapper.dataset.langBound === '1') {
            return;
        }

        wrapper.dataset.langBound = '1';

        toggle.addEventListener('click', (event) => {
            event.preventDefault();
            event.stopPropagation();
            wrapper.classList.toggle('active');
        });

        wrapper.querySelectorAll('[data-lang]').forEach((button) => {
            button.addEventListener('click', (event) => {
                event.preventDefault();
                setLanguage(button.dataset.lang);
                wrapper.classList.remove('active');
            });
        });

        document.addEventListener('click', () => {
            wrapper.classList.remove('active');
        });
    }

    function setLanguageLabel(lang) {
        const toggle = document.querySelector('.lang-toggle');
        if (toggle) {
            toggle.textContent = lang === 'ar' ? 'العربية' : 'Language';
        }
    }

    function refreshCarousels(lang) {
        if (!window.jQuery || !window.jQuery.fn || !window.jQuery.fn.owlCarousel) {
            return;
        }

        const $ = window.jQuery;
        const rtl = lang === 'ar';

        const carousels = [
            {
                selector: '.header-carousel',
                options: {
                    autoplay: false,
                    smartSpeed: 1500,
                    items: 1,
                    dots: false,
                    loop: true,
                    nav: true,
                    navText: [
                        '<i class="bi bi-chevron-left"></i>',
                        '<i class="bi bi-chevron-right"></i>'
                    ]
                }
            },
            {
                selector: '.testimonial-carousel',
                options: {
                    autoplay: false,
                    smartSpeed: 1000,
                    center: true,
                    dots: true,
                    loop: true,
                    responsive: {
                        0: { items: 1 },
                        768: { items: 2 },
                        992: { items: 3 }
                    }
                }
            }
        ];

        carousels.forEach((item) => {
            $(item.selector).each(function () {
                const $carousel = $(this);
                const owl = $carousel.data('owl.carousel');

                if (owl) {
                    owl.settings.rtl = rtl;
                    owl.options.rtl = rtl;
                    $carousel.trigger('refresh.owl.carousel');
                } else {
                    $carousel.owlCarousel(Object.assign({}, item.options, { rtl: rtl }));
                }
            });
        });
    }

    function setLanguage(lang) {
        const selected = lang === 'ar' ? 'ar' : 'en';
        localStorage.setItem(STORAGE_KEY, selected);

        setDirection(selected);
        applyTextTranslations(selected);
        applyAttributeTranslations(selected);
        setLanguageLabel(selected);
        refreshCarousels(selected);
    }

    document.addEventListener('DOMContentLoaded', () => {
        ensureLanguageControl();
        bindLanguageControlEvents();

        const savedLanguage = localStorage.getItem(STORAGE_KEY) || 'en';
        setLanguage(savedLanguage);
    });

    window.setLanguage = setLanguage;
})();
