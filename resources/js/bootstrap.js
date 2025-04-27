import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Import Bootstrap JS components separately
// Note: We're importing these in app.js now
