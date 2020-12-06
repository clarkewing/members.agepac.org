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
Vue.component('wysiwyg', require('./components/Wysiwyg.vue').default);
Vue.component('logout-button', require('./components/LogoutButton').default);
Vue.component('thread-result', require('./components/ThreadResult').default);
Vue.component('expanding-search-bar', require('./components/ExpandingSearchBar').default);
Vue.component('payment-method', require('./components/PaymentMethod').default);
Vue.component('new-payment-method', require('./components/NewPaymentMethod').default);
Vue.component('profile.avatar', require('./components/Profile/Avatar').default);
Vue.component('profile.tags', require('./components/Profile/Tags').default);
Vue.component('profile.bio', require('./components/Profile/Bio').default);
Vue.component('profile.flight-hours', require('./components/Profile/FlightHours').default);
Vue.component('profile.location', require('./components/Profile/Location').default);
Vue.component('profile.experience', require('./components/Profile/Experience').default);
Vue.component('profile.education', require('./components/Profile/Education').default);
Vue.component('profile-result', require('./components/ProfileResult').default);
Vue.component('create-company', require('./components/CreateCompany').default);
Vue.component('company-result', require('./components/CompanyResult').default);

Vue.component('thread-view', require('./pages/Thread').default);
Vue.component('instant-search', require('./pages/InstantSearch').default);
Vue.component('subscription-plans', require('./pages/SubscriptionPlans').default);
Vue.component('company-view', require('./pages/Company').default);

Vue.component('thread-poll', require('./components/ThreadPoll').default);

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
