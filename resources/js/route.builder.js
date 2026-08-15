const routeBuilderForms = document.querySelectorAll('[data-route-builder-form]');

const resetRouteBuilderLoading = () => {
    const loading = document.querySelector('[data-route-builder-loading]');

    if (loading) {
        loading.hidden = true;
    }

    routeBuilderForms.forEach((routeBuilderForm) => {
        const submitButton = routeBuilderForm.querySelector('[data-route-builder-submit]');
        const submitLabel = routeBuilderForm.querySelector('[data-route-builder-submit-label]');
        const status = routeBuilderForm.querySelector('[data-route-builder-status]');

        if (submitButton) {
            submitButton.removeAttribute('aria-busy');
            submitButton.removeAttribute('disabled');
        }

        if (submitLabel && submitLabel.dataset.originalLabel) {
            submitLabel.textContent = submitLabel.dataset.originalLabel;
        }

        if (status) {
            status.hidden = true;
        }
    });
};

routeBuilderForms.forEach((routeBuilderForm) => {
    const submitLabel = routeBuilderForm.querySelector('[data-route-builder-submit-label]');

    if (submitLabel && !submitLabel.dataset.originalLabel) {
        submitLabel.dataset.originalLabel = submitLabel.textContent;
    }

    routeBuilderForm.addEventListener('submit', () => {
        const submitButton = routeBuilderForm.querySelector('[data-route-builder-submit]');
        const currentSubmitLabel = routeBuilderForm.querySelector('[data-route-builder-submit-label]');
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

        if (currentSubmitLabel) {
            currentSubmitLabel.textContent = loadingLabel;
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

window.addEventListener('pageshow', resetRouteBuilderLoading);

const timeAnswerButtons = document.querySelectorAll('[data-time-answer]');

const appendPreferenceAnswer = (routePreferencesForm, answer) => {
    const textarea = routePreferencesForm.querySelector('[name="description"]');

    if (!textarea) {
        return;
    }

    const currentText = textarea.value.trim();
    const normalizedText = currentText.replace(/[.!?…\s]+$/, '');

    textarea.value = normalizedText ? `${normalizedText}. ${answer}` : answer;
};

timeAnswerButtons.forEach((button) => {
    button.addEventListener('click', () => {
        const routePreferencesForm = document.querySelector('[data-route-preferences-form]');

        if (!routePreferencesForm) {
            return;
        }

        const timeConfirmed = routePreferencesForm.querySelector('[data-time-confirmed]');
        const answer = button.dataset.timeAnswer?.trim();

        if (!answer) {
            return;
        }

        appendPreferenceAnswer(routePreferencesForm, answer);

        if (timeConfirmed) {
            timeConfirmed.value = '1';
        }

        routePreferencesForm.requestSubmit();
    });
});

const budgetAnswerButtons = document.querySelectorAll('[data-budget-answer]');

budgetAnswerButtons.forEach((button) => {
    button.addEventListener('click', () => {
        const routePreferencesForm = document.querySelector('[data-route-preferences-form]');

        if (!routePreferencesForm) {
            return;
        }

        const budgetConfirmed = routePreferencesForm.querySelector('[data-budget-confirmed]');
        const answer = button.dataset.budgetAnswer?.trim();

        if (!answer) {
            return;
        }

        appendPreferenceAnswer(routePreferencesForm, answer);

        if (budgetConfirmed) {
            budgetConfirmed.value = '1';
        }

        routePreferencesForm.requestSubmit();
    });
});
