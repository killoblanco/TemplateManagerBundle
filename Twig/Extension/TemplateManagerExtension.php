<?php

namespace killoblanco\TemplateManagerBundle\Twig\Extension;

use killoblanco\TemplateManagerBundle\Entity\TemplateDefaults;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Twig_Environment;
use Twig_Extension_StringLoader;
use Twig_SimpleFunction;

class TemplateManagerExtension extends \Twig_Extension
{
    protected $doctrine;

    /**
     * TemplateManagerExtension constructor.
     * @param $doctrine
     */
    public function __construct(RegistryInterface $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function getFunctions()
    {
        return [
            new Twig_SimpleFunction('bindControl', [$this, 'bindControlFunction'], ['is_safe' => ['html']]),
            new Twig_SimpleFunction('discoverControls', [$this, 'discoverControlsFunction'], ['needs_environment' => true, 'is_safe' => ['html']]),
            new Twig_SimpleFunction('generateScriptHandlers', [$this, 'generateScriptHandlersFunction'], ['needs_environment' => true, 'is_safe' => ['html']]),
            new Twig_SimpleFunction('bindTarget', [$this, 'bindTargetFunction'], ['is_safe' => ['html']]),
        ];
    }

    public function bindControlFunction($controlType, $controlValue, $attrs = null)
    {
        if ($controlType == 'url') {
            $control = $this->openControlTag('url');
            $control .= ' name="' . $controlValue . '[href]"';
            if ($attrs) {
                $control .= $this->expandAttrs($attrs);
            }
            $control .= ' v-model="' . $controlValue . '.href" class="form-control" >';
            $control .= $this->openControlTag('text');
            $control .= ' name="' . $controlValue . '[text]"';
            if ($attrs) {
                $control .= $this->expandAttrs($attrs);
            }
            $control .= ' v-model="' . $controlValue . '.text" class="form-control" >';

            return $this->getBootstrap($control, $controlValue);
        } else if ($controlType == 'img_link') {
            $control = $this->openControlTag('url');
            $control .= ' name="' . $controlValue . '[href]"';
            if ($attrs) {
                $control .= $this->expandAttrs($attrs);
            }
            $control .= ' v-model="' . $controlValue . '.href" class="form-control" >';
            $control .= $this->openControlTag('img');
            $control .= ' name="' . $controlValue . '[src]"';
            if ($attrs) {
                $control .= $this->expandAttrs($attrs);
            }
            $control .= ' v-model="' . $controlValue . '.src" class="form-control" >';

            return $this->getBootstrap($control, $controlValue);

        } else {
            $control = $this->openControlTag($controlType);

            $control .= ' name="' . $controlValue . '" ';

            if ($attrs) {
                $control .= $this->expandAttrs($attrs);
            }

            $control .= ' v-model="' . $controlValue . '"';
            $control .= ' class="form-control" ';

            $control .= $this->closeControlTag($controlType);

            return $this->getBootstrap($control, $controlValue);
        }
    }

    public function bindTargetFunction($controlType, $controlValue, $attrs = null)
    {
        switch ($controlType) {
            case 'tel':
                $target = '<a :href="`tel:${' . $controlValue . '}`" ';
                if ($attrs) {
                    $target .= $this->expandAttrs($attrs);
                }
                $target .= '>{{' . $controlValue . '}}</a>';
                return $target;
            case 'email':
                $target = '<a :href="`mailto:${' . $controlValue . '}`" ';
                if ($attrs) {
                    $target .= $this->expandAttrs($attrs);
                }
                $target .= '>{{' . $controlValue . '}}</a>';
                return $target;
            case 'url':
                $target = '<a target="_blank" v-bind:href="' . $controlValue . '.href" ';
                if ($attrs) {
                    $target .= $this->expandAttrs($attrs);
                }
                $target .= '>{{' . $controlValue . '.text}}</a>';
                return $target;
            case 'link':
                $target = '<a target="_blank" v-bind:href="' . $controlValue . '" ';
                if ($attrs) {
                    $target .= $this->expandAttrs($attrs);
                }
                $target .= '>{{' . $controlValue . '}}</a>';
                return $target;
            case 'img':
                $target = '<img v-bind:src="' . $controlValue . '" ';
                $target .= $this->expandAttrs($attrs);
                $target .= '>';
                return $target;
            case 'img_link':
                $target = '<a target="_blank" v-bind:href="' . $controlValue . '.href" ';
                $target .= '><img v-bind:src="' . $controlValue . '.src" ';
                if ($attrs) {
                    $target .= $this->expandAttrs($attrs);
                }
                $target .= '></a>';
                return $target;
            default:
                return '{{'.$controlValue.'}}';
                // Todo: revisar esto
                //$target = '<p ';
                //if ($attrs) {
                //    $target .= $this->expandAttrs($attrs);
                //}
                //$target .= '>{{' . $controlValue . '}}</p>';
                //return $target;
        }
    }

    public function discoverControlsFunction(Twig_Environment $twig, $template)
    {
        $em = $this->doctrine;

        $template = $em->getRepository('TemplateManagerBundle:Template')
            ->find($template);

        $controls = $this->convertToControls($template->getBase());

        $response = '<div class="page-heaer"><h4>Template Controllers</h4></div>';
        $response .= implode('', $controls);

        $response = $twig->createTemplate($response);

        return $response->render([]);

    }

    public function generateScriptHandlersFunction(Twig_Environment $twig, $app_name, $template)
    {
        $em = $this->doctrine;

        $response = "<script>new Vue({el: '" . $app_name . "' ,data: ";

        $template = $em->getRepository('TemplateManagerBundle:Template')
            ->find($template);

        $response .= $this->getDefaultValue($template);

        $response .= ", }) </script>";

        $response = $twig->createTemplate($response);
        return $response->render([]);

    }


    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'template_manager';
    }

