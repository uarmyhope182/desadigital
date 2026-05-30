/*
 * Lightweight DOM helper (Vanilla JS)
 * Replaces common jQuery $ usages with a small, dependency-free API.
 * Provides: $(selector|element|fn) for selection and ready handler,
 * basic methods: on, off, addClass, removeClass, toggleClass, html, text,
 * val, attr, css, append, remove, find, each, ajax (fetch wrapper), serializeForm
 */
(function (global) {
    'use strict';

    function isFunction(obj) {
        return typeof obj === 'function';
    }

    function toArray(list) {
        return Array.prototype.slice.call(list || []);
    }

    function wrap(elements) {
        // Normalize to array of elements
        var arr = [];
        if (!elements) arr = [];
        else if (elements instanceof Element || elements === window || elements === document) arr = [elements];
        else if (elements.nodeType) arr = [elements];
        else if (Array.isArray(elements)) arr = elements;
        else if (NodeList.prototype.isPrototypeOf(elements) || HTMLCollection.prototype.isPrototypeOf(elements)) arr = toArray(elements);
        else arr = [elements];

        return createWrapper(arr);
    }

    function createWrapper(list) {
        return {
            elements: list,
            each: function (fn) {
                list.forEach(function (el, i) { fn.call(el, el, i); });
                return this;
            },
            on: function (evt, selectorOrHandler, handler) {
                // .on(event, handler) or .on(event, selector, handler) for delegation
                if (isFunction(selectorOrHandler)) {
                    list.forEach(function (el) { el.addEventListener(evt, selectorOrHandler); });
                } else {
                    var selector = selectorOrHandler;
                    list.forEach(function (el) {
                        el.addEventListener(evt, function (e) {
                            var target = e.target;
                            while (target && target !== el) {
                                if (target.matches && target.matches(selector)) {
                                    handler.call(target, e);
                                    return;
                                }
                                target = target.parentNode;
                            }
                        });
                    });
                }
                return this;
            },
            off: function (evt, fn) {
                list.forEach(function (el) { el.removeEventListener(evt, fn); });
                return this;
            },
            addClass: function (name) { list.forEach(function (el) { el.classList.add(name); }); return this; },
            removeClass: function (name) { list.forEach(function (el) { el.classList.remove(name); }); return this; },
            toggleClass: function (name) { list.forEach(function (el) { el.classList.toggle(name); }); return this; },
            hasClass: function (name) { return list[0] ? list[0].classList.contains(name) : false; },
            html: function (content) { if (content === undefined) return list[0] ? list[0].innerHTML : null; list.forEach(function (el) { el.innerHTML = content; }); return this; },
            text: function (content) { if (content === undefined) return list[0] ? list[0].textContent : null; list.forEach(function (el) { el.textContent = content; }); return this; },
            val: function (value) { if (value === undefined) return list[0] ? list[0].value : null; list.forEach(function (el) { el.value = value; }); return this; },
            attr: function (name, value) { if (value === undefined) return list[0] ? list[0].getAttribute(name) : null; list.forEach(function (el) { el.setAttribute(name, value); }); return this; },
            css: function (prop, value) { if (value === undefined) return list[0] ? getComputedStyle(list[0])[prop] : null; list.forEach(function (el) { el.style[prop] = value; }); return this; },
            append: function (child) { list.forEach(function (el) { if (typeof child === 'string') el.insertAdjacentHTML('beforeend', child); else el.appendChild(child); }); return this; },
            remove: function () { list.forEach(function (el) { if (el.parentNode) el.parentNode.removeChild(el); }); return this; },
            find: function (selector) { var found = []; list.forEach(function (el) { found = found.concat(toArray(el.querySelectorAll(selector))); }); return wrap(found); },
            closest: function (selector) { return wrap(list[0] ? list[0].closest(selector) : null); },
            modal: function(action) {
                list.forEach(function(el) {
                    if (window.bootstrap && window.bootstrap.Modal) {
                        var inst = bootstrap.Modal.getOrCreateInstance(el);
                        if (action === 'show') inst.show();
                        else if (action === 'hide') inst.hide();
                        else if (action === 'toggle') { /* bootstrap doesn't have toggle; emulate */ inst._isShown ? inst.hide() : inst.show(); }
                    }
                });
                return this;
            }
        };
    }

    function $(selectorOrFn) {
        if (isFunction(selectorOrFn)) {
            // ready
            if (document.readyState === 'complete' || document.readyState === 'interactive') {
                setTimeout(selectorOrFn, 0);
            } else {
                document.addEventListener('DOMContentLoaded', selectorOrFn);
            }
            return;
        }

        if (selectorOrFn === window || selectorOrFn === document) return wrap(selectorOrFn);

        if (typeof selectorOrFn === 'string') {
            selectorOrFn = selectorOrFn.trim();
            try {
                if (selectorOrFn[0] === '<' && selectorOrFn[selectorOrFn.length - 1] === '>') {
                    // Create element from HTML string
                    var container = document.createElement('div');
                    container.innerHTML = selectorOrFn;
                    return wrap(container.firstChild);
                }
            } catch (e) { /* ignore */ }
            var nodeList = document.querySelectorAll(selectorOrFn);
            if (nodeList.length === 1) return wrap(nodeList[0]);
            return wrap(nodeList);
        }

        // element or array
        return wrap(selectorOrFn);
    }

    // $$ returns array of elements
    function $$(selector) { return toArray(document.querySelectorAll(selector)); }

    // Simple ajax wrapper using fetch
    $.ajax = function (opts) {
        opts = opts || {};
        var method = (opts.method || 'GET').toUpperCase();
        var headers = opts.headers || {};
        var body = null;

        if (opts.data) {
            if (opts.processData === false) {
                body = opts.data;
            } else if (headers['Content-Type'] && headers['Content-Type'].indexOf('application/json') !== -1) {
                body = JSON.stringify(opts.data);
            } else if (method === 'GET' || method === 'HEAD') {
                // append to query string
                var url = new URL(opts.url, window.location.origin);
                Object.keys(opts.data).forEach(function (k) { url.searchParams.append(k, opts.data[k]); });
                opts.url = url.toString();
            } else {
                body = new URLSearchParams();
                Object.keys(opts.data).forEach(function (k) { body.append(k, opts.data[k]); });
            }
        }

        var fetchOpts = { method: method, headers: headers, credentials: opts.credentials || 'same-origin' };
        if (body) fetchOpts.body = body;

        return fetch(opts.url, fetchOpts).then(function (res) {
            if (!res.ok) throw new Error('HTTP ' + res.status);
            if (opts.dataType === 'json' || res.headers.get('content-type') && res.headers.get('content-type').indexOf('application/json') !== -1) return res.json();
            return res.text();
        }).then(function (data) {
            if (isFunction(opts.success)) opts.success(data);
            return data;
        }).catch(function (err) {
            if (isFunction(opts.error)) opts.error(err);
            throw err;
        });
    };

    // convenience aliases
    $.get = function (url, data, success, dataType) {
        if (isFunction(data)) { dataType = success; success = data; data = undefined; }
        return $.ajax({ url: url, method: 'GET', data: data, success: success, dataType: dataType });
    };
    $.post = function (url, data, success, dataType) {
        if (isFunction(data)) { dataType = success; success = data; data = undefined; }
        return $.ajax({ url: url, method: 'POST', data: data, success: success, dataType: dataType, headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' } });
    };
    $.getJSON = function (url, data, success) {
        if (isFunction(data)) { success = data; data = undefined; }
        return $.ajax({ url: url, method: 'GET', data: data, success: success, dataType: 'json' });
    };

    // serialize form to object
    $.serializeForm = function (form) {
        var elm = typeof form === 'string' ? document.querySelector(form) : form;
        if (!elm) return {};
        var data = {};
        var fd = new FormData(elm);
        fd.forEach(function (value, key) {
            if (data.hasOwnProperty(key)) {
                if (!Array.isArray(data[key])) data[key] = [data[key]];
                data[key].push(value);
            } else {
                data[key] = value;
            }
        });
        return data;
    };

    // expose helpers
    global.$ = $;
    global.$$ = $$;
    global.DOM = { ready: function (fn) { $(fn); } };

})(window);

/* End of main.js */