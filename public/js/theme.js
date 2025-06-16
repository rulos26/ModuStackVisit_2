document.addEventListener('DOMContentLoaded', function() {
    // Función para detectar el tema del sistema
    function detectSystemTheme() {
        return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
    }

    // Función para aplicar el tema
    function applyTheme(theme) {
        document.documentElement.setAttribute('data-bs-theme', theme);
    }

    // Aplicar tema inicial
    applyTheme(detectSystemTheme());

    // Escuchar cambios en el tema del sistema
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', e => {
        applyTheme(e.matches ? 'dark' : 'light');
    });
}); 