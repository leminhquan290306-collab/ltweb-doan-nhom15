/*

* main.js
* Xử lý menu điều hướng trên thiết bị di động.
  */

document.documentElement.classList.add('js');

const nutMenu = document.querySelector('.nut-menu');
const menu = document.querySelector('.menu-list');

if (nutMenu && menu) {
nutMenu.addEventListener('click', () => {
const dangMo = menu.classList.toggle('mo');

    nutMenu.setAttribute('aria-expanded', String(dangMo));
});

document.addEventListener('keydown', (suKien) => {
    if (suKien.key === 'Escape') {
        menu.classList.remove('mo');
        nutMenu.setAttribute('aria-expanded', 'false');
        nutMenu.focus();
    }
});


}
