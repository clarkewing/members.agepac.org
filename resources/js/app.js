/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

Vue.component('flash', require('./components/Flash.vue').default);
Vue.component('user-notifications', require('./components/UserNotifications.vue').default);
Vue.component('avatar-form', require('./components/AvatarForm.vue').default);
Vue.component('wysiwyg', require('./components/Wysiwyg.vue').default);
Vue.component('logout-button', require('./components/LogoutButton').default);
Vue.component('thread-result', require('./components/ThreadResult').default);
Vue.component('expanding-search-bar', require('./components/ExpandingSearchBar').default);
Vue.component('payment-method', require('./components/PaymentMethod').default);
Vue.component('new-payment-method', require('./components/NewPaymentMethod').default);

Vue.component('registration-form', require('./pages/RegistrationForm').default);
Vue.component('thread-view', require('./pages/Thread').default);
Vue.component('instant-search', require('./pages/InstantSearch').default);
Vue.component('subscription-plans', require('./pages/SubscriptionPlans').default);

import lineClamp from 'vue-line-clamp';
Vue.use(lineClamp, {textOverflow: ' ...'});

import VuePluralize from 'vue-pluralize';
Vue.use(VuePluralize);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: '#app',
});
