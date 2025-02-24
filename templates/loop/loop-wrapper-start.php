<?php
/**
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

// Store column count for displaying the grid
if ( empty( $mb_hms_loop[ 'columns' ] ) ) {
	$mb_hms_loop[ 'columns' ] = apply_filters( 'loop_room_columns', 3 );
}

?>

<div class="mb_hms room-loop room-loop--archive-rooms room-loop--columns-<?php echo absint( $mb_hms_loop[ 'columns' ] ); ?>">
