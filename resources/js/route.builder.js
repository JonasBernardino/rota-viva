const routeBuilderForm = document.querySelector('[data-route-builder-form]');

routeBuilderForm?.addEventListener('submit', () => {
    const submitButton = routeBuilderForm.querySelector('[data-route-builder-submit]');
    const submitLabel = routeBuilderForm.querySelector('[data-route-builder-submit-label]');
    const status = routeBuilderForm.querySelector('[data-route-builder-status]');
    const loading = document.querySelector('[data-route-builder-loading]');

    if (submitButton) {
        submitButton.setAttribute('aria-busy', 'true');
        submitButton.setAttribute('disabled', 'disabled');
    }

    if (submitLabel) {
        submitLabel.textContent = 'Criando sua rota...';
    }

    if (status) {
        status.hidden = false;
    }

    if (loading) {
        loading.hidden = false;
    }
});
