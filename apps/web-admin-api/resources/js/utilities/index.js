export function isBrowser() {
    return typeof window !== 'undefined';
}

function isModifiedClick(event) {
    return event.metaKey || event.ctrlKey || event.shiftKey || event.altKey || event.button !== 0;
}

function markPageLeaving() {
    window.requestAnimationFrame(() => {
        document.body.classList.add('is-page-leaving');
    });
}

function canSoftNavigate(url) {
    return url.origin === window.location.origin && ['http:', 'https:'].includes(url.protocol);
}

function isHashOnlyNavigation(url) {
    return url.hash && url.pathname === window.location.pathname && url.search === window.location.search;
}

function copyHeadState(nextDocument) {
    document.title = nextDocument.title;

    const currentToken = document.querySelector('meta[name="csrf-token"]');
    const nextToken = nextDocument.querySelector('meta[name="csrf-token"]');

    if (currentToken && nextToken) {
        currentToken.setAttribute('content', nextToken.getAttribute('content') || '');
    }
}

function initializeDynamicDom() {
    window.Alpine?.initTree?.(document.body);
    document.dispatchEvent(new CustomEvent('mms:navigated'));
}

let activeNavigationController = null;

async function softNavigate(url, { push = true } = {}) {
    if (activeNavigationController) {
        activeNavigationController.abort();
    }

    activeNavigationController = new AbortController();
    document.body.classList.add('is-soft-navigating');

    try {
        const response = await fetch(url.href, {
            method: 'GET',
            headers: {
                Accept: 'text/html',
                'X-Requested-With': 'XMLHttpRequest',
            },
            signal: activeNavigationController.signal,
        });

        if (!response.ok || !response.headers.get('content-type')?.includes('text/html')) {
            window.location.assign(url.href);
            return;
        }

        const html = await response.text();
        const nextDocument = new DOMParser().parseFromString(html, 'text/html');

        copyHeadState(nextDocument);
        document.body.className = nextDocument.body.className;
        document.body.innerHTML = nextDocument.body.innerHTML;

        if (push) {
            window.history.pushState({}, '', url.href);
        }

        window.scrollTo({ top: 0, left: 0, behavior: 'auto' });
        initializeDynamicDom();
    } catch (error) {
        if (error.name !== 'AbortError') {
            window.location.assign(url.href);
        }
    } finally {
        activeNavigationController = null;
        document.body.classList.remove('is-page-leaving');
        document.body.classList.remove('is-soft-navigating');
    }
}

if (isBrowser()) {
    window.addEventListener('pageshow', () => {
        document.body.classList.remove('is-page-leaving');
    });

    document.addEventListener('click', (event) => {
        if (isModifiedClick(event) || event.defaultPrevented) {
            return;
        }

        const link = event.target.closest('a[href]');

        if (! link || link.hasAttribute('download') || link.dataset.noTransition === 'true' || link.dataset.noSoftNav === 'true' || link.target) {
            return;
        }

        const url = new URL(link.href, window.location.href);

        if (! canSoftNavigate(url) || isHashOnlyNavigation(url)) {
            return;
        }

        event.preventDefault();
        softNavigate(url);
    });

    document.addEventListener('submit', (event) => {
        const form = event.target;

        if (! form || form.dataset.noTransition === 'true' || form.target === '_blank') {
            return;
        }

        const method = (form.getAttribute('method') || 'GET').toUpperCase();

        if (method === 'GET') {
            const url = new URL(form.action || window.location.href, window.location.href);
            const formData = new FormData(form);

            url.search = new URLSearchParams(formData).toString();

            if (canSoftNavigate(url)) {
                event.preventDefault();
                softNavigate(url);
                return;
            }
        }

        markPageLeaving();
    });

    window.addEventListener('popstate', () => {
        const url = new URL(window.location.href);

        if (canSoftNavigate(url)) {
            softNavigate(url, { push: false });
        }
    });
}
