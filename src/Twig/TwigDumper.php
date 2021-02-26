<?php

namespace Mindesia\Twig {

    use Twig\Extension\AbstractExtension;
    use Twig\TwigFunction;

    class TwigDumper extends AbstractExtension
    {
        public function getFunctions()
        {
            // dump is safe if var_dump is overridden by xdebug
            $isDumpOutputHtmlSafe = \extension_loaded('xdebug')
                // false means that it was not set (and the default is on) or it explicitly enabled
                && (false === ini_get('xdebug.overload_var_dump') || ini_get('xdebug.overload_var_dump'))
                // false means that it was not set (and the default is on) or it explicitly enabled
                // xdebug.overload_var_dump produces HTML only when html_errors is also enabled
                && (false === ini_get('html_errors') || ini_get('html_errors'))
                || 'cli' === \PHP_SAPI;

            return [
                new TwigFunction('dump', 'symfony_dumper', ['is_safe' => $isDumpOutputHtmlSafe ? ['html'] : [], 'needs_context' => true, 'needs_environment' => true, 'is_variadic' => true]),
            ];
        }
    }

    class_alias('Mindesia\Twig\TwigDumper', 'Twig_Extension_Dumper');
}

namespace {

    use Twig\Environment;
    use Twig\Template;
    use Twig\TemplateWrapper;

    function symfony_dumper(Environment $env, $context, ...$vars)
    {
        if (!$env->isDebug()) {
            return;
        }

        ob_start();

        if (!$vars) {
            $vars = [];
            foreach ($context as $key => $value) {
                if (!$value instanceof Template && !$value instanceof TemplateWrapper) {
                    $vars[$key] = $value;
                }
            }

            dump($vars);
        } else {
            dump(...$vars);
        }

        return ob_get_clean();
    }
}
