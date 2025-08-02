<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class EzMegaMenu_Admin {

    public function __construct() {
        add_action( 'admin_menu', [ $this, 'add_admin_page' ] );
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_assets' ] );
    }

    public function add_admin_page() {
        add_theme_page(
            __( 'ezMegaMenu', 'ezmegamenu' ),
            __( 'ezMegaMenu', 'ezmegamenu' ),
            'manage_options',
            'ezmegamenu',
            [ $this, 'render_admin_page' ]
        );
    }

    public function enqueue_assets( $hook ) {
        if ( 'appearance_page_ezmegamenu' !== $hook ) {
            return;
        }
        wp_enqueue_media();
        wp_enqueue_style( 'ezmegamenu-admin', EZMM_PLUGIN_URL . 'assets/css/ezmegamenu-admin.css', [], '0.1.0' );
        wp_enqueue_script( 'jquery-ui-sortable' );
        wp_enqueue_script( 'ezmegamenu-admin', EZMM_PLUGIN_URL . 'assets/js/ezmegamenu-admin.js', [ 'jquery', 'jquery-ui-sortable' ], '0.1.0', true );
        wp_localize_script( 'ezmegamenu-admin', 'ezmm_admin', [
            'media_title'  => __( 'Select or Upload Icon', 'ezmegamenu' ),
            'media_button' => __( 'Use this icon', 'ezmegamenu' ),
            'col_title'    => __( 'Column title', 'ezmegamenu' ),
            'col_icon'     => __( 'Icon URL', 'ezmegamenu' ),
            'upload'       => __( 'Upload', 'ezmegamenu' ),
        ] );
    }

    public function render_admin_page() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        $menus      = get_option( 'ezmegamenu_menus', [] );
        $editing_id = isset( $_GET['ezmm_edit'] ) ? sanitize_text_field( $_GET['ezmm_edit'] ) : '';
        $editing    = $menus[ $editing_id ] ?? [
            'name'       => '',
            'open_event' => 'hover',
            'sticky'     => 0,
            'icon'       => '',
            'structure'  => [],
        ];

        if ( isset( $_POST['ezmm_action'] ) && 'save_menu' === $_POST['ezmm_action'] ) {
            check_admin_referer( 'ezmm_save_menu' );
            $menu_id   = sanitize_text_field( $_POST['ezmm_menu_id'] ?? '' );
            $structure = json_decode( wp_unslash( $_POST['ezmm_structure'] ?? '[]' ), true );
            if ( ! is_array( $structure ) ) {
                $structure = [];
            }

            $menu = [
                'name'       => sanitize_text_field( $_POST['ezmm_name'] ?? '' ),
                'open_event' => sanitize_text_field( $_POST['ezmm_open_event'] ?? 'hover' ),
                'sticky'     => isset( $_POST['ezmm_sticky'] ) ? 1 : 0,
                'icon'       => esc_url_raw( $_POST['ezmm_icon'] ?? '' ),
                'structure'  => $structure,
            ];
            $menu['columns'] = count( $structure );

            if ( empty( $menu['name'] ) ) {
                echo '<div class="notice notice-error"><p>' . esc_html__( 'Menu name is required.', 'ezmegamenu' ) . '</p></div>';
            } else {
                if ( empty( $menu_id ) ) {
                    $menu_id = uniqid( 'ezmm_' );
                }
                $menus[ $menu_id ] = $menu;
                update_option( 'ezmegamenu_menus', $menus );
                echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Menu saved.', 'ezmegamenu' ) . '</p></div>';
                $editing_id = $menu_id;
                $editing    = $menu;
            }
        }

        if ( isset( $_GET['ezmm_delete'] ) ) {
            $delete_id = sanitize_text_field( $_GET['ezmm_delete'] );
            if ( isset( $menus[ $delete_id ] ) ) {
                unset( $menus[ $delete_id ] );
                update_option( 'ezmegamenu_menus', $menus );
                echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Menu deleted.', 'ezmegamenu' ) . '</p></div>';
            }
        }
        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'ezMegaMenu', 'ezmegamenu' ); ?></h1>

            <h2><?php echo $editing_id ? esc_html__( 'Edit Menu', 'ezmegamenu' ) : esc_html__( 'Add New Menu', 'ezmegamenu' ); ?></h2>
            <form method="post" id="ezmm-menu-form">
                <?php wp_nonce_field( 'ezmm_save_menu' ); ?>
                <input type="hidden" name="ezmm_action" value="save_menu" />
                <input type="hidden" name="ezmm_menu_id" value="<?php echo esc_attr( $editing_id ); ?>" />
                <input type="hidden" name="ezmm_structure" id="ezmm_structure" value="<?php echo esc_attr( wp_json_encode( $editing['structure'] ) ); ?>" />
                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="ezmm_name"><?php esc_html_e( 'Menu Name', 'ezmegamenu' ); ?></label></th>
                        <td><input name="ezmm_name" type="text" id="ezmm_name" class="regular-text" value="<?php echo esc_attr( $editing['name'] ); ?>" required></td>
                    </tr>
                    <tr>
                        <th scope="row"><?php esc_html_e( 'Open Event', 'ezmegamenu' ); ?></th>
                        <td>
                            <label><input type="radio" name="ezmm_open_event" value="hover" <?php checked( $editing['open_event'], 'hover' ); ?>> <?php esc_html_e( 'Hover', 'ezmegamenu' ); ?></label>
                            <label><input type="radio" name="ezmm_open_event" value="click" <?php checked( $editing['open_event'], 'click' ); ?>> <?php esc_html_e( 'Click', 'ezmegamenu' ); ?></label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="ezmm_sticky"><?php esc_html_e( 'Sticky', 'ezmegamenu' ); ?></label></th>
                        <td><input type="checkbox" name="ezmm_sticky" id="ezmm_sticky" value="1" <?php checked( $editing['sticky'], 1 ); ?> /></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="ezmm_icon"><?php esc_html_e( 'Icon', 'ezmegamenu' ); ?></label></th>
                        <td>
                            <input type="text" name="ezmm_icon" id="ezmm_icon" class="regular-text" value="<?php echo esc_url( $editing['icon'] ); ?>" />
                            <button type="button" class="button ezmm-upload"><?php esc_html_e( 'Upload', 'ezmegamenu' ); ?></button>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php esc_html_e( 'Columns', 'ezmegamenu' ); ?></th>
                        <td>
                            <ul id="ezmm-columns-list">
                                <?php foreach ( $editing['structure'] as $col ) : ?>
                                    <li class="ezmm-column-item"><span class="handle">&#9776;</span>
                                        <input type="text" class="ezmm-col-title" placeholder="<?php esc_attr_e( 'Column title', 'ezmegamenu' ); ?>" value="<?php echo esc_attr( $col['title'] ); ?>" />
                                        <input type="text" class="ezmm-col-icon" placeholder="<?php esc_attr_e( 'Icon URL', 'ezmegamenu' ); ?>" value="<?php echo esc_url( $col['icon'] ); ?>" />
                                        <button class="button ezmm-col-upload"><?php esc_html_e( 'Upload', 'ezmegamenu' ); ?></button>
                                        <button class="button ezmm-remove-column">&times;</button>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                            <button type="button" class="button" id="ezmm-add-column"><?php esc_html_e( 'Add Column', 'ezmegamenu' ); ?></button>
                            <p class="description"><?php esc_html_e( 'Drag columns to reorder. Maximum 6 columns.', 'ezmegamenu' ); ?></p>
                        </td>
                    </tr>
                </table>
                <?php submit_button( __( 'Save Menu', 'ezmegamenu' ) ); ?>
            </form>

            <?php if ( ! empty( $menus ) ) : ?>
                <h2><?php esc_html_e( 'Existing Menus', 'ezmegamenu' ); ?></h2>
                <table class="widefat">
                    <thead>
                        <tr>
                            <th><?php esc_html_e( 'Name', 'ezmegamenu' ); ?></th>
                            <th><?php esc_html_e( 'Columns', 'ezmegamenu' ); ?></th>
                            <th><?php esc_html_e( 'Open Event', 'ezmegamenu' ); ?></th>
                            <th><?php esc_html_e( 'Sticky', 'ezmegamenu' ); ?></th>
                            <th><?php esc_html_e( 'Actions', 'ezmegamenu' ); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ( $menus as $menu_id => $menu ) : ?>
                            <tr>
                                <td><?php echo esc_html( $menu['name'] ); ?></td>
                                <td><?php echo esc_html( $menu['columns'] ); ?></td>
                                <td><?php echo esc_html( ucfirst( $menu['open_event'] ) ); ?></td>
                                <td><?php echo $menu['sticky'] ? esc_html__( 'Yes', 'ezmegamenu' ) : esc_html__( 'No', 'ezmegamenu' ); ?></td>
                                <td>
                                    <a href="<?php echo esc_url( admin_url( 'themes.php?page=ezmegamenu&ezmm_edit=' . $menu_id ) ); ?>"><?php esc_html_e( 'Edit', 'ezmegamenu' ); ?></a> |
                                    <a href="<?php echo esc_url( admin_url( 'themes.php?page=ezmegamenu&ezmm_delete=' . $menu_id ) ); ?>" onclick="return confirm('<?php esc_attr_e( 'Delete this menu?', 'ezmegamenu' ); ?>');"><?php esc_html_e( 'Delete', 'ezmegamenu' ); ?></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
        <?php
    }
}
