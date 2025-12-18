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
});