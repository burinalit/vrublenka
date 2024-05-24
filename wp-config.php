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
define( 'DB_NAME', 'alrin_wp8' );

/** Database username */
define( 'DB_USER', 'alrin_wp8' );

/** Database password */
define( 'DB_PASSWORD', 'tG3Z9u1A*' );

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
define( 'AUTH_KEY',         'a7**A1oAS3})~d_RVaOck!<:xorN~Bp]c_JJHvo]uJF.o8W7N,wLjd~YQ07(TuCE' );
define( 'SECURE_AUTH_KEY',  '};X# `&%:zW8O>D{WX,}DF`rig!n_ecAys]*r{Akdy%283_dHeS@c!Pe kT0p%s]' );
define( 'LOGGED_IN_KEY',    '/9.^#|p<,L6tKF)<}2bRnJ@UGds|wO$M1g/s;x~U])TlocVs]uZ?>Y&>. dVi2U4' );
define( 'NONCE_KEY',        '!rGxl6uaO>fqyyL)Ql[nmWv[S;)4MOm127q>Bl5Awb~D;#jq| G`dT@2}A#jkSkc' );
define( 'AUTH_SALT',        '/1-/q@]M@^fWAgCU8L|S<WjIh9gyP+xg,7_hr5ci(OWTgM/SbbgvPQO?One*?~ed' );
define( 'SECURE_AUTH_SALT', '7J 6DF9h1$XTcl8-yNe;lwXC=:Of3vS34A/2DwhJz0#=lDx(8AMDrv6xTkW|;,x#' );
define( 'LOGGED_IN_SALT',   '&2<_a{];+C|U=cN^wR7VQMW1$<ixhxrlQZl`pNoPRj0T|Jtxc#7LGk$@0BlHG0C8' );
define( 'NONCE_SALT',       'fLoOTXk@a^Q]:0k2`U%beKstrA<?0Fy~^#~ORD%aKgr&z~[Sk@cqpimy@+pMI@`/' );

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
