document.addEventListener('DOMContentLoaded', function () {
    if (document.getElementById('tile-gallery')) {
        var swiper = new Swiper("#tile-gallery", {
            slidesPerView: "auto",
            spaceBetween: 0,
            grabCursor: true,
            navigation: {
                nextEl: ".swiper-button-next",
                prevEl: ".swiper-button-prev",
            },
            scrollbar: {
                el: ".swiper-scrollbar",
                clickable: true,
            },
            mousewheel: true,
            keyboard: {
                enabled: true,
            },
        });
    }

    // Idea Basket functionality
    const addToBasketLinks = document.querySelectorAll('#add-to-basket');
    addToBasketLinks.forEach(function(link) {
        link.addEventListener('click', function(e) {
            e.preventDefault();

            const productUnqueName = this.getAttribute('data-product-name');
            const productName = this.getAttribute('data-tile-name');
            const productFinish = this.getAttribute('data-product-finish');
            const productSize = this.getAttribute('data-product-size');
            const productImageId = this.getAttribute('data-product-image_id');
            const productImageUrl = this.getAttribute('data-product-image_url');

            if (productUnqueName && productImageId) {
                // Get existing basket items
                let basketItems = JSON.parse(localStorage.getItem('idea-basket-items')) || [];

                // Check if item already exists
                const existingItem = basketItems.find(item => item.name === productUnqueName && item.imageId === productImageId);

                if (!existingItem) {
                    // Add new item
                    basketItems.push({
                        UnqueName: productUnqueName,
                        name: productName,
                        finish: productFinish,
                        size: productSize,
                        imageId: productImageId,
                        imageUrl: productImageUrl,
                        dateAdded: new Date().toISOString()
                    });

                    // Save to localStorage
                    localStorage.setItem('idea-basket-items', JSON.stringify(basketItems));

                    // Show success message
                    showBasketMessage('Item added to Idea Basket!', 'success');
                } else {
                    // Show already added message
                    showBasketMessage('Item already in Idea Basket!', 'info');
                }
            }
        });
    });

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
            messageEl.style.backgroundColor = '#28a745';
        } else {
            messageEl.style.backgroundColor = '#17a2b8';
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
});


var swiperThumb = new Swiper(".collection-inner-slider-thumb", {
    spaceBetween: 10,
    slidesPerView: 4,
    freeMode: true,
    watchSlidesProgress: true,
});
var swiperPreview = new Swiper(".collection-inner-slider-preview", {
    spaceBetween: 10,
    thumbs: {
    swiper: swiperThumb,
    },
});
