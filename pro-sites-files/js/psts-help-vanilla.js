(function(){
    // Vanilla tooltip handler for .help_tip icons
    document.addEventListener('DOMContentLoaded', function(){
        var tips = Array.prototype.slice.call(document.querySelectorAll('img.help_tip'));
        if (!tips.length) { return; }

        function hideAll() {
            document.querySelectorAll('.psts-help-text-wrapper').forEach(function(w){
                w.style.display = 'none';
            });
        }

        function getWrapper(el) {
            var wrap = el.nextElementSibling;
            if (!(wrap && wrap.classList.contains('psts-help-text-wrapper'))) {
                wrap = el.parentElement ? el.parentElement.querySelector('.psts-help-text-wrapper') : null;
            }
            return wrap;
        }

        tips.forEach(function(tip){
            tip.style.cursor = 'pointer';
            tip.setAttribute('tabindex', '0');
            tip.setAttribute('role', 'button');

            // Direct click handler
            tip.addEventListener('click', function(e){
                e.preventDefault();
                e.stopPropagation();
                var wrap = getWrapper(tip);
                var visible = wrap && wrap.style.display === 'block';
                hideAll();
                if (wrap && !visible) { wrap.style.display = 'block'; }
            });

            // Hover handlers
            tip.addEventListener('mouseover', function(){
                var wrap = getWrapper(tip);
                if (wrap) { wrap.style.display = 'block'; }
            });

            tip.addEventListener('mouseout', function(){
                var wrap = getWrapper(tip);
                if (wrap) { wrap.style.display = 'none'; }
            });

            // Keyboard activate
            tip.addEventListener('keydown', function(e){
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    var wrap = getWrapper(tip);
                    var visible = wrap && wrap.style.display === 'block';
                    hideAll();
                    if (wrap && !visible) { wrap.style.display = 'block'; }
                }
            });
        });

        document.addEventListener('click', function(e){
            var tip = e.target.closest('img.help_tip');
            if (tip) {
                e.preventDefault();
                e.stopPropagation();
                var wrap = getWrapper(tip);
                var visible = wrap && wrap.style.display === 'block';
                hideAll();
                if (wrap && !visible) { wrap.style.display = 'block'; }
                return;
            }
            if (!e.target.closest('.psts-help-text-wrapper')) {
                hideAll();
            }
        });

        document.addEventListener('mouseover', function(e){
            var tip = e.target.closest('img.help_tip');
            if (tip) {
                var wrap = getWrapper(tip);
                if (wrap) { wrap.style.display = 'block'; }
            }
        });

        document.addEventListener('mouseout', function(e){
            var tip = e.target.closest('img.help_tip');
            if (tip) {
                var wrap = getWrapper(tip);
                if (wrap) { wrap.style.display = 'none'; }
            }
        });

        document.addEventListener('keydown', function(e){
            var tip = e.target.closest('img.help_tip');
            if (tip && (e.key === 'Enter' || e.key === ' ')) {
                e.preventDefault();
                var wrap = getWrapper(tip);
                var visible = wrap && wrap.style.display === 'block';
                hideAll();
                if (wrap && !visible) { wrap.style.display = 'block'; }
            }
        });
    });
})();
