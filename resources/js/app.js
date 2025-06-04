/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

import './bootstrap';
import { createApp } from 'vue';
import '@fortawesome/fontawesome-free/css/all.min.css';
import '@fortawesome/fontawesome-free/js/all.min.js';
import flatpickr from 'flatpickr';
import 'flatpickr/dist/flatpickr.min.css';
import './bootstrap'; // ili obavezni include
import { Serbian } from 'flatpickr/dist/l10n/sr.js';

/**
 * Next, we will create a fresh Vue application instance. You may then begin
 * registering components with the application instance so they are ready
 * to use in your application's views. An example is included for you.
 */

const app = createApp({});

import ExampleComponent from './components/ExampleComponent.vue';
app.component('example-component', ExampleComponent);

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// Object.entries(import.meta.glob('./**/*.vue', { eager: true })).forEach(([path, definition]) => {
//     app.component(path.split('/').pop().replace(/\.\w+$/, ''), definition.default);
// });

/**
 * Finally, we will attach the application instance to a HTML element with
 * an "id" attribute of "app". This element is included with the "auth"
 * scaffolding. Otherwise, you will need to add an element yourself.
 */

//app.mount('#app');

//document.addEventListener("DOMContentLoaded", () => {
//    const navBar = document.getElementById("nav-bar");
//    const toggleButton = document.getElementById("nav-toggle");
//
//    toggleButton.addEventListener("click", () => {
//        navBar.classList.toggle("active");
//    });
//});

// Selektujemo sve elemente s klasom 'toggle-password'
document.querySelectorAll('.toggle-password').forEach(function(toggle) {
    toggle.addEventListener('click', function() {
        // Pretpostavljamo da je input polje odmah prije toggle elementa
        let passwordInput = this.previousElementSibling;
        if (passwordInput && passwordInput.getAttribute('type')) {
            // Ako je trenutno tip 'password', promijeni u 'text'; inače, nazad u 'password'
            const currentType = passwordInput.getAttribute('type');
            passwordInput.setAttribute('type', currentType === 'password' ? 'text' : 'password');
        }
    });
});


document.addEventListener('DOMContentLoaded', function () {
  flatpickr('#service_date', {
    // Vrednost (value) u inputu će biti u formatu "YYYY-MM-DD"
    dateFormat: 'Y-m-d',

    // Korisnik vidi lokalni prikaz "DD.MM.YYYY", ali stvarno value ostaje Y-m-d
    altInput: true,
    altFormat: 'd.m.Y',

    locale: Serbian,

    // Ako hoćeš da datum bude minimalno danas:
    // minDate: "today",
  });
});


document.addEventListener('DOMContentLoaded', function() {
  function adjustChatContainerHeight() {
    const chatContainer = document.querySelector('.chat-container');
    const viewportHeight = window.innerHeight; // Stvarna visina viewport-a
    const headerHeight = document.querySelector('.chat-header').offsetHeight;
    const inputHeight = document.querySelector('#chat-input-container').offsetHeight;

    // Prilagodi visinu chat-container-a
    chatContainer.style.maxHeight = `${viewportHeight - headerHeight - inputHeight}px`;
  }

  // Pozovi funkciju na učitavanje stranice i pri promeni veličine prozora
  adjustChatContainerHeight();
  window.addEventListener('resize', adjustChatContainerHeight);
});

