<?php
/**
 * Admin View: Calendar
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$today     = new Datetime();
$today     = $today->format( 'Y-m-d' );
$next_week = clone( $marker );
$next_week = $next_week->modify( '+7 days' )->format( 'Y-m-d' );
$prev_week = clone( $marker );
$prev_week = $prev_week->modify( '-7 days' )->format( 'Y-m-d' );

?>

<div class="wrap mb_hms">

	<h1><?php esc_html_e( 'Booking calendar', 'wp-mb_hms' ); ?></h1>

	<?php
	$classes = array();

	foreach ( mbh_get_reservation_statuses() as $id => $name ) {
		$id = str_replace( 'mbh-', '', $id );
		if ( ! empty( $_GET[ $id ] ) && 'false' == $_GET[ $id ] ) {
			$classes[] = 'no-' . $id;
		}
	}

	$classes[] = 'weeks-' . $weeks;
	?>

	<div class="bc booking-calendar <?php echo esc_attr( implode( ' ', $classes ) ); ?>">

		<ul class="bc-filter">
			<?php foreach ( mbh_get_reservation_statuses() as $id => $name ) :
				$id = esc_attr( str_replace( 'mbh-', '', $id ) );
				$status = ! empty( $_GET[ $id ] ) && 'false' == $_GET[ $id ] ? 'true' : 'false';
				?>
				<li class="bc-filter__item bc-filter__item--<?php echo $id; ?>">
					<a href="<?php echo esc_url( add_query_arg( $id, $status ) ); ?>" class="bc-filter__link bc-filter__link--<?php echo $id; ?> <?php echo 'true' == $status ? 'bc-filter__link--hidden' : ''; ?>">
						<span class="bc-filter__icon bc-filter__icon--<?php echo $id; ?>"></span>
						<?php echo esc_html( $name ); ?>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>

		<ul class="bc-nav">
			<li class="bc-nav__item bc-nav__item--prev">
				<a class="button bc-nav__button bc-nav__button--prev" href="<?php echo esc_url( add_query_arg( 'marker', $prev_week ) ); ?>"><i class="dashicons dashicons-arrow-left-alt2"></i></a>
			</li>
			<li class="bc-nav__item bc-nav__item--next">
				<a class="button bc-nav__button bc-nav__button--next" href="<?php echo esc_url( add_query_arg( 'marker', $next_week ) ); ?>"><i class="dashicons dashicons-arrow-right-alt2"></i></a>
			</li>
		</ul>

		<ul class="bc-weeks">
			<li class="bc-weeks__item bc-weeks__item--1">
				<a class="button bc-weeks__button bc-weeks__button--1" href="<?php echo esc_url( add_query_arg( 'weeks', 1 ) ); ?>">7</a>
			</li>
			<li class="bc-weeks__item bc-weeks__item--2">
				<a class="button bc-weeks__button bc-weeks__button--2" href="<?php echo esc_url( add_query_arg( 'weeks', 2 ) ); ?>">14</a>
			</li>
			<li class="bc-weeks__item bc-weeks__item--3">
				<a class="button bc-weeks__button bc-weeks__button--3" href="<?php echo esc_url( add_query_arg( 'weeks', 3 ) ); ?>">21</a>
			</li>
			<li class="bc-weeks__item bc-weeks__item--4">
				<a class="button bc-weeks__button bc-weeks__button--4" href="<?php echo esc_url( add_query_arg( 'weeks', 4 ) ); ?>">28</a>
			</li>
			<li class="bc-weeks__item bc-weeks__item--today">
				<a class="button bc-weeks__button bc-weeks__button--roday" href="<?php echo esc_url( add_query_arg( 'marker', $today ) ); ?>"><?php esc_html_e( 'Today', 'wp-mb_hms' ); ?></a>
			</li>
		</ul>

		<form action="<?php echo admin_url( 'admin.php' ); ?>" method="get" class="form form--bc">
			<?php
			$room_terms = get_terms( array(
				'taxonomy'   => 'room_cat',
				'hide_empty' => false,
			) );

			if ( ! empty( $room_terms ) && ! is_wp_error( $room_terms ) ) : ?>
				<select name="room_term" id="room_term">
					<option value="0"><?php esc_html_e( 'All Categories', 'wp-mb_hms' ) ?></option>

					<?php foreach ( $room_terms as $room_term ) : ?>
						<option value="<?php echo absint( $room_term->term_id ) ?>" <?php selected( $room_cat, $room_term->term_id ); ?>><?php echo esc_html( $room_term->name ) ?></option>
					<?php endforeach; ?>
				</select>

				<input type="submit" class="button" value="<?php esc_attr_e( 'Filter', 'wp-mb_hms' ); ?>">
			<?php endif; ?>

			<input type="hidden" name="page" value="mb_hms-calendar">
			<input type="hidden" name="weeks" value="<?php if ( ! empty( $_GET[ 'weeks' ] ) ) echo absint( $_GET[ 'weeks' ] ); ?>">
			<input class="bc-datepicker" type="text" placeholder="YYYY-MM-DD" name="marker" value="<?php echo esc_attr( $marker->format( 'Y-m-d' ) ); ?>">
			<input type="submit" class="button" value="<?php esc_attr_e( 'Go to date', 'wp-mb_hms' ); ?>">
		</form>

		<div class="bc__table-wrapper">

		<?php include_once( 'html-admin-calendar-table.php' ); ?>

		</div>

	</div>

</div>
