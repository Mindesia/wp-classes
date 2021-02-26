<?php

namespace Mindesia\WP_Class;

use Timber\Timber;

class Utils
{
    public static function get_current_user_roles()
    {
        if (is_user_logged_in()) {

            $user = wp_get_current_user();
            $roles = $user->roles;
            return $roles;
            // Use this to return a single value
            // return $roles[0];
        } else {
            return [];
        }
    }

    public static function user_has_role($role)
    {
        $user_roles = self::get_current_user_roles();

        if (in_array($role, $user_roles)) {
            return true;
        } else {
            return false;
        }
    }

    public static function is_local($whitelist = ['127.0.0.1', '::1'])
    {
        return in_array($_SERVER['REMOTE_ADDR'], $whitelist);
    }

    public static function decamelize($string)
    {
        return strtolower(preg_replace(['/([a-z\d])([A-Z])/', '/([^_])([A-Z][a-z])/'], '$1_$2', $string));
    }

    public static function default_template($file)
    {
        return str_replace('.php', '', basename($file));
    }

    public static function body_class(string $classes = "", $post = null)
    {
        if ($post == null) {   
            return (Timber::get_post())->slug . ' ' . $classes;
        } else {
            return $post->slug . ' ' . $classes;
        }
    }
}
