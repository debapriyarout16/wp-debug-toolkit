<?php
/*
Plugin Name: WP Debug Toolkit
Plugin URI: https://github.com/debapriyarout16/wp-debug-toolkit
Description: Debug site info, active plugins, WP_DEBUG status, and send test emails via SMTP or default mail.
Version: 0.4
Author: Debapriya Rout
Author URI: https://github.com/debapriyarout16
License: GPL2
*/

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

// -------------------
// Admin Menu
// -------------------
add_action('admin_menu', function() {
    add_menu_page(
        'WP Debug Toolkit',
        'Debug Toolkit',
        'manage_options',
        'wp-debug-toolkit',
        'wpdt_dashboard_page',
        'dashicons-admin-tools',
        80
    );
    add_submenu_page(
        'wp-debug-toolkit',
        'SMTP Settings',
        'SMTP Settings',
        'manage_options',
        'wpdt-smtp-settings',
        'wpdt_smtp_settings_page'
    );
});

// -------------------
// Register SMTP Options
// -------------------
add_action('admin_init', function() {
    register_setting('wpdt_smtp_options', 'wpdt_smtp_host');
    register_setting('wpdt_smtp_options', 'wpdt_smtp_port');
    register_setting('wpdt_smtp_options', 'wpdt_smtp_username');
    register_setting('wpdt_smtp_options', 'wpdt_smtp_password');
    register_setting('wpdt_smtp_options', 'wpdt_smtp_encryption');
});

// -------------------
// PHPMailer Hook for SMTP
// -------------------
add_action('phpmailer_init', function($phpmailer) {
    $host = get_option('wpdt_smtp_host');
    $port = get_option('wpdt_smtp_port');
    $username = get_option('wpdt_smtp_username');
    $password = get_option('wpdt_smtp_password');
    $encryption = get_option('wpdt_smtp_encryption');

    if ($host && $port && $username && $password) {
        $phpmailer->isSMTP();
        $phpmailer->Host = $host;
        $phpmailer->Port = $port;
        $phpmailer->SMTPAuth = true;
        $phpmailer->Username = $username;
        $phpmailer->Password = $password;
        $phpmailer->SMTPSecure = ($encryption !== 'none') ? $encryption : '';
    }
});

// -------------------
// Dashboard Page
// -------------------
function wpdt_dashboard_page() {
    ?>
    <div class="wrap">
        <h1>WP Debug Toolkit</h1>
        <p>Welcome! This is a basic debugging toolkit for WordPress.</p>

        <h2>Site Information</h2>
        <ul>
            <li><strong>WordPress Version:</strong> <?php echo get_bloginfo('version'); ?></li>
            <li><strong>PHP Version:</strong> <?php echo phpversion(); ?></li>
            <li><strong>Active Theme:</strong> <?php $theme=wp_get_theme(); echo $theme->get('Name'); ?></li>
            <li><strong>Number of Active Plugins:</strong> <?php echo count(get_option('active_plugins')); ?></li>
            <li><strong>WP_DEBUG:</strong> <?php echo (defined('WP_DEBUG') && WP_DEBUG) ? 'Enabled' : 'Disabled'; ?></li>
        </ul>

        <h2>Active Plugins</h2>
        <ul>
        <?php
        $active_plugins = get_option('active_plugins');
        foreach ($active_plugins as $plugin) {
            $plugin_data = get_plugin_data(WP_PLUGIN_DIR.'/'.$plugin);
            echo '<li>'.esc_html($plugin_data['Name']).' - v'.esc_html($plugin_data['Version']).'</li>';
        }
        ?>
        </ul>

        <h2>Email Test Tool</h2>

        <?php
        // Local environment warning
        if (strpos($_SERVER['HTTP_HOST'], 'localhost')!==false || strpos($_SERVER['HTTP_HOST'], '.local')!==false) {
            echo '<div class="notice notice-warning"><p>You are running locally. Emails may be captured by MailHog and not reach a real inbox.</p></div>';
        }
        ?>

        <form method="post">
            <input type="email" name="wpdt_test_email_to" placeholder="Recipient email" required style="width: 300px;"/>
            <?php submit_button('Send Test Email', 'primary', 'wpdt_send_test_email'); ?>
        </form>

        <?php
        if (isset($_POST['wpdt_send_test_email'])) {
            $to = sanitize_email($_POST['wpdt_test_email_to']);
            $subject = 'WP Debug Toolkit - Test Email';
            $message = 'This is a test email sent from your WordPress site.';

            if (wp_mail($to, $subject, $message)) {
                echo '<div class="notice notice-success"><p>Email successfully sent to <strong>'.esc_html($to).'</strong></p></div>';
            } else {
                echo '<div class="notice notice-error"><p>Failed to send email. Check your SMTP configuration or hosting mail settings.</p></div>';
            }
        }
        ?>
    </div>
    <?php
}

// -------------------
// SMTP Settings Page
// -------------------
function wpdt_smtp_settings_page() {
    ?>
    <div class="wrap">
        <h1>SMTP Settings</h1>
        <form method="post" action="options.php">
            <?php settings_fields('wpdt_smtp_options'); ?>
            <table class="form-table">
                <tr><th>SMTP Host</th><td><input type="text" name="wpdt_smtp_host" value="<?php echo esc_attr(get_option('wpdt_smtp_host')); ?>" /></td></tr>
                <tr><th>SMTP Port</th><td><input type="number" name="wpdt_smtp_port" value="<?php echo esc_attr(get_option('wpdt_smtp_port')); ?>" /></td></tr>
                <tr><th>Username</th><td><input type="text" name="wpdt_smtp_username" value="<?php echo esc_attr(get_option('wpdt_smtp_username')); ?>" /></td></tr>
                <tr><th>Password</th><td><input type="password" name="wpdt_smtp_password" value="<?php echo esc_attr(get_option('wpdt_smtp_password')); ?>" /></td></tr>
                <tr><th>Encryption</th>
                    <td>
                        <select name="wpdt_smtp_encryption">
                            <option value="none" <?php selected(get_option('wpdt_smtp_encryption'),'none'); ?>>None</option>
                            <option value="ssl" <?php selected(get_option('wpdt_smtp_encryption'),'ssl'); ?>>SSL</option>
                            <option value="tls" <?php selected(get_option('wpdt_smtp_encryption'),'tls'); ?>>TLS</option>
                        </select>
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}
