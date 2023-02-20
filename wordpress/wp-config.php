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
define('WP_CACHE', true);
define( 'WPCACHEHOME', 'C:\xampp\htdocs\wordpress\wp-content\plugins\wp-super-cache/' );
define( 'DB_NAME', 'wordpress' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

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
define( 'AUTH_KEY',         'O5-2v;Ba$xg(~VMg`/ArjESwJc#JRdxAbRdZ%|7U6@t+ZwzBWX.r_4ej|.m>niLf' );
define( 'SECURE_AUTH_KEY',  '7}@Iqso)3w|JU:.g(%KfJhy}Z_QU$Q2fxcQ!LOr@@?kmP|?IN=hKvDYxv DrPIhg' );
define( 'LOGGED_IN_KEY',    'Up+h=,=[0 xFW]<[JqLV7LW4)5eftR3V(;lmqpX|+/sc9E1JJ:t,;T@Pkcuv![pS' );
define( 'NONCE_KEY',        '^>SRVbv|3fJ<M`DPl/eUw@dAMZi6aj}~hX+xkv1Tw6ugLHawk64#Dvi}FR|.,(7t' );
define( 'AUTH_SALT',        '>FtU:Tjbmeg-K:QaOvty1ME4v_TYy0&7/q?.`e&(7xi;n@LkazLCjQ);ys?!zxHm' );
define( 'SECURE_AUTH_SALT', 'm4cZqia+VWL9pxvn% X^Lpos7ke#*sy7w,dc)?IiMb*wgX$aj+-zq):TZwLxS;2v' );
define( 'LOGGED_IN_SALT',   'hAB_R8o;?PKkeckHN8RBuQ)_Uwn>sU}pbPMHvm0}`5{i[=x.QI[)^/0}o%DYY.!o' );
define( 'NONCE_SALT',       'FuE^p%-2}r?6~gT12OhW{8ly/n@r3`5I3}|SP~phVrsmLEb5]>1{&5>2pL+Gv0Ft' );

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
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
