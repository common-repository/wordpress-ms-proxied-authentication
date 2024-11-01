<?php
/*
Plugin Name: WordPress MU Proxied Authentication
Plugin URI: http://voccs.com/software/wordpress/wpmu-proxied-authentication/
Description: Allows remote login of a proxied MU site.
Version: 1.0
Author: Ryan Lee
Author URI: http://voccs.com/
*/
/*
Copyright (c) 2010, voccs, LLC
All rights reserved.

Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:

    * Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
    * Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.
    * Neither the name of voccs, LLC nor the names of its contributors may be used to endorse or promote products derived from this software without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*/

function proxied_set_cookie($cookie, $expire, $expiration, $user_id, $type) {
    $domain = get_option('proxied_domain');
    $path_prefix = get_option('proxied_base_path','');
    if (!$domain) return;

    switch($type) {
        case "secure_auth":
            $secure = true;
    		$auth_cookie_name = SECURE_AUTH_COOKIE;
            break;
        case "auth":
        default:
            $secure = false;
    		$auth_cookie_name = AUTH_COOKIE;
            break;
    }

	if ( version_compare(phpversion(), '5.2.0', 'ge') ) {
        if ($type === "auth" || $type === "secure_auth") {
     		setcookie($auth_cookie_name, $cookie, $expire, $path_prefix . PLUGINS_COOKIE_PATH, $domain, $secure, true);
	    	setcookie($auth_cookie_name, $cookie, $expire, $path_prefix . ADMIN_COOKIE_PATH, $domain, $secure, true);
        } elseif ($type === "logged_in") {
    		setcookie(LOGGED_IN_COOKIE, $cookie, $expire, $path_prefix, $domain, false, true);
        }
	} else {
        $domain .= '; HttpOnly';
        if ($type === "auth" || $type === "secure_auth") {
    		setcookie($auth_cookie_name, $cookie, $expire, $path_prefix . PLUGINS_COOKIE_PATH, $domain, $secure);
	    	setcookie($auth_cookie_name, $cookie, $expire, $path_prefix . ADMIN_COOKIE_PATH, $domain, $secure);
        } elseif ($type === "logged_in") {
    		setcookie(LOGGED_IN_COOKIE, $cookie, $expire, $path_prefix, $domain);
        }
	}
}
add_action( 'set_auth_cookie', 'proxied_set_cookie', 10, 5 );
add_action( 'set_logged_in_cookie', 'proxied_set_cookie', 10, 5 );

function proxied_clear_cookie() {
    $domain = get_option('proxied_domain');
    $path_prefix = get_option('proxied_base_path','');
    if (!$domain) return;

	setcookie(AUTH_COOKIE, ' ', time() - 31536000, $path_prefix . ADMIN_COOKIE_PATH, $domain);
	setcookie(SECURE_AUTH_COOKIE, ' ', time() - 31536000, $path_prefix . ADMIN_COOKIE_PATH, $domain);
	setcookie(AUTH_COOKIE, ' ', time() - 31536000, $path_prefix . PLUGINS_COOKIE_PATH, $domain);
	setcookie(SECURE_AUTH_COOKIE, ' ', time() - 31536000, $path_prefix . PLUGINS_COOKIE_PATH, $domain);
	setcookie(LOGGED_IN_COOKIE, ' ', time() - 31536000, $path_prefix, $domain);

	// Old cookies
	setcookie(AUTH_COOKIE, ' ', time() - 31536000, $path_prefix, $domain);
	setcookie(SECURE_AUTH_COOKIE, ' ', time() - 31536000, $path_prefix, $domain);

	// Even older cookies
	setcookie(USER_COOKIE, ' ', time() - 31536000, $path_prefix, $domain);
	setcookie(PASS_COOKIE, ' ', time() - 31536000, $path_prefix, $domain);
}
add_action( 'clear_auth_cookie', 'proxied_clear_cookie', 10, 0);

function proxied_register_settings() {
    register_setting( 'proxied-settings-group', 'proxied_domain' );
    register_setting( 'proxied-settings-group', 'proxied_base_path' );
}

function proxied_plugin_options() {
    if (!current_user_can('manage_options'))  {
        wp_die( __('You do not have sufficient permissions to access this page.') );
    }
?>
<div class="wrap">
<h2>Proxied Plugin Options</h2>
<form method="post" action="options.php">
    <?php settings_fields( 'proxied-settings-group' ); ?>
    <table class="form-table">
        <tr valign="top">
        <th scope="row">Proxied Domain</th>
        <td><input type="text" name="proxied_domain" value="<?php echo get_option('proxied_domain'); ?>" /></td>
        </tr>
         
        <tr valign="top">
        <th scope="row">Proxied Base Path</th>
        <td><input type="text" name="proxied_base_path" value="<?php echo get_option('proxied_base_path'); ?>" /></td>
        </tr>
        
    </table>
    
    <p class="submit">
    <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
    </p>
</form>
</div>
<?php }

function proxied_plugin_menu() {
    add_options_page('Proxied Plugin Option', 'Proxied Plugin', 'manage_options', 'wpmu-proxied-options', 'proxied_plugin_options');
    add_action( 'admin_init', 'proxied_register_settings' );
}
add_action( 'admin_menu', 'proxied_plugin_menu' );

?>
