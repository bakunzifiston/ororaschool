function revealPublicSections() {
    const nodes = document.querySelectorAll('[data-experience="public"] [data-reveal]');

    if (nodes.length === 0) {
        return;
    }

    const show = (node) => node.classList.add('is-in');

    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches || ! ('IntersectionObserver' in window)) {
        nodes.forEach(show);

        return;
    }

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (! entry.isIntersecting) {
                return;
            }

            show(entry.target);
            observer.unobserve(entry.target);
        });
    }, {
        threshold: 0.14,
        rootMargin: '0px 0px -6% 0px',
    });

    nodes.forEach((node) => observer.observe(node));
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', revealPublicSections);
} else {
    revealPublicSections();
}
