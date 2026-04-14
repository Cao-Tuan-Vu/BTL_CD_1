<div id="chat-widget" class="chat-widget" data-endpoint="{{ route('api.chatbot.message') }}">
    <button
        id="chat-toggle"
        class="chat-toggle"
        type="button"
        aria-expanded="false"
        aria-controls="chat-panel"
        aria-label="Mở chat hỗ trợ AI"
    >
        <span class="chat-toggle-icon" aria-hidden="true">AI</span>
        <span class="sr-only">Mở chat hỗ trợ AI</span>
    </button>

    <section id="chat-panel" class="chat-panel" aria-hidden="true">
        <header class="chat-header">
            <div>
                <h1>Trợ lý AI nội thất</h1>
                <p>Tư vấn nhanh 24/7</p>
            </div>
            <button id="chat-close" class="chat-close" type="button" aria-label="Đóng chat">&times;</button>
        </header>

        <div id="chat-window" class="chat-window" aria-live="polite">
            <div class="chat-row assistant">
                <div>
                    <div class="chat-bubble">Xin chào! Mình là trợ lý AI. Bạn cần tư vấn sản phẩm nội thất nào hôm nay?</div>
                    <div class="chat-meta">AI trợ lý</div>
                </div>
            </div>
        </div>

        <form id="chat-form" class="chat-form">
            <label class="sr-only" for="chat-message">Tin nhắn</label>
            <textarea id="chat-message" placeholder="VD: Tư vấn sofa dưới 10 triệu cho phòng khách nhỏ..." required></textarea>

            <div class="chat-form-footer">
                <small>Enter để gửi, Shift + Enter để xuống dòng</small>
                <button id="chat-submit" class="chat-submit" type="submit">Gửi</button>
            </div>
        </form>
    </section>
</div>
