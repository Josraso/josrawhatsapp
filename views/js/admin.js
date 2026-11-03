/*
 * JosraWhatsapp - Back-office
 * Sin dependencias: las pestanas se mueven con JS propio para no depender
 * de la version de Bootstrap del tema de administracion (1.6 a 9).
 */
(function () {
    'use strict';

    function onReady(fn) {
        if (document.readyState !== 'loading') {
            fn();
        } else {
            document.addEventListener('DOMContentLoaded', fn);
        }
    }

    function all(root, sel) {
        return Array.prototype.slice.call(root.querySelectorAll(sel));
    }

    function addClass(el, c) {
        if (el && (' ' + el.className + ' ').indexOf(' ' + c + ' ') === -1) {
            el.className = (el.className + ' ' + c).replace(/^\s+/, '');
        }
    }

    function removeClass(el, c) {
        if (!el) { return; }
        el.className = (' ' + el.className + ' ').replace(' ' + c + ' ', ' ').replace(/^\s+|\s+$/g, '');
    }

    function hasClass(el, c) {
        return el && (' ' + el.className + ' ').indexOf(' ' + c + ' ') !== -1;
    }

    /* ---------------- Pestanas ---------------- */

    function initTabs(root) {
        var nav = document.getElementById('josra-nav');
        if (!nav) { return; }

        var links = all(nav, 'a[data-josra-tab]');

        function activate(id) {
            var i;
            for (i = 0; i < links.length; i++) {
                var isTarget = links[i].getAttribute('data-josra-tab') === id;
                if (isTarget) {
                    addClass(links[i], 'active');
                    addClass(links[i].parentNode, 'active');
                } else {
                    removeClass(links[i], 'active');
                    removeClass(links[i].parentNode, 'active');
                }
            }

            var panes = all(root, '.josra-pane');
            for (i = 0; i < panes.length; i++) {
                if (panes[i].id === 'josra-pane-' + id) {
                    addClass(panes[i], 'active');
                    addClass(panes[i], 'in');
                } else {
                    removeClass(panes[i], 'active');
                    removeClass(panes[i], 'in');
                }
            }

            try {
                window.sessionStorage.setItem('josrawhatsapp_tab', id);
            } catch (e) {
                // sin sessionStorage tampoco pasa nada
            }
        }

        for (var i = 0; i < links.length; i++) {
            links[i].onclick = (function (link) {
                return function (ev) {
                    if (ev && ev.preventDefault) { ev.preventDefault(); }
                    activate(link.getAttribute('data-josra-tab'));
                    return false;
                };
            }(links[i]));
        }

        // Si no venimos de guardar, recuperamos la ultima pestana abierta
        var activeNow = null;
        for (var j = 0; j < links.length; j++) {
            if (hasClass(links[j], 'active')) {
                activeNow = links[j].getAttribute('data-josra-tab');
            }
        }
        if (activeNow === 'general') {
            try {
                var saved = window.sessionStorage.getItem('josrawhatsapp_tab');
                if (saved) {
                    var exists = false;
                    for (var k = 0; k < links.length; k++) {
                        if (links[k].getAttribute('data-josra-tab') === saved) { exists = true; }
                    }
                    if (exists) { activate(saved); }
                }
            } catch (e) {
                // nada
            }
        }
    }

    /* ---------------- Rejilla de horarios ---------------- */

    function setDay(button, enabled) {
        var input = document.getElementById(button.getAttribute('data-target'));
        if (!input) { return; }

        input.value = enabled ? '1' : '0';
        button.className = 'josra-day-toggle ' + (enabled ? 'josra-on' : 'josra-off');

        var state = button.querySelector('.josra-day-state');
        if (state) {
            state.innerHTML = enabled
                ? button.getAttribute('data-on')
                : button.getAttribute('data-off');
        }
    }

    function setShift2(day, enabled) {
        var block = document.getElementById('josra_shift2_' + day);
        var flag = document.getElementById('josra_t2_enabled_' + day);
        var button = document.querySelector('.josra-shift2-toggle[data-day="' + day + '"]');
        if (!block || !flag || !button) { return; }

        flag.value = enabled ? '1' : '0';
        block.style.display = enabled ? '' : 'none';
        button.innerHTML = enabled
            ? button.getAttribute('data-remove')
            : button.getAttribute('data-add');
    }

    function initSchedule() {
        var schedule = document.querySelector('.josra-schedule');
        if (!schedule) { return; }

        var toggles = all(schedule, '.josra-day-toggle');
        var i;

        for (i = 0; i < toggles.length; i++) {
            toggles[i].onclick = (function (button) {
                return function () {
                    var input = document.getElementById(button.getAttribute('data-target'));
                    setDay(button, !(input && input.value === '1'));
                };
            }(toggles[i]));
        }

        var shiftButtons = all(schedule, '.josra-shift2-toggle');
        for (i = 0; i < shiftButtons.length; i++) {
            shiftButtons[i].onclick = (function (button) {
                return function () {
                    var day = button.getAttribute('data-day');
                    var flag = document.getElementById('josra_t2_enabled_' + day);
                    setShift2(day, !(flag && flag.value === '1'));
                };
            }(shiftButtons[i]));
        }

        var copy = document.getElementById('josra_copy_monday');
        if (copy) {
            copy.onclick = function () {
                var days = ['martes', 'miercoles', 'jueves', 'viernes', 'sabado', 'domingo'];
                var fields = ['t1_from', 't1_to', 't2_from', 't2_to'];
                var srcFlag = document.getElementById('josra_t2_enabled_lunes');
                var a;
                var b;

                for (a = 0; a < days.length; a++) {
                    for (b = 0; b < fields.length; b++) {
                        var src = schedule.querySelector('[name="josra_' + fields[b] + '_lunes"]');
                        var dst = schedule.querySelector('[name="josra_' + fields[b] + '_' + days[a] + '"]');
                        if (src && dst) { dst.value = src.value; }
                    }
                    if (srcFlag) {
                        setShift2(days[a], srcFlag.value === '1');
                    }
                }
            };
        }

        var openAll = document.getElementById('josra_open_all');
        if (openAll) {
            openAll.onclick = function () {
                for (var a = 0; a < toggles.length; a++) { setDay(toggles[a], true); }
            };
        }

        var closeWeekend = document.getElementById('josra_close_weekend');
        if (closeWeekend) {
            closeWeekend.onclick = function () {
                var weekend = ['sabado', 'domingo'];
                for (var a = 0; a < weekend.length; a++) {
                    var button = schedule.querySelector('[data-target="josra_day_enabled_' + weekend[a] + '"]');
                    if (button) { setDay(button, false); }
                }
            };
        }
    }

    /* ---------------- Vista previa en vivo ---------------- */

    function initPreview(root) {
        var img = document.getElementById('josra_preview_img');
        var imgMobile = document.getElementById('josra_preview_img_mobile');
        if (!img || !imgMobile) { return; }

        function field(name) {
            return root.querySelector('[name="' + name + '"]');
        }

        function refresh() {
            var sizeEl = field('JOSRA_WAP_ICON_SIZE');
            var colorEl = field('JOSRA_WAP_ICON_COLOR');
            var scaleEl = field('JOSRA_WAP_MOBILE_SCALE');

            var size = sizeEl ? parseInt(sizeEl.value, 10) : 75;
            var color = colorEl ? parseInt(colorEl.value, 10) : 0;
            var scale = scaleEl ? parseInt(scaleEl.value, 10) : 70;

            if (isNaN(size) || size < 1) { size = 75; }
            if (isNaN(color)) { color = 0; }
            if (isNaN(scale) || scale < 1) { scale = 70; }

            var mobileSize = Math.round(size * scale / 100);

            img.width = size;
            img.style.filter = 'hue-rotate(' + color + 'deg)';
            imgMobile.width = mobileSize;
            imgMobile.style.filter = 'hue-rotate(' + color + 'deg)';

            var l1 = document.getElementById('josra_preview_size');
            var l2 = document.getElementById('josra_preview_size_mobile');
            if (l1) { l1.innerHTML = size + ' px'; }
            if (l2) { l2.innerHTML = mobileSize + ' px'; }

            // El icono elegido
            var checked = root.querySelector('input[name="JOSRA_WAP_ICON"]:checked');
            if (checked) {
                var src = img.getAttribute('src').replace(/icon[0-9]+\.png/, 'icon' + checked.value + '.png');
                img.setAttribute('src', src);
                imgMobile.setAttribute('src', src);
            }

            // Marco del icono seleccionado
            var options = all(root, '.josra-icon-option');
            for (var i = 0; i < options.length; i++) {
                var radio = options[i].querySelector('input[type="radio"]');
                if (radio && radio.checked) {
                    addClass(options[i], 'josra-icon-selected');
                } else {
                    removeClass(options[i], 'josra-icon-selected');
                }
                var thumb = options[i].querySelector('img');
                if (thumb) {
                    thumb.style.filter = 'hue-rotate(' + color + 'deg)';
                }
            }
        }

        var live = all(root, '.josra-live, input[name="JOSRA_WAP_ICON"]');
        for (var i = 0; i < live.length; i++) {
            live[i].oninput = refresh;
            live[i].onchange = refresh;
            live[i].onkeyup = refresh;
        }

        refresh();
    }

    onReady(function () {
        var root = document.querySelector('.josra-config');
        if (!root) { return; }

        initTabs(root);
        initSchedule();
        initPreview(root);
    });
}());
