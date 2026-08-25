(function () {
    'use strict';

    function initGatewayTabs() {
        var gateways = document.getElementById('gateways');

        if (!gateways) {
            return;
        }

        var tabs = gateways.querySelectorAll(':scope > ul li a');

        if (!tabs.length) {
            return;
        }

        tabs.forEach(function (tab) {
            tab.addEventListener('click', function (e) {
                e.preventDefault();

                var targetId = tab.getAttribute('href');

                if (!targetId || targetId.charAt(0) !== '#') {
                    return;
                }

                tabs.forEach(function (otherTab) {
                    otherTab.parentElement.classList.remove('ui-tabs-active');
                });

                gateways.querySelectorAll(':scope > div').forEach(function (panel) {
                    panel.style.display = 'none';
                });

                tab.parentElement.classList.add('ui-tabs-active');

                var panel = document.querySelector(targetId);

                if (panel) {
                    panel.style.display = '';
                }
            });
        });

        var activeTab = gateways.querySelector(
            ':scope > ul li.ui-tabs-active a'
        );

        if (!activeTab) {
            activeTab = tabs[0];
        }

        if (activeTab) {
            activeTab.click();
        }
    }

    window.pstsInitGatewayTabs = initGatewayTabs;

    document.addEventListener('DOMContentLoaded', function () {
        initGatewayTabs();
    });

})();