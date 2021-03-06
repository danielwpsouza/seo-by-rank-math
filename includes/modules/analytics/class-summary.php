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
 * Summary class.
 */
class Summary {

	/**
	 * Get Widget.
	 *
	 * @return object
	 */
	public function get_widget() {
		global $wpdb;

		$cache_key = Stats::get()->get_cache_key( 'dashboard_stats_widget' );
		$cache     = get_transient( $cache_key );

		if ( false !== $cache ) {
			return $cache;
		}

		$stats = DB::analytics()
			->selectSum( 'impressions', 'impressions' )
			->selectSum( 'clicks', 'clicks' )
			->selectAvg( 'position', 'position' )
			->whereBetween( 'created', [ Stats::get()->start_date, Stats::get()->end_date ] )
			->one();

		$old_stats = DB::analytics()
			->selectSum( 'impressions', 'impressions' )
			->selectSum( 'clicks', 'clicks' )
			->selectAvg( 'position', 'position' )
			->whereBetween( 'created', [ Stats::get()->compare_start_date, Stats::get()->compare_end_date ] )
			->one();

		$stats->clicks = [
			'total'      => (int) $stats->clicks,
			'previous'   => (int) $old_stats->clicks,
			'difference' => $stats->clicks - $old_stats->clicks,
		];

		$stats->impressions = [
			'total'      => (int) $stats->impressions,
			'previous'   => (int) $old_stats->impressions,
			'difference' => $stats->impressions - $old_stats->impressions,
		];

		$stats->position = [
			'total'      => (float) \number_format( $stats->position, 2 ),
			'previous'   => (float) \number_format( $old_stats->position, 2 ),
			'difference' => (float) \number_format( $stats->position - $old_stats->position, 2 ),
		];

		$stats->keywords = $this->get_keywords_summary();

		$stats = apply_filters( 'rank_math/analytics/get_widget', $stats );

		set_transient( $cache_key, $stats, DAY_IN_SECONDS * Stats::get()->days );

		return $stats;
	}

	/**
	 * Get Optimization stats.
	 *
	 * @return object
	 */
	public function get_optimization_summary() {
		global $wpdb;

		$stats = (object) [
			'good'    => 0,
			'ok'      => 0,
			'bad'     => 0,
			'noData'  => 0,
			'total'   => 0,
			'average' => 0,
		];

		$data = $wpdb->get_results(
			"SELECT COUNT(object_id) AS count,
				CASE
					WHEN seo_score BETWEEN 81 AND 100 THEN 'good'
					WHEN seo_score BETWEEN 51 AND 80 THEN 'ok'
					WHEN seo_score BETWEEN 1 AND 50 THEN 'bad'
					WHEN seo_score = 0 THEN 'noData'
					ELSE 'none'
				END AS type
			FROM {$wpdb->prefix}rank_math_analytics_objects
			WHERE is_indexable = 1
			GROUP BY type"
		);

		$total = 0;
		foreach ( $data as $row ) {
			$total += (int) $row->count; // phpcs:ignore
			$stats->{$row->type} = (int) $row->count;
		}
		$stats->total   = $total;
		$stats->average = 0;

		// Average.
		$average = DB::objects()
			->selectCount( 'object_id', 'total' )
			->where( 'is_indexable', 1 )
			->selectSum( 'seo_score', 'score' )
			->one();

		$average->total += property_exists( $stats, 'noData' ) ? $stats->noData : 0; // phpcs:ignore

		if ( $average->total > 0 ) {
			$stats->average = $average->score / $average->total;
			$stats->average = \round( $stats->average, 2 );
		}

		return $stats;
	}

	/**
	 * Get console data/
	 *
	 * @return object
	 */
	public function get_analytics_summary() {
		$stats = DB::analytics()
			->selectCount( 'DISTINCT(page)', 'posts' )
			->selectSum( 'impressions', 'impressions' )
			->selectSum( 'clicks', 'clicks' )
			->selectAvg( 'position', 'position' )
			->selectAvg( 'ctr', 'ctr' )
			->whereBetween( 'created', [ $this->start_date, $this->end_date ] )
			->one();

		$old_stats = DB::analytics()
			->selectCount( 'DISTINCT(page)', 'posts' )
			->selectSum( 'impressions', 'impressions' )
			->selectSum( 'clicks', 'clicks' )
			->selectAvg( 'position', 'position' )
			->selectAvg( 'ctr', 'ctr' )
			->whereBetween( 'created', [ $this->compare_start_date, $this->compare_end_date ] )
			->one();

		$stats->clicks = [
			'total'      => (int) $stats->clicks,
			'previous'   => (int) $old_stats->clicks,
			'difference' => $stats->clicks - $old_stats->clicks,
		];

		$stats->impressions = [
			'total'      => (int) $stats->impressions,
			'previous'   => (int) $old_stats->impressions,
			'difference' => $stats->impressions - $old_stats->impressions,
		];

		$stats->position = [
			'total'      => (float) \number_format( $stats->position, 2 ),
			'previous'   => (float) \number_format( $old_stats->position, 2 ),
			'difference' => (float) \number_format( $stats->position - $old_stats->position, 2 ),
		];

		$stats->ctr = [
			'total'      => (float) \number_format( $stats->ctr, 2 ),
			'previous'   => (float) \number_format( $old_stats->ctr, 2 ),
			'difference' => (float) \number_format( $stats->ctr - $old_stats->ctr, 2 ),
		];

		$stats->keywords = $this->get_keywords_summary();
		$stats->graph    = $this->get_analytics_summary_graph();

		$stats = apply_filters( 'rank_math/analytics/summary', $stats );

		return array_filter( (array) $stats );
	}

