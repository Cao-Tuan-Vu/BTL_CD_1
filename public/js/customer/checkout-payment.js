(() => {
    const radios = document.querySelectorAll('[data-payment-method]');
    const panels = document.querySelectorAll('[data-payment-panel]');

    if (radios.length === 0 || panels.length === 0) {
        return;
    }

    const syncPanelVisibility = () => {
        const checkedRadio = document.querySelector('[data-payment-method]:checked');
        const selectedCode = checkedRadio ? checkedRadio.dataset.methodCode : '';

        panels.forEach((panel) => {
            panel.hidden = panel.dataset.paymentPanel !== selectedCode;
        });
    };

    radios.forEach((radio) => {
        radio.addEventListener('change', syncPanelVisibility);
    });

    syncPanelVisibility();
})();
