<?php

namespace Mindesia\WP_Class;

use Timber\Term as TimberTerm;

class Term
{
    /**
     * Get terms by term ids or term slugs
     *
     * @param array $term_ids_or_slugs
     * @return array
     */
    public static function get_all_by_id_or_slug(array $term_ids_or_slugs, $taxonomy_name = ''): array
    {
        $term_list = [];

        foreach ($term_ids_or_slugs as $term) {
            \array_push($term_list, new TimberTerm($term, $taxonomy_name));
        }

        $term_list = array_unique($term_list);

        return $term_list;
    }

    /**
     * Get all terms by taxonomy
     *
     * @param string $taxonomy
     * @return array
     */
    public static function get_all_by_taxonomy(string $taxonomy): array {

        $term_list = [];

        $terms =  get_terms( [
            'taxonomy' => $taxonomy,
            'hide_empty' => false,
        ]);

        foreach ($terms as $term) {
            \array_push($term_list, new TimberTerm($term->slug));
        }

        return $term_list;
    }
}
