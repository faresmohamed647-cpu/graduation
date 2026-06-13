/**
 * SafeStep API Service
 * Standardizes fetch requests, authentication, and error handling.
 */

(function() {
    'use strict';

    if (window.ApiService) return;

    function firstValidationMessage(data, fallback) {
        if (!data) return fallback;
        if (data.errors && typeof data.errors === 'object') {
            for (const value of Object.values(data.errors)) {
                const text = Array.isArray(value) ? value[0] : value;
                if (text) return text;
            }
        }
        const message = data.message;
        if (message && message !== 'Validation failed' && message !== 'Validation failed.') {
            return message;
        }
        return fallback;
    }

    const ApiService = {
        getBaseUrl() {
            return window.location.origin + '/api';
        },

        getHeaders() {
            const token = localStorage.getItem('safestep_token') || localStorage.getItem('token') || window.__API_TOKEN || '';
            const csrfMeta = document.querySelector('meta[name="csrf-token"]');
            
            const headers = {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            };

            if (token) headers['Authorization'] = `Bearer ${token}`;
            if (csrfMeta) headers['X-CSRF-TOKEN'] = csrfMeta.content;

            return headers;
        },

        resolveUrl(endpoint) {
            if (endpoint.startsWith('http')) {
                return endpoint;
            }
            // Endpoints already include /api/... — do not prepend /api again.
            if (endpoint.startsWith('/api/')) {
                return window.location.origin + endpoint;
            }
            return `${this.getBaseUrl()}${endpoint.startsWith('/') ? '' : '/'}${endpoint}`;
        },

        async request(endpoint, options = {}) {
            const url = this.resolveUrl(endpoint);
            
            const config = {
                method: options.method || 'GET',
                headers: { ...this.getHeaders(), ...(options.headers || {}) },
                credentials: 'same-origin'
            };

            if (options.body && typeof options.body === 'object' && !(options.body instanceof FormData)) {
                config.body = JSON.stringify(options.body);
            } else if (options.body) {
                config.body = options.body;
                // Don't set Content-Type for FormData, browser does it with boundary
                if (options.body instanceof FormData) {
                    delete config.headers['Content-Type'];
                }
            }

            try {
                const response = await fetch(url, config);

                if (response.status === 401) {
                    localStorage.removeItem('safestep_token');
                    localStorage.removeItem('token');
                    const role = new URLSearchParams(window.location.search).get('role') || '';
                    window.location.href = role ? `/login?role=${role}` : '/login';
                    throw new Error('Session expired. Please login again.');
                }

                if (response.status === 403) {
                    throw new Error('Access Denied');
                }

                const data = await response.json().catch(() => ({}));

                if (!response.ok) {
                    console.error(`[API] ${config.method} ${endpoint} failed:`, response.status, data);
                    const fallback = response.status === 422
                        ? 'Please check the form and try again.'
                        : `API ${response.status}: ${url}`;
                    throw new Error(firstValidationMessage(data, fallback));
                }

                return data;
            } catch (error) {
                console.error(`[API] Network error on ${endpoint}:`, error);
                throw error;
            }
        },

        get(endpoint) {
            return this.request(endpoint, { method: 'GET' });
        },

        post(endpoint, body) {
            return this.request(endpoint, { method: 'POST', body });
        },

        patch(endpoint, body) {
            return this.request(endpoint, { method: 'PATCH', body });
        },

        put(endpoint, body) {
            return this.request(endpoint, { method: 'PUT', body });
        },

        delete(endpoint) {
            return this.request(endpoint, { method: 'DELETE' });
        }
    };

    window.ApiService = ApiService;
    window.safestepApi = (url, options) => ApiService.request(url, options);
    console.log('[API] Service Initialized with safestepApi alias');
})();
