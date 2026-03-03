// Real-time filtering module for products list

function applyFilters() {
    const form = document.getElementById('filterForm');
    if (!form) return;

    const url = form.dataset.url;
    const filters = {
        category: document.getElementById('category').value,
        brand: document.getElementById('brand').value,
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

    fetch(url + '?' + queryParams.toString(), {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
        .then(response => response.text())
        .then(html => {
            const list = document.getElementById('productsList');
            if (list) {
                list.innerHTML = html;
                bindPaginationLinks();
            }
        })
        .catch(console.error);
}

function bindFilterEvents() {
    const ids = ['category', 'brand', 'min_price', 'max_price', 'in_stock'];
    ids.forEach(id => {
        const el = document.getElementById(id);
        if (!el) return;
        const event = el.tagName === 'INPUT' && el.type === 'number' ? 'input' : 'change';
        el.addEventListener(event, applyFilters);
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
            fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(r => r.text())
                .then(html => {
                    list.innerHTML = html;
                    bindPaginationLinks();
                })
                .catch(console.error);
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
