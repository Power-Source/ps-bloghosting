<?php

if ( ! class_exists( 'ProSites_View_Pricing' ) ) {
	class ProSites_View_Pricing {

		public static function get_page_name() {
			return __( 'Bloghosting Preistabelle', 'psts' );
		}

		public static function get_menu_name() {
			return __( 'Preistabelle', 'psts' );
		}

		public static function get_description() {
			return __( 'Hier kannst Du Pläne und Preise sowie Einstellungen für Funktionstabellen aktivieren. ', 'psts' );
		}

		public static function get_page_slug() {
			return 'psts-pricing-settings';
		}

		public static function render_page() {

			if ( ! is_super_admin() ) {
				echo "<p>" . __( 'Netter Versuch...', 'psts' ) . "</p>"; //If accessed properly, this message doesn't appear.
				return false;
			}

			// Might move this to a controller, not sure if needed yet.
			ProSites_Model_Pricing::process_form();

			?>
			<form method="post" action="">
				<?php

				$page_header_options = array(
					'title'       => self::get_page_name(),
					'desc'        => self::get_description(),
					'page_header' => true,
				);

				$options = array(
					'header_save_button'  => true,
					'section_save_button' => true,
					'nonce_name'          => 'psts_pricing_settings',
					'button_name'         => 'pricing',
				);

				ProSites_Helper_Tabs_Pricing::render( __CLASS__, $page_header_options, $options );

				?>

			</form>
			<?php
		}

		/**
		 * Pricing Table
		 *
		 * @return string
		 */
		public static function render_tab_pricing_table() {
			global $psts;

			$active_tab = ProSites_Helper_Tabs_Pricing::get_active_tab();
			ProSites_Helper_Settings::settings_header( $active_tab );

			//			$class_name = 'ProSites_Gateway_2Checkout';
			$featured_level      = $psts->get_setting( 'featured_level' );
			$plans_table_enabled = $psts->get_setting( 'plans_table_enabled', 'enabled' );

			$coupons_enabled       = $psts->get_setting( 'coupons_enabled' );
			$highlight_featured    = $psts->get_setting( 'psts_checkout_show_featured', false );
			$checked               = 'enabled' == $plans_table_enabled ? 'enabled' : 'disabled';
			$coupons_checked       = 'enabled' == $coupons_enabled ? 'enabled' : 'disabled';
			$show_featured_checked = 'enabled' == $highlight_featured ? 'enabled' : 'disabled';

			$pricing_gateways_style     = $psts->get_setting( 'pricing_gateways_style', 'tabbed' );
			$pricing_table_period_style = $psts->get_setting( 'pricing_table_period_style' );
			$features_table_enabled     = $psts->get_setting( 'comparison_table_enabled' );

			?>
			<input type="hidden" name="pricing_settings" value="<?php echo esc_attr( $active_tab['tab_key'] ); ?>"/>
			<table class="form-table">
				<tr>
					<th scope="row"><?php _e( 'Preistabelle aktivieren', 'psts' ) ?></th>
					<td>
						<input type="checkbox" name="psts[plans_table_enabled]"
						       value="1" <?php checked( $checked, 'enabled' ); ?> />
					</td>
				</tr>

				<!-- @todo THIS NEEDS TO BE IMPLEMENTED ASAP -->
				<tr>
					<th scope="row"><?php esc_html_e( 'Periodenauswahlposition', 'psts' ) ?></th>
					<td>
						<label>
							<p><input type="radio" name="psts[pricing_table_period_position]"
							          value="option1" <?php checked( $psts->get_setting( 'pricing_table_period_position', 'option1' ), 'option1' ); ?> />
								<?php esc_html_e( 'Erste Spalte (Teil der Tabelle)', 'psts' ); ?></p>
						</label>
						<label>
							<p><input type="radio" name="psts[pricing_table_period_position]"
							          value="option2" <?php checked( $psts->get_setting( 'pricing_table_period_position', 'option1' ), 'option2' ); ?> />
								<?php esc_html_e( 'Über der Tabelle', 'psts' ); ?></p>
							<p class="description"><?php esc_html_e( 'Aus visuellen Gründen wird durch Verschieben des Periodenwählers nach oben auch die erste Spalte/Details aus der Tabelle entfernt. Wenn das Gutscheinfeld an die erste Spalte angehängt ist, wird es automatisch unter die Tabelle verschoben.', 'psts' ); ?></p>
						</label>
						<!-- <label>
							<p><input type="radio" name="psts[pricing_table_period_position]" value="option3" <?php //checked( $psts->get_setting( 'pricing_table_period_position', 'option1' ), 'option3' ); ?> />
								<?php //esc_html_e( 'Below the table', 'psts' ); ?></p>
						</label> -->
					</td>
				</tr>

				<tr>
					<th scope="row"><?php _e( 'Gutscheine zulassen', 'psts' ) ?></th>
					<td>
						<input type="checkbox" name="psts[coupons_enabled]"
						       value="1" <?php checked( $coupons_checked, 'enabled' ); ?> />
					</td>
				</tr>

				<tr>
					<th scope="row"><?php esc_html_e( 'Gutschein Position', 'psts' ) ?></th>
					<td><?php
						$pos_bottom = $psts->get_setting( 'pricing_table_coupon_position', 'option1' ) == 'option2' ? 1 : 0;
						?>
						<label>
							<p>
								<input type="radio" name="psts[pricing_table_coupon_position]"
								       value="option1" <?php checked( $psts->get_setting( 'pricing_table_coupon_position', 'option1' ), 'option1' ); ?> />
								<?php esc_html_e( 'Erste Spalte (Teil der Tabelle)', 'psts' ); ?></p>
						</label>
						<label>
							<p>
								<input type="radio" name="psts[pricing_table_coupon_position]"
								       value="option2" <?php checked( $pos_bottom, 1 ); ?> />
								<?php esc_html_e( 'Unterhalb der Checkout-Tabelle.', 'psts' ); ?></p>
						</label>
					</td>

				</tr>

				<tr>
					<th scope="row"><?php _e( 'Highlight \'Empfohlen\' Level', 'psts' ) ?></th>
					<td>
						<input type="checkbox" name="psts[psts_checkout_show_featured]"
						       value="1" <?php checked( $show_featured_checked, 'enabled' ); ?> />
					</td>
				</tr>

				<tr>
					<th scope="row"><?php _e( 'Gateways-Layout', 'psts' ); ?>
						<br/><span class="description"
						           style="font-weight:normal; color:#888; "><?php _e( 'Wähle aus, wie die Gateways angezeigt werden sollen.', 'psts' ) ?></span>
					</th>
					<td>
						<select name="psts[pricing_gateways_style]" class="chosen">
							<option
								value="tabbed"<?php selected( $pricing_gateways_style, 'tabbed' ) ?>><?php _e( 'Registerkarten Layout', 'psts' ) ?></option>
							<option
								value="raw"<?php selected( $pricing_gateways_style, 'raw' ) ?>><?php _e( 'Rohes HTML-Layout', 'psts' ) ?></option>
						</select>
					</td>
				</tr>

				<tr>
					<th scope="row"><?php _e( 'Level Anordnung', 'psts' ) ?>
						<br/><span class="description"
						           style="font-weight:normal; color:#888; "><?php _e( 'Wähle die Reihenfolge aus, in der Deine Level in den Preis- und Funktionstabellen angezeigt werden sollen.', 'psts' ) ?></span>
					</th>
					<td>

						<?php
						$level_list = get_site_option( 'psts_levels' );
						$last_level = ( is_array( $level_list ) ) ? count( $level_list ) : 0;
						$periods    = (array) $psts->get_setting( 'enabled_periods' );

						$default_order = array();
						for ( $i = 1; $i <= $last_level; $i ++ ) {
							$default_order[] = $i;
						}
						$default_order = implode( ',', $default_order );

						$pricing_levels_order = $psts->get_setting( 'pricing_levels_order', $default_order );
						$pricing_levels_order = explode( ',', $pricing_levels_order );

						$remove_pricing_item = false;
						if ( count( $pricing_levels_order ) != count( $level_list ) ) {

							foreach ( $level_list as $level_code => $level ) {

								if ( ! in_array( $level_code, $pricing_levels_order ) && count( $level_list ) > count( $pricing_levels_order ) ) {
									$pricing_levels_order[] = $level_code;
								} else {
									$remove_pricing_item = true;
								}

							}

						}

						// Make sure the level doesn't show up if its been deleted.
						if ( $remove_pricing_item ) {
							foreach ( $pricing_levels_order as $item_key => $order_item ) {
								if ( ! in_array( $order_item, array_keys( $level_list ) ) ) {
									unset( $pricing_levels_order[ $item_key ] );
								}
							}
						}


						// define the columns to display, the syntax is 'internal name' => 'display name'
						$posts_columns = array(
							'level'       => array(
								'title' => __( 'Level', 'psts' ),
								'width' => '35px',
							),
							'name'        => array(
								'title' => __( 'Name', 'psts' ),
								'width' => '',
							),
							'pricing'     => array(
								'title' => __( 'Preis', 'psts' ),
								'width' => '',
							),
							'is_featured' => array(
								'title' => __( 'Empfohlener Level', 'psts' ),
								'width' => '',
							),
						);
						?>

						<table width="100%" cellpadding="3" cellspacing="3" class="widefat pricing-table"
						       id="prosites-level-list">
							<thead>
							<tr>
								<?php
								foreach ( $posts_columns as $col ) {
									$style = ! empty( $col['width'] ) ? ' style="max-width:' . $col['width'] . '"' : '';
									echo '<th scope="col"' . $style . '>' . esc_html( $col['title'] ) . '</th>';
								}

								?>
							</tr>
							</thead>
							<tbody id="the-list">
							<?php
							if ( is_array( $level_list ) && count( $level_list ) ) {
								$bgcolor = $class = '';
								foreach ( $pricing_levels_order as $order ) {
									$level_code = $order;
									$level      = ! empty( $level_list[ $order ] ) ? $level_list[ $order ] : '';
									if ( empty( $level ) ) {
										continue;
									}
									$class = ( 'alternate' == $class ) ? '' : 'alternate';
									$level = $level_list[ $order ];
									$class = ( 'alternate' == $class ) ? '' : 'alternate';

									echo '<tr class="' . $class . ' blog-row" data-level="' . $level_code . '">';

									foreach ( $posts_columns as $column_name => $column_display_name ) {
										switch ( $column_name ) {
											case 'level':
												?>
												<td scope="row" style="padding-left: 20px;">
													<strong><?php echo $level_code; ?></strong>
												</td>
												<?php
												break;

											case 'name':
												?>
												<td scope="row">
													<strong><?php echo esc_html( $level['name'] ); ?></strong>
												</td>
												<?php
												break;

											case 'pricing':

												$period_1  = ( isset( $level['price_1'] ) ) ? $psts->format_currency() . number_format( (float) $level['price_1'], 2, '.', '' ) : '';
												$period_3  = ( isset( $level['price_3'] ) ) ? $psts->format_currency() . number_format( (float) $level['price_3'], 2, '.', '' ) : '';
												$period_12 = ( isset( $level['price_12'] ) ) ? $psts->format_currency() . number_format( (float) $level['price_12'], 2, '.', '' ) : '';

												echo '<td>' . $period_1 . ' / ' . $period_3 . ' / ' . $period_12 . '</td>';

												break;

											case 'is_featured':
												?>
												<td scope="row">
													<?php $is_featured = $featured_level == $level_code ? 1 : 0; ?>
													<input value="<?php echo esc_attr( $level_code ); ?>"
													       name="psts[featured_level]"
													       type="radio" <?php echo checked( $is_featured, 1 ); ?> />
												</td>
												<?php
												break;

										}
									}
									?>
									</tr>
									<?php
								}
								?>
								<input type="hidden" name="psts[pricing_levels_order]"
								       value="<?php echo implode( ',', $pricing_levels_order ); ?>"/>
								<?php
							} else {
								?>
								<tr style='background-color: <?php echo $bgcolor; ?>'>
									<td colspan="6"><?php _e( 'Noch keine Levels.', 'psts' ) ?></td>
								</tr>
								<?php
							} // end if levels
							?>

							</tbody>
						</table>

					</td>
				</tr>
			</table>

			<?php
			//			$gateway = new ProSites_Gateway_2Checkout();
			//			echo $gateway->settings();
		}

		/**
		 * Pricing Table
		 *
		 * @return string
		 */
		public static function render_tab_comparison_table() {
			global $psts;

			$active_tab = ProSites_Helper_Tabs_Pricing::get_active_tab();
			ProSites_Helper_Settings::settings_header( $active_tab );

			$plans_table_enabled = $psts->get_setting( 'comparison_table_enabled' );
			$checked             = 'enabled' == $plans_table_enabled ? 'enabled' : 'disabled';
			$level_list          = get_site_option( 'psts_levels' );
			$last_level          = ( is_array( $level_list ) ) ? count( $level_list ) : 0;

			$table_settings  = ProSites_Model_Pricing::load_feature_settings();
			$enabled_modules = $psts->get_setting( 'modules_enabled', array() );

			?>
			<input type="hidden" name="pricing_settings" value="<?php echo esc_attr( $active_tab['tab_key'] ); ?>"/>
			<table class="form-table">
				<tr>
					<th scope="row"><?php _e( 'Featuretabelle aktivieren', 'psts' ) ?></th>
					<td>
						<input type="checkbox" name="psts[comparison_table_enabled]"
						       value="1" <?php checked( $checked, 'enabled' ); ?> />
					</td>
				</tr>

				<!-- MODULE TABLE -->
				<tr id="module-comparison-table">
					<td colspan="2">
						<div
							class="form-description"><?php _e( 'Verwende das folgende Formular, um Deine Featurevergleichstabelle zu erstellen.', 'psts' ); ?></div>
						<div class="level-select-bar">
							<?php
							//								_e( 'Select level: ', 'psts' );
							if ( is_array( $level_list ) && count( $level_list ) ) {
								foreach ( $level_list as $key => $level ) {
									$class = 1 == $key ? 'selected' : '';

									echo '<strong><a data-id="' . $key . '" class="' . $class . '">' . $level['name'] . '</a></strong>';
									if ( $key != ( count( $level_list ) ) ) {
										echo ' | ';
									}

								}
							}
							?>
							<input type="hidden" name="current_level" value="1"/>
						</div>

						<script type="text/javascript">
						(function(){
							function tagLevelFields() {
								var nodes = document.querySelectorAll('[name*="[levels]"]');
								nodes.forEach(function(node){
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

							function switchLevel(level) {
								var nodes = document.querySelectorAll('select[data-level], textarea[data-level], input[data-level], .chosen-container[data-level]');
								nodes.forEach(function(node){
									node.style.display = (node.getAttribute('data-level') === String(level)) ? '' : 'none';
									if (node.matches('.chosen-container')) {
										var search = node.querySelector('.chosen-search');
										if (search) { search.style.display = 'none'; }
									}
								});
								var current = document.querySelector('.level-select-bar input[name="current_level"]');
								if (current) {
									current.value = level;
								}
							}

							document.addEventListener('DOMContentLoaded', function(){
								tagLevelFields();
								var bar = document.querySelector('.level-select-bar');
								if (!bar) { return; }
								var currentInput = bar.querySelector('input[name="current_level"]');
								var startLevel = (currentInput && currentInput.value) ? currentInput.value : '1';
								switchLevel(startLevel);

								bar.addEventListener('click', function(e){
									if (e.target && e.target.matches('.level-select-bar a')) {
										e.preventDefault();
										bar.querySelectorAll('a').forEach(function(a){ a.classList.remove('selected'); });
										e.target.classList.add('selected');
										switchLevel(e.target.getAttribute('data-id'));
									}
								});
							});
						})();
						</script>

						<script type="text/javascript">
						// Vanilla fallback for comparison table (works without jQuery)
						document.addEventListener('DOMContentLoaded', function(){
							var tableBody = document.querySelector('#prosites-level-list.feature-table tbody');
							if (!tableBody) { return; }

							function getLevelCount() {
								var hiddenLevels = document.querySelector('[name="new-feature-levels"]');
								var val = hiddenLevels ? parseInt(hiddenLevels.value || '0', 10) : 0;
								if (!val || val < 1) {
									var anchors = document.querySelectorAll('.level-select-bar a');
									val = anchors.length || 1;
								}
								return val;
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
								document.querySelectorAll('select[data-level], textarea[data-level], input[data-level], .chosen-container[data-level]').forEach(function(node){
									node.style.display = (node.getAttribute('data-level') === String(level)) ? '' : 'none';
									if (node.classList.contains('chosen-container')) {
										var s = node.querySelector('.chosen-search');
										if (s) { s.style.display = 'none'; }
									}
								});
								var hidden = document.querySelector('.level-select-bar input[name="current_level"]');
								if (hidden) { hidden.value = level; }
							}

							function tagLevelFields() {
								document.querySelectorAll('[name*="[levels]"]').forEach(function(node){
									var m = node.name.match(/\[levels\]\[(\d+)\]/);
									if (m && m[1]) {
										node.setAttribute('data-level', m[1]);
										var next = node.nextElementSibling;
										if (next && next.classList.contains('chosen-container')) {
											next.setAttribute('data-level', m[1]);
										}
									}
								});
							}

							function updateOrderAndStripes() {
								var rows = tableBody.querySelectorAll('tr');
								var order = [];
								rows.forEach(function(row, idx){
									var pos = row.querySelector('.position');
									if (pos) { pos.textContent = idx + 1; }
									row.classList.toggle('alternate', idx % 2 !== 0);
									var keyInput = row.querySelector('td:first-child [name*="module_key"], td:first-child [name*="custom"]');
									if (keyInput && keyInput.value) { order.push(keyInput.value); }
								});
								var orderInput = document.querySelector('input[name="psts[feature_table][feature_order]"]');
								if (orderInput) { orderInput.value = order.join(','); }
							}

							function buildRow(name, description, text, levels) {
								var count = tableBody.querySelectorAll('tr').length;
								var rowClass = (count + 1) % 2 === 0 ? '' : 'alternate';
								var existing = tableBody.querySelectorAll('tr.custom .order-col [type="hidden"]');
								var n = existing.length;
								var custom = 'custom-' + (n + 1);
								while (Array.from(existing).some(function(el){ return el.value === custom; })) {
									n += 1;
									custom = 'custom-' + (n + 1);
								}

								var indicator = '';
								for (var i = 1; i <= levels; i++) {
									indicator += '<select name="psts[feature_table][' + custom + '][levels][' + i + '][status]">';
									indicator += '<option value="tick">&#x2713;</option>';
									indicator += '<option value="cross">&#x2718;</option>';
									indicator += '<option value="none">None</option>';
									indicator += '</select>';
								}

								var customText = '';
								for (var j = 1; j <= levels; j++) {
									customText += '<textarea name="psts[feature_table][' + custom + '][levels][' + j + '][text]">' + text + '</textarea>';
								}

								var html = '';
								html += '<tr class="' + rowClass + ' custom new-feature blog-row">';
								html += '<td scope="row" style="padding-left: 10px" class="order-col">';
								html += '<div class="position">' + (count + 1) + '</div>';
								html += '<input type="hidden" name="psts[feature_table][' + custom + '][custom]" value="' + custom + '" />';
								html += '<a class="delete"><span class="dashicons dashicons-trash"></span></a>';
								html += '</td>';
								html += '<td scope="row" style="padding-left: 20px;"><input type="checkbox" checked="checked" name="psts[feature_table][' + custom + '][visible]" value="1"></td>';
								html += '<td scope="row">';
								html += '<div class="text-item">' + name + '</div>';
								html += '<div class="edit-box" style="display:none">';
								html += '<input class="editor" type="text" name="psts[feature_table][' + custom + '][name]" value="' + name + '" /><br />';
								html += '<span><a class="save-link">save</a> <a style="margin-left: 10px;" class="reset-link">reset</a></span></div>';
								html += '<input type="hidden" value="' + name + '" />';
								html += '</td>';
								html += '<td scope="row">';
								html += '<div class="text-item">' + description + '</div>';
								html += '<div class="edit-box" style="display:none">';
								html += '<textarea class="editor" name="psts[feature_table][' + custom + '][description]">' + description + '</textarea><br />';
								html += '<span><a class="save-link">save</a> <a style="margin-left: 10px;" class="reset-link">reset</a></span></div>';
								html += '<input type="hidden" value="' + description + '" />';
								html += '</td>';
								html += '<td scope="row" class="level-settings">' + indicator + '</td>';
								html += '<td scope="row">' + customText + '</td>';
								html += '</tr>';
								return html;
							}

							function clearInputs() {
								var row = document.querySelector('#add-pricing-feature tr');
								if (!row) { return; }
								row.querySelectorAll('input[type="text"], textarea').forEach(function(el){ el.value = ''; });
							}

							// Add feature (vanilla)
							document.addEventListener('click', function(e){
								if (e.target && e.target.id === 'add-feature-button') {
									e.preventDefault();
									var name = (document.querySelector('[name="new-feature-name"]') || {}).value || '';
									var description = (document.querySelector('[name="new-feature-description"]') || {}).value || '';
									var text = (document.querySelector('[name="new-feature-text"]') || {}).value || '';
									var levels = getLevelCount();
									if (!name && !description && !text) { return false; }
									var noFeatures = document.querySelector('.no-features');
									if (noFeatures) { noFeatures.style.display = 'none'; }
									var rowHtml = buildRow(name, description, text, levels);
									tableBody.insertAdjacentHTML('beforeend', rowHtml);
									tagLevelFields();
									updateOrderAndStripes();
									switchLevel(getActiveLevel());
									clearInputs();
									if (noFeatures) { tableBody.appendChild(noFeatures); }
								}
							});

							// Delete custom feature
							document.addEventListener('click', function(e){
								if (e.target && (e.target.closest('.order-col .delete'))) {
									e.preventDefault();
									var row = e.target.closest('tr');
									if (!row) { return; }
									var hidden = row.querySelector('.order-col input[type="hidden"]');
									var markInput = document.querySelector('[name="mark_for_delete"]');
									if (hidden && markInput) {
										var marks = markInput.value ? markInput.value.split(',') : [];
										marks.push(hidden.value);
										markInput.value = marks.filter(Boolean).join(',');
									}
									row.remove();
									updateOrderAndStripes();
								}
							});

							// Inline edit (double click / save / reset)
							tableBody.addEventListener('dblclick', function(e){
								if (e.target && e.target.classList.contains('text-item')) {
									var editBox = e.target.nextElementSibling;
									if (editBox) {
										e.target.style.display = 'none';
										editBox.style.display = '';
									}
								}
							});
							tableBody.addEventListener('click', function(e){
								if (e.target && e.target.classList.contains('save-link')) {
									e.preventDefault();
									var td = e.target.closest('td');
									var textItem = td.querySelector('.text-item');
									var editor = td.querySelector('.editor');
									if (textItem && editor) { textItem.textContent = editor.value; }
									var box = e.target.closest('.edit-box');
									if (box) { box.style.display = 'none'; }
									if (textItem) { textItem.style.display = ''; }
								}
								if (e.target && e.target.classList.contains('reset-link')) {
									e.preventDefault();
									var td2 = e.target.closest('td');
									var textItem2 = td2.querySelector('.text-item');
									var hidden = td2.querySelector('input[type="hidden"]');
									var editor2 = td2.querySelector('.editor');
									if (hidden && textItem2) { textItem2.textContent = hidden.value; }
									if (hidden && editor2) { editor2.value = hidden.value; }
									var box2 = e.target.closest('.edit-box');
									if (box2) { box2.style.display = 'none'; }
									if (textItem2) { textItem2.style.display = ''; }
								}
							});

							// Level bar click
							var bar = document.querySelector('.level-select-bar');
							if (bar) {
								bar.addEventListener('click', function(e){
									if (e.target && e.target.matches('.level-select-bar a')) {
										e.preventDefault();
										bar.querySelectorAll('a').forEach(function(a){ a.classList.remove('selected'); });
										e.target.classList.add('selected');
										switchLevel(e.target.getAttribute('data-id'));
									}
								});
							}

							tagLevelFields();
							switchLevel(getActiveLevel());
							updateOrderAndStripes();
						});
						</script>

						<?php
						// define the columns to display, the syntax is 'internal name' => 'display name'
						$posts_columns = array(
							'order'       => array(
								'title' => __( '#', 'psts' ),
								'width' => '8px',
								'class' => '',
							),
							'visible'     => array(
								'title' => __( 'Sichtbar', 'psts' ),
								'width' => '36px',
								'class' => '',
							),
							'name'        => array(
								'title' => __( 'Name', 'psts' ),
								'small' => __( '(Doppelklick zum Ändern)' ),
								'width' => '150px',
								'class' => '',
							),
							'description' => array(
								'title' => __( 'Beschreibung', 'psts' ),
								'small' => __( '(Doppelklick zum Ändern)' ),
								'width' => '',
								'class' => '',
							),
							'tick_cross'  => array(
								'title' => __( 'Indikator', 'psts' ),
								'small' => __( '(für ausgewähltes Level)', 'psts' ),
								'width' => '60px',
								'class' => 'level-settings',
							),
							'custom'      => array(
								'title' => __( 'Benutzerdefinierter Text', 'psts' ),
								'small' => __( '(für ausgewähltes Level)', 'psts' ),
								'width' => '',
								'class' => '',
							),
						);

						$status_array_normal = array(
							'tick'  => '&#x2713',
							'cross' => '&#x2718',
						);

						$status_array_module = array(
							'module'  => __( 'Level: %s', 'psts' ),
							'inverse' => __( 'Umkehren: %s', 'psts' ),
						);

						$hover_actions = array(
							'edit'  => __( 'bearbeiten', 'psts' ),
							'save'  => __( 'ändern', 'psts' ),
							'reset' => __( 'zurücksetzen', 'psts' ),
						);

					$feature_order = array();
					$level_keys    = '';

						?>

						<table width="100%" cellpadding="3" cellspacing="3" class="widefat feature-table"
						       id="prosites-level-list">
							<thead>
							<tr>
								<?php
								foreach ( $posts_columns as $col ) {
									$style = ! empty( $col['width'] ) ? ' style="max-width:' . $col['width'] . '"' : '';
									$class = ! empty( $col['class'] ) ? ' class="' . $col['class'] . '"' : '';
									$small = ! empty( $col['small'] ) ? ' <small>' . esc_html( $col['small'] ) . '</small>' : '';
									echo '<th scope="col"' . $style . $class . '>' . esc_html( $col['title'] ) . $small . '</th>';
								}

								?>
							</tr>
							</thead>
							<tbody id="the-list">
							<?php
							if ( ! empty( $table_settings ) ) {
								$bgcolor       = $class = '';
								$count         = 0;
								$modules_array = array();
								foreach ( $table_settings as $key => $setting ) {
									if ( 'modules' == $key || 'feature_order' == $key || 'levels' == $key ) {
										continue;
									}
									// don't show disabled modules
									if ( isset( $setting['module'] ) && ! in_array( $setting['module'], $enabled_modules ) ) {
										continue;
									}

									$feature_order[] = $key;

									$count += 1;
									$level_code = 0;
									//										$level = $level_list[ $order ];
									$class = $count % 2 == 0 ? '' : 'alternate';
									$class .= empty( $setting['module'] ) ? ' custom' : ' module';

									echo '<tr class="' . $class . ' blog-row" data-level="' . $level_code . '">';

									foreach ( $posts_columns as $column_name => $column ) {
										switch ( $column_name ) {
											case 'order':
												$style = ! empty( $column['width'] ) ? ' max-width:' . $column['width'] . ';' : '';
												?>
												<td scope="row" style="padding-left: 10px; <?php echo $style; ?>" class="order-col">
													<div class="position"><?php echo $count; ?></div>
													<?php
													if ( isset( $setting['custom'] ) ) {
														echo '<input type="hidden" name="psts[feature_table][' . $key . '][custom]" value="' . esc_attr( $setting['custom'] ) . '" />';
														echo '<a class="delete"><span class="dashicons dashicons-trash"></span></a>';
													}
													if ( isset( $setting['module'] ) ) {
														$modules_array[] = $setting['module'];
														echo '<input type="hidden" name="psts[feature_table][' . $key . '][module]" value="' . esc_attr( $setting['module'] ) . '" />';
														echo '<input type="hidden" name="psts[feature_table][' . $key . '][module_key]" value="' . $key . '" />';
													}
													?>
												</td>
												<?php
												break;
											case 'visible':
												?>
												<td scope="row" style="padding-left: 20px;">
													<?php
													if ( ! isset( $setting['visible'] ) ) {
														$setting['visible'] = false;
													}
													?>
													<input type="checkbox"
													       name="psts[feature_table][<?php echo $key; ?>][visible]"
													       value="1" <?php checked( $setting['visible'] ) ?>>
												</td>
												<?php
												break;

											case 'name':
												$original_value = '';
												if ( isset( $setting['module'] ) && ! empty( $setting['module'] ) ) {
													if ( method_exists( $setting['module'], 'get_name' ) ) {
														$original_value = call_user_func( $setting['module'] . '::get_name' );
													}
												}
												if ( isset( $setting['custom'] ) && ! empty( $setting['custom'] ) ) {
													$original_value = $setting['name'];
												}
												?>
												<td scope="row">
													<div
														class="text-item"><?php echo esc_html( $setting['name'] ); ?></div>
													<div class="edit-box" style="display:none">
														<input class="editor" type="text"
														       name="psts[feature_table][<?php echo $key; ?>][name]"
														       value="<?php echo esc_html( $setting['name'] ); ?>"/><br/>
														<span><a
																class="save-link"><?php echo esc_html( $hover_actions['save'] ); ?></a> <a
																style="margin-left: 10px;"
																class="reset-link"><?php echo esc_html( $hover_actions['reset'] ); ?></a></span>
													</div>
													<input type="hidden"
													       value='<?php echo esc_html( $original_value ); ?>'/>
												</td>
												<?php
												break;

											case 'description':
												if ( isset( $setting['module'] ) && ! empty( $setting['module'] ) ) {
													if ( method_exists( $setting['module'], 'get_description' ) ) {
														$original_value = call_user_func( $setting['module'] . '::get_description' );
													}
												}
												if ( isset( $setting['custom'] ) && ! empty( $setting['custom'] ) ) {
													$original_value = $setting['description'];
												}
												?>
												<td scope="row">
													<div
														class="text-item"><?php echo ProSites::filter_html( $setting['description'] ); ?></div>
													<div class="edit-box" style="display:none">
														<textarea class="editor" type="text"
														          name="psts[feature_table][<?php echo $key; ?>][description]"><?php echo ProSites::filter_html( $setting['description'] ); ?></textarea><br/>
														<span><a
																class="save-link"><?php echo esc_html( $hover_actions['save'] ); ?></a> <a
																style="margin-left: 10px;"
																class="reset-link"><?php echo esc_html( $hover_actions['reset'] ); ?></a></span>
													</div>
													<input type="hidden"
													       value='<?php echo esc_html( ProSites::filter_html( $original_value ) ); ?>'/>
												</td>
												<?php
												break;

											case 'tick_cross':
												?>
												<td scope="row" class="<?php echo esc_attr( $column['class'] ); ?>">
													<?php

													// We're working with level based settings
													if ( is_array( $level_list ) && count( $level_list ) ) {
														foreach ( $level_list as $level_id => $level ) {
															if ( ! empty( $setting['levels'][ $level_id ]['status'] ) && ! is_array( $setting['levels'][ $level_id ]['status'] ) ) {
																$status       = $setting['levels'][ $level_id ]['status'];
																$level_status = '';
																if ( isset( $setting['module'] ) && method_exists( $setting['module'], 'get_level_status' ) ) {
																	$level_status = call_user_func( $setting['module'] . '::get_level_status', $level_id );
																}
																if ( ! empty( $setting['module'] ) ) {
																	$chosen_array = $status_array_module;
																	$invert       = 'tick' == $level_status ? 'cross' : 'tick';

																	$chosen_array['module']  = sprintf( $chosen_array['module'], $status_array_normal[ $level_status ] );
																	$chosen_array['inverse'] = sprintf( $chosen_array['inverse'], $status_array_normal[ $invert ] );

																} else {
																	$chosen_array = $status_array_normal;
																}
																if ( ! empty( $setting['module'] ) ) {
																	//																	echo '*hide* ' . $setting['levels'][ $key ]['status'];
																} else {
																	//																	echo '*hide* Not a module';
																}
																?>
																<!-- Change name... -->
																<select class="chosen"
																        name="psts[feature_table][<?php echo $key; ?>][levels][<?php echo $level_id; ?>][status]"
																        data-level="level-<?php echo $level_id; ?>[status]">
																	<?php
																	foreach ( $chosen_array as $item_key => $item ) {
																		echo '<option value="' . esc_attr( $item_key ) . '" ' . selected( $status, $item_key ) . '>' . $item . '</option>';
																	}
																	echo '<option value="none" ' . selected( $status, 'none' ) . '>' . __( 'Keiner', 'psts' ) . '</option>';
																	?>
																</select>
																<?php
															} elseif ( isset( $setting['levels'][ $level_id ]['status'] ) && is_array( $setting['levels'][ $level_id ]['status'] ) ) {

//																$new_status = $setting['levels'][ $level_id ]['status'];
//																if( method_exists( $setting[ 'module' ], 'get_level_status' ) ) {
//																	$new_status = call_user_func( $setting[ 'module' ] . '::get_level_status', $level_id );
//																	$old_status = $setting['levels'][ $level_id ]['status'] ;
//																	if( 'none' != $old_status['selection'] ) {
//																		$new_status['selection'] = $new_status['value'];
//																	} else {
//																		$new_status['selection'] = 'none';
//																	}
//
//																}
//																$keys = array_keys( $new_status );
																$keys = array_keys( $setting['levels'][ $level_id ]['status'] );

																foreach ( $keys as $index ) {
																	echo '<input type="hidden" name="psts[feature_table][' . $key . '][levels][' . $level_id . '][status][' . $index . ']" value="' . $setting['levels'][ $level_id ]['status'][ $index ] . '" />';
																}

																?>

																<select class="chosen"
																        name="psts[feature_table][<?php echo $key; ?>][levels][<?php echo $level_id; ?>][status][selection]">
																	<?php
																	$value     = $setting['levels'][ $level_id ]['status']['value'];
																	$selection = isset( $setting['levels'][ $level_id ]['status']['selection'] ) ? $setting['levels'][ $level_id ]['status']['selection'] : $value;
																	$selected  = selected( $selection, $value, false );
																	echo '<option value="' . esc_attr( $value ) . '" ' . $selected . '>' . esc_html( $setting['levels'][ $level_id ]['status']['display'] ) . '</option>';
																	echo '<option value="none" ' . selected( $selection, 'none' ) . '>' . __( 'Keiner', 'psts' ) . '</option>';
																	?>
																</select>
																<?php
															}
														}
													}

													// There are no level specific settings
													if ( isset( $setting['active'] ) && ( ! empty( $setting['active'] ) || false === $setting['active'] ) ) {
														$module_active = true;

														if ( method_exists( $setting['module'], 'is_active' ) ) {
															$module_active = call_user_func( $setting['module'] . '::is_active' );
														}

														$active_status = array(
															'active'   => array(
																'title'  => __( 'Aktiv: %s', 'psts' ),
																'status' => 'tick',
															),
															'inactive' => array(
																'title'  => __( 'Nicht aktiv: %s', 'psts' ),
																'status' => 'cross',
															),
														);

														$value = $setting['active'];
														if ( $module_active ) {
															$option = '<option value="module" ' . selected( $value, 'module', false ) . '>' . sprintf( $active_status['active']['title'], $status_array_normal[ $active_status['active']['status'] ] ) . '</option>';
														} else {
															$option = '<option value="module" ' . selected( $value, 'module', false ) . '>' . sprintf( $active_status['inactive']['title'], $status_array_normal[ $active_status['inactive']['status'] ] ) . '</option>';
														}

														?>
														<select class="chosen"
														        name="psts[feature_table][<?php echo $key; ?>][active]">
															<?php
															echo $option;
															echo '<option value="none" ' . selected( $value, 'none' ) . '>' . __( 'Keiner', 'psts' ) . '</option>';
															?>
														</select>
														<?php
													}

													?>
												</td>
												<?php
												break;

											case 'custom':
												?>
												<td scope="row">
													<?php
													$x = '';
													if ( is_array( $level_list ) && count( $level_list ) ) {
														foreach ( $level_list as $level_id => $level ) {
															?>
															<textarea
																name="psts[feature_table][<?php echo $key; ?>][levels][<?php echo $level_id; ?>][text]"><?php echo esc_html( $setting['levels'][ $level_id ]['text'] ); ?></textarea>
															<?php
														}
													}
													?>
												</td>
												<?php
												break;

										}
									}
									?>
									</tr>
									<?php
									$level_keys = array_keys( $level_list );
									$level_keys = implode( ',', $level_keys );
								}
								?>
								<input type="hidden" name="psts[feature_table][modules]"
								       value="<?php echo implode( ',', $modules_array ); ?>"/>
								<input type="hidden" name="psts[feature_table][levels]"
								       value="<?php echo $level_keys; ?>"/>
								<!--										<input type="hidden" name="psts[pricing_levels_order]" value="--><?php //echo implode( ',' , $pricing_levels_order ); ?><!--" />-->
								<?php
							} else {
								?>
								<tr class='no-features'>
									<td colspan="6"><?php _e( 'Es wurden noch keine Funktionen hinzugefügt.', 'psts' ) ?></td>
								</tr>
								<?php
							} // end if levels
							?>

							</tbody>
						</table>

						<?php
						// Add order...
						$feature_order = implode( ',', $feature_order );
						echo '<input type="hidden" name="psts[feature_table][feature_order]" value="' . $feature_order . '" />';
						// Mark for delete...
						echo '<input type="hidden" name="mark_for_delete" value="" />';
						?>

					</td>
				</tr>


				<tr id="add-feature-box">
					<td colspan="2">
						<strong><?php _e( 'Benutzerdefinierte Funktion hinzufügen', 'psts' ); ?></strong>
						<table id="add-pricing-feature" class="form-table">
							<thead>
							<tr>
								<th><?php _e( 'Name', 'psts' ); ?></th>
								<th class="psts-help-div"><?php _e( 'Beschreibung', 'psts' ); ?>
								<?php echo $psts->help_text( __( 'Hinweis: Verwende doppelte Anführungszeichen, um Werte wie z.B.: <code>style=&quot;color:#f00&quot;</code>, Andernfalls werden möglicherweise einige Tags auf dieser Seite nicht gerendert.', 'psts' ) ); ?></th>
								<th><?php _e( 'Benutzerdefinierter Text', 'psts' ); ?></th>
								<th></th>
							</tr>
							</thead>
							<tbody>
							<tr class="alternate">
								<td>
									<input name="new-feature-name" type="text"/>
									<input name="new-feature-levels" type="hidden"
									       value="<?php echo count( $level_list ); ?>"/>
								</td>
								<td><textarea name="new-feature-description"></textarea></td>
								<td><textarea name="new-feature-text"></textarea></td>
								<td><input type="button" class="button" name="add-feature-button"
								           id="add-feature-button" value="Hinzufügen"/></td>

							</tr>
							</tbody>
						</table>

					</td>
				</tr>

			</table>
			<?php
			//			$gateway = new ProSites_Gateway_2Checkout();
			//			echo $gateway->settings();

		}

		public static function render_tab_pricing_style() {
			ProSites_View_Pricing_Styling::render_tab_pricing_style();
		}


	}
}