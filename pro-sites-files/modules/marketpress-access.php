<?php

/*
PS Bloghosting (Module: MarketPress Zugriff)
*/

class ProSites_Module_MarketPress_Access {

	public static function get_name() {
		return __( 'MarketPress Zugriff', 'psts' );
	}

	public static function get_description() {
		return __( 'Steuert, welche Bloghosting-Levels MarketPress in Subsites nutzen duerfen.', 'psts' );
	}

	public static function get_class_restriction() {
		return 'Marketpress';
	}

	public function __construct() {
		add_action( 'psts_page_after_modules', array( $this, 'plug_network_page' ) );
	}

	public function plug_network_page() {
		$module_page = add_submenu_page(
			'psts',
			__( 'Bloghosting MarketPress Zugriff', 'psts' ),
			__( 'MarketPress Zugriff', 'psts' ),
			'manage_network_options',
			'psts-marketpress-access',
			array( $this, 'admin_page' )
		);

		add_action( 'admin_print_styles-' . $module_page, array( $this, 'load_settings_style' ) );
	}

	public function load_settings_style() {
		ProSites_Helper_UI::load_psts_style();
	}

	public function admin_page() {
		global $psts;

		$levels = (array) get_site_option( 'psts_levels', array() );
		$saved  = array_map( 'absint', (array) $psts->get_setting( 'marketpress_allowed_levels', array() ) );
		$saved  = array_values( array_unique( $saved ) );
		$updated = false;

		if ( isset( $_POST['psts_marketpress_access_save'] ) ) {
			check_admin_referer( 'psts_marketpress_access' );

			$allowed = array();
			if ( isset( $_POST['marketpress_allowed_levels'] ) && is_array( $_POST['marketpress_allowed_levels'] ) ) {
				foreach ( $_POST['marketpress_allowed_levels'] as $raw_level ) {
					$level = absint( $raw_level );
					if ( $level >= 0 ) {
						$allowed[] = $level;
					}
				}
			}

			$allowed = array_values( array_unique( $allowed ) );
			$psts->update_setting( 'marketpress_allowed_levels', $allowed );
			$saved   = $allowed;
			$updated = true;
		}

		?>
		<div class="wrap">
			<h1><?php _e( 'MarketPress Zugriff', 'psts' ); ?></h1>
			<?php if ( $updated ) : ?>
				<div class="updated"><p><?php _e( 'Einstellungen gespeichert.', 'psts' ); ?></p></div>
			<?php endif; ?>

			<p><?php _e( 'Waehle die Bloghosting-Levels aus, die MarketPress in Subsites nutzen duerfen.', 'psts' ); ?></p>

			<form method="post" action="">
				<?php wp_nonce_field( 'psts_marketpress_access' ); ?>
				<table class="widefat striped">
					<thead>
						<tr>
							<th style="width:80px;"><?php _e( 'Erlaubt', 'psts' ); ?></th>
							<th style="width:80px;"><?php _e( 'Level', 'psts' ); ?></th>
							<th><?php _e( 'Paketname', 'psts' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td><input type="checkbox" name="marketpress_allowed_levels[]" value="0" <?php checked( in_array( 0, $saved, true ) ); ?> /></td>
							<td>0</td>
							<td><?php _e( 'Free / Basis', 'psts' ); ?></td>
						</tr>
						<?php foreach ( $levels as $level_id => $level_data ) : ?>
							<?php
							$level_id = absint( $level_id );
							$label = isset( $level_data['name'] ) ? (string) $level_data['name'] : sprintf( __( 'Level %d', 'psts' ), $level_id );
							?>
							<tr>
								<td><input type="checkbox" name="marketpress_allowed_levels[]" value="<?php echo esc_attr( (string) $level_id ); ?>" <?php checked( in_array( $level_id, $saved, true ) ); ?> /></td>
								<td><?php echo esc_html( (string) $level_id ); ?></td>
								<td><?php echo esc_html( $label ); ?></td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>

				<p class="submit">
					<input type="submit" name="psts_marketpress_access_save" class="button-primary" value="<?php _e( 'Aenderungen speichern', 'psts' ); ?>" />
				</p>
			</form>
		</div>
		<?php
	}
}
