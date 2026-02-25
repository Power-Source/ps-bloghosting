(function(){

    // Vanilla fallback when jQuery is not available (comparison table needs to work without jQuery)
    if (typeof jQuery === 'undefined') {
        document.addEventListener('DOMContentLoaded', function() {
            if (window.console && console.debug) {
                console.debug('psts vanilla init (no jQuery)');
            }

            var featureTableBody = document.querySelector('#prosites-level-list.feature-table tbody');
            if (!featureTableBody) {
                return;
            }

            function getActiveLevel() {
                var hidden = document.querySelector('.level-select-bar input[name="current_level"]');
                var level = hidden ? hidden.value : '';
                if (!level) {
                    var first = document.querySelector('.level-select-bar a');
                    level = first ? first.getAttribute('data-id') : '1';
                }
                return level;
            }

            function switchLevel(level) {
                var nodes = document.querySelectorAll('select[data-level], textarea[data-level], input[data-level], .chosen-container[data-level]');
                nodes.forEach(function(node){
                    node.style.display = (node.getAttribute('data-level') === String(level)) ? '' : 'none';
                    if (node.classList.contains('chosen-container')) {
                        var search = node.querySelector('.chosen-search');
                        if (search) { search.style.display = 'none'; }
                    }
                });
                            function escapeHtml(text) {
                                var div = document.createElement('div');
                                div.textContent = text;
                                return div.innerHTML;
                            }

                var hidden = document.querySelector('.level-select-bar input[name="current_level"]');
                if (hidden) { hidden.value = level; }
            }

            function tagLevelFields() {
                document.querySelectorAll('[name*="[levels]"]').forEach(function(node){
                    var match = node.name.match(/\[levels\]\[(\d+)\]/);
                    if (match && match[1]) {
                        node.setAttribute('data-level', match[1]);
                        var next = node.nextElementSibling;
                        if (next && next.classList.contains('chosen-container')) {
                            next.setAttribute('data-level', match[1]);
                        }
                    }
                });
            }

            function rearrangeFeatureRows() {
                var rows = featureTableBody.querySelectorAll('tr');
                var order = [];
                rows.forEach(function(row, idx){
                    var pos = row.querySelector('.position');
                    if (pos) { pos.textContent = idx + 1; }
                    row.classList.toggle('alternate', idx % 2 !== 0);
                    var keyInput = row.querySelector('td:first-child [name*="module_key"], td:first-child [name*="custom"]');
                    if (keyInput) {
                        order.push(keyInput.value);
                    }
                });
                var orderInput = document.querySelector('input[name="psts[feature_table][feature_order]"]');
                if (orderInput) {
                    orderInput.value = order.join(',');
                }
            }

            function setInlineEditing() {
                featureTableBody.querySelectorAll('.text-item').forEach(function(item){
                    item.onmousedown = null;
                    item.ondblclick = function(){
                        var editBox = item.nextElementSibling;
                        if (editBox) {
                            editBox.style.display = '';
                            item.style.display = 'none';
                        }
                    };
                });

                featureTableBody.querySelectorAll('.save-link').forEach(function(link){
                    link.onclick = function(e){
                        e.preventDefault();
                        var parent = link.closest('div');
                        var textItem = link.closest('td').querySelector('.text-item');
                        var editor = parent.querySelector('.editor');
                        if (textItem && editor) {
                            textItem.textContent = editor.value;
                            textItem.style.display = '';
                            parent.style.display = 'none';
                        }
                    };
                });

                featureTableBody.querySelectorAll('.reset-link').forEach(function(link){
                    link.onclick = function(e){
                        e.preventDefault();
                        var td = link.closest('td');
                        var textItem = td.querySelector('.text-item');
                        var hidden = td.querySelector('input[type="hidden"]');
                        var editor = td.querySelector('.editor');
                        if (textItem && hidden) {
                            textItem.textContent = hidden.value;
                            textItem.style.display = '';
                        }
                        if (editor && hidden) {
                            editor.value = hidden.value;
                        }
                        var parent = link.closest('div');
                        if (parent) { parent.style.display = 'none'; }
                    };
                });

                featureTableBody.querySelectorAll('.order-col .delete').forEach(function(btn){
                    btn.onclick = function(e){
                        e.preventDefault();
                        var row = btn.closest('tr');
                        if (!row) { return; }
                        var markInput = document.querySelector('[name="mark_for_delete"]');
                        var hidden = btn.previousElementSibling;
                        if (hidden && markInput) {
                            var mark = hidden.value;
                            var current = markInput.value ? markInput.value.split(',') : [];
                            current.push(mark);
                            markInput.value = current.filter(Boolean).join(',');
                        }
                        row.remove();
                        rearrangeFeatureRows();
                    };
                });
            }

            function buildFeatureRow(name, description, text, levels) {
                var allCount = featureTableBody.querySelectorAll('tr').length;
                var rowClass = (allCount + 1) % 2 === 0 ? '' : 'alternate';
                var customFeatures = featureTableBody.querySelectorAll('tr.custom .order-col [type="hidden"]');
                var counter = customFeatures.length ? customFeatures.length : 0;
                var customName = 'custom-' + (counter + 1);
                while (Array.from(customFeatures).some(function(el){ return el.value === customName; })) {
                    counter += 1;
                    customName = 'custom-' + (counter + 1);
                                // Escape user input to prevent XSS
                                var escapedName = escapeHtml(name || '');
                                var escapedDescription = escapeHtml(description || '');
                                var escapedText = escapeHtml(text || '');

                }

                var noneLabel = 'None';
                var saveAction = 'save';
                var resetAction = 'reset';

                var indicatorOptions = '';
                for (var i = 1; i <= levels; i++) {
                    indicatorOptions += '<select name="psts[feature_table][' + customName + '][levels][' + i + '][status]">';
                    indicatorOptions += '<option value="tick">&#x2713;</option>';
                    indicatorOptions += '<option value="cross">&#x2718;</option>';
                    indicatorOptions += '<option value="none">' + noneLabel + '</option>';
                    indicatorOptions += '</select>';
                }

                var customTextAreas = '';
                for (var j = 1; j <= levels; j++) {
                    customTextAreas += '<textarea name="psts[feature_table][' + customName + '][levels][' + j + '][text]">' + escapedText + '</textarea>';
                }

                var html = '';
                html += '<tr class="' + rowClass + ' custom new-feature blog-row">';
                html += '<td scope="row" style="padding-left: 10px" class="order-col">';
                html += '<div class="position">' + (allCount + 1) + '</div>';
                html += '<input type="hidden" name="psts[feature_table][' + customName + '][custom]" value="' + customName + '" />';
                html += '<a class="delete"><span class="dashicons dashicons-trash"></span></a>';
                html += '</td>';
                html += '<td scope="row" style="padding-left: 20px;"><input type="checkbox" checked="checked" name="psts[feature_table][' + customName + '][visible]" value="1"></td>';
                html += '<td scope="row">';
                html += '<div class="text-item">' + escapedName + '</div>';
                html += '<div class="edit-box" style="display:none">';
                html += '<input class="editor" type="text" name="psts[feature_table][' + customName + '][name]" value="' + escapedName + '" /><br />';
                html += '<span><a class="save-link">' + saveAction + '</a> <a style="margin-left: 10px;" class="reset-link">' + resetAction + '</a></span></div>';
                html += '<input type="hidden" value="' + escapedName + '" />';
                html += '</td>';
                html += '<td scope="row">';
                html += '<div class="text-item">' + escapedDescription + '</div>';
                html += '<div class="edit-box" style="display:none">';
                html += '<textarea class="editor" name="psts[feature_table][' + customName + '][description]">' + escapedDescription + '</textarea><br />';
                html += '<span><a class="save-link">' + saveAction + '</a> <a style="margin-left: 10px;" class="reset-link">' + resetAction + '</a></span></div>';
                html += '<input type="hidden" value="' + escapedDescription + '" />';
                html += '</td>';
                html += '<td scope="row" class="level-settings">' + indicatorOptions + '</td>';
                html += '<td scope="row">' + customTextAreas + '</td>';
                html += '</tr>';
                return html;
            }

            document.addEventListener('click', function(e){
                if (e.target && e.target.id === 'add-feature-button') {
                    e.preventDefault();
                    var name = (document.querySelector('[name="new-feature-name"]') || {}).value || '';
                    var description = (document.querySelector('[name="new-feature-description"]') || {}).value || '';
                    var text = (document.querySelector('[name="new-feature-text"]') || {}).value || '';
                    var levels = parseInt((document.querySelector('[name="new-feature-levels"]') || {}).value || '0', 10);
                    if (!levels || levels < 1) {
                        levels = document.querySelectorAll('.level-select-bar a').length || 1;
                    }
                    if (!name && !description && !text) {
                        return false;
                    }
                    var noFeatures = document.querySelector('.no-features');
                    if (noFeatures) { noFeatures.style.display = 'none'; }

                    var rowHtml = buildFeatureRow(name, description, text, levels || 0);
                    featureTableBody.insertAdjacentHTML('beforeend', rowHtml);
                    if (window.console && console.debug) {
                        console.debug('psts vanilla add-feature appended row');
                    }
                    if (noFeatures) { featureTableBody.appendChild(noFeatures); }
                    tagLevelFields();
                    setInlineEditing();
                    switchLevel(getActiveLevel());
                    rearrangeFeatureRows();

                    var row = e.target.closest('tr');
                    if (row) {
                        row.querySelectorAll('input[type="text"], textarea').forEach(function(input){ input.value = ''; });
                    }
                }
            });

            if (document.querySelector('.level-select-bar')) {
                document.querySelector('.level-select-bar').addEventListener('click', function(e){
                    if (e.target && e.target.matches('.level-select-bar a')) {
                        e.preventDefault();
                        document.querySelectorAll('.level-select-bar a').forEach(function(a){ a.classList.remove('selected'); });
                        e.target.classList.add('selected');
                        switchLevel(e.target.getAttribute('data-id'));
                    }
                });
            }

            tagLevelFields();
            setInlineEditing();
            switchLevel(getActiveLevel());
            rearrangeFeatureRows();
        });

        return;
    }

jQuery(document).ready(function($){

    // Fallback defaults if localization is missing
    if (typeof window.prosites_levels === 'undefined') {
        window.prosites_levels = {
            confirm_level_delete: 'Delete level?',
            confirm_feature_delete: 'Delete feature?'
        };
    }

    // Confirm deleting level
    $('[name^="delete_level"]').click(function ( item ) {
        //Disable Save button, as it creates problem
        jQuery('input[name="save_levels"]').prop('disabled', true);

        /**
         * Get the position from the text input because altering button breaks it
         */
        var row_index = $( $( $( $(item.currentTarget).parents('tr') ).find('td')[1]).find('input')).attr('data-position');

        if ( confirm( prosites_levels.confirm_level_delete ) ) {
            prosite_update_level_rows( { deleteRow: row_index } );
            return true;
        }

        return false;
    });

    // When the page loads, disable/enable price inputs accordingly
    if (!$('#enable_1').is(':checked')) {
        $('.price-1').attr('disabled', true);
    }
    if (!$('#enable_3').is(':checked')) {
        $('.price-3').attr('disabled', true);
    }
    if (!$('#enable_12').is(':checked')) {
        $('.price-12').attr('disabled', true);
    }

    // And remember to update it when the user checks the enabled boxes
    $('#enable_1').change(function () {
        if (this.checked) {
            $('.price-1').removeAttr('disabled');
        } else {
            $('.price-1').attr('disabled', true);
        }
        prosites_levels_mark_dirty();
    });
    $('#enable_3').change(function () {
        if (this.checked) {
            $('.price-3').removeAttr('disabled');
        } else {
            $('.price-3').attr('disabled', true);
        }
        prosites_levels_mark_dirty();
    });
    $('#enable_12').change(function () {
        if (this.checked) {
            $('.price-12').removeAttr('disabled');
        } else {
            $('.price-12').attr('disabled', true);
        }
        prosites_levels_mark_dirty();
    });

    $('#prosites-level-list tbody input').change( function() {
        prosites_levels_mark_dirty();
    });


    /* ---- ---- ---- LEVEL SETTINGS PAGE ---- ---- ---- */

    // Make the levels sortable
    $('#prosites-level-list.level-settings tbody').sortable({
        opacity: 0.5,
        cursor: 'pointer',
        axis: 'y',
        placeholder: "prosite-level-placeholder",
        update: function() {

            // Leave this here for now... just in case we want to make it update via AJAX
            //var ordr = jQuery(this).sortable('serialize') + '&action=list_update_order';
            //jQuery.post(ajaxurl, ordr, function(response){
            //    //alert(response);
            //});

            prosite_update_level_rows();
            prosites_levels_mark_dirty();
        }
    });

    function prosite_update_level_rows( args ) {
        var rows = $('#prosites-level-list tbody tr');
        var deleted_row = -1;

        if ( args !== undefined ) {
            if( args.deleteRow !== undefined ) {
                deleted_row = args.deleteRow;
            }
        }

        var t_index = 0;
        $.each( rows, function( index, row ) {

            /**
             * Get the columns
             */
            var cols = $( row ).find( 'td' );
            var row_position = $( $( $(cols)[1]).find('input') ).attr('data-position');

            if( row_position == deleted_row ) {
                $( row ).hide();
                prosite_update_level_cols( cols, -99 );
                return true;
            }

            /**
             * True index count in case row got deleted.
             */
            t_index += 1;
            /**
             * Update row class
             */
            $(row).removeClass('alternate');
            if (t_index % 2 != 0) {
                $(row).addClass('alternate');
            }

            prosite_update_level_cols( cols, t_index );

        });

    }

    function prosite_update_level_cols( cols, t_index ) {
        /**
         * Update row number
         */
        $(cols[0]).html('<strong>' + ( t_index ) + '</strong>');

        /**
         * Update input field names.
         *
         */
        $.each(cols, function (c_idx, col) {
            if (0 < c_idx && c_idx < ( $(cols).length - 1 )) {
                var input_field = $(col).find('input');
                var current_name = $(input_field).attr('name');
                var new_name = current_name.substr(0, current_name.indexOf('[')) + '[' + ( t_index ) + ']';
                input_field.attr('name', new_name);
            }
            if (c_idx == 1 ) {
                var input_field = $(col).find('input');
                input_field.attr('data-position', t_index);
            }
        });

    }

    function prosites_levels_mark_dirty() {
        $( '.save_levels_dirty' ).css( 'display', 'inline-block' );
    }

    /* ---- ---- ---- PRICING SETTINGS PAGE ---- ---- ---- */
    // Make the levels sortable (guard if sortable is missing)
    if ($.fn.sortable) {
        $('#prosites-level-list.pricing-table tbody').sortable({
            opacity: 0.5,
            cursor: 'pointer',
            axis: 'y',
            placeholder: "prosite-level-placeholder",
            update: function() {

                var rows = $('#prosites-level-list tbody tr');
                var level_order = new Array();

                $.each( rows, function( index, row ) {
                    level_order[ level_order.length ] = $( row ).attr('data-level');

                    $(row).removeClass('alternate');
                    if ( index % 2 == 0) {
                        $(row).addClass('alternate');
                    }

                } );

                level_order = level_order.join( ',' );
                $('input[name="psts[pricing_levels_order]"]').val( level_order );
            }
        });
    }

    /* ---- ---- ---- COMPARISON/ FEATURE TABLE PAGE ---- ---- ---- */
   // Make the features sortable
    function make_features_sortable() {
        if (!$.fn.sortable) {
            return;
        }

        $('#prosites-level-list.feature-table tbody').sortable({
            opacity: 0.5,
            cursor: 'pointer',
            axis: 'y',
            placeholder: "prosite-level-placeholder",
            update: function () {
                rearrange_feature_rows();
            }
        });
    }

    function rearrange_feature_rows() {
        var rows = $('#prosites-level-list tbody tr');
        var module_order = new Array();
        //
        $.each(rows, function (index, row) {
            var first_cell = $(row).find('td:first-child');
            var pos_label = $(first_cell).find('.position');
            var mod_key = $($(row).find('td:first-child [name*=module_key], td:first-child [name*=custom]')).val();
            module_order[index] = mod_key;

            $(pos_label).text(index + 1);

            $(row).removeClass('alternate');
            if (index % 2 == 0) {
                $(row).addClass('alternate');
            }

        });

        module_order = module_order.join(',');

        $('input[name="psts[feature_table][feature_order]"]').val(module_order);
    }

    tag_level_fields();
    make_features_sortable();
    switch_level( get_active_level() );

    $(document).on('click', '.level-select-bar a', function (e) {

        var element = e.currentTarget;

        $('.level-select-bar a').removeClass('selected');

        var current_level = $(element).attr('data-id');
        switch_level( current_level );
        set_active_level( current_level );
        $(element).addClass('selected');

    });

    function switch_level( level ) {
        // Hide all level-specific fields while leaving row wrappers intact
        var $targets = $('select[data-level], textarea[data-level], input[data-level], .chosen-container[data-level]');
        $targets.css('display', 'none');

        // Show only the active level fields
        var $active = $targets.filter('[data-level="' + level + '"]');
        $active.css('display', '');

        // Hide chosen search inputs to reduce clutter when native selects are shown
        $active.find('.chosen-search').css('display', 'none');
    }

    function get_active_level() {
        var level = $( '.level-select-bar [name=current_level]').val();
        if (!level || level === '') {
            var firstAnchor = $('.level-select-bar a').first();
            level = firstAnchor.length ? firstAnchor.attr('data-id') : '1';
        }
        return level;
    }

    function set_active_level( level ) {
        $( '.level-select-bar [name=current_level]').val( level );
    }
    
    function escapeHtml(text) {
        var map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };

        return text.replace(/[&<>"']/g, function(m) { return map[m]; });
    }

    /* ---- ---- ---- ADD FEATURES BUTTON ---- ---- ---- */
    // Delegated so it works even if the row is re-rendered
    $( document ).on('click', '#add-feature-button', function( e ) {
        if (window.console && console.debug) {
            console.debug('psts add-feature clicked');
        }
        var name = $('[name=new-feature-name]').val();
        var description = $('[name=new-feature-description]').val();
        var text = $('[name=new-feature-text]').val();
        var levels = $('[name=new-feature-levels]').val();

        // Do not add if values are empty.
        if ( name === '' && description === '' && text === '' ) {
            return false;
        }

        var no_features = $('.no-features').hide().detach();

        // Get the following with script translation
        var save_action = 'save';
        var reset_action = 'reset';
        var none_label = 'None';

        var all_item_count = $( '#prosites-level-list.feature-table tbody tr').length;

        var custom_features = $( '#prosites-level-list.feature-table tbody tr.custom .order-col [type=hidden]');
        var number_custom = custom_features.length;

        // Set our custom name
        if( 0 == number_custom ) {
            var custom_name = 'custom-1';
        } else {
            var custom_name = 'custom-2';
            var counter = 1;
            // Make sure we get a valid custom name
            while( ! check_valid_custom_name( custom_name, custom_features ) ) {
                counter += 1;
                custom_name = 'custom-' + counter;
            }
        }

        var row_class = ( all_item_count + 1 ) % 2 == 0 ? '' : 'alternate';


        var feature_row = '<tr class="' + row_class + ' custom new-feature" blog-row">';
        var key = custom_name;
        var feature_order = '<td scope="row" style="padding-left: 10px" class="order-col">';
        feature_order += '<div class="position">' + ( all_item_count + 1 ) + '</div>';
        feature_order += '<input type="hidden" name="psts[feature_table][' + key + '][custom]" value="' + custom_name + '" />';
        feature_order += '<a class="delete"><span class="dashicons dashicons-trash"></span></a>';
        feature_order += '</td>';

        var feature_visible = '<td scope="row" style="padding-left: 20px;">';
        feature_visible += '<input type="checkbox" checked="checked" name="psts[feature_table][' + key + '][visible]" value="1">';
        feature_visible += '</td>'

        var feature_name = '<td scope="row">';
        feature_name += '<div class="text-item">' + name + '</div>';
        feature_name += '<div class="edit-box" style="display:none">';
        feature_name += '<input class="editor" type="text" name="psts[feature_table][' + key + '][name]" value="' + name + '" /><br />';
        feature_name += '<span><a class="save-link">' + save_action + '</a> <a style="margin-left: 10px;" class="reset-link">' + reset_action + '</a></span></div>';
        feature_name += '<input type="hidden" value="' + name + '" />'

        var feature_description = '<td scope="row">';
        feature_description += '<div class="text-item">' + description + '</div>';
        feature_description += '<div class="edit-box" style="display:none">';
        feature_description += '<textarea class="editor" name="psts[feature_table][' + key + '][description]">' + description + '</textarea><br />';
        feature_description += '<span><a class="save-link">' + save_action + '</a> <a style="margin-left: 10px;" class="reset-link">' + reset_action + '</a></span></div>';
		feature_description += '<input type="hidden" value="' + escapeHtml( description ) + '" />';
		
        var feature_indicator = '<td scope="row" class="level-settings">';
        for( var i = 1; i <= levels ; i++ ) {
            feature_indicator += '<select class="chosen" name="psts[feature_table][' + key + '][levels][' + i + '][status]">';
            feature_indicator += '<option value="tick">&#x2713;</option>';
            feature_indicator += '<option value="cross">&#x2718;</option>';
            feature_indicator += '<option value="none">' + none_label + '</option>';
            feature_indicator += '</select>';
        }
        feature_indicator += '</td>';

        var feature_custom = '<td scope="row">';
        for( var i = 1; i <= levels ; i++ ) {
            feature_custom += '<textarea name="psts[feature_table][' + key + '][levels][' + i + '][text]">' + text + '</textarea>';
        }
        feature_custom += '</td>';

        feature_row += feature_order + feature_visible + feature_name + feature_description + feature_indicator + feature_custom;
        feature_row += '</tr>';

        $( '#prosites-level-list.feature-table tbody').append( feature_row );
        $( '#prosites-level-list.feature-table tbody').append( no_features );

        if (window.console && console.debug) {
            console.debug('psts add-feature appended row', feature_row);
        }

        // Activate chosen
        if ( jQuery.isFunction(jQuery.fn.chosen) && jQuery('.chosen').length ) {
            jQuery('.chosen').chosen({disable_search_threshold: 10}).change(function () {
                jQuery(this).trigger('chosen:updated')
            });
        }

        tag_level_fields();

        // Clear inputs.
        $( this ).parents('tr').find('input[type=text], textarea').val('');

        set_inline_editing();
        switch_level( get_active_level() );
        rearrange_feature_rows();
        make_features_sortable();

    } );

    function check_valid_custom_name( custom_name, custom_items ) {
        var valid = true;

        $.each( custom_items, function( index, item ) {
            if( $( item ).val() == custom_name ) {
                valid = false;
            }
        } );

        // check items marked for delete
        var marked = $('[name=mark_for_delete]').val();
        marked = marked.split( ',' );

        $.each( marked, function( index, item ) {
            if( item == custom_name ) {
                valid = false;
            }
        } );

        return valid;
    }

    function set_inline_editing() {
        // Inline editing
        $('#prosites-level-list.feature-table .text-item').unbind( 'dblclick' );
        $('#prosites-level-list.feature-table .text-item').dblclick(function (e) {
            var element = e.currentTarget;
            $(element).next().show();
            $(element).hide();
        });

        $('#prosites-level-list.feature-table .save-link').unbind( 'click' );
        $('#prosites-level-list.feature-table .save-link').click(function (e) {
            var element = e.currentTarget;
            var text = $($(element).parents('td')[0]).find('.text-item');
            var parent = $($(element).parents('div')[0]);
            var editor = $(parent).find('.editor');

            $(text).html($(editor).val());
            $(text).show();
            $(parent).hide();
        });

        $('#prosites-level-list.feature-table .reset-link').unbind( 'click' );
        $('#prosites-level-list.feature-table .reset-link').click(function (e) {
            var element = e.currentTarget;
            var text = $($(element).parents('td')[0]).find('.text-item');
            var parent = $($(element).parents('div')[0]);
            var table_cell = $(element).parents('td')[0];
            var original = $(table_cell).find('[type=hidden]');
            var editor = $(parent).find('.editor');

            $(text).html($(original).val());
            $(text).show();
            $(editor).val($(original).val());
            $(parent).hide();
        });

        $('#prosites-level-list.feature-table .order-col .delete').unbind( 'click' );
        $('#prosites-level-list.feature-table .order-col .delete').click( function (e) {

            if ( confirm( prosites_levels.confirm_feature_delete ) ) {
                var element = e.currentTarget;
                var mark = $(element).prev().val();
                var row = $(element).parents('tr')[0];

                $(row).remove();
                rearrange_feature_rows();

                var marked = $('[name=mark_for_delete]').val();
                marked = '' == marked ? mark : marked + ',' + mark;
                $('[name=mark_for_delete]').val(marked);
            }

        } );
    }

    function tag_level_fields() {
        // Tag level-specific inputs/selects/textareas with data-level for easier toggling
        $('[name*="[levels]"]').each(function() {
            var name = this.name;
            var match = name.match(/\[levels\]\[(\d+)\]/);
            if (match && match[1]) {
                var level = match[1];
                $(this).attr('data-level', level);
                var chosen = $(this).next('.chosen-container');
                if (chosen.length) {
                    chosen.attr('data-level', level);
                }
            }
        });
    }

    set_inline_editing();
    switch_level( get_active_level() );
});

})();
