<?php
/**
 * The Analytics Module
 *
 * @since      1.0.49
 * @package    RankMath
 * @subpackage RankMath\modules
 * @author     Rank Math <support@rankmath.com>
 */

namespace RankMath\Analytics;

defined( 'ABSPATH' ) || exit;

/**
 * Objects class.
 */
class Objects extends Summary {

	/**
	 * Get objects for pages.
	 *
	 * @param  array $pages Array of urls.
	 * @return array
	 */
	public function get_objects( $pages ) {
		if ( empty( $pages ) ) {
			return [];
		}

		$pages = DB::objects()
			->whereIn( 'page', \array_unique( $pages ) )
			->where( 'is_indexable', 1 )
			->get( ARRAY_A );

		return $this->set_page_as_key( $pages );
	}

	/**
	 * Get page views for pages.
	 *
	 * @param WP_REST_Request $request Filters.
	 *
	 * @return array
	 */
	public function get_objects_by_score( $request ) {
		global $wpdb;

		$filters    = [
			'good'   => $request->get_param( 'good' ),
			'ok'     => $request->get_param( 'ok' ),
			'bad'    => $request->get_param( 'bad' ),
			'noData' => $request->get_param( 'noData' ),
		];
		$field_name = 'seo_score';
		$per_page   = 25;
		$offset     = ( $request->get_param( 'page' ) - 1 ) * $per_page;

		$conditions = [];
		if ( $filters['good'] ) {
			$conditions[] = "{$field_name} BETWEEN 81 AND 100";
		}

		if ( $filters['ok'] ) {
			$conditions[] = "{$field_name} BETWEEN 51 AND 80";
		}

		if ( $filters['bad'] ) {
			$conditions[] = "{$field_name} BETWEEN 1 AND 50";
		}

		if ( $filters['noData'] ) {
			$conditions[] = "{$field_name} = 0";
		}

		$subwhere = '';
		if ( count( $conditions ) > 0 ) {
			$subwhere = implode( ' OR ', $conditions );
			$subwhere = " AND ({$subwhere})";
		}

		$limit = "LIMIT {$offset}, {$per_page}";

		// phpcs:disable
		$pages = $wpdb->get_results(
			"SELECT * FROM {$wpdb->prefix}rank_math_analytics_objects 
			WHERE is_indexable = 1 
			{$subwhere}
			ORDER BY created DESC
			{$limit}",
			ARRAY_A
		);

		$total_rows = $wpdb->get_var(
			"SELECT count(*) FROM {$wpdb->prefix}rank_math_analytics_objects 
			WHERE is_indexable = 1 
			{$subwhere}
			ORDER BY created DESC"
		);

		return [
			'rows'      => $this->set_page_as_key( $pages ),
			'rowsFound' => $total_rows,
		];
	}
}