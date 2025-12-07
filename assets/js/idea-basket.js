jQuery(document).ready(function($) {
    'use strict';

    // Load and display basket items
    function loadBasketItems() {
        const basketItems = JSON.parse(localStorage.getItem('idea-basket-items')) || [];
        const $basketContainer = $('#basket-items');
        const $emptyMessage = $('#empty-basket');
        const $submitContainer = $('#submit-btn-container');
        const $tagline = $('#idea-basket-tagline');

        $basketContainer.empty();

        if (basketItems.length === 0) {
            $emptyMessage.show();
            $submitContainer.hide();
            $tagline.hide();
            return;
        }

        $emptyMessage.hide();
        $submitContainer.show();
        $tagline.show();

        // Sort items by date added (newest first)
        basketItems.sort((a, b) => new Date(b.dateAdded) - new Date(a.dateAdded));

        basketItems.forEach(function(item, index) {
            const itemHtml =`
                <div class="basket-item" data-index="${index}">
                    <div class="basket-item-image">
                        ${item.imageUrl ? `<img src="${item.imageUrl}" alt="${item.name}" loading="lazy">` : '<div class="no-image">No Image</div>'}
                        <div class="basket-item-actions">
                            <button class="btn btn-danger btn-sm remove-item" data-index="${index}"><i class="fas fa-trash"></i>X</button>
                        </div>
                    </div>
                    <div class="basket-item-details">
                        <h3 class="basket-item-title">${escapeHtml(item.name)}</h3>
                        <p>${escapeHtml(item.finish)}</p>
                        <p>${escapeHtml(item.size)}</p>
                    </div>
                    
                </div>
            `;
            $basketContainer.append(itemHtml);
        });
    }

    // Get image URL from attachment ID
    function getImageUrl(imageId) {
        // Use WordPress AJAX to get image URL
        // For now, return a placeholder - this would need server-side handling
        return `/wp-content/uploads/${imageId}.jpg`; // This is a placeholder
    }

    // Format date for display
    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString() + ' ' + date.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
    }

    // Escape HTML to prevent XSS
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Remove item from basket
    $(document).on('click', '.remove-item', function() {
        const index = $(this).data('index');
        let basketItems = JSON.parse(localStorage.getItem('idea-basket-items')) || [];

        if (index >= 0 && index < basketItems.length) {
            // Remove the item
            basketItems.splice(index, 1);

            // Save updated basket
            localStorage.setItem('idea-basket-items', JSON.stringify(basketItems));

            // Reload the basket display
            loadBasketItems();

            // Show success message
            showBasketMessage('Item removed from Idea Basket!', 'success');
        }
    });

    // Show message function (similar to single-tile.js)
    function showBasketMessage(message, type) {
        // Remove existing message
        const existingMessage = document.querySelector('.basket-message');
        if (existingMessage) {
            existingMessage.remove();
        }

        // Create message element
        const messageEl = document.createElement('div');
        messageEl.className = `basket-message ${type}`;
        messageEl.textContent = message;
        messageEl.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 10px 20px;
            border-radius: 5px;
            color: white;
            font-weight: bold;
            z-index: 9999;
            opacity: 0;
            transition: opacity 0.3s ease;
        `;

        if (type === 'success') {
            messageEl.style.backgroundColor = '#000000';
        } else {
            messageEl.style.backgroundColor = '#dc3545';
        }

        document.body.appendChild(messageEl);

        // Show message
        setTimeout(() => {
            messageEl.style.opacity = '1';
        }, 100);

        // Hide message after 3 seconds
        setTimeout(() => {
            messageEl.style.opacity = '0';
            setTimeout(() => {
                if (messageEl.parentNode) {
                    messageEl.parentNode.removeChild(messageEl);
                }
            }, 300);
        }, 3000);
    }

    // Load basket items on page load
    loadBasketItems();
});
