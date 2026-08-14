import '../css/app.css';

import * as bootstrap from 'bootstrap';

window.bootstrap = bootstrap;

const root = document.documentElement;
const decreaseFontButton = document.querySelector('#decrease-font');
const increaseFontButton = document.querySelector('#increase-font');
const contrastButton = document.querySelector('#contrast-toggle');

let fontScale = 1;

const updateFontScale = (nextScale) => {
    fontScale = Math.min(1.2, Math.max(0.9, nextScale));
    root.style.setProperty('--font-scale', fontScale);
};

decreaseFontButton?.addEventListener('click', () => updateFontScale(fontScale - 0.1));
increaseFontButton?.addEventListener('click', () => updateFontScale(fontScale + 0.1));

contrastButton?.addEventListener('click', () => {
    const isHighContrast = document.body.classList.toggle('high-contrast');
    contrastButton.setAttribute('aria-pressed', String(isHighContrast));
});
