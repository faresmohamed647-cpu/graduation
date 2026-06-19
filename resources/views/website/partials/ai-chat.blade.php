<!-- AI Chat Widget -->
<div class="chat-widget" onclick="toggleChat()" title="Ask SafeStep AI / اسأل مساعد SafeStep" role="button" aria-label="Open SafeStep AI chat">
    <i class="fa fa-robot"></i>
    <span class="chat-widget-pulse" aria-hidden="true"></span>
</div>

<div class="chat-box" id="chatBox" role="dialog" aria-labelledby="chatHeaderTitle">
    <div class="chat-header">
        <div class="chat-header-brand">
            <span class="chat-header-icon" aria-hidden="true"><i class="fa fa-robot"></i></span>
            <span id="chatHeaderTitle">🤖 SafeStep AI Assistant</span>
        </div>
        <div class="chat-header-actions">
            <button type="button" class="chat-lang-btn" id="chatLangEn" onclick="event.stopPropagation(); setChatLanguage('en')" aria-label="English">EN</button>
            <button type="button" class="chat-lang-btn" id="chatLangAr" onclick="event.stopPropagation(); setChatLanguage('ar')" aria-label="العربية">ع</button>
            <button type="button" onclick="event.stopPropagation(); toggleChat()" class="chat-close" aria-label="Close chat">&times;</button>
        </div>
    </div>

    <div class="chat-body" id="chatBody">
        <div class="chat-message bot" id="chatWelcome">
            <strong id="chatBotLabel">SafeStep AI:</strong>
            <span id="chatWelcomeText">Hello! 👋 Pick a quick question or ask me anything about SafeStep.</span>
        </div>
        <div class="chat-quick-wrap">
            <p class="chat-quick-label" id="chatQuickLabel">Quick questions:</p>
            <div class="chat-quick-questions" id="chatQuickQuestions"></div>
        </div>
    </div>

    <div class="chat-footer">
        <input type="text" id="chatInput" placeholder="Type your question..." autocomplete="off" />
        <button type="button" id="chatSendBtn" onclick="sendChatMessage()">
            <i class="fas fa-paper-plane" aria-hidden="true"></i>
            <span class="chat-send-label">Send</span>
        </button>
    </div>
</div>

<script src="{{ asset('js/safestep-chat.js') }}" defer></script>
