(() => {
    const reduceMotionQuery = window.matchMedia('(prefers-reduced-motion: reduce)');
    const prefersReducedMotion = reduceMotionQuery.matches;

    const revealTargets = Array.from(
        document.querySelectorAll('.hero, .card, .product-card, .site-footer .footer-inner > div')
    ).filter((el) => !el.closest('#chat-widget'));

    if (revealTargets.length === 0) {
        return;
    }

    revealTargets.forEach((el, index) => {
        el.classList.add('reveal-on-scroll');

        if (el.classList.contains('hero')) {
            el.dataset.reveal = 'up';
        }

        const parent = el.parentElement;
        if (parent && parent.classList.contains('grid') && parent.classList.contains('two')) {
            el.dataset.reveal = index % 2 === 0 ? 'left' : 'right';
        }

        const delayStep = el.classList.contains('product-card') ? 40 : 65;
        const delay = Math.min((index % 6) * delayStep, 220);
        el.style.setProperty('--reveal-delay', `${delay}ms`);
    });

    if (prefersReducedMotion || !('IntersectionObserver' in window)) {
        revealTargets.forEach((el) => el.classList.add('is-visible'));
        return;
    }

    const observer = new IntersectionObserver(
        (entries, instance) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) {
                    return;
                }

                requestAnimationFrame(() => {
                    entry.target.classList.add('is-visible');
                });

                instance.unobserve(entry.target);
            });
        },
        {
            root: null,
            rootMargin: '0px 0px -8% 0px',
            threshold: 0.12,
        }
    );

    revealTargets.forEach((el) => observer.observe(el));
})();
