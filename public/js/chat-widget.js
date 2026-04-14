(function () {
    "use strict";

    const widget = document.getElementById("chat-widget");

    if (!widget) {
        return;
    }

    const endpoint = widget.getAttribute("data-endpoint") || "";
    const toggleButton = document.getElementById("chat-toggle");
    const closeButton = document.getElementById("chat-close");
    const panel = document.getElementById("chat-panel");
    const form = document.getElementById("chat-form");
    const input = document.getElementById("chat-message");
    const submitButton = document.getElementById("chat-submit");
    const chatWindow = document.getElementById("chat-window");

    if (!endpoint || !toggleButton || !panel || !form || !input || !submitButton || !chatWindow) {
        return;
    }

    const OPEN_CLASS = "open";
    const history = [];

    function isOpen() {
        return widget.classList.contains(OPEN_CLASS);
    }

    function scrollToBottom() {
        chatWindow.scrollTop = chatWindow.scrollHeight;
    }

    function setOpen(nextOpen) {
        widget.classList.toggle(OPEN_CLASS, nextOpen);
        toggleButton.setAttribute("aria-expanded", String(nextOpen));
        panel.setAttribute("aria-hidden", String(!nextOpen));

        if (nextOpen) {
            window.setTimeout(function () {
                input.focus();
                scrollToBottom();
            }, 180);
        }
    }

    function appendMessage(role, content, metaText) {
        const row = document.createElement("div");
        row.className = "chat-row " + role;

        const wrapper = document.createElement("div");
        const bubble = document.createElement("div");
        bubble.className = "chat-bubble";
        bubble.textContent = content;
        wrapper.appendChild(bubble);

        if (metaText) {
            const meta = document.createElement("div");
            meta.className = "chat-meta";
            meta.textContent = metaText;
            wrapper.appendChild(meta);
        }

        row.appendChild(wrapper);
        chatWindow.appendChild(row);
        scrollToBottom();
    }

    function appendTyping() {
        const row = document.createElement("div");
        row.className = "chat-row assistant";
        row.id = "chat-typing-indicator";
        row.innerHTML = "<div class=\"chat-typing\"><span>AI dang tra loi</span><span class=\"chat-typing-dot\"></span><span class=\"chat-typing-dot\"></span><span class=\"chat-typing-dot\"></span></div>";
        chatWindow.appendChild(row);
        scrollToBottom();
    }

    function removeTyping() {
        const typingNode = document.getElementById("chat-typing-indicator");

        if (typingNode) {
            typingNode.remove();
        }
    }

    async function sendMessage(message) {
        const response = await fetch(endpoint, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json"
            },
            body: JSON.stringify({
                message: message,
                history: history.slice(-10)
            })
        });

        let data = {};

        try {
            data = await response.json();
        } catch (error) {
            throw new Error("Khong doc duoc phan hoi tu chatbot.");
        }

        if (!response.ok) {
            throw new Error(data.message || "Khong the ket noi chatbot luc nay.");
        }

        return data;
    }

    toggleButton.addEventListener("click", function () {
        setOpen(!isOpen());
    });

    if (closeButton) {
        closeButton.addEventListener("click", function () {
            setOpen(false);
            toggleButton.focus();
        });
    }

    document.addEventListener("keydown", function (event) {
        if (event.key === "Escape" && isOpen()) {
            setOpen(false);
            toggleButton.focus();
        }
    });

    document.addEventListener("click", function (event) {
        if (!isOpen()) {
            return;
        }

        if (widget.contains(event.target)) {
            return;
        }

        setOpen(false);
    });

    form.addEventListener("submit", async function (event) {
        event.preventDefault();

        const message = input.value.trim();

        if (message === "") {
            return;
        }

        appendMessage("user", message, "Ban");
        history.push({ role: "user", content: message });

        input.value = "";
        submitButton.disabled = true;
        appendTyping();

        try {
            const data = await sendMessage(message);
            removeTyping();

            const reply = (data.data && data.data.reply) || "AI chua tra loi.";
            const provider = data.data && data.data.provider;
            const model = (data.data && data.data.model) || "default";
            const meta = provider === "fallback" ? "AI tro ly (fallback)" : "AI tro ly (" + model + ")";

            appendMessage("assistant", reply, meta);
            history.push({ role: "assistant", content: reply });
        } catch (error) {
            removeTyping();
            appendMessage("assistant", error.message || "Co loi xay ra.", "Loi he thong");
        } finally {
            submitButton.disabled = false;
            input.focus();
        }
    });

    input.addEventListener("keydown", function (event) {
        if (event.key === "Enter" && !event.shiftKey) {
            event.preventDefault();
            form.requestSubmit();
        }
    });

    setOpen(false);
})();
