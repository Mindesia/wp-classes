<?php

namespace Mindesia\Twig {

    use Cocur\Slugify\Bridge\Twig\SlugifyExtension;
    use Cocur\Slugify\Slugify;
    use Timber\Twig_Function;
    use Twig\Environment;
    use Twig\Extension\StringLoaderExtension;
    use Twig\Extra\Intl\IntlExtension;

    class Twig
    {
        public function __construct()
        {
            add_filter('timber/twig', [$this, 'add_to_twig']);
        }
        
        /** To add my own functions to twig.
         *
         * @param string $twig get extension.
         */
        public function add_to_twig(Environment $twig)
        {
            // Extensions
            $twig->addExtension(new StringLoaderExtension());
            $twig->addExtension(new TwigDumper());
            $twig->addExtension(new SlugifyExtension(Slugify::create()));
            $twig->addExtension(new IntlExtension());

            // Filters
            // $twig->addFilter(new TwigFilter('my_foo', 'my_foo'));

            // Functions
            $twig->addFunction(new Twig_Function('is_local', "isLocal"));
            $twig->addFunction(new Twig_Function('dd', "dumpAndDie"));
            $twig->addFunction(new Twig_Function('goto', "goToUrl"));

            return $twig;
        }
    }
}

/**
 * Functions & filters declaration
 */

namespace {

    use Mindesia\WP_Class\Utils;


    function isLocal()
    {
        return Utils::is_local();
    }

    function goToUrl($url)
    {
        return "onclick=\"location.href='" . $url . "';\"";
    }

    function dumpAndDie($val)
    {
        return dd($val);
    }
}
