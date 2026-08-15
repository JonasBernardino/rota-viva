const routeBuilderForms = document.querySelectorAll('[data-route-builder-form]');

routeBuilderForms.forEach((routeBuilderForm) => {
    routeBuilderForm.addEventListener('submit', () => {
        const submitButton = routeBuilderForm.querySelector('[data-route-builder-submit]');
        const submitLabel = routeBuilderForm.querySelector('[data-route-builder-submit-label]');
        const status = routeBuilderForm.querySelector('[data-route-builder-status]');
        const loading = document.querySelector('[data-route-builder-loading]');

        const loadingLabel = routeBuilderForm.dataset.loadingLabel ?? 'Processando...';
        const loadingTitle = routeBuilderForm.dataset.loadingTitle;
        const loadingDescription = routeBuilderForm.dataset.loadingDescription;

        const loadingPanel = loading?.querySelector('.route-builder-loading__panel');
        const loadingTitleElement = loadingPanel?.querySelector('strong');
        const loadingDescriptionElement = loadingPanel?.querySelector('span:last-child');

        if (submitButton) {
            submitButton.setAttribute('aria-busy', 'true');
            submitButton.setAttribute('disabled', 'disabled');
        }

        if (submitLabel) {
            submitLabel.textContent = loadingLabel;
        }

        if (loadingTitle && loadingTitleElement) {
            loadingTitleElement.textContent = loadingTitle;
        }

        if (loadingDescription && loadingDescriptionElement) {
            loadingDescriptionElement.textContent = loadingDescription;
        }

        if (status) {
            status.hidden = false;
        }

        if (loading) {
            loading.hidden = false;
        }
    });
});
