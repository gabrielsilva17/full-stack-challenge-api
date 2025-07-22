import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap';
import axios from 'axios';

window.axios = axios;
axios.defaults.headers.common['X-CSRF-TOKEN'] =
  document.querySelector('meta[name="csrf-token"]').getAttribute('content');
