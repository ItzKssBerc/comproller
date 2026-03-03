document.addEventListener('DOMContentLoaded', () => {
    const element = document.getElementById('typewriter');
    if (!element) return;

    const textHu = element.getAttribute('data-text-hu') || 'Üdvözöljük a rendszerben';
    const textEn = element.getAttribute('data-text-en') || 'Welcome to the system';
    const locale = document.documentElement.lang || 'hu';

    const text = locale === 'hu' ? textHu : textEn;
    let index = 0;

    function type() {
        if (index < text.length) {
            element.innerHTML += text.charAt(index);
            index++;
            setTimeout(type, 100);
        } else {
            element.classList.add('cursor-blink');
        }
    }

    type();
});
