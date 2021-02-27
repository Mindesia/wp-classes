<?php

namespace Mindesia\WP_Class;

use Timber\Timber;
use Timber\PostQuery;
use Cocur\Slugify\Slugify;
use Timber\Term as TimberTerm;

class Post
{
    /**
     * Get all Posts
     *
     * @return array
     */
    public static function get_all(): array
    {
        $args = array(
            'posts_per_page' => -1,
            'orderby' => [
                'date' => 'DESC'
            ]
        );

        return Timber::get_posts($args);
    }

     /**
      * Get all Posts by PostType
      *
      * @param array $post_type_slugs
      * @param integer $per_page
      * @param string $sort_field
      * @param string $taxonomy
      * @param string $terms
      * @return array
      */
    public static function get_all_by_post_type(array $post_type_slugs, int $per_page = null, string $sort_field = null, string $taxonomy = null, string $terms = null): array
    {
        $posts = [];
        $per_page = $per_page == null ? "-1" : $per_page;

        $current_page = get_query_var('paged');
        $current_page = max(1, $current_page);

        $offset = ($current_page - 1) * $per_page;

        foreach ($post_type_slugs as $post_type) {

            $args = [
                'post_type' => $post_type,
                'posts_per_page' => $per_page,
                'paged' => $current_page,
                'offset'         => $offset,
                'orderby' => 'date',
                'order' => 'DESC',
            ];

            if ($sort_field) {
                $args['meta_key'] = $sort_field;
                $args['orderby'] = 'meta_value, meta_value_num';
            }

            if ($taxonomy && $terms) {
                $args['tax_query'] = [
                    [
                        'taxonomy' => $taxonomy,
                        'field' => 'slug',
                        'terms' => $terms,
                    ]
                ];
            }

            $post_type_posts = Timber::get_posts($args);
            $posts = \array_merge($posts, $post_type_posts);
        }

        return $posts;
    }

    /**
     * Undocumented function
     *
     * @param array $post_type_slugs
     * @param string $taxonomy
     * @param array $terms
     * @param integer $max_posts
     * @return void
     */
    public static function get_similar(array $post_type_slugs, string $taxonomy = null, array $terms = null, int $max_posts = 3)
    {
        $posts = [];

        foreach ($post_type_slugs as $post_type) {

            $args = [
                'post_type' => $post_type,
                'orderby' => 'rand',
                'posts_per_page' => $max_posts,
                'tax_query' => [
                    [
                        'taxonomy' => $taxonomy,
                        'field' => 'slug',
                        'terms' => $terms,
                    ]
                ]
            ];

            $post_type_posts = Timber::get_posts($args);
            $posts = \array_merge($posts, $post_type_posts);
        }

        return $posts;
    }

    public static function get_pagination($post_type_slug, $per_page = null)
    {
        $current_page = get_query_var('paged');
        $current_page = max(1, $current_page);
        $per_page != null ?? 1; 

        if (!$post_type_slug) {
            return;
        }

        $args = [
            'post_type' => $post_type_slug[0]->get_post_type()->slug,
            'posts_per_page' => $per_page,
            'paged' => $current_page,
            'orderby' => [
                'date' => 'DESC'
            ]
        ];

        return new PostQuery($args);
    }

    /**
     * Get pagination from posts list
     *
     * @param array $posts_list
     * @return void
     */
    public static function get_pagination_old(array $posts_list, $per_page = null)
    {
        $current_page = get_query_var('paged');
        $current_page = max(1, $current_page);
        $per_page != null ?? 1; 

        $args = [
            'post_type' => $posts_list[0]->get_post_type(),
            'posts_per_page' => $per_page,
            'orderby' => [
                'date' => 'DESC'
            ]
        ];

        $posts = Timber::get_posts($args);

        $total_rows = max(0, \count($posts));
        $total_pages = ceil($total_rows / $per_page);

        return paginate_links([
            'total'   => $total_pages,
            'current' => $current_page,
        ]);
    }

    /**
     * Get all posts by term ids
     *
     * @param integer $term_ids
     * @return array
     */
    public static function get_all_by_term_ids(array $term_ids, $taxonomy_name): array
    {
        $posts = [];

        foreach ($term_ids as $term_id) {
            $termPosts = (new TimberTerm($term_id, $taxonomy_name))->get_posts(-1);
            \array_push($posts, $termPosts);
        }

        return $posts;
    }

    /**
     * Get slugified title
     *
     * @return string
     */
    public static function get_title_slug($post = null): string
    {
        if ($post == null) {
            $post = Timber::get_post();
        }
        
        $title_string = html_entity_decode($post->title());
        return (new Slugify())->slugify($title_string);
    }
}
