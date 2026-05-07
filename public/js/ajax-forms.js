(function() {
    'use strict';

    if (window.__ajaxFormsInitialized) {
        console.log('[ajax-form] Already initialized, skipping.');
        return;
    }
    window.__ajaxFormsInitialized = true;
    console.log('[ajax-form] Global AJAX form handler initialized.');

    function closestCard(el) {
        while (el && el !== document.body) {
            if (el.classList && el.classList.contains('card')) return el;
            el = el.parentElement;
        }
        return null;
    }

    function closestForm(el, selector) {
        while (el && el !== document.body) {
            if (el.tagName === 'FORM' && el.classList && el.classList.contains(selector.replace('form.', ''))) return el;
            el = el.parentElement;
        }
        return null;
    }

    function showFormFeedback(form, type, message) {
        var container = closestCard(form) || form.parentElement;
        var feedback = container ? container.querySelector('.ajax-form-feedback') : null;

        if (!feedback) {
            feedback = document.createElement('div');
            feedback.className = 'ajax-form-feedback';
            feedback.style.cssText = 'padding:12px 16px;border-radius:8px;margin-bottom:16px;font-size:14px;font-weight:500;transition:opacity .3s ease;display:none;';
            if (form.parentNode) {
                form.parentNode.insertBefore(feedback, form);
            }
        }

        feedback.style.display = 'block';
        feedback.style.opacity = '1';
        if (type === 'success') {
            feedback.style.background = 'rgba(34,197,94,.1)';
            feedback.style.color = '#16a34a';
            feedback.style.border = '1px solid rgba(34,197,94,.3)';
            feedback.innerHTML = '<i class="fas fa-check-circle" style="margin-right:6px;"></i> ' + (message || 'Saved successfully.');
        } else {
            feedback.style.background = 'rgba(239,68,68,.1)';
            feedback.style.color = '#dc2626';
            feedback.style.border = '1px solid rgba(239,68,68,.3)';
            feedback.innerHTML = '<i class="fas fa-exclamation-circle" style="margin-right:6px;"></i> ' + (message || 'An error occurred. Please try again.');
        }

        setTimeout(function() {
            feedback.style.opacity = '0';
            setTimeout(function() { feedback.style.display = 'none'; }, 300);
        }, 5000);
    }

    function setButtonLoading(btn, loading) {
        if (!btn) return;
        if (loading) {
            if (!btn.dataset.ajaxOriginalText) {
                btn.dataset.ajaxOriginalText = btn.innerHTML;
            }
            btn.disabled = true;
            btn.innerHTML = '<span style="display:inline-block;width:14px;height:14px;border:2px solid currentColor;border-right-color:transparent;border-radius:50%;animation:ajax-spin 1s linear infinite;margin-right:6px;vertical-align:middle;"></span> ' + (btn.dataset.loadingText || 'Submitting...');
        } else {
            btn.disabled = false;
            btn.innerHTML = btn.dataset.ajaxOriginalText || btn.textContent;
        }
    }

    // Add spinner animation style once
    if (!document.getElementById('ajax-form-styles')) {
        var style = document.createElement('style');
        style.id = 'ajax-form-styles';
        style.textContent = '@keyframes ajax-spin{to{transform:rotate(360deg)}}';
        document.head.appendChild(style);
    }

    document.addEventListener('submit', function(e) {
        var form = closestForm(e.target, 'form.ajax-form');
        if (!form) return;
        console.log('FORM SUBMIT TRIGGERED', form.id || form.getAttribute('action') || form.dataset.action || form.className);

        // Use capture phase to intercept BEFORE inline handlers
// This ensures global AJAX runs first, preventing page reload
        // Note: Can't use {capture:true} here with inline handlers also calling preventDefault
        // Instead, we check if preventDefault was already called

        // If already handled (e.g., by add-form.js), skip silently
        if (e.defaultPrevented) {
            return;
        }

        var action = form.getAttribute('action') || form.dataset.action;
        if (!action) {
            e.preventDefault();
            console.log('[ajax-form] No action found; prevented default submit to avoid page reload');
            showFormFeedback(form, 'success', 'Submitted locally.');
            return;
        }

        console.log('[ajax-form] Intercepting submit on:', form.id || form.action, '->', action);
        e.preventDefault();
        console.log('[ajax-form] preventDefault applied; submitting with fetch/xhr');

        var method = (form.getAttribute('method') || 'POST').toUpperCase();
        var formData = new FormData(form);
        var csrfMeta = document.querySelector('meta[name="csrf-token"]');
        var csrfToken = csrfMeta ? csrfMeta.content : '';
        var apiToken = window.__API_TOKEN || localStorage.getItem('token') || localStorage.getItem('safestep_token') || '';
        var submitBtn = form.querySelector('button[type="submit"], input[type="submit"]');

        if (!formData.has('full_name') && formData.has('name')) {
            formData.append('full_name', formData.get('name'));
        }
        if (!formData.has('name') && formData.has('full_name')) {
            formData.append('name', formData.get('full_name'));
        }
        if (formData.has('role')) {
            formData.set('role', String(formData.get('role')).toLowerCase());
        }

        setButtonLoading(submitBtn, true);

        var headers = {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        };
        if (csrfToken) headers['X-CSRF-TOKEN'] = csrfToken;
        if (apiToken) headers['Authorization'] = 'Bearer ' + apiToken;

        fetch(action, {
            method: method,
            headers: headers,
            body: formData,
            credentials: 'same-origin'
        })
        .then(function(res) {
            return res.json().catch(function() { return {}; }).then(function(data) {
                return { res: res, data: data };
            });
        })
        .then(function(result) {
            var res = result.res;
            var data = result.data;
            setButtonLoading(submitBtn, false);

            var isSuccess = res.ok && (data.status === 'success' || data.success === true);
            console.log('[ajax-form] Response:', res.status, data);

            if (isSuccess) {
                console.log('[ajax-form] Success on:', form.id || form.action);
                var successEvent = new CustomEvent('ajaxform:success', {
                    bubbles: true,
                    cancelable: true,
                    detail: { response: data, form: form }
                });
                form.dispatchEvent(successEvent);

                if (!successEvent.defaultPrevented) {
                    showFormFeedback(form, 'success', data.message || 'Saved successfully.');
                    if (!form.dataset.keepValues) {
                        form.reset();
                    }
                    if (form.dataset.redirectOnSuccess !== 'false' && data.redirect) {
                        window.location.href = data.redirect;
                    }
                }
            } else {
                console.warn('[ajax-form] Error on:', form.id || form.action, data);
                var errorEvent = new CustomEvent('ajaxform:error', {
                    bubbles: true,
                    cancelable: true,
                    detail: { response: data, form: form, status: res.status }
                });
                form.dispatchEvent(errorEvent);

                if (!errorEvent.defaultPrevented) {
                    var msg = data.message;
                    if (!msg && data.errors) {
                        var keys = Object.keys(data.errors);
                        if (keys.length) {
                            var firstErr = data.errors[keys[0]];
                            msg = Array.isArray(firstErr) ? firstErr[0] : firstErr;
                        }
                    }
                    showFormFeedback(form, 'error', msg || 'An error occurred. Please try again.');
                }
            }
        })
        .catch(function(err) {
            setButtonLoading(submitBtn, false);
            console.error('[ajax-form] Network/parse error on:', form.id || form.action, err);
            var errorEvent = new CustomEvent('ajaxform:error', {
                bubbles: true,
                cancelable: true,
                detail: { error: err, form: form }
            });
            form.dispatchEvent(errorEvent);

            if (!errorEvent.defaultPrevented) {
                showFormFeedback(form, 'error', 'Network error. Please check your connection and try again.');
            }
        });
    });
})();
