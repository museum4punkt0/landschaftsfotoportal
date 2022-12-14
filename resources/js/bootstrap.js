window._ = require('lodash');

/**
 * We'll load jQuery and the Bootstrap jQuery plugin which provides support
 * for JavaScript based Bootstrap features such as modals and tabs. This
 * code may be modified to fit the specific needs of your application.
 */

try {
    window.Popper = require('popper.js').default;
    window.$ = window.jQuery = require('jquery');

    require('bootstrap');
} catch (e) {}

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

window.axios = require('axios');

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

// import Echo from 'laravel-echo';

// window.Pusher = require('pusher-js');

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: process.env.MIX_PUSHER_APP_KEY,
//     cluster: process.env.MIX_PUSHER_APP_CLUSTER,
//     encrypted: true
// });

/**
 * jQuery-easing
 */
require('jquery.easing');

/**
 * jQuery-UI
 */

import 'jquery-ui/ui/widgets/autocomplete.js';
import 'jquery-ui/ui/widgets/sortable.js';

/**
 * Summernote WYSIWYG editor, uses Bootstrap v4
 */

import 'summernote/dist/summernote-bs4';
//import 'summernote/dist/lang/summernote-de-DE';

/**
 * OpenLayers map
 */

import osm_map from './map.js';
window.osm_map = osm_map;

/**
 * Diff tools for item revisions
 */

import itemDiff from './diff.js';
window.itemDiff = itemDiff;

/**
 * Sidebar menu
 */

import menu from './menu.js';
window.menu = menu;
