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
define( 'DB_NAME', 'kevi_ho_geo_plugin' );

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
define( 'AUTH_KEY',         'Di;G#%NT 53#m|dV=TtE6,4xoL?ChI[+uDnBXI4asIQuC7uP?X<go&1nd?VpF,)L' );
define( 'SECURE_AUTH_KEY',  '9}s,R6%=!aI ~t!Sd?7_I0<8-VC=2g-hgI|w`x%)Icz~+zK(&UtVV7#|.~RoN&u]' );
define( 'LOGGED_IN_KEY',    'cEBGaZ)*x$f7cpQ1:7j5Ym}+CHH$`N[n;eVU^#:x|MX%E|w^sI(HqHbc<l6$<Oe:' );
define( 'NONCE_KEY',        '2HH,u(N@>?gKNCi91i_Z8gV?M];ri|B4Q}yYFY!)s)=[zN3{?7eRi3*!5O 3EBuY' );
define( 'AUTH_SALT',        '0^%s.(fwL>,x]gjG5-v>AQzT>L? lnaBT)FN$7%-)c6pjJYRv]NoO&mLDFI8]}Mv' );
define( 'SECURE_AUTH_SALT', '}|..#?J-y[&c2-%+rcm,uJz}1z[s]DRJjNUbv<(t:?eL>km+@}Q[Y$=fZP~fJ;9:' );
define( 'LOGGED_IN_SALT',   '>1,eF4)khx/n|<p0:o)@~*KqMfN,zQ}=-Ol4[,Q#5aOSmI$x0-kL?#_dj0E)%v~`' );
define( 'NONCE_SALT',       'nm%gZ&:$x;6qEafZ#apSbTG:GbuxtVU~Hi|VK`E(hyoB-}sM7&KK@4HAr2OtA.sA' );

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
