import Alpine from 'alpinejs';

// Preferencia de texto grande ("AA" en el header)
Alpine.store('a11y', {
    large: (() => {
        try {
            return localStorage.getItem('text-lg') === '1';
        } catch {
            return false;
        }
    })(),
    toggle() {
        this.large = !this.large;
        document.documentElement.classList.toggle('text-lg-mode', this.large);
        try {
            localStorage.setItem('text-lg', this.large ? '1' : '0');
        } catch {}
    },
});

document.documentElement.classList.toggle('text-lg-mode', Alpine.store('a11y').large);

window.Alpine = Alpine;
Alpine.start();

// Animación de entrada de secciones
const revealables = document.querySelectorAll('.reveal');

if ('IntersectionObserver' in window) {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.12 });

    revealables.forEach((el) => observer.observe(el));
} else {
    revealables.forEach((el) => el.classList.add('is-visible'));
}
