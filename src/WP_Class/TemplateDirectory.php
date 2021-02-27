<?php

namespace Mindesia\WP_Class;

class TemplateDirectory
{
    public function __construct()
    {
        add_filter('template_include', [$this, 'rp_get_front_page_template']);
        add_filter('category_template', [$this, 'rp_get_category_template']);
        add_filter('page_template', [$this, 'rp_get_page_template']);
        add_filter('single_template', [$this, 'rp_get_single_template']);
        add_filter('404_template', [$this, 'rp_get_404_template']);
    }
    public static $pages_folder = "Controller/pages";
    public static $pages_template_folder = "Controller/pages/templates";
    public static $pages_static_folder = "Controller/pages/statics";

    public function rp_get_front_page_template($template)
    {
        if (\is_front_page()) {
            $templates = locate_template([self::$pages_folder . "/front-page.php"]);
            if ($templates != '') {
                return $templates;
            }
        }

        return $template;
    }

    /** Single */
    public function rp_get_single_template()
    {
        $object = get_queried_object();

        $templates = [];

        if (!empty($object->post_type)) {
            $name_decoded = urldecode($object->post_name);

            if ($object->post_type == 'static-pages') {
                $templates[] = self::$pages_static_folder . "/{$name_decoded}.php";
            }

            $template = get_page_template_slug($object);

            if ($template && 0 === validate_file($template)) {
                $templates[] = $template;
            }

            if ($name_decoded !== $object->post_name) {
                $templates[] = self::$pages_folder . "/single-{$object->post_type}-{$name_decoded}.php";
            }

            $templates[] = self::$pages_folder . "/single-{$object->post_type}-{$object->post_name}.php";
            $templates[] = self::$pages_folder . "/single-{$object->post_type}.php";
        }

        $templates[] = self::$pages_folder . "/single.php";

        $template = locate_template($templates);

        return $template;
    }

    /** Categories */
    public function rp_get_category_template()
    {
        $category = get_queried_object();
        $templates = [];

        if (!empty($category->slug)) {

            $slug_decoded = urldecode($category->slug);
            if ($slug_decoded !== $category->slug) {
                $templates[] = self::$pages_folder . "/category-{$slug_decoded}.php";
            }

            $templates[] = self::$pages_folder . "/category-{$category->slug}.php";
            $templates[] = self::$pages_folder . "/category-{$category->term_id}.php";
        }
        $templates[] = self::$pages_folder . "/category.php";
        $template = locate_template($templates);

        return $template;
        // return get_query_template( 'category', $templates );
    }

    /** Pages */
    public function rp_get_page_template()
    {
        $id = get_queried_object_id();
        $template = get_page_template_slug();
        $pagename = get_query_var('pagename');


        if (!$pagename && $id) {
            // If a static page is set as the front page, $pagename will not be set. Retrieve it from the queried object
            $post = get_queried_object();
            if ($post)
                $pagename = $post->post_name;
        }

        $templates = [];

        if(class_exists('ACF') ) {
            // get template acf 
            $acf_template_page = get_field("template_page");
            
            if ($acf_template_page) {
                $templates[] = self::$pages_template_folder . '/' . $acf_template_page . ".php";
            }
        }

        if ($template && validate_file($template) === 0) {
            $templates[] = self::$pages_folder . '/' . $template;
        }

        // if there's a custom template then still give that priority
        if ($pagename) {
            $templates[] = self::$pages_folder . "/page-$pagename.php";
        }

        // change the default search for the page-$slug template to use our directory
        // you could also look in the theme root directory either before or after this
        if ($id) {
            $templates[] = self::$pages_folder . "/page-$id.php";
        }

        $templates[] = self::$pages_folder . "/page.php";

        $template = locate_template($templates);

        return $template;
    }

    /** 404 */
    public function rp_get_404_template()
    {
        $templates[] = self::$pages_folder . "/404.php";
        $template = locate_template($templates);

        return $template;
    }
}