	/**
	 * Get posts summary.
	 *
	 * @return object
	 */
	public function get_posts_summary() {
		$cache_key = $this->get_cache_key( 'posts_summary', $this->days . 'days' );
		$cache     = get_transient( $cache_key );

		if ( false !== $cache ) {
			return $cache;
		}

		$summary = DB::analytics()
			->selectCount( 'DISTINCT(page)', 'posts' )
			->selectSum( 'impressions', 'impressions' )
			->selectSum( 'clicks', 'clicks' )
			->selectAvg( 'ctr', 'ctr' )
			->whereBetween( 'created', [ $this->start_date, $this->end_date ] )
			->one();

		$summary = apply_filters( 'rank_math/analytics/posts_summary', $summary );

		$summary = wp_parse_args(
			array_filter( (array) $summary ),
			[
				'ctr'         => 0,
				'posts'       => 0,
				'clicks'      => 0,
				'pageviews'   => 0,
				'impressions' => 0,
			]
		);

		set_transient( $cache_key, $summary, DAY_IN_SECONDS );

		return $summary;
	}

	/**
	 * Get keywords summary.
	 *
	 * @return array
	 */
	public function get_keywords_summary() {
		global $wpdb;

		// Get Total Keywords Counts.
		$keywords_count = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT(DISTINCT(query))
				FROM {$wpdb->prefix}rank_math_analytics_gsc
				WHERE created BETWEEN %s AND %s
				GROUP BY Date(created)
				ORDER BY Date(created) DESC
				LIMIT 1",
				$this->start_date,
				$this->end_date
			)
		);

		$old_keywords_count = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT(DISTINCT(query))
				FROM {$wpdb->prefix}rank_math_analytics_gsc
				WHERE created BETWEEN %s AND %s
				GROUP BY Date(created)
				ORDER BY Date(created) DESC
				LIMIT 1",
				$this->compare_start_date,
				$this->compare_end_date
			)
		);

		$keywords = [
			'total'      => (int) $keywords_count,
			'previous'   => (int) $old_keywords_count,
			'difference' => (int) $keywords_count - (int) $old_keywords_count,
		];

		return $keywords;
	}

	/**
	 * Get graph data.
	 *
	 * @return array
	 */
	public function get_analytics_summary_graph() {
		global $wpdb;

		$data          = new \stdClass();
		$intervals     = $this->get_intervals();
		$sql_daterange = $this->get_sql_date_intervals( $intervals );

		// phpcs:disable
		$query = $wpdb->prepare(
			"SELECT DATE_FORMAT( created, '%%Y-%%m-%%d') as date, SUM(clicks) as clicks, SUM(impressions) as impressions, AVG(position) as position, AVG(ctr) as ctr, {$sql_daterange}
			FROM {$wpdb->prefix}rank_math_analytics_gsc
			WHERE created BETWEEN %s AND %s
			GROUP BY range_group",
			$this->start_date,
			$this->end_date
		);
		$analytics = $wpdb->get_results( $query );
		$analytics = $this->set_dimension_as_key( $analytics, 'range_group' );
		// phpcs:enable

		// phpcs:disable
		$query = $wpdb->prepare(
			"SELECT t.range_group, MAX(CONCAT(t.range_group, ':', t.date, ':', t.keywords )) as mixed FROM
				(SELECT COUNT(DISTINCT(query)) as keywords, Date(created) as date, {$sql_daterange}
				FROM {$wpdb->prefix}rank_math_analytics_gsc
				WHERE created BETWEEN %s AND %s
				GROUP BY range_group, Date(created)) AS t
			GROUP BY t.range_group",
			$this->start_date,
			$this->end_date
		);
		$keywords = $wpdb->get_results( $query );
		// phpcs:enable

		$keywords = $this->extract_data_from_mixed( $keywords, 'mixed', ':', [ 'keywords', 'date' ] );
		$keywords = $this->set_dimension_as_key( $keywords, 'range_group' );

		// merge metrics data.
		$data->analytics = [];
		$data->analytics = $this->get_merged_metrics( $analytics, $keywords, true );

		$data->merged = $this->get_date_array(
			$intervals['dates'],
			[
				'clicks'      => [],
				'impressions' => [],
				'position'    => [],
				'ctr'         => [],
				'keywords'    => [],
				'pageviews'   => [],
			]
		);

		// Convert types.
		$data->analytics = array_map( [ $this, 'normalize_graph_rows' ], $data->analytics );

		// Merge for performance.
		$data->merged = $this->get_merge_data_graph( $data->analytics, $data->merged, $intervals['map'] );

		// For developers.
		$data = apply_filters( 'rank_math/analytics/analytics_summary_graph', $data, $intervals );

		$data->merged = $this->get_graph_data_flat( $data->merged );
		$data->merged = array_values( $data->merged );

		return $data;
	}
}
