import './bootstrap';
import apiClient from './api-client';
import * as auth from './auth';
import * as utils from './utils';
import Alpine from 'alpinejs';

window.apiClient = apiClient;
window.auth = auth;
window.utils = { ...window.utils, ...utils };
window.Alpine = Alpine;

Alpine.start();
