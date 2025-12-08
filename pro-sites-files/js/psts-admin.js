(function(){

    // Vanilla fallback if jQuery is missing (helps keep tooltips working)
    if (typeof jQuery === 'undefined') {
        document.addEventListener('DOMContentLoaded', function(){

            // Make tips obviously interactive
            document.querySelectorAll('img.help_tip').forEach(function(el){
                el.style.cursor = 'pointer';
                el.setAttribute('tabindex', '0');
                el.setAttribute('role', 'button');
            });

            function hideAllHelp() {
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

            document.addEventListener('click', function(e){
                var tip = e.target.closest('img.help_tip');
                if (tip) {
                    e.preventDefault();
                    e.stopPropagation();
                    var w = getWrapper(tip);
                    var isVisible = w && w.style.display !== 'none' && w.style.display !== '' ? true : (w && w.offsetParent !== null);
                    hideAllHelp();
                    if (w && !isVisible) { w.style.display = 'block'; }
                    return;
                }
                if (!e.target.closest('.psts-help-text-wrapper')) {
                    hideAllHelp();
                }
            });

            // Hover support (optional, simple show/hide)
            document.addEventListener('mouseover', function(e){
                var tip = e.target.closest('img.help_tip');
                if (tip) {
                    var w = getWrapper(tip);
                    if (w) { w.style.display = 'block'; }
                }
            });
            document.addEventListener('mouseout', function(e){
                var tip = e.target.closest('img.help_tip');
                if (tip) {
                    var w = getWrapper(tip);
                    if (w) { w.style.display = 'none'; }
                }
            });

            // Keyboard activate (Enter/Space)
            document.addEventListener('keydown', function(e){
                var tip = e.target.closest('img.help_tip');
                if (tip && (e.key === 'Enter' || e.key === ' ')) {
                    e.preventDefault();
                    var w = getWrapper(tip);
                    var isVisible = w && w.style.display !== 'none' && w.style.display !== '' ? true : (w && w.offsetParent !== null);
                    hideAllHelp();
                    if (w && !isVisible) { w.style.display = 'block'; }
                }
            });
        });

        return;
    }

jQuery(document).ready(function($){
    
/* ---------------------------------------------------------------------------- */
/* Iris Colorpicker
/* ---------------------------------------------------------------------------- */

    function wpmudev_forums_iris_colorpicker() {
        
        if ($('.color-picker').length) {
            
            $('.color-picker').wpColorPicker();
        
        }
    }
    
    wpmudev_forums_iris_colorpicker();

    /** Show help text on tap/click (mobile friendly) and hover **/
    var hideAllHelp = function() {
        jQuery('.psts-help-text-wrapper:visible').fadeOut(50);
    };

    jQuery(document).on('click', function(e) {
        // close when clicking outside any help tip or tooltip
        if (!jQuery(e.target).closest('.help_tip, .psts-help-text-wrapper').length) {
            hideAllHelp();
        }
    });

    // Delegate to handle dynamically rendered help icons
    jQuery(document)
        .on('click', 'img.help_tip', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var wrapper = jQuery(this).nextAll('.psts-help-text-wrapper').first();
            if (!wrapper.length) {
                wrapper = jQuery(this).parent().find('.psts-help-text-wrapper').first();
            }
            var isVisible = wrapper.is(':visible');
            hideAllHelp();
            if (!isVisible && wrapper.length) {
                wrapper.fadeIn(50);
            }
        })
        .on('mouseenter', 'img.help_tip', function() {
            var wrapper = jQuery(this).nextAll('.psts-help-text-wrapper').first();
            if (!wrapper.length) {
                wrapper = jQuery(this).parent().find('.psts-help-text-wrapper').first();
            }
            wrapper.fadeIn(50);
        })
        .on('mouseleave', 'img.help_tip', function() {
            var wrapper = jQuery(this).nextAll('.psts-help-text-wrapper').first();
            if (!wrapper.length) {
                wrapper = jQuery(this).parent().find('.psts-help-text-wrapper').first();
            }
            wrapper.fadeOut(50);
        });
    //If chosen function exists and there is any select with class chosen
    if ( jQuery.isFunction(jQuery.fn.chosen) && jQuery('.chosen').length ) {
        jQuery('.chosen').chosen({disable_search_threshold: 10}).change(function () {
            jQuery(this).trigger('chosen:updated')
        });
    }

    /**
     * Make sure that settings wrapper go as far as it needs to go.
     */
    var height = $('.psts-tab-container .psts-tabs').height() + 10;
    $('.psts-wrap .psts-settings').css('min-height', height);
    $('#psts_ProSites_Module_Plugins, #psts_ProSites_Module_Plugins_Manager').change(function () {
        if ($(this).is(':checked')) {
            var id = $(this).attr('id');
            if (id == 'psts_ProSites_Module_Plugins') {
                if ($('#psts_ProSites_Module_Plugins_Manager').is(':checked')) {
                    alert(prosites_admin.disable_premium_plugin_manager);
                    $('#psts_ProSites_Module_Plugins_Manager').prop('checked', false);
                }
            } else if (id == 'psts_ProSites_Module_Plugins_Manager') {
                if ($('#psts_ProSites_Module_Plugins').is(':checked')) {
                    alert(prosites_admin.disable_premium_plugin);
                    $('#psts_ProSites_Module_Plugins').prop('checked', false);
                }
            }
        }
    });
    
    /**
    * On posting quota settings, if the level is selected
    * reload the page if per level posting quotas are enabled
    */
    $(document).ready(function(e) {
        $('#pq_level').on('change', function(e){
            if ( $(".per_level:checked").val() == 1 ){
               self.location=self.location+'&level='+this.options[this.selectedIndex].value;
            } 
        });
    });
});

})();