/**
 * SafeStep AI Chat — Q&A knowledge base + quick questions UI
 */
(function () {
    'use strict';

    const QA = [
        {
            id: 'hello',
            q: { en: 'Hello', ar: 'مرحبا' },
            a: {
                en: "Hello! 🤖 I'm SafeStep AI. Pick a quick question below or type your own about tracking, registration, pricing, or safety.",
                ar: 'أهلاً بيك! 🤖 أنا مساعد SafeStep. اختار سؤال سريع من تحت أو اكتب سؤالك عن التتبع، التسجيل، الأسعار، أو الأمان.'
            },
            keys: ['hello', 'hi', 'hey', 'مرحبا', 'اهلا', 'سلام']
        },
        {
            id: 'register',
            q: { en: 'How do I register?', ar: 'إزاي أسجل؟' },
            a: {
                en: 'Parents can register via "Join SafeStep" or visit /apply/parent. Schools, drivers, and admins have separate application pages. After submitting, our team reviews and activates your account.',
                ar: 'ولي الأمر يقدر يسجل من "Join SafeStep" أو صفحة /apply/parent. المدارس والسواقين والإدارة ليهم صفحات تقديم منفصلة. بعد الإرسال، فريقنا بيراجع ويفعّل الحساب.'
            },
            keys: ['register', 'sign up', 'join', 'apply', 'تسجيل', 'سجل', 'اشتراك', 'انضمام']
        },
        {
            id: 'pricing',
            q: { en: 'What are the prices?', ar: 'إيه الأسعار؟' },
            a: {
                en: '3 plans: Smart (450 EGP/month), Premium (650 EGP/month — 7-day free trial), Private VIP (950 EGP/month). 25% off annual billing, 10% off quarterly.',
                ar: '3 باقات: Smart (450 جنيه/شهر)، Premium (650 جنيه/شهر — 7 أيام تجربة مجانية)، Private VIP (950 جنيه/شهر). خصم 25% سنوي و10% ربع سنوي.'
            },
            keys: ['price', 'cost', 'pricing', 'plan', 'سعر', 'تكلفة', 'باقة', 'فلوس']
        },
        {
            id: 'tracking',
            q: { en: 'How does GPS tracking work?', ar: 'التتبع بيشتغل إزاي؟' },
            a: {
                en: 'Each bus has a GPS device sending live location updates. Parents see the bus on a map, get arrival alerts, and can review trip history. Visit /tracking for a live demo.',
                ar: 'كل حافلة فيها جهاز GPS بيبعت الموقع لحظياً. ولي الأمر يشوف الباص على الخريطة، يستلم تنبيهات الوصول، ويراجع سجل الرحلات. زور /tracking للمعاينة.'
            },
            keys: ['track', 'gps', 'location', 'map', 'تتبع', 'خريطة', 'مكان', 'وين']
        },
        {
            id: 'safety',
            q: { en: 'What safety features do you offer?', ar: 'إيه مميزات الأمان؟' },
            a: {
                en: '24/7 in-bus cameras, driver background checks, SOS button in the app, speed monitoring, end-of-trip child check, and AES-256 encrypted data.',
                ar: 'كاميرات 24/7 داخل الحافلة، فحص خلفية للسائقين، زر SOS في التطبيق، مراقبة السرعة، فحص إلزامي بعد الرحلة، وتشفير AES-256 للبيانات.'
            },
            keys: ['safety', 'secure', 'camera', 'sos', 'أمان', 'حماية', 'كاميرا', 'طوارئ']
        },
        {
            id: 'notifications',
            q: { en: 'How do notifications work?', ar: 'الإشعارات بتشتغل إزاي؟' },
            a: {
                en: 'Instant app and SMS alerts for bus arrival, school departure, delays, emergencies, and route changes — usually 5 minutes before arrival.',
                ar: 'إشعارات فورية عبر التطبيق وSMS لوصول الباص، مغادرة المدرسة، التأخير، الطوارئ، وتغيير المسار — غالباً قبل الوصول بـ 5 دقائق.'
            },
            keys: ['notification', 'alert', 'sms', 'إشعار', 'تنبيه', 'رسالة']
        },
        {
            id: 'driver',
            q: { en: 'Who are the drivers?', ar: 'مين السواقين؟' },
            a: {
                en: 'All drivers pass background checks and safety training. They use a dedicated driver app to update trip status, pickup/dropoff, and live location.',
                ar: 'كل السواقين بيمرّوا بفحص خلفية وتدريب سلامة. بيستخدموا تطبيق السائق لتحديث الرحلة والتحميل/النزول والموقع المباشر.'
            },
            keys: ['driver', 'سائق', 'سواق']
        },
        {
            id: 'school',
            q: { en: 'How do schools use SafeStep?', ar: 'المدارس بتستخدمه إزاي؟' },
            a: {
                en: 'Schools get a full dashboard: fleet monitoring, routes, trips, attendance, driver management, parent communication, reports, and emergency center.',
                ar: 'المدارس عندها لوحة تحكم كاملة: مراقبة الأسطول، المسارات، الرحلات، الحضور، إدارة السواقين، التواصل مع الأهالي، التقارير، ومركز الطوارئ.'
            },
            keys: ['school', 'مدرسة', 'مدارس']
        },
        {
            id: 'how-it-works',
            q: { en: 'How does SafeStep work?', ar: 'SafeStep بيشتغل إزاي؟' },
            a: {
                en: '4 steps: 1) Register 2) Install GPS on buses 3) Set routes & alerts 4) Go live! See /how-it-works for details.',
                ar: '4 خطوات: 1) التسجيل 2) تركيب GPS 3) ضبط المسارات والإشعارات 4) التشغيل! شوف /how-it-works للتفاصيل.'
            },
            keys: ['how it works', 'how to use', 'steps', 'كيف', 'ازاي', 'طريقة']
        },
        {
            id: 'payment',
            q: { en: 'What payment methods are accepted?', ar: 'طرق الدفع إيه؟' },
            a: {
                en: 'Visa, MasterCard, Vodafone Cash, and bank transfer. Pay monthly or annually from the payment page.',
                ar: 'فيزا، ماستركارد، فودافون كاش، وتحويل بنكي. الدفع شهري أو سنوي من صفحة الدفع.'
            },
            keys: ['pay', 'payment', 'visa', 'دفع', 'فيزا', 'كاش']
        },
        {
            id: 'cancel',
            q: { en: 'Can I cancel anytime?', ar: 'أقدر ألغي في أي وقت؟' },
            a: {
                en: 'Yes — cancel anytime with no fees from your account or by contacting support.',
                ar: 'أيوه — تقدر تلغي في أي وقت بدون رسوم من حسابك أو بالتواصل مع الدعم.'
            },
            keys: ['cancel', 'refund', 'unsubscribe', 'إلغاء', 'استرجاع']
        },
        {
            id: 'support',
            q: { en: 'How do I contact support?', ar: 'أتواصل مع الدعم إزاي؟' },
            a: {
                en: 'Reach us 24/7 via /contact, phone +20 3 123 4567, or this AI chat in English and Arabic.',
                ar: 'تواصل معانا 24/7 عبر /contact، الهاتف +20 3 123 4567، أو الشات الذكي ده بالعربي والإنجليزي.'
            },
            keys: ['support', 'help', 'contact', 'دعم', 'مساعدة', 'تواصل']
        },
        {
            id: 'privacy',
            q: { en: 'Is my data private?', ar: 'بياناتي آمنة؟' },
            a: {
                en: 'Yes. AES-256 encryption, role-based access (parent/driver/admin/school), and no third-party data sharing.',
                ar: 'أيوه. تشفير AES-256، صلاحيات حسب الدور (ولي أمر/سائق/إدارة/مدرسة)، ومفيش مشاركة بيانات مع طرف تالت.'
            },
            keys: ['privacy', 'data', 'خصوصية', 'بيانات']
        },
        {
            id: 'absent',
            q: { en: 'What if my child is absent?', ar: 'لو طفلي غايب؟' },
            a: {
                en: 'Mark your child as absent in the parent app so the driver skips your stop and saves time for others.',
                ar: 'سجّل غياب طفلك من تطبيق ولي الأمر عشان السائق يتخطى محطتك ويوفر وقت باقي الأطفال.'
            },
            keys: ['absent', 'sick', 'غياب', 'مرض']
        },
        {
            id: 'faq',
            q: { en: 'Where is the full FAQ?', ar: 'الأسئلة الشائعة فين؟' },
            a: {
                en: 'Visit /faq for the complete FAQ with categories: General, Pricing, Technical, Security, and Support.',
                ar: 'زور /faq للأسئلة الشائعة الكاملة: عام، أسعار، تقني، أمان، ودعم.'
            },
            keys: ['faq', 'questions', 'أسئلة']
        }
    ];

    const QUICK_IDS = ['register', 'pricing', 'tracking', 'safety', 'notifications', 'how-it-works', 'support', 'cancel'];

    const CHAT_STRINGS = {
        you: { en: 'You', ar: 'أنت' },
        bot: { en: 'SafeStep AI', ar: 'مساعد SafeStep' }
    };

    function getChatLang() {
        const override = localStorage.getItem('chat_lang');
        if (override === 'ar' || override === 'en') return override;
        const saved = localStorage.getItem('lang') || localStorage.getItem('language');
        if (saved) return saved.startsWith('ar') ? 'ar' : 'en';
        const htmlLang = document.documentElement.lang;
        if (htmlLang && htmlLang.startsWith('ar')) return 'ar';
        return 'en';
    }

    window.setChatLanguage = function (lang) {
        if (lang !== 'ar' && lang !== 'en') return;
        localStorage.setItem('chat_lang', lang);
        setChatLocale();
    };

    function t(obj) {
        const lang = getChatLang();
        return obj[lang] || obj.en;
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function findAnswer(input) {
        const raw = input.trim();
        const q = raw.toLowerCase();
        const lang = getChatLang();

        for (const item of QA) {
            if (t(item.q).toLowerCase() === q) return t(item.a);
            if (item.q.en.toLowerCase() === q || item.q.ar === raw) return t(item.a);
        }

        for (const item of QA) {
            if (item.keys.some((k) => q.includes(k.toLowerCase()) || raw.includes(k))) {
                return t(item.a);
            }
        }

        if (lang === 'ar' || /[\u0600-\u06FF]/.test(raw)) {
            return 'سؤال ممتاز! اختار سؤال من القائمة السريعة أو اسأل عن التسجيل، الأسعار، التتبع، الأمان، أو الدعم.';
        }
        return 'Great question! Pick a quick question below or ask about registration, pricing, tracking, safety, or support.';
    }

    function appendMessage(role, html) {
        const body = document.getElementById('chatBody');
        if (!body) return;
        const lang = getChatLang();
        const label = role === 'user' ? t(CHAT_STRINGS.you) : t(CHAT_STRINGS.bot);
        const msg = document.createElement('div');
        msg.className = 'chat-message ' + role;
        msg.innerHTML = role === 'user'
            ? '<strong>' + escapeHtml(label) + ':</strong> ' + escapeHtml(html)
            : '<strong>' + escapeHtml(label) + ':</strong> ' + html;
        body.appendChild(msg);
        body.scrollTop = body.scrollHeight;
    }

    function appendBotAnswer(html, delay) {
        setTimeout(() => {
            const lang = getChatLang();
            const label = t(CHAT_STRINGS.bot);
            const msg = document.createElement('div');
            msg.className = 'chat-message bot';
            msg.innerHTML = '<strong>' + escapeHtml(label) + ':</strong> ' + html;
            const body = document.getElementById('chatBody');
            if (body) {
                body.appendChild(msg);
                body.scrollTop = body.scrollHeight;
            }
        }, delay || 500);
    }

    function renderQuickQuestions() {
        const wrap = document.getElementById('chatQuickQuestions');
        if (!wrap) return;
        const lang = getChatLang();
        wrap.innerHTML = QUICK_IDS.map((id) => {
            const item = QA.find((x) => x.id === id);
            if (!item) return '';
            const label = lang === 'ar' ? item.q.ar : item.q.en;
            return `<button type="button" class="chat-quick-btn" data-qid="${id}">${escapeHtml(label)}</button>`;
        }).join('');

        wrap.querySelectorAll('.chat-quick-btn').forEach((btn) => {
            btn.addEventListener('click', () => {
                const item = QA.find((x) => x.id === btn.dataset.qid);
                if (item) window.askQuickQuestion(item);
            });
        });

        const label = document.getElementById('chatQuickLabel');
        if (label) label.textContent = lang === 'ar' ? 'أسئلة سريعة:' : 'Quick questions:';
    }

    function setChatLocale() {
        const lang = getChatLang();
        const input = document.getElementById('chatInput');
        const welcome = document.getElementById('chatWelcomeText');
        const sendBtn = document.getElementById('chatSendBtn');
        const header = document.getElementById('chatHeaderTitle');
        const botLabel = document.getElementById('chatBotLabel');
        const langEn = document.getElementById('chatLangEn');
        const langAr = document.getElementById('chatLangAr');

        if (langEn) langEn.classList.toggle('active', lang === 'en');
        if (langAr) langAr.classList.toggle('active', lang === 'ar');
        if (botLabel) botLabel.textContent = t(CHAT_STRINGS.bot) + ':';

        if (lang === 'ar') {
            if (input) input.placeholder = 'اكتب سؤالك هنا...';
            if (welcome) welcome.innerHTML = 'أهلاً بيك! 🤖 اختار سؤالاً سريعاً أو اكتب سؤالك عن SafeStep.';
            if (sendBtn) {
                const label = sendBtn.querySelector('.chat-send-label');
                if (label) label.textContent = 'إرسال';
            }
            if (header) header.textContent = '🤖 مساعد SafeStep الذكي';
        } else {
            if (input) input.placeholder = 'Type your question...';
            if (welcome) welcome.innerHTML = 'Hello! 👋 Pick a quick question or ask me anything about SafeStep.';
            if (sendBtn) {
                const label = sendBtn.querySelector('.chat-send-label');
                if (label) label.textContent = 'Send';
            }
            if (header) header.textContent = '🤖 SafeStep AI Assistant';
        }
        renderQuickQuestions();
    }

    window.toggleChat = function () {
        const chat = document.getElementById('chatBox');
        if (!chat) return;
        const open = chat.style.display !== 'flex';
        chat.style.display = open ? 'flex' : 'none';
        if (open) setChatLocale();
    };

    window.sendChatMessage = function (presetText) {
        const input = document.getElementById('chatInput');
        const text = (presetText || (input && input.value) || '').trim();
        if (!text) return;

        appendMessage('user', text);
        if (input && !presetText) input.value = '';
        appendBotAnswer(findAnswer(text));
    };

    window.askQuickQuestion = function (item) {
        appendMessage('user', t(item.q));
        appendBotAnswer(t(item.a), 400);
    };

    document.addEventListener('DOMContentLoaded', () => {
        setChatLocale();
        const input = document.getElementById('chatInput');
        if (input) {
            input.addEventListener('keydown', (e) => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    window.sendChatMessage();
                }
            });
        }
    });

    document.addEventListener('languageChanged', setChatLocale);
})();
