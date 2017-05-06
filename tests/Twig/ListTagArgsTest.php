<?php

namespace Aaronadal\Tests\Twig;


use Aaronadal\ListTwigTag\Twig\TwigExtension;

/**
 * @author Aarón Nadal <aaronadal.dev@gmail.com>
 */
class ListTagArgsTest extends \PHPUnit_Framework_TestCase
{

    const ENABLE_CACHE = false;

    /**
     * @var \Twig_Environment
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
            {%- list num in numbers using template with args -%}
                {{- num -}}
            {%- else -%}
                {{- "None" -}}
            {%- endlist -%}
            ';
    }

    public function setUp()
    {
        $this->twig = new \Twig_Environment(new \Twig_Loader_Array(array()));
        $this->twig->addExtension(new \Twig_Extension_StringLoader());
        $this->twig->addExtension(new TwigExtension());

        if(self::ENABLE_CACHE) {
            $this->twig->setCache(__DIR__ . '/../cache');
        }
    }

    public function testListTagArgsInBody()
    {
        $template = $this->twig->createTemplate($this->getTemplate());
        $render = $template->render(array('numbers' => array(1, 2, 3), 'args' => array('separator' => '-')));

        $this->assertSame('1-2-3', $render);
    }

    public function testListTagArgsInElse()
    {
        $template = $this->twig->createTemplate($this->getTemplate());
        $render = $template->render(array('numbers' => array(), 'args' => array('separator' => '!')));

        $this->assertSame('None!', $render);
    }

}