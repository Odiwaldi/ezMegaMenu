<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class EzMegaMenu_Frontend {

    public function __construct() {
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_assets' ] );
        add_shortcode( 'ezmegamenu', [ $this, 'render_shortcode' ] );
    }

    public function enqueue_assets() {
        wp_enqueue_style( 'ezmegamenu', EZMM_PLUGIN_URL . 'assets/css/ezmegamenu.css', [], '0.1.0' );
        wp_enqueue_script( 'ezmegamenu', EZMM_PLUGIN_URL . 'assets/js/ezmegamenu.js', [ 'jquery' ], '0.1.0', true );
    }

    public function render_shortcode( $atts ) {
        $atts = shortcode_atts( [
            'id' => '',
        ], $atts, 'ezmegamenu' );

        $menus = get_option( 'ezmegamenu_menus', [] );
        $id    = sanitize_text_field( $atts['id'] );

        if ( empty( $id ) || empty( $menus[ $id ] ) ) {
            return '';
        }

        $menu       = $menus[ $id ];
        $open_event = esc_attr( $menu['open_event'] );
        $sticky     = ! empty( $menu['sticky'] ) ? ' ezmm-sticky' : '';
        $icon       = ! empty( $menu['icon'] ) ? '<span class="ezmm-menu-icon"><img src="' . esc_url( $menu['icon'] ) . '" alt="" /></span>' : '';
        $structure  = isset( $menu['structure'] ) && is_array( $menu['structure'] ) ? $menu['structure'] : [];
        $columns    = count( $structure );

        $output  = '<nav class="ezmm-menu' . $sticky . '" data-open-event="' . $open_event . '">';
        $output .= $icon;
        $output .= '<ul class="ezmm-columns columns-' . $columns . '">';
        if ( $columns ) {
            foreach ( $structure as $col ) {
                $col_icon  = ! empty( $col['icon'] ) ? '<img src="' . esc_url( $col['icon'] ) . '" alt="" />' : '';
                $col_title = ! empty( $col['title'] ) ? esc_html( $col['title'] ) : '';
                $output   .= '<li class="ezmm-column">' . $col_icon . $col_title . '</li>';
            }
        }
        $output .= '</ul></nav>';

        return $output;
    }
}
