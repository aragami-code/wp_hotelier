<?php
/**

 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $room, $mb_hms_loop;

// Store loop count we're currently on
if ( empty( $mb_hms_loop[ 'loop' ] ) ) {
	$mb_hms_loop[ 'loop' ] = 0;
}

// Store column count for displaying the grid
if ( empty( $mb_hms_loop[ 'columns' ] ) ) {
	$mb_hms_loop[ 'columns' ] = apply_filters( 'loop_room_columns', 3 );
}

// Ensure visibility
if ( ! $room ) {
	return;
}

// Increase loop count
$mb_hms_loop[ 'loop' ]++;

// Extra post classes
$classes = array();
$classes[] = 'room-loop__item';

// first row item
if ( 0 == ( $mb_hms_loop[ 'loop' ] - 1 ) % $mb_hms_loop[ 'columns' ] || 1 == $mb_hms_loop[ 'columns' ] ) {
	$classes[] = 'room-loop__item--first';
}

// last row item
if ( 0 == $mb_hms_loop[ 'loop' ] % $mb_hms_loop[ 'columns' ] ) {
	$classes[] = 'room-loop__item--last';
}

// even/odd items
if ( 0 == $mb_hms_loop[ 'loop' ] % 2 ) {
	$classes[] = 'room-loop__item--even';
} else {
	$classes[] = 'room-loop__item--odd';
}

// number of columns (last rule, to override the previous ones)
$classes[] = 'room-loop__item--columns-' . $mb_hms_loop[ 'columns' ];
?>

<li <?php post_class( $classes ); ?>>

	<?php
	/**
	 * 
	 */
	do_action( 'mb_hms_archive_item_room' );
	?>

</li>
