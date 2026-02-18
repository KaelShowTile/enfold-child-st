document.addEventListener('DOMContentLoaded', function() {
    const pdfBtn = document.getElementById('collection-pdf-btn');
    const pdfBtnText = pdfBtn.textContent;
    const pdfContent = document.getElementById('collection-pdf-content');

    if (pdfBtn && pdfContent) {
        pdfBtn.addEventListener('click', function(e) {
            e.preventDefault();

            // Toggle visibility
            if (pdfContent.style.display === 'none') {
                pdfContent.style.display = 'block';
                pdfBtn.textContent = 'Close Catalogue';
                
                // Trigger a window resize event
                // This forces DearFlip to recalculate its width/height 
                // now that the container is no longer display:none
                window.dispatchEvent(new Event('resize'));
            } else {
                pdfContent.style.display = 'none';
                pdfBtn.textContent = pdfBtnText;
            }
        });
    }

    // Add to Basket Functionality
    const addToBasketButtons = document.querySelectorAll('.add-tile-to-basket');

    addToBasketButtons.forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();

            const cardDetail = this.closest('.tile-card-detail');
            const selectDropdown = cardDetail.querySelector('.select-tile-dropdown');

            if (!selectDropdown) return;

            const selectedOption = selectDropdown.options[selectDropdown.selectedIndex];

            if (!selectedOption) return;

            const productUnqueName = selectedOption.getAttribute('data-product-name');
            const productName = selectedOption.getAttribute('data-tile-name');
            const productFinish = selectedOption.getAttribute('data-finish');
            const productSize = selectedOption.getAttribute('data-size');
            const productImageUrl = selectedOption.getAttribute('data-image-url');

            if (productUnqueName) {
                let basketItems = JSON.parse(localStorage.getItem('idea-basket-items')) || [];
                const existingItem = basketItems.find(item => item.UnqueName === productUnqueName);

                if (!existingItem) {
                    basketItems.push({
                        UnqueName: productUnqueName,
                        name: productName,
                        finish: productFinish,
                        size: productSize,
                        imageUrl: productImageUrl,
                        dateAdded: new Date().toISOString()
                    });

                    localStorage.setItem('idea-basket-items', JSON.stringify(basketItems));
                    showBasketMessage('Item added to Idea Basket!', 'success');
                } else {
                    showBasketMessage('Item already in Idea Basket!', 'info');
                }
            }
        });
    });

    function showBasketMessage(message, type) {
        const existingMessage = document.querySelector('.basket-message');
        if (existingMessage) existingMessage.remove();

        const messageEl = document.createElement('div');
        messageEl.className = `basket-message ${type}`;
        messageEl.textContent = message;
        messageEl.style.cssText = `position: fixed; top: 20px; right: 20px; padding: 10px 20px; border-radius: 5px; color: white; font-weight: bold; z-index: 9999; opacity: 0; transition: opacity 0.3s ease; background-color: ${type === 'success' ? '#28a745' : '#17a2b8'};`;

        document.body.appendChild(messageEl);
        setTimeout(() => { messageEl.style.opacity = '1'; }, 100);
        setTimeout(() => { messageEl.style.opacity = '0'; setTimeout(() => { if (messageEl.parentNode) messageEl.parentNode.removeChild(messageEl); }, 300); }, 3000);
    }
});