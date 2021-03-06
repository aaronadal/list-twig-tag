<?php

namespace Aaronadal\Tests\Twig;


use Aaronadal\TwigListLoop\Twig\TwigExtension;
use PHPUnit_Framework_TestCase;
use Twig\Environment;
use Twig\Extension\StringLoaderExtension;
use Twig\Loader\ArrayLoader;

/**
 * @author Aarón Nadal <aaronadal.dev@gmail.com>
 */
class ListTagIfTest extends PHPUnit_Framework_TestCase
{

    const ENABLE_CACHE = false;

    /**
     * @var Environment
     */
    private $twig;

    private function getListTemplate()
    {
        return
            '
            {%- if list | length -%}
                {%- for item in list -%}
                    {{- loop.index0 ? separator -}}{{- item -}}
                {%- endfor -%}
            {%- else -%}
                {{- else -}}{{- separator -}}
            {%- endif -%}
            ';
    }

    private function getTemplate()
    {
        return
            '
            {%- set template = template_from_string("' . $this->getListTemplate() . '") -%}
            {%- list num in numbers if num is odd using template with args -%}
                {{- num -}}
            {%- else -%}
                {{- "None" -}}
            {%- endlist -%}
            ';
    }

    public function setUp()
    {
        $this->twig = new Environment(new ArrayLoader(array()));
        $this->twig->addExtension(new StringLoaderExtension());
        $this->twig->addExtension(new TwigExtension());

        if(self::ENABLE_CACHE) {
            $this->twig->setCache(__DIR__ . '/../cache');
        }
    }

    public function testListTagIfInBody()
    {
        $template = $this->twig->createTemplate($this->getTemplate());
        $render = $template->render(array('numbers' => array(1, 2, 3, 4), 'args' => array('separator' => '-')));

        $this->assertSame('1-3', $render);
    }

    public function testListTagIfWithNonMatchList()
    {
        $template = $this->twig->createTemplate($this->getTemplate());
        $render = $template->render(array('numbers' => array(0, 2, 4), 'args' => array('separator' => '!')));

        $this->assertSame('None!', $render);
    }

    public function testListTagIfWithEmptyList()
    {
        $template = $this->twig->createTemplate($this->getTemplate());
        $render = $template->render(array('numbers' => array(), 'args' => array('separator' => '!')));

        $this->assertSame('None!', $render);
    }

}
