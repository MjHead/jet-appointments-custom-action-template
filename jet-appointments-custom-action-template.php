<?php
/**
 * Plugin Name: JetAppointmentsBooking - custom action page template
 * Description: Allow to use Elementor templates as custom action pages templates
 * Plugin URI:
 * Author: Crocoblock
 * Version: 1.0.0
 */

namespace Jet_Appointments_Custom_Action_Template;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! defined( 'JET_APPOINTMENTS_CUSTOM_TEMPLATE__CONFIRM' ) ) {
	define( 'JET_APPOINTMENTS_CUSTOM_TEMPLATE__CONFIRM', false );
}

if ( ! defined( 'JET_APPOINTMENTS_CUSTOM_TEMPLATE__CANCEL' ) ) {
	define( 'JET_APPOINTMENTS_CUSTOM_TEMPLATE__CANCEL', 401 );
}

if ( ! defined( 'JET_APPOINTMENTS_CUSTOM_TEMPLATE__ERROR_CONFIRM' ) ) {
	define( 'JET_APPOINTMENTS_CUSTOM_TEMPLATE__ERROR_CONFIRM', false );
}

if ( ! defined( 'JET_APPOINTMENTS_CUSTOM_TEMPLATE__ERROR_CANCEL' ) ) {
	define( 'JET_APPOINTMENTS_CUSTOM_TEMPLATE__ERROR_CANCEL', 386 );
}

final class Plugin {

	/**
	 * Instance
	 */
	private static $_instance = null;

	/**
	 * Instance
	 * Ensures only one instance of the class is loaded or can be loaded.
	 */
	public static function instance() {

		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;

	}

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
	public function __construct() {
		// Init Plugin
		add_action( 'plugins_loaded', [ $this, 'init' ] );
	}

	public function config() {
		return [
			[
				'template' => JET_APPOINTMENTS_CUSTOM_TEMPLATE__CONFIRM,
				'hook'     => 'jet-apb/public-actions/custom-action-page-content',
				'validate' => function( $actions ) {
					return 'confirm' === $actions->get_action();
				}
			],
			[
				'template' => JET_APPOINTMENTS_CUSTOM_TEMPLATE__CANCEL,
				'hook'     => 'jet-apb/public-actions/custom-action-page-content',
				'validate' => function( $actions ) {
					return 'cancel' === $actions->get_action();
				}
			],
			[
				'template' => JET_APPOINTMENTS_CUSTOM_TEMPLATE__ERROR_CONFIRM,
				'hook'     => 'jet-apb/public-actions/custom-error-page-content',
				'validate' => function( $actions ) {
					return 'confirm' === $actions->get_action();
				}
			],
			[
				'template' => JET_APPOINTMENTS_CUSTOM_TEMPLATE__ERROR_CANCEL,
				'hook'     => 'jet-apb/public-actions/custom-error-page-content',
				'validate' => function( $actions ) {
					return 'cancel' === $actions->get_action();
				}
			],
		];
	}

	/**
	 * Initialize the plugin
	 */
	public function init() {

		// At the moment works only with Elementor
		if ( ! defined( 'ELEMENTOR_VERSION' ) ) {
			return;
		}

		foreach ( $this->config() as $page ) {

			if ( empty( $page['template'] ) ) {
				continue;
			}

			add_filter( $page['hook'], function( $page_content, $actions ) use ( $page ) {

				if ( ! empty( $page['validate'] ) 
					&& is_callable( $page['validate'] ) 
					&& false === call_user_func( $page['validate'], $actions ) 
				) {
					return $page_content;
				}

				ob_start();
				?>
				<?php wp_head(); ?>
				<body <?php body_class(); ?>>
				<?php wp_body_open(); ?>
				<?php echo \Elementor\Plugin::instance()->frontend->get_builder_content( $page['template'] ); ?>
				<?php wp_footer(); ?>
				<?php
				return ob_get_clean();

			}, 10, 2 );

		}
	}

}

Plugin::instance();
