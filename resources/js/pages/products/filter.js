// Real-time filtering module for products list
let filterTimeout = null;

function showFilterError() {
    const list = document.getElementById('productsList');
    if (!list) return;

    let banner = document.getElementById('filterErrorBanner');
    if (!banner) {
        banner = document.createElement('div');
        banner.id = 'filterErrorBanner';
        banner.className =
            'flash-error col-span-full rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-medium text-rose-800';
        banner.textContent = "Couldn't load products — check your connection and try again.";
        list.prepend(banner);
    }
}

function clearFilterError() {
    document.getElementById('filterErrorBanner')?.remove();
}

function applyFilters() {
    const form = document.getElementById('filterForm');
    if (!form) return;

    const url = form.dataset.url;
    const filters = {
        search: document.getElementById('search').value,
        category: document.getElementById('category-filter').value,
        brand: document.getElementById('brand-filter').value,
        min_price: document.getElementById('min_price').value,
        max_price: document.getElementById('max_price').value,
        in_stock: document.getElementById('in_stock').checked ? 1 : 0
    };

    const queryParams = new URLSearchParams();
    Object.keys(filters).forEach(key => {
        if (filters[key] !== '' && filters[key] !== 0) {
            queryParams.append(key, filters[key]);
        }
    });

    const list = document.getElementById('productsList');
    if (list) list.classList.add('opacity-50');

    fetch(url + '?' + queryParams.toString(), {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
        .then(response => {
            if (!response.ok) throw new Error('Request failed');
            return response.text();
        })
        .then(html => {
            clearFilterError();
            if (list) {
                list.innerHTML = html;
                bindPaginationLinks();
            }
        })
        .catch(() => showFilterError())
        .finally(() => {
            if (list) list.classList.remove('opacity-50');
        });
}

function bindFilterEvents() {
    const ids = ['search', 'category-filter', 'brand-filter', 'min_price', 'max_price', 'in_stock'];
    ids.forEach(id => {
        const el = document.getElementById(id);
        if (!el) return;
        const useInputEvent = el.tagName === 'INPUT' && el.type !== 'checkbox';
        const event = useInputEvent ? 'input' : 'change';
        el.addEventListener(event, () => {
            if (useInputEvent) {
                clearTimeout(filterTimeout);
                filterTimeout = setTimeout(applyFilters, 180);
                return;
            }

            applyFilters();
        });
    });

    const reset = document.getElementById('resetBtn');
    if (reset) {
        reset.addEventListener('click', () => {
            ids.forEach(id => {
                const el = document.getElementById(id);
                if (!el) return;
                if (el.type === 'checkbox') el.checked = false;
                else el.value = '';
            });
            applyFilters();
        });
    }
}

// handle pagination link clicks inside the products list
function bindPaginationLinks() {
    const list = document.getElementById('productsList');
    if (!list) return;
    list.querySelectorAll('.pagination a').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const url = this.getAttribute('href');
            if (!url) return;
            // fetch page using existing filters
            list.classList.add('opacity-50');
            fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(r => {
                    if (!r.ok) throw new Error('Request failed');
                    return r.text();
                })
                .then(html => {
                    clearFilterError();
                    list.innerHTML = html;
                    bindPaginationLinks();
                })
                .catch(() => showFilterError())
                .finally(() => list.classList.remove('opacity-50'));
        });
    });
}

// initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        bindFilterEvents();
        bindPaginationLinks();
    });
} else {
    bindFilterEvents();
    bindPaginationLinks();
}

export { applyFilters, bindFilterEvents, bindPaginationLinks };
