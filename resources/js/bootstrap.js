import axios from 'axios';
window.axios = axios;

/**
 * Standard Laravel Headers
 */
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * CSRF Token Setup
 * Automatically attaches the CSRF token to every Axios request.
 */
let token = document.head.querySelector('meta[name="csrf-token"]');

if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}

/**
 * Global API Error Interceptor
 * Handle session expirations (419) and unauthorized access (401) gracefully.
 */
window.axios.interceptors.response.use(
    response => response,
    error => {
        if (error.response && [401, 419].includes(error.response.status)) {
            // If the session expires, a simple reload sends them to the login page
            window.location.reload();
        }
        
        // Return the error so specific components can still handle it
        return Promise.reject(error);
    }
);