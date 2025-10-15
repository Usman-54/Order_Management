document.addEventListener('DOMContentLoaded', () => {
    const modalEl = document.getElementById('productModal');
    const modalBody = modalEl.querySelector('.modal-body');
    const bsModal = new bootstrap.Modal(modalEl);

    // Dynamic + button: load product detail HTML into modal
    document.querySelectorAll('.add-product-btn').forEach(btn => {
        btn.addEventListener('click', async function () {
            const id = this.dataset.id;
            console.log('Loading productDetail for id:', id);
            try {
                const res = await fetch('productDetail.php?id=' + encodeURIComponent(id));
                if (!res.ok) throw new Error('Network error: ' + res.status);
                const html = await res.text();
                modalBody.innerHTML = html;
                bsModal.show();
            } catch (err) {
                console.error(err);
                modalBody.innerHTML = '<div class="alert alert-danger">Could not load product details.</div>';
                bsModal.show();
            }
        });
    });

    // Static info button
    document.querySelectorAll('.open-info-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const title = this.dataset.title || '';
            const desc  = this.dataset.desc  || '';
            const price = this.dataset.price || '0.00';
            modalBody.innerHTML = `
                <h4>${title}</h4>
                <p>${desc}</p>
                <p><strong>Price: $${parseFloat(price).toFixed(2)}</strong></p>
            `;
            bsModal.show();
        });
    });
});
