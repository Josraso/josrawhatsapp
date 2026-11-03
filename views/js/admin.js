/*
 * JosraWhatsapp - Back-office
 * Sin dependencias: vale igual en el BO de 1.6 que en el de 9.
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

    function setDayState(button, enabled) {
        var input = document.getElementById(button.getAttribute('data-target'));
        if (!input) {
            return;
        }

        input.value = enabled ? '1' : '0';

        button.className = 'josra-day-toggle ' + (enabled ? 'josra-on' : 'josra-off');

        var state = button.querySelector('.josra-day-state');
        if (state) {
            state.innerHTML = enabled ? state.getAttribute('data-on') : state.getAttribute('data-off');
        }
    }

    onReady(function () {
        var schedule = document.querySelector('.josra-schedule');
        if (!schedule) {
            return;
        }

        // Guarda los textos Abierto/Cerrado tal y como vienen traducidos del .tpl
        var toggles = schedule.querySelectorAll('.josra-day-toggle');
        var firstOpen = '';
        var firstClosed = '';
        var i;

        for (i = 0; i < toggles.length; i++) {
            var st = toggles[i].querySelector('.josra-day-state');
            if (!st) {
                continue;
            }
            var txt = st.innerHTML.replace(/^\s+|\s+$/g, '');
            if (toggles[i].className.indexOf('josra-on') !== -1 && firstOpen === '') {
                firstOpen = txt;
            }
            if (toggles[i].className.indexOf('josra-off') !== -1 && firstClosed === '') {
                firstClosed = txt;
            }
        }

        for (i = 0; i < toggles.length; i++) {
            var state = toggles[i].querySelector('.josra-day-state');
            if (state) {
                state.setAttribute('data-on', firstOpen);
                state.setAttribute('data-off', firstClosed);
            }

            toggles[i].onclick = (function (button) {
                return function () {
                    var input = document.getElementById(button.getAttribute('data-target'));
                    setDayState(button, !(input && input.value === '1'));
                };
            }(toggles[i]));
        }

        // Turno de tarde
        var shiftButtons = schedule.querySelectorAll('.josra-shift2-toggle');
        for (i = 0; i < shiftButtons.length; i++) {
            shiftButtons[i].onclick = (function (button) {
                return function () {
                    var day = button.getAttribute('data-day');
                    var block = document.getElementById('josra_shift2_' + day);
                    var flag = document.getElementById('josra_t2_enabled_' + day);
                    if (!block || !flag) {
                        return;
                    }

                    var visible = flag.value === '1';
                    flag.value = visible ? '0' : '1';
                    block.style.display = visible ? 'none' : '';
                    button.innerHTML = visible
                        ? button.getAttribute('data-add')
                        : button.getAttribute('data-remove');
                };
            }(shiftButtons[i]));
        }

        // Copiar el lunes al resto de dias
        var copyButton = document.getElementById('josra_copy_monday');
        if (copyButton) {
            copyButton.onclick = function () {
                var days = ['martes', 'miercoles', 'jueves', 'viernes', 'sabado', 'domingo'];
                var fields = ['t1_from', 't1_to', 't2_from', 't2_to'];
                var j;
                var k;

                for (j = 0; j < days.length; j++) {
                    for (k = 0; k < fields.length; k++) {
                        var src = schedule.querySelector('[name="josra_' + fields[k] + '_lunes"]');
                        var dst = schedule.querySelector('[name="josra_' + fields[k] + '_' + days[j] + '"]');
                        if (src && dst) {
                            dst.value = src.value;
                        }
                    }

                    // Turno de tarde del lunes
                    var srcFlag = document.getElementById('josra_t2_enabled_lunes');
                    var dstFlag = document.getElementById('josra_t2_enabled_' + days[j]);
                    var dstBlock = document.getElementById('josra_shift2_' + days[j]);
                    var dstButton = schedule.querySelector('.josra-shift2-toggle[data-day="' + days[j] + '"]');

                    if (srcFlag && dstFlag && dstBlock && dstButton) {
                        dstFlag.value = srcFlag.value;
                        dstBlock.style.display = srcFlag.value === '1' ? '' : 'none';
                        dstButton.innerHTML = srcFlag.value === '1'
                            ? dstButton.getAttribute('data-remove')
                            : dstButton.getAttribute('data-add');
                    }
                }
            };
        }
    });
}());
