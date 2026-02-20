const tabButtons = document.querySelectorAll('[role="tab"]');
const tabPanels = document.querySelectorAll('[role="tabpanel"]');

tabButtons.forEach(btn => {
    btn.addEventListener('click', (e) => {
        const targetPanelId = e.target.getAttribute('aria-controls');

        // 1. Reset all tabs
        tabButtons.forEach(button => button.setAttribute('aria-selected', 'false'));
        
        // 2. Hide all panels
        tabPanels.forEach(panel => panel.setAttribute('hidden', 'true'));

        // 3. Activate the clicked tab and its panel
        e.target.setAttribute('aria-selected', 'true');
        document.getElementById(targetPanelId).removeAttribute('hidden');
    });
});