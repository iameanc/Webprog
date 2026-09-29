const searchForm = document.querySelector('#search-form');
const listingResults = document.querySelector('#listing-results');

if (searchForm && listingResults) {
    let requestNumber = 0;
    let debounceTimer;

    const makeText = (tag, className, text) => {
        const element = document.createElement(tag);
        if (className) element.className = className;
        element.textContent = text;
        return element;
    };

    const renderItems = (items) => {
        listingResults.replaceChildren();
        if (!items.length) {
            listingResults.append(makeText('p', 'empty-state', 'No listings match those filters. Try a broader search.'));
            return;
        }

        for (const item of items) {
            const card = makeText('article', 'item-card', '');
            const photoWrap = makeText('div', 'item-photo-wrap', '');
            const photo = document.createElement('img');
            photo.className = 'item-photo';
            photo.src = item.photo_path;
            photo.alt = `Photo of ${item.title}`;
            photo.loading = 'lazy';
            const typeBadge = makeText('span', `badge badge-${item.item_type}`, item.item_type[0].toUpperCase() + item.item_type.slice(1));
            photoWrap.append(photo, typeBadge);

            const body = makeText('div', 'item-card-body', '');
            const topline = makeText('div', 'item-card-topline', '');
            topline.append(makeText('span', '', item.category_name), makeText('span', `badge badge-${item.status}`, item.status[0].toUpperCase() + item.status.slice(1)));
            const title = makeText('h2', '', item.title);
            const description = makeText('p', 'item-description', item.description);
            const meta = makeText('dl', 'item-meta', '');
            const location = makeText('div', '', '');
            location.append(makeText('dt', '', 'Location'), makeText('dd', '', item.location));
            const date = makeText('div', '', '');
            date.append(makeText('dt', '', 'Date'), makeText('dd', '', new Date(`${item.item_date}T00:00:00`).toLocaleDateString(undefined, { month: 'short', day: 'numeric', year: 'numeric' })));
            meta.append(location, date);
            body.append(topline, title, description, meta);

            if (item.item_type === 'found' && item.status === 'open' && Number(item.user_id) !== Number(window.findItUserId) && window.findItUserId) {
                const form = makeText('form', 'claim-form', '');
                form.method = 'post';
                form.action = 'claim.php';
                const csrf = document.createElement('input');
                csrf.type = 'hidden'; csrf.name = 'csrf_token'; csrf.value = window.findItCsrf;
                const hiddenId = document.createElement('input');
                hiddenId.type = 'hidden'; hiddenId.name = 'item_id'; hiddenId.value = item.id;
                const label = makeText('label', '', 'How can you identify this item?');
                label.htmlFor = `proof-live-${item.id}`;
                const proof = document.createElement('textarea');
                proof.id = label.htmlFor; proof.name = 'proof'; proof.rows = 2; proof.maxLength = 1000; proof.required = true;
                proof.placeholder = 'Describe a detail only the owner would know';
                const submit = makeText('button', 'button button-small', 'Submit claim');
                submit.type = 'submit';
                form.append(csrf, hiddenId, label, proof, submit);
                body.append(form);
            }
            card.append(photoWrap, body);
            listingResults.append(card);
        }
    };

    const refreshListings = async () => {
        const currentRequest = ++requestNumber;
        const params = new URLSearchParams(new FormData(searchForm));
        listingResults.setAttribute('aria-busy', 'true');
        try {
            const response = await fetch(`search.php?${params}`, { headers: { Accept: 'application/json' } });
            if (!response.ok) throw new Error('Search request failed');
            const data = await response.json();
            if (currentRequest !== requestNumber) return;
            renderItems(data.items);
            const count = document.querySelector('#result-count');
            if (count) count.textContent = `${data.count} ${data.count === 1 ? 'item' : 'items'}`;
        } catch {
            if (currentRequest === requestNumber) listingResults.replaceChildren(makeText('p', 'empty-state', 'Listings could not be loaded. Please refresh the page.'));
        } finally {
            if (currentRequest === requestNumber) listingResults.removeAttribute('aria-busy');
        }
    };

    searchForm.addEventListener('input', (event) => {
        if (event.target.matches('input[type="search"]')) {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(refreshListings, 220);
        } else {
            refreshListings();
        }
    });
    searchForm.addEventListener('change', refreshListings);
    searchForm.addEventListener('submit', (event) => { event.preventDefault(); refreshListings(); });
}

const photoInput = document.querySelector('#photo-input');
const photoPreview = document.querySelector('#photo-preview');
if (photoInput && photoPreview) {
    photoInput.addEventListener('change', () => {
        const file = photoInput.files?.[0];
        if (!file) {
            photoPreview.hidden = true;
            photoPreview.removeAttribute('src');
            return;
        }
        photoPreview.src = URL.createObjectURL(file);
        photoPreview.hidden = false;
    });
}
