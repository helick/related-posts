<?php

namespace Helick\RelatedPosts;

use WP_Query;

/**
 * Get the related post ids.
 *
 * @param int   $postId
 * @param array $args
 *
 * @return array
 */
function get(int $postId, array $args = []): array
{
    $defaultArgs = [
        'limit'        => 10,
        'post_types'   => ['post'],
        'taxonomies'   => ['category'],
        'terms'        => [],
        'terms_not_in' => [],
        'ep_integrate' => defined('EP_VERSION'),
    ];

    $args = wp_parse_args($args, $defaultArgs);

    /**
     * Control the args.
     *
     * @param array $args
     * @param int   $postId
     */
    $args = apply_filters('helick_related_posts_args', $args, $postId);

    $transient = sprintf('helick_related_posts_%d_%s', $postId, hash('md5', json_encode((array)$args)));

    $relatedPostIds = get_transient($transient);

    if (is_array($relatedPostIds)) {
        return $relatedPostIds;
    }

    $manualRelatedPostIds = array_column((array)carbon_get_post_meta($postId, 'helick_related_posts'), 'id');
    $manualRelatedPostIds = array_map('intval', $manualRelatedPostIds);

    $limit = $args['limit'] - count($manualRelatedPostIds);

    if ($limit > 0) {
        $queryArgs = [
            'post_type'      => $args['post_types'],
            'post_status'    => 'publish',
            'posts_per_page' => $limit,
            'order'          => 'DESC',
            'tax_query'      => [],
            'fields'         => 'ids',
            'post__not_in'   => array_merge([$postId], $manualRelatedPostIds),
            'ep_integrate'   => $args['ep_integrate'],
        ];

        if (empty($args['terms'])) {
            $terms = wp_get_object_terms($postId, $args['taxonomies']);
            if (is_wp_error($terms)) {
                $terms = [];
            }
        } else {
            $terms = $args['terms'];
        }

        foreach ($terms as $term) {
            if (!isset($queryArgs['tax_query'][$term->taxonomy])) {
                $queryArgs['tax_query'][$term->taxonomy] = [
                    'taxonomy' => $term->taxonomy,
                    'field'    => 'term_id',
                    'terms'    => [],
                ];
            }

            array_push($queryArgs['tax_query'][$term->taxonomy]['terms'], $term->term_id);
        }

        foreach ($args['terms_not_in'] as $term) {
            if (!isset($queryArgs['tax_query']['not_' . $term->taxonomy])) {
                $queryArgs['tax_query']['not_' . $term->taxonomy] = [
                    'taxonomy' => $term->taxonomy,
                    'field'    => 'term_id',
                    'terms'    => [],
                    'operator' => 'NOT IN',
                ];
            }

            array_push($queryArgs['tax_query']['not_' . $term->taxonomy]['terms'], $term->term_id);
        }

        $queryArgs['tax_query']             = array_values($queryArgs['tax_query']);
        $queryArgs['tax_query']['relation'] = 'OR';

        if ($args['ep_integrate']) {
            $relatedTaxonomies = array_map(function ($taxonomy) {
                return "terms.{$taxonomy}.name";
            }, $args['taxonomies']);

            $relatedFields = apply_filters('helick_related_posts_fields', array_merge([
                'post_title',
                'post_content',
            ], $relatedTaxonomies));

            $queryArgs['more_like']        = $postId;
            $queryArgs['more_like_fields'] = $relatedFields;
        }

        /**
         * Control the query args.
         *
         * @param array $queryArgs
         * @param int   $postId
         * @param array $args
         */
        $queryArgs = apply_filters('helick_related_posts_query_args', $queryArgs, $postId, $args);

        $query = new WP_Query($queryArgs);
        wp_reset_postdata();

        $relatedPostIds = array_merge($manualRelatedPostIds, $query->posts);
        $relatedPostIds = array_map('intval', $relatedPostIds);
        $relatedPostIds = array_unique($relatedPostIds);
    }

    $relatedPostIds = array_slice($relatedPostIds, 0, $args['limit']);

    set_transient($transient, $relatedPostIds, HOUR_IN_SECONDS);

    return $relatedPostIds;
}
