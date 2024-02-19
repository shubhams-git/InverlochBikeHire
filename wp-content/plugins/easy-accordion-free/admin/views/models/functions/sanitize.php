<?php
/**
 * Framework sanitize file.
 *
 * @link       https://shapedplugin.com/
 * @since      2.0.0
 *
 * @package    easy-accordion-free
 * @subpackage easy-accordion-free/framework
 */

if ( ! defined( 'ABSPATH' ) ) {
	die;
} // Cannot access directly.

if ( ! function_exists( 'eapro_sanitize_replace_a_to_b' ) ) {
	/**
	 *
	 * Sanitize
	 * Replace letter a to letter b
	 *
	 * @param  string $value string.
	 * @since 1.0.0
	 * @version 1.0.0
	 */
	function eapro_sanitize_replace_a_to_b( $value ) {

		return str_replace( 'a', 'b', $value );
	}
}

if ( ! function_exists( 'eapro_sanitize_title' ) ) {
	/**
	 *
	 * Sanitize title
	 *
	 * @param  string $value string.
	 * @since 1.0.0
	 * @version 1.0.0
	 */
	function eapro_sanitize_title( $value ) {

		return sanitize_title( $value );
	}
}

if ( ! function_exists( 'eapro_allowed_title_tags' ) ) {
	/**
	 *
	 * Sanitize allowed html tags.
	 */
	function eapro_allowed_title_tags() {

		$allowed_tags = array(
			'b'      => array(),
			'strong' => array(),
			'i'      => array(),
			'u'      => array(),
		);

		return apply_filters( 'sp_easy_accordion_title_allowed_tags', $allowed_tags );
	}
}

if ( ! function_exists( 'eapro_allowed_description_tags' ) ) {
	/**
	 *
	 * Sanitize allowed html tags.
	 */
	function eapro_allowed_description_tags() {

		$allowed_tags           = wp_kses_allowed_html( 'post' );
		$allowed_tags['iframe'] = array(
			'src'             => array(),
			'height'          => array(),
			'width'           => array(),
			'frameborder'     => array(),
			'allowfullscreen' => array(),
			'title'           => array(),
			'alt'             => array(),
			'class'           => array(),
		);
		$allowed_tags['style']  = array(
			'type'  => array(),
			'media' => array(),
		);

		// Add attributes for the 'audio' tag.
		$allowed_tags['audio'] = array(
			'controls' => true,
			'src'      => array(),
			'autoplay' => array(),
			'loop'     => array(),
			'preload'  => array(),
			'muted'    => array(),
		);

		// Add attributes for the 'source' tag.
		$allowed_tags['source'] = array(
			'src'    => array(),
			'type'   => array(),
			'media'  => array(),
			'sizes'  => array(),
			'srcset' => array(),
		);
		// Add attributes for the 'video' tag.
		$allowed_tags['video'] = array(
			'src'      => array(),
			'autoplay' => array(),
			'controls' => array(),
			'width'    => array(),
			'height'   => array(),
			'loop'     => array(),
			'preload'  => array(),
			'poster'   => array(),
			'muted'    => array(),
		);

		return apply_filters( 'sp_easy_accordion_desc_allowed_tags', $allowed_tags );
	}
}

if ( ! function_exists( 'eapro_sanitize_accordion_title_content' ) ) {
	/**
	 *
	 * Sanitize the accordion title and content in group field only.
	 *
	 * @param  array $value array.
	 */
	function eapro_sanitize_accordion_title_content( $value ) {
		if ( empty( $value ) ) {
			return $value;
		}
		$eapro_allowed_title_tags       = eapro_allowed_title_tags();
		$eapro_allowed_description_tags = eapro_allowed_description_tags();

		if ( is_array( $value ) ) {
			$count = count( $value );
			for ( $i = 0; $i < $count; $i++ ) {
				if ( ! empty( $value[ $i ]['accordion_content_title'] ) ) {
					// Sanitize Accordion Item Title.
					$value[ $i ]['accordion_content_title'] = wp_kses( $value[ $i ]['accordion_content_title'], $eapro_allowed_title_tags );
				}
				if ( ! empty( $value[ $i ]['accordion_content_description'] ) ) {
					// Sanitize Accordion Item Content.
					$value[ $i ]['accordion_content_description'] = wp_kses( $value[ $i ]['accordion_content_description'], $eapro_allowed_description_tags );
				}
			}
		}
		return $value;
	}
}
