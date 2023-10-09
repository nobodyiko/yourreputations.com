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
 * * Localized language
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'u232709270_KrvhV' );

/** Database username */
define( 'DB_USER', 'u232709270_7tmSW' );

/** Database password */
define( 'DB_PASSWORD', 'Gu6uVLkBnc' );

/** Database hostname */
define( 'DB_HOST', '127.0.0.1' );

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
define( 'AUTH_KEY',          'vQIZbS%  d:^>+ca0[m)$B[C]Pm-W*fKmgE~1FY:9`5Q}_n<sIYHNzXI$j+Mdj$=' );
define( 'SECURE_AUTH_KEY',   '~im oQSXsFmDIEi>Y[0<d+k%/s&Pd_}C^03nT~7 0MT2]wor*9wV+Y/POr,tNa9H' );
define( 'LOGGED_IN_KEY',     'OKd#FGmV_<cfh7~AylmoBK9p7LiL{ewDe`1S_f=IF]m8CLxlBnmbcXv`2&m]w[nl' );
define( 'NONCE_KEY',         't)V/65a*iL2=TW%.D?a~@{]*),I9m#@*cBRH&A(g`QcJhS@SfaT|z{hvR5KK6HcB' );
define( 'AUTH_SALT',         'BNEh409Cv^*&Pi.Z[A!@_?59.eCu&<})AVx|mi60et4.qjDu^20&[Xz+?jOjT9id' );
define( 'SECURE_AUTH_SALT',  'd@%a[NuV#E7Ma!Rdd[,f{D+a$^ei`S%9XzU#V__2U{m9ISY>OpdWM<x.~Wy_4JD;' );
define( 'LOGGED_IN_SALT',    '4v2^hz6ruX8/>dHJ3V(5pmLYF;|ap3;*PzvU[2s.Y*)LPUFBV`xC=82Xa0px&+J3' );
define( 'NONCE_SALT',        'LJ2N gyJ{Sw@=2~IdSio56_SP3+&{>-p  !tTb3TkjES|jCad[EO#8wt?.C>ksHp' );
define( 'WP_CACHE_KEY_SALT', '(:_cQ?5i&v4:j/90 dlo[~zD|&WF>e6ZRfs0 A?VlGiZU9TabMkYM1?#!+M1cxOO' );


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



define( 'FS_METHOD', 'direct' );
define( 'WP_AUTO_UPDATE_CORE', 'minor' );
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