    private function openControlTag($type)
    {
        switch ($type) {
            case 'textarea':
                return '<textarea rows= 5 style="max-width:100%;"';
            case 'tel':
                return '<input type="tel"';
            case 'email':
                return '<input type="email"';
            case 'time':
                return '<input type="time"';
            case 'datetime':
                return '<input type="datetime-local"';
            case 'date':
                return '<input type="date"';
            case 'url':
                return '<input type="url"';
            case 'number':
            case 'num':
                return '<input type="number"';
            default:
                return '<input type="text"';

        }
    }

    private function closeControlTag($type)
    {
        switch ($type) {
            case 'textarea':
                return '></textarea>';
            default:
                return ' >';
                break;

        }
    }

    private function getBootstrap($controller, $name)
    {
        $bootstrap = '<div class="form-group">';

        if ($name) {
            $bootstrap .= '<label for="' . $name . '">' . ucfirst($name) . ':</label>';
        }

        $bootstrap .= $controller . '</div>';

        return $bootstrap;

    }

    private function expandAttrs($attrs)
    {
        if (is_array($attrs)) {
            $r = '';
            foreach ($attrs as $key => $value) {
                $r .= ' ' . $key . '="' . $value . '"';
            }
            return $r;
        } else {
            return '';
        }
    }

    private function convertToControls($targets)
    {
        preg_match_all("/\{{2}[\s\w]+(?:Target).+\}{2}/", $targets, $matches);
        $response = [];
        foreach ($matches[0] as $match) {
            array_push(
                $response,
                preg_replace(["/(?:Target)/", "/\,\s\{.+\}{2}/"], ["Control", ") }}"], $match)
            );
        }
        return $response;
    }

    private function getDefaultValue($template)
    {
        $em = $this->doctrine;

        $defaults = $em->getRepository(TemplateDefaults::class)
            ->findBy(['template' => $template]);

        if ($defaults) {
            return $defaults[0]->getData();
        } else {

            $controls = $this->convertToControls($template->getBase());

            $defaults = [
                'text' => 'Sample Text',
                'link' => 'http://www.optimeconsulting.com/',
                'img' => 'https://placekitten.com/700/200',
                'number' => 1998,
                'date' => '2016-11-08',
                'datetime' => '2016-11-08T00:00',
                'time' => '14:18',
                'email' => 'kvasquez@optimeconsulting.com',
                'tel' => '+572572238',
                'url' => [
                    'href' => 'http://www.optimeconsulting.com/',
                    'text' => 'Sample Text',
                ],
                'img_link' => [
                    'href' => 'http://www.optimeconsulting.com/',
                    'src' => 'https://placekitten.com/700/200',
                ],
                'textarea' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. At consequuntur eaque facere nulla provident, quidem temporibus. Ad dicta dignissimos dolorum est fugiat ipsum, non possimus, similique vel vitae!',
            ];

            $response = '{';

            foreach ($controls as $control) {
                preg_match("/\'(?P<type>\w+)\'\,(\'|[[:blank:]])*\'(?P<name>\w+)\'/", $control, $matches);
                if ($matches['type'] == 'url' || $matches['type'] == 'img_link') {
                    $response .= $matches['name'] . ": " . json_encode($defaults[$matches['type']]) . ", ";
                } else {
                    $response .= $matches['name'] . ": '" . $defaults[$matches['type']] . "', ";
                }
            }

            $response .= '}';

            return $response;
        }


    }
}