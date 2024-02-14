<?php

/**
 * Register and enqueue scripts for plugin.
 *
 * @since 1.0.0
 * @package BlockArt
 */

namespace BlockArt;

defined( 'ABSPATH' ) || exit;

use BlockArt\Traits\Singleton;
use JsonMachine\Items;

/**
 * Register and enqueue scripts for plugin.
 *
 * @since 1.0.0
 */
class ScriptStyle {


	use Singleton;

	/**
	 * Scripts.
	 *
	 * @var array
	 */
	private $scripts = array();

	/**
	 * Styles.
	 *
	 * @var array
	 */
	private $styles = array();

	/**
	 * Localized scripts.
	 *
	 * @var array
	 */
	private $localized_scripts = array();

	/**
	 * Setting.
	 *
	 * @var \BlockArt\Setting
	 */
	private $setting;

	/**
	 * Constructor.
	 */
	protected function __construct() {
		$this->setting = new Setting();
		$this->init_hooks();
	}

	/**
	 * Initialize hooks.
	 *
	 * @since 1.0.0
	 */
	private function init_hooks() {
		add_action( 'init', array( $this, 'after_wp_init' ) );
		add_action( 'init', array( $this, 'register_scripts_styles' ), 11 );
		add_filter(
			'wp_handle_upload',
			function ( $upload ) {
				delete_transient( '_blockart_media_items' );
				return $upload;
			}
		);
		add_action(
			'wp_head',
			function () {
				printf( '<script>window._BLOCKART_WEBPACK_PUBLIC_PATH_ = "%s"</script>', esc_url( BLOCKART_DIST_DIR_URL . '/' ) );
			}
		);
		add_action( 'enqueue_block_editor_assets', array( $this, 'localize_block_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'localize_admin_scripts' ) );
	}

	/**
	 * Get asset url.
	 *
	 * @param string $filename Asset filename.
	 * @param boolean $dev Has dev url.
	 * @return string
	 */
	public static function get_asset_url( $filename, $dev = true ) {
		$path = plugins_url( 'dist/', BLOCKART_PLUGIN_FILE );

		if ( $dev && blockart_is_development() ) {
			$path = 'http://localhost:3000/dist/';
		}

		return $path . $filename;
	}

	/**
	 * After WP init.
	 *
	 * @return void
	 */
	public function after_wp_init() {
		$blocks_asset    = $this->get_asset_file( 'blocks' );
		$dashboard_asset = $this->get_asset_file( 'dashboard' );
		$suffix          = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		$is_wp62         = version_compare( get_bloginfo( 'version' ), '6.2', '>=' );
		$map_api_key     = $this->setting->get( 'integrations.google-maps-embed-api-key', '' );

		$this->scripts = array(
			'blocks'          => array(
				'src'     => self::get_asset_url( $is_wp62 ? "blocks$suffix.js" : "blocks-17$suffix.js" ),
				'deps'    => $blocks_asset['dependencies'],
				'version' => $blocks_asset['version'],
				'i18n'    => true,
			),
			'admin'           => array(
				'src'     => self::get_asset_url( $is_wp62 ? "dashboard$suffix.js" : "dashboard-17$suffix.js" ),
				'deps'    => $dashboard_asset['dependencies'],
				'version' => $dashboard_asset['version'],
				'i18n'    => true,
			),
			'frontend-utils'  => array(
				'src'     => self::get_asset_url( "frontend-utils$suffix.js", false ),
				'deps'    => array(),
				'version' => BLOCKART_VERSION,
			),
			'frontend-common' => array(
				'src'     => self::get_asset_url( "common$suffix.js", false ),
				'deps'    => array( 'blockart-frontend-utils' ),
				'version' => BLOCKART_VERSION,
			),
			'google-maps'     => array(
				'src'      => "https://maps.googleapis.com/maps/api/js?key=$map_api_key&libraries=places&callback=Function.prototype",
				'deps'     => array(),
				'version'  => BLOCKART_VERSION,
				'callback' => function () use ( $map_api_key ) {
					return (bool) $map_api_key;
				},
			),
		);

		foreach ( array(
			'countdown',
			'counter',
			'lottie',
			'map',
			'notice',
			'progress',
			'slider',
			'table-of-contents',
			'tabs',
			'timeline',
			'modal',
		) as $view_script ) {
			$this->scripts[ "frontend-$view_script" ] = array(
				'src'     => self::get_asset_url( "$view_script$suffix.js", false ),
				'deps'    => array_merge(
					array( 'blockart-frontend-common' ),
					'map' === $view_script ? array( 'blockart-google-maps' ) : array()
				),
				'version' => BLOCKART_VERSION,
			);
		}

		$this->styles = array(
			'blocks'        => array(
				'src'     => self::get_asset_url( 'style-blocks.css', false ),
				'version' => $blocks_asset['version'],
				'deps'    => array(),
			),
			'blocks-editor' => array(
				'src'     => self::get_asset_url( 'blocks.css', false ),
				'version' => $blocks_asset['version'],
				'deps'    => array(),
			),
		);
	}

	/**
	 * Get all media items.
	 *
	 * @return array
	 */
	private function get_media_items() {
		$media_items = get_transient( '_blockart_media_items' );
		if ( empty( $media_items ) ) {
			$media_items = array_map(
				function ( $item ) {
					$item                  = (array) $item;
					$item['media_details'] = wp_get_attachment_metadata( $item['ID'] );
					$item['alt_text']      = get_post_meta( $item['ID'], '_wp_attachment_image_alt', true );
					$item['mime_type']     = $item['post_mime_type'];
					$item['source_url']    = wp_get_attachment_url( $item['ID'] );
					return $item;
				},
				get_posts(
					array(
						'post_type'      => 'attachment',
						'post_status'    => 'inherit',
						'posts_per_page' => -1,
						'orderby'        => 'title',
						'order'          => 'ASC',
					)
				)
			);
			set_transient( '_blockart_media_items', $media_items, DAY_IN_SECONDS );
		}
		return $media_items;
	}

	/**
	 * Register scripts.
	 *
	 * @return void
	 */
	public function register_scripts() {
		foreach ( $this->scripts as $handle => $script ) {
			if ( empty( $script['callback'] ) ) {
				wp_register_script( "blockart-$handle", $script['src'], $script['deps'], $script['version'], true );
			} elseif ( is_callable( $script['callback'] ) && call_user_func_array( $script['callback'], array() ) ) {
				wp_register_script( "blockart-$handle", $script['src'], $script['deps'], $script['version'], true );
			}
			if ( isset( $script['i18n'] ) && $script['i18n'] ) {
				wp_set_script_translations( "blockart-$handle", 'blockart', BLOCKART_LANGUAGES );
			}
		}
	}

	/**
	 * Register styles.
	 *
	 * @return void
	 */
	public function register_styles() {
		foreach ( $this->styles as $handle => $style ) {
			if ( empty( $style['callback'] ) ) {
				wp_register_style( "blockart-$handle", $style['src'], $style['deps'], $style['version'] );
			} elseif ( is_callable( $style['callback'] ) && call_user_func_array( $style['callback'], array() ) ) {
				wp_register_style( "blockart-$handle", $style['src'], $style['deps'], $style['version'] );
			}
		}
	}

	/**
	 * Register scripts and styles for plugin.
	 *
	 * @since 1.0.0
	 */
	public function register_scripts_styles() {
		$this->register_scripts();
		$this->register_styles();
	}

	/**
	 * Get asset file
	 *
	 * @param string $prefix Filename prefix.
	 * @return array|mixed
	 */
	private function get_asset_file( string $prefix ) {
		$asset_file = dirname( BLOCKART_PLUGIN_FILE ) . "/dist/$prefix.asset.php";

		return file_exists( $asset_file )
			? include $asset_file
			: array(
				'dependencies' => array(),
				'version'      => BLOCKART_VERSION,
			);
	}

	/**
	 * Localize block scripts.
	 *
	 * @return void
	 */
	public function localize_admin_scripts() {
		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		if ( ! function_exists( 'wp_get_themes' ) ) {
			require_once ABSPATH . 'wp-admin/includes/theme.php';
		}
		$installed_plugin_slugs = array_keys( get_plugins() );
		$allowed_plugin_slugs   = array(
			'everest-forms/everest-forms.php',
			'user-registration/user-registration.php',
			'learning-management-system/lms.php',
			'magazine-blocks/magazine-blocks.php',
		);

		$installed_theme_slugs = array_keys( wp_get_themes() );
		$current_theme         = get_stylesheet();
		$google_fonts          = Items::fromFile( BLOCKART_PLUGIN_DIR . '/assets/json/google-fonts.json' );

		$localized_scripts = apply_filters(
			'blockart_localize_admin_scripts',
			array(
				'name' => '_BLOCKART_DASHBOARD_',
				'data' => array(
					'version'     => BLOCKART_VERSION,
					'plugins'     => array_reduce(
						$allowed_plugin_slugs,
						function ( $acc, $curr ) use ( $installed_plugin_slugs ) {
							if ( in_array( $curr, $installed_plugin_slugs, true ) ) {
								if ( is_plugin_active( $curr ) ) {
									$acc[ $curr ] = 'active';
								} else {
									$acc[ $curr ] = 'inactive';
								}
							} else {
								$acc[ $curr ] = 'not-installed';
							}
							return $acc;
						},
						array()
					),
					'themes'      => array(
						'zakra'    => strpos( $current_theme, 'zakra' ) !== false ? 'active' : (
							in_array( 'zakra', $installed_theme_slugs, true ) ? 'inactive' : 'not-installed'
						),
						'colormag' => strpos( $current_theme, 'colormag' ) !== false || strpos( $current_theme, 'colormag-pro' ) !== false ? 'active' : (
							in_array( 'colormag', $installed_theme_slugs, true ) || in_array( 'colormag-pro', $installed_theme_slugs, true ) ? 'inactive' : 'not-installed'
						),
					),
					'adminUrl'    => admin_url(),
					'googleFonts' => iterator_to_array( $google_fonts ),
				),
			)
		);
		wp_localize_script( 'blockart-admin', $localized_scripts['name'], $localized_scripts['data'] );
	}

	/**
	 * Localize block scripts.
	 *
	 * @return void
	 */
	public function localize_block_scripts() {
		global $pagenow;

		$font_awesome_icons = Items::fromFile( Icon::FONT_AWESOME_ICONS_PATH );
		$blockart_icons     = Items::fromFile( Icon::BLOCKART_ICONS_PATH );
		$google_fonts       = Items::fromFile( BLOCKART_PLUGIN_DIR . '/assets/json/google-fonts.json' );

		$localized_scripts = apply_filters(
			'blockart_localize_block_scripts',
			array(
				'name' => '_BLOCKART_',
				'data' => array(
					'isNotPostEditor' => 'widgets.php' === $pagenow || 'customize.php' === $pagenow,
					'isWP59OrAbove'   => is_wp_version_compatible( '5.9' ),
					'nonce'           => wp_create_nonce( '_blockart_nonce' ),
					'ajaxUrl'         => admin_url( 'admin-ajax.php' ),
					'mediaItems'      => $this->get_media_items(),
					'configs'         => blockart_array_except(
						$this->setting->get(),
						array(
							'asset-generation',
							'performance.local-google-fonts',
							'performance.preload-local-fonts',
							'editor.responsive-breakpoints',
							'global-styles',
						)
					) + array(
						'global-styles' => json_decode( $this->setting->get( 'global-styles' ) ),
					),
					'googleFonts'     => iterator_to_array( $google_fonts ),
					'icons'           => array(
						'font-awesome' => array_values( iterator_to_array( $font_awesome_icons ) ),
						'blockart'     => array_values( iterator_to_array( $blockart_icons ) ),
						'all'          => array_merge( iterator_to_array( $font_awesome_icons ), iterator_to_array( $blockart_icons ) ),
					),
				),
			)
		);

		wp_localize_script( 'blockart-blocks', $localized_scripts['name'], $localized_scripts['data'] );
	}
}
