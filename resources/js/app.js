import "../css/app.css";

import * as bootstrap from "bootstrap";
import { initializeRouteMaps } from "./maps/route-map";
import { initializeHeatmap } from "./dashboard/heatmap";
import './route.builder';

window.bootstrap = bootstrap;

const root = document.documentElement;
const decreaseFontButton = document.querySelector("#decrease-font");
const increaseFontButton = document.querySelector("#increase-font");
const contrastButton = document.querySelector("#contrast-toggle");
const themeButton = document.querySelector("#theme-toggle");
const themeLabel = themeButton?.querySelector(".theme-label");
const themeSymbol = themeButton?.querySelector(".theme-symbol");

let fontScale = 1;
const storedTheme = localStorage.getItem("rota-viva-theme");

const applyTheme = (theme) => {
    const normalizedTheme = theme === "dark" ? "dark" : "light";

    root.dataset.theme = normalizedTheme;
    localStorage.setItem("rota-viva-theme", normalizedTheme);

    themeButton?.setAttribute(
        "aria-pressed",
        String(normalizedTheme === "dark"),
    );

    if (themeLabel) {
        themeLabel.textContent =
            normalizedTheme === "dark" ? "Tema claro" : "Tema escuro";
    }

    if (themeSymbol) {
        themeSymbol.textContent = normalizedTheme === "dark" ? "☀" : "☾";
    }
};

applyTheme(storedTheme ?? "light");

const updateFontScale = (nextScale) => {
    fontScale = Math.min(1.2, Math.max(0.9, nextScale));
    root.style.setProperty("--font-scale", fontScale);
};

decreaseFontButton?.addEventListener("click", () =>
    updateFontScale(fontScale - 0.1),
);
increaseFontButton?.addEventListener("click", () =>
    updateFontScale(fontScale + 0.1),
);

contrastButton?.addEventListener("click", () => {
    const isHighContrast = document.body.classList.toggle("high-contrast");
    contrastButton.setAttribute("aria-pressed", String(isHighContrast));
});

themeButton?.addEventListener("click", () => {
    applyTheme(root.dataset.theme === "dark" ? "light" : "dark");
});

document.addEventListener("DOMContentLoaded", () => {
    initializeRouteMaps();
    initializeHeatmap();
});
