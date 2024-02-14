<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'wp_test' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'NCkA50vCCiDQyZxboARax5j55qfV5xTPSxEkzBJjM3ye2wHvGVXktoi1qve8aikR');
define('SECURE_AUTH_KEY',  'pXcTD6yc4YXGP68tbeVGkEtdMKcThJDRv2SKb9aemQvnaDAhi8Y0EbQZe0FXYJIj');
define('LOGGED_IN_KEY',    'XlmrtievbcA6jYcM1mYISybKkHVpQM7RS2kfcGOkdwBe1cW3erjs6leyUiNP4kkh');
define('NONCE_KEY',        'F1UDANKym9GPQC21NvaIqXdpiv3wG9eD8wUPvEBxxiy231qsuoNEibDUdXY06ptO');
define('AUTH_SALT',        'UYsXGLC0Dl9poDSvWioz1pG5GpTzFAPn37m0NPWbC70thvrQuQYkLvJkHIrHwE7t');
define('SECURE_AUTH_SALT', 'cJuD6PbFqfZs8qA1koNnqg8UUSYdgtBOeUJhIbc3qJVS6ATkI38wKXWP8Ubw75yX');
define('LOGGED_IN_SALT',   'Ui4GoMAYTURXXFTjssunIiJDrODtQSFl4wB59eJEHphjWVMrsOkywtq9SYduOrDJ');
define('NONCE_SALT',       'IeZXSY1UgiG6HbDI6NEu8RrYvVMulUE6naMEHdRH5qxL1x1uePEX06veBPDmuJOh');

/**
 * Other customizations.
 */
define('FS_METHOD','direct');
define('FS_CHMOD_DIR',0755);
define('FS_CHMOD_FILE',0644);
define('WP_TEMP_DIR',dirname(__FILE__).'/wp-content/uploads');


/**#@-*/

/**
 * WordPress database table prefix.
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
define( 'WP_DEBUG', true );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';

@ini_set( 'upload_max_filesize' , '300M' );
@ini_set( 'post_max_size', '300M');
@ini_set( 'memory_limit', '400M' );
@ini_set( 'max_execution_time', '0' );
@ini_set( 'max_input_time', '0' );
