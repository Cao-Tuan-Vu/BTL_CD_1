(() => {
    const EXIT_ANIMATION_MS = 260;
    const DEFAULT_DURATION = 5000;
    const VALID_TYPES = new Set(['success', 'error', 'warning']);
    const timerMap = new WeakMap();
    const roots = [];

    const normalizeType = (type) => (VALID_TYPES.has(type) ? type : 'success');

    const dismissToast = (toast) => {
        if (!toast || toast.classList.contains('is-leaving')) {
            return;
        }

        const timeoutId = timerMap.get(toast);
        if (timeoutId) {
            window.clearTimeout(timeoutId);
            timerMap.delete(toast);
        }

        toast.classList.remove('is-visible');
        toast.classList.add('is-leaving');

        const removeToast = () => {
            if (toast.isConnected) {
                toast.remove();
            }
        };

        toast.addEventListener('transitionend', removeToast, { once: true });
        window.setTimeout(removeToast, EXIT_ANIMATION_MS + 30);
    };

    const createToastElement = (message, type) => {
        const toast = document.createElement('article');
        toast.className = 'toast toast--' + type;
        toast.setAttribute('role', type === 'error' ? 'alert' : 'status');
        toast.setAttribute('aria-live', type === 'error' ? 'assertive' : 'polite');
        toast.innerHTML = [
            '<span class="toast__marker" aria-hidden="true"></span>',
            '<div class="toast__message"></div>',
            '<button class="toast__close" type="button" aria-label="Close notification">&times;</button>',
        ].join('');

        toast.querySelector('.toast__message').textContent = message;
        toast.querySelector('.toast__close').addEventListener('click', () => dismissToast(toast));

        return toast;
    };

    const showToastInRoot = (root, message, type = 'success', options = {}) => {
        if (!root || typeof message !== 'string' || message.trim() === '') {
            return null;
        }

        const stack = root.querySelector('[data-toast-stack]');
        if (!stack) {
            return null;
        }

        const normalizedType = normalizeType(type);
        const duration = Number(options.duration ?? root.dataset.toastDuration ?? DEFAULT_DURATION);
        const finalDuration = Number.isFinite(duration) && duration >= 1000 ? duration : DEFAULT_DURATION;

        const toast = createToastElement(message.trim(), normalizedType);
        stack.appendChild(toast);

        requestAnimationFrame(() => {
            toast.classList.add('is-visible');
        });

        const timeoutId = window.setTimeout(() => dismissToast(toast), finalDuration);
        timerMap.set(toast, timeoutId);

        return toast;
    };

    const parseInitialToasts = (root) => {
        const payloadEl = root.querySelector('[data-toast-payload]');
        if (!payloadEl) {
            return [];
        }

        try {
            const payload = JSON.parse(payloadEl.textContent || '[]');
            return Array.isArray(payload) ? payload : [];
        } catch (error) {
            return [];
        }
    };

    const initRoot = (root) => {
        if (!root || root.dataset.toastReady === '1') {
            return;
        }

        root.dataset.toastReady = '1';
        roots.push(root);

        const initialToasts = parseInitialToasts(root);
        initialToasts.forEach((toast) => {
            if (!toast || typeof toast.message !== 'string') {
                return;
            }

            showToastInRoot(root, toast.message, toast.type);
        });
    };

    const initAllRoots = () => {
        document.querySelectorAll('[data-toast-root]').forEach(initRoot);
    };

    const withPrimaryRoot = () => roots[0] || document.querySelector('[data-toast-root]');

    const api = {
        show(message, type = 'success', options = {}) {
            const root = withPrimaryRoot();
            return showToastInRoot(root, message, type, options);
        },
        success(message, options = {}) {
            return this.show(message, 'success', options);
        },
        error(message, options = {}) {
            return this.show(message, 'error', options);
        },
        warning(message, options = {}) {
            return this.show(message, 'warning', options);
        },
        clear() {
            document.querySelectorAll('[data-toast-stack] .toast').forEach((toast) => dismissToast(toast));
        },
    };

    window.Toast = api;

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAllRoots, { once: true });
    } else {
        initAllRoots();
    }
})();
