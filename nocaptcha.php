<?php
/*
 * Plugin Name: Nocaptcha Mail.Ru
 * Plugin URL: https://nocaptcha.mail.ru
 * Description: Adds integration with Nocaptcha Mail.Ru - free intelligent CAPTCHA service.
 * Version: 1.0.1
 * Author: Oleg Kovalev
 * Author URI: mailto:man0xff@gmail.com
 * License: MIT
 * License URI: http://opensource.org/licenses/MIT
 * Text Domain: nocaptcha
 * Domain Path: /languages/
 */

require_once( ABSPATH . 'wp-admin/includes/translation-install.php' );

define( 'NOCAPTCHA_API_SERVER', 'https://api-nocaptcha.mail.ru' );

$nocaptcha_widgets_num = 0;

function nocaptcha_load_textdomain() {
    load_plugin_textdomain( 'nocaptcha', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}

function nocaptcha_options_register() {
    add_settings_section(
        'auth',
        __( 'Authorization', 'nocaptcha' ),
        'nocaptcha_options_auth_section',
        'nocaptcha'
    );
    add_settings_section(
        'common',
        __( 'General Settings', 'nocaptcha' ),
        null,
        'nocaptcha'
    );
    add_settings_field(
        'public_key',
        __( 'Public Key', 'nocaptcha' ),
        'nocaptcha_options_public_key',
        'nocaptcha',
        'auth'
    );
    add_settings_field(
        'private_key',
        __( 'Private Key', 'nocaptcha' ),
        'nocaptcha_options_private_key',
        'nocaptcha',
        'auth'
    );
    add_settings_field(
        'forms',
        __( 'Forms', 'nocaptcha' ),
        'nocaptcha_options_forms',
        'nocaptcha',
        'common'
    );
    add_settings_field(
        'ru_lang',
        __( 'Use Russian-language widget for languages', 'nocaptcha' ),
        'nocaptcha_options_ru_lang',
        'nocaptcha',
        'common'
    );
    add_settings_field(
        'margins',
        __( 'Widget margins', 'nocaptcha' ),
        'nocaptcha_options_margin',
        'nocaptcha',
        'common'
    );
    register_setting( 'nocaptcha', 'nocaptcha_public_key' );
    register_setting( 'nocaptcha', 'nocaptcha_private_key' );
    register_setting( 'nocaptcha', 'nocaptcha_ru_lang' );
    register_setting( 'nocaptcha', 'nocaptcha_form_login' );
    register_setting( 'nocaptcha', 'nocaptcha_form_reg' );
    register_setting( 'nocaptcha', 'nocaptcha_form_lost' );
    register_setting( 'nocaptcha', 'nocaptcha_form_comment' );
    register_setting( 'nocaptcha', 'nocaptcha_hide_auth' );
    register_setting( 'nocaptcha', 'nocaptcha_use_margins' );
    register_setting( 'nocaptcha', 'nocaptcha_margins' );
}

function nocaptcha_options_auth_section() {
    _e( '<p>Before using Nocaptcha service you should <a href="https://nocaptcha.mail.ru/site/add">register</a> your domain and get the public and private keys.</p>', 'nocaptcha' );
}

function nocaptcha_options_public_key() {
    $val = nocaptcha_get_option( 'nocaptcha_public_key' );
    echo '<input type="text" size="40" maxlength="32" value="' . $val . '" name="nocaptcha_public_key">';
}

function nocaptcha_options_private_key() {
    $val = nocaptcha_get_option( 'nocaptcha_private_key' );
    echo '<input type="text" size="40" maxlength="32" value="' . $val . '" name="nocaptcha_private_key" autocomplete="off">';
}

function nocaptcha_options_margin() {
    ?>
    <p><label>
        <input type="radio" name="nocaptcha_use_margins" value="1" <?php checked( nocaptcha_get_option( 'nocaptcha_use_margins' ), 1 ); ?>>
        <?php _e( 'Use top and bottom margins', 'nocaptcha' ); ?>
    </label>
        <input style="margin-right: 30px;" type="text" name="nocaptcha_margins" size="5" value="<?php echo nocaptcha_get_option( 'nocaptcha_margins' ); ?>">
    <label>
        <input type="radio" name="nocaptcha_use_margins" value="0" <?php checked( nocaptcha_get_option( 'nocaptcha_use_margins' ), 0 ); ?>>
        <?php _e( 'Do not use margins', 'nocaptcha' ); ?>
    </label>
    </p>
    <p class="description">
        <?php _e( 'If necessary, you can adjust the margins in the theme stylesheet.', 'nocaptcha' ); ?>
    </p>
    <?php
}

function nocaptcha_options_forms() {
    ?>
    <p><label style="margin-right: 30px;">
        <input type="checkbox" name="nocaptcha_form_comment" value="1" <?php checked( nocaptcha_get_option( 'nocaptcha_form_comment' ), 1 ); ?>>
        <?php _e( 'Comment form', 'nocaptcha' ); ?>
    </label><label>
        <input type="checkbox" name="nocaptcha_hide_auth" value="1" <?php checked( nocaptcha_get_option( 'nocaptcha_hide_auth' ), 1 ); ?>>
        <?php _e( 'Hide CAPTCHA for logged in users', 'nocaptcha' ); ?>
    </label></p>
    <p><label>
        <input type="checkbox" name="nocaptcha_form_login" value="1" <?php checked( nocaptcha_get_option( 'nocaptcha_form_login' ), 1 ); ?>>
        <?php _e( 'Login form', 'nocaptcha' ); ?>
    </label></p>
    <p><label>
        <input type="checkbox" name="nocaptcha_form_reg" value="1" <?php checked( nocaptcha_get_option( 'nocaptcha_form_reg' ), 1 ); ?>>
        <?php _e( 'Registration form', 'nocaptcha' ); ?>
    </label></p>
    <p><label>
        <input type="checkbox" name="nocaptcha_form_lost" value="1" <?php checked( nocaptcha_get_option( 'nocaptcha_form_lost' ), 1 ); ?>>
        <?php _e( 'Lost password form', 'nocaptcha' ); ?>
    </label></p>
    <p class="description">
        <?php _e( 'How to add Nocaptcha to arbitrary form described below.', 'nocaptcha' ); ?>
    </p>
    <?php
}

function nocaptcha_options_ru_lang() {
    $selected = preg_split( '/,/', nocaptcha_get_option( 'nocaptcha_ru_lang' ), -1, PREG_SPLIT_NO_EMPTY );
    $values = array();
    $translations = wp_get_available_translations();
    $values['en_US'] = 'English (United States)';
    foreach ( $translations as $locale => $translation ) {
        $values[$locale] = $translation['native_name'];
    }

    nocaptcha_multiselect( array(
        'values'          => $values,
        'selected'        => $selected,
        'name'            => 'nocaptcha_ru_lang',
        'label_selected'  => __( 'Selected languages', 'nocaptcha' ),
        'label_available' => __( 'Available languages', 'nocaptcha' ),
    ));
    ?>
    <p class="description">
        <?php _e( 'English-language widget will be used for the rest of languages.', 'nocaptcha' ); ?>
    </p>
    <?php
}

function nocaptcha_options_add_menu() {
    add_options_page(
        __( 'Nocaptcha Mail.Ru Settings', 'nocaptcha' ),
        __( 'Nocaptcha Mail.Ru', 'nocaptcha' ),
        'manage_options',
        'nocaptcha',
        'nocaptcha_options'
    );
}

function nocaptcha_options() {
    ?>
    <div class="wrap nocaptcha-wrap">
        <h2><?php _e( 'Nocaptcha Mail.Ru Settings', 'nocaptcha' ); ?></h2>
        <form method="post" action="options.php">
            <?php settings_fields('nocaptcha'); ?>
            <?php do_settings_sections('nocaptcha'); ?>
            <?php submit_button(); ?>
        </form>
        <h2><?php _e( 'How to add Nocaptcha to arbitrary form', 'nocaptcha' ); ?></h2>
        <?php _e( '
        <p>Next three functions are used for that:</p>
        <ul>
            <li>
                <span class="code">nocaptcha_get_widget( $tabindex = null )</span>
                &ndash; returns string with widget\'s HTML code. Optional
                parameter $tabindex sets tabindex of the CAPTCHA input field.
            </li>
            <li>
                <span class="code">nocaptcha_add_widget( $tabindex = null )</span>
                &ndash; prints widget\'s HTML code. Optional
                parameter $tabindex sets tabindex of the CAPTCHA input field.
            </li>
            <li>
                <span class="code">nocaptcha_check_request()</span>
                &ndash; checks CAPTCHA code entered by the user. 
                Returns <span class="code">true</span> if check is passed,
                <span class="code">false</span> if invalid code entered,
                <span class="code">null</span> if internal error occured.
                Information about internal error is printed to the PHP error log.
            </li>
        </ul>
        <p>Nocaptcha script will be added to the page\'s footer if there is at least one widget on the page.</p>
        ', 'nocaptcha' ); ?>
    <?php
}

function nocaptcha_error_log( $message ) {
    error_log( 'ERROR: nocaptcha: ' . $message );
}

function nocaptcha_check_request() {
    $params = array(
        'private_key'   => nocaptcha_get_option( 'nocaptcha_private_key' ),
        'captcha_id'    => $_REQUEST['captcha_id'],
        'captcha_value' => $_REQUEST['captcha_value'],
    );
    $url = NOCAPTCHA_API_SERVER . '/check?' . http_build_query( $params );
    $resp = @file_get_contents( $url );
    if ( $resp === false ) {
        nocaptcha_error_log( 'check request failed' );
        return null;
    }
    $data = json_decode( $resp );
    if ( ! $data ) {
        nocaptcha_error_log( 'invalid check response' );
        return null;
    }
    if ( $data->status == 'not found' ) {
        return false;
    }
    if ( $data->status !== 'ok' ) {
        nocaptcha_error_log( 'service returned an error: ' . $data->desc );
        return null;
    }
    if ( ! $data->is_correct ) {
        return false;
    }
    return true;
}

function nocaptcha_check_request_error( $errors = null ) {
    $result = nocaptcha_check_request();
    if ( $result === null ) {
        return nocaptcha_add_error( $errors, __( '<strong>ERROR</strong>: Internal service error.', 'nocaptcha' ) );
    } elseif ( $result === false ) {
        return nocaptcha_add_error( $errors, __( '<strong>ERROR</strong>: Incorrect CAPTCHA code.', 'nocaptcha' ) );
    }
    return null;
}

function nocaptcha_check_login_request( $user, $password ) {
    $errors = nocaptcha_check_request_error();
    if ( $errors !== null ) {
        return $errors;
    }
    return $user;
}

function nocaptcha_check_registration_request( $errors, $sanitized_user_login, $user_email ) {
    nocaptcha_check_request_error( $errors );
    return $errors;
}

function nocaptcha_check_password_reset_request( $result, $user_id ) {
    $errors = nocaptcha_check_request_error();
    if ( $errors !== null ) {
        return $errors;
    }
    return $result;
}

function nocaptcha_check_comment_request() {
    $errors = nocaptcha_check_request_error();
    if ( $errors !== null ) {
        wp_die( $errors );
    }
}

function nocaptcha_add_error( $errors, $message ) {
    if ( $errors === null ) {
        return new WP_Error( 'nocaptcha_error', $message );
    }
    $errors->add( 'nocaptcha_error', $message );
    return $errors;
}

function nocaptcha_get_widget( $tabindex = null ) {
    global $nocaptcha_widgets_num;

    if ( $nocaptcha_widgets_num == 0 ) {
        add_action( 'wp_footer', 'nocaptcha_add_settings_and_script', 100 );
        add_action( 'login_footer', 'nocaptcha_add_settings_and_script', 100 );
    }

    $tag = '<div';
    if ( ! is_null( $tabindex ) ) {
        $tag .= ' data-tabindex="' . $tabindex . '"';
    }
    $tag .= ' id="nocaptcha' . $nocaptcha_widgets_num . '"></div>';
    $nocaptcha_widgets_num++;
    return $tag;
}

function nocaptcha_add_widget() {
    echo nocaptcha_get_widget();
}

function nocaptcha_add_widget_and_script() {
    echo nocaptcha_get_widget();
    echo nocaptcha_get_settings_and_script();
}

function nocaptcha_add_widget_comment( $default ) {
    if ( nocaptcha_get_option( 'nocaptcha_hide_auth' ) && is_user_logged_in() ) {
        return $default;
    }
    return $default . nocaptcha_get_widget();
}

function nocaptcha_add_widget_comment_button( $default ) {
    if ( nocaptcha_get_option( 'nocaptcha_hide_auth' ) && is_user_logged_in() ) {
        return $default;
    }
    return nocaptcha_get_widget() . $default;
}

function nocaptcha_get_language() {
    $locale = get_locale();
    $locales = preg_split( '/,/', nocaptcha_get_option( 'nocaptcha_ru_lang' ), -1, PREG_SPLIT_NO_EMPTY );
    if ( in_array( $locale, $locales ) ) {
        return 'ru';
    }
    return 'en';
}

function nocaptcha_add_settings_and_script() {
    echo nocaptcha_get_settings_and_script();
}

function nocaptcha_get_settings_and_script() {
    global $nocaptcha_widgets_num;

    if ( $nocaptcha_widgets_num == 0 ) {
        return '';
    }

    $language = nocaptcha_get_language();
    $containers = array();
    for ( $i = 0; $i < $nocaptcha_widgets_num; $i++ ) {
        $containers[] = "'nocaptcha$i'";
    }
    $containers = join( ',', $containers );
    $url = NOCAPTCHA_API_SERVER . '/captcha?public_key=' . nocaptcha_get_option( 'nocaptcha_public_key' );

    $data  = '<script type="text/javascript">var nocaptchaSettings={containers:[';
    $data .= $containers . '],lang:"' . $language . '"};</script>';
    $data .= '<script type="text/javascript" src="' . $url . '"></script>';
    return $data;
}

function nocaptcha_get_option( $name ) {
    $defaults = array(
        'nocaptcha_public_key'   => '',
        'nocaptcha_private_key'  => '',
        'nocaptcha_ru_lang'      => 'ru_RU,uk',
        'nocaptcha_form_login'   => 0,
        'nocaptcha_form_reg'     => 0,
        'nocaptcha_form_lost'    => 0,
        'nocaptcha_form_comment' => 0,
        'nocaptcha_hide_auth'    => 0,
        'nocaptcha_use_margins'  => 1,
        'nocaptcha_margins'      => '20px',
    );
    return get_option( $name, $defaults[$name] );
}

function nocaptcha_add_widget_margins() {
    $margin = nocaptcha_get_option( 'nocaptcha_margins' );
    ?>
    <style type="text/css">
        .nocaptcha {
            margin-top: <?php echo $margin; ?>;
            margin-bottom: <?php echo $margin; ?>;
        }
    </style>
    <?php
}

function nocaptcha_add_forms_actions() {
    global $wp_version;

    if ( nocaptcha_get_option( 'nocaptcha_form_login' ) ) {
        add_action( 'login_form', 'nocaptcha_add_widget', 100 );
        add_filter( 'wp_authenticate_user', 'nocaptcha_check_login_request', 1, 2 );
    }
    if ( nocaptcha_get_option( 'nocaptcha_form_reg' ) ) {
        add_action( 'register_form', 'nocaptcha_add_widget', 100 );
        add_filter( 'registration_errors', 'nocaptcha_check_registration_request', 1, 3 );
    }
    if ( nocaptcha_get_option( 'nocaptcha_form_lost' ) ) {
        add_action( 'lostpassword_form',
                    'nocaptcha_add_widget', 100 );
        add_filter( 'allow_password_reset',
                    'nocaptcha_check_password_reset_request', 1, 2 );
    }
    if ( nocaptcha_get_option( 'nocaptcha_form_comment' ) ) {
        if ( ! nocaptcha_get_option( 'nocaptcha_hide_auth' ) || ! is_user_logged_in() ) {
            if ( version_compare( $wp_version, '4.2.0' ) >= 0 ) {
                add_action( 'comment_form_submit_button',
                            'nocaptcha_add_widget_comment_button', 1 );
            } else {
                add_action( 'comment_form_field_comment',
                            'nocaptcha_add_widget_comment', 100 );
            }
            add_filter( 'pre_comment_on_post',
                        'nocaptcha_check_comment_request', 1 );
        }
    }
}

add_action('plugins_loaded', 'nocaptcha_load_textdomain');

if ( is_admin() ) {
    require_once( 'multiselect.php' );
    add_action( 'admin_menu' , 'nocaptcha_options_add_menu' );
    add_action( 'admin_init' , 'nocaptcha_options_register' );
} else {
    add_action( 'init', 'nocaptcha_add_forms_actions' );
    if ( nocaptcha_get_option( 'nocaptcha_use_margins' ) ) {
        add_action( 'wp_head', 'nocaptcha_add_widget_margins' );
        add_action( 'login_head', 'nocaptcha_add_widget_margins' );
    }
}
?>
