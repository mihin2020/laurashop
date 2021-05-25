<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'larashop' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', '' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

if ( !defined('WP_CLI') ) {
    define( 'WP_SITEURL', $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] );
    define( 'WP_HOME',    $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] );
}



/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'YYjE7xCrcYj1RyfMPZxxhlFHokpuijKAnJRWW40Zc7TsrsIfLpilGyQCy6dZsdVt' );
define( 'SECURE_AUTH_KEY',  'WCc2RtOqlGWdd9ClrgFM8mvk7hJvqPymQXyZ5wPVF1RZCMc2UNwBVsFVbfW9SAkQ' );
define( 'LOGGED_IN_KEY',    'QBpfP8jZo5Md3ixEaJFiFEbgtWumFW2HueR1p88D9aMoXzUyburUkC9xjxpGVuYX' );
define( 'NONCE_KEY',        'FUWkZt99k0P2j1YIYoA5UaFtvkG5ZEaziwKZXQ16ADFEsPSQAt5hV5crXXDFBXQP' );
define( 'AUTH_SALT',        'NHx3KDxaK9qcvGwoQItatzso4XUYobjYOctD41ED5ie3IYJcp7wUIMuxOgrKsWcl' );
define( 'SECURE_AUTH_SALT', 'tYdhqa7IQWEaeiKgfn2LmEG7WpaPsRPDZ9n5W0WeFQQ1Olpz9dpi7CblWcemD5Sg' );
define( 'LOGGED_IN_SALT',   '72h8zGZ88qxSLu3VkcKMh2TI0dS18DEE9ymXmlFIbeKxKJhQYrfxt8u7IrX6tBoP' );
define( 'NONCE_SALT',       'BLYG7CwGbiQcdNo6CohjBUJUGttRXBOKC4wMdNCHGczM14EE4guSdb2swWcEzgoT' );

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
