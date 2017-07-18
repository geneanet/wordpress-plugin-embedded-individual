<?php
/**
 * Provides a public-facing view for the plugin.
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link http://www.geneanet.org
 * @since 1.0.0
 *
 * @package Geneanet_Embedded_Individual
 * @subpackage Geneanet_Embedded_Individual/public/partials
 */

?>
<script type="text/javascript">
	document.addEventListener( 'DOMContentLoaded', function() {
		window.top.GeneanetEmbeddedIndividual.loadGeneanetEmbeddedIndividual( document.getElementById( '<?php echo esc_html( $embed_individual_id ); ?>' ), '<?php echo esc_attr( $basename ); ?>', '<?php echo esc_attr( $index ); ?>' );
	} );
</script>
<div id="<?php echo esc_attr( $embed_individual_id ); ?>" class="geneanet-embedded-individual <?php echo esc_attr( $align ); ?>">
	<div class="geneanet-embedded-individual-content" onclick="window.open('<?php echo esc_js( $url ) ?>');return false;">
		<div class="geneanet-embedded-individual-column-left">
			<?php
			if ( 0 === $sex ) {
				$individual_image = 'default-male.png';
			} elseif ( 1 === $sex ) {
				$individual_image = 'default-female.png';
			} else {
				$individual_image = 'default-unknown.png';
			}
			?>
			<img class="geneanet-embedded-individual-image" src="<?php echo esc_attr( plugin_dir_url( __FILE__ ) . '../img/' . $individual_image ); ?>" />
		</div>
		<div class="geneanet-embedded-individual-column-right">
			<div class="geneanet-embedded-individual-name">
				<?php echo esc_html( $firstname ); ?> <?php echo esc_html( $lastname ); ?>
			</div>
			<div class="geneanet-embedded-individual-sosa geneanet-embedded-individual-loading">
				<svg version="1.1" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" viewBox="0 0 219 219" enable-background="new 0 0 219 219" xml:space="preserve">
					<g>
						<circle fill="#95C417" cx="109.5" cy="109.5" r="100"/>
					</g>
					<g>
						<g>
							<path fill="#FFFFFF" d="M185.407,109.5c0,41.924-33.988,75.902-75.907,75.902c-41.921,0-75.904-33.979-75.904-75.902
		                                            c0-41.922,33.983-75.906,75.904-75.906C151.419,33.594,185.407,67.578,185.407,109.5z M109.5,72.65
		                                            c-20.352,0-36.85,16.5-36.85,36.852c0,20.348,16.498,36.85,36.85,36.85c20.354,0,36.846-16.502,36.846-36.85
		                                            C146.344,89.15,129.852,72.65,109.5,72.65z"/>
						</g>
					</g>
				</svg>
				<span><?php esc_html_e( 'Sosa/Ahnentafel number: ', $plugin_name ); ?></span><span class="geneanet-embedded-individual-sosa-number"></span>
			</div>
			<a href="<?php echo esc_url( $url ); ?>" class="geneanet-embedded-individual-link" target="_blank"><?php
			if ( 'tree' === $link_type ) {
				esc_html_e( 'View Family Tree', $plugin_name );
			} else {
				esc_html_e( 'View Individual', $plugin_name );
			}
			?></a>
			<div class="geneanet-embedded-individual-loader">
				<img src="<?php echo esc_attr( plugin_dir_url( __FILE__ ) . '../img/spinner.gif' ); ?>" />
			</div>
			<div class="geneanet-embedded-individual-error alert-box-error"></div>
			<div class="geneanet-embedded-individual-info-block">
				<div class="geneanet-embedded-individual-info-field geneanet-embedded-individual-loading"><span class="geneanet-embedded-individual-birth geneanet-embedded-individual-info"><span class="geneanet-embedded-individual-date"></span><span class="geneanet-embedded-individual-place"></span></span></div>
				<div class="geneanet-embedded-individual-info-field geneanet-embedded-individual-loading"><span class="geneanet-embedded-individual-death geneanet-embedded-individual-info"><span class="geneanet-embedded-individual-date"></span><span class="geneanet-embedded-individual-place"></span></span></div>
			</div>
			<div class="geneanet-embedded-individual-info-block">
				<div class="geneanet-embedded-individual-info-field geneanet-embedded-individual-loading"><?php esc_html_e( 'Father: ', $plugin_name ); ?><span class="geneanet-embedded-individual-father geneanet-embedded-individual-info"></span></div>
				<div class="geneanet-embedded-individual-info-field geneanet-embedded-individual-loading"><?php esc_html_e( 'Mother: ', $plugin_name ); ?><span class="geneanet-embedded-individual-mother geneanet-embedded-individual-info"></span></div>
			</div>
			<div class="geneanet-embedded-individual-info-block">
				<div class="geneanet-embedded-individual-info-field geneanet-embedded-individual-loading"><span class="geneanet-embedded-individual-families geneanet-embedded-individual-info"></span></div>
			</div>
		</div>
	</div>

	<div class="geneanet-embedded-individual-logo">
		<a href="http://www.geneanet.org" target="_blank">
			<img src="<?php echo esc_url( plugin_dir_url( __FILE__ ) . '../img/logo.png' ); ?>" />
		</a>
	</div>
</div>
