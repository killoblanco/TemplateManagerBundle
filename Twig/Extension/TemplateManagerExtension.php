<?php

namespace killoblanco\TemplateManagerBundle\Twig\Extension;

use Twig_Environment;
use Twig_Extension_StringLoader;
use Twig_SimpleFunction;

class TemplateManagerExtension extends \Twig_Extension
{

    public function getFunctions()
    {
        return [
            new Twig_SimpleFunction('bindControl', [$this, 'bindControlFunction'], ['is_safe' => ['html']]),
            new Twig_SimpleFunction('discoverControls', [$this, 'discoverControlsFunction'], ['needs_environment' => true, 'is_safe' => ['html']]),
            new Twig_SimpleFunction('generateScriptHandlers', [$this, 'generateScriptHandlersFunction'], ['needs_environment' => true, 'is_safe' => ['html']]),
            new Twig_SimpleFunction('bindTarget', [$this, 'bindTargetFunction'], ['is_safe' => ['html']]),
            new Twig_SimpleFunction('templateManagerConfig', [$this, 'templateManagerConfigFunction'], ['is_safe' => ['html']]),
        ];
    }

    public function bindControlFunction($controlType, $controlValue, $attrs = null)
    {
        $control = $this->openControlTag($controlType);

        if ($attrs) {
            $control .= $this->expandAttrs($attrs);
        }

        $control .= ' v-model="' . $controlValue . '"';
        $control .= ' class="form-control" ';

        $control .= $this->closeControlTag($controlType);

        return $this->getBootstrap($control, $controlValue);
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
            case 'link':
            case 'url':
                $target = '<a v-bind:href="' . $controlValue . '" ';
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
            default:
                $target = '<p ';
                if ($attrs) {
                    $target .= $this->expandAttrs($attrs);
                }
                $target .= '>{{' . $controlValue . '}}</p>';
                return $target;
        }
    }

    public function templateManagerConfigFunction($app)
    {
        $app = "<script>new Vue({el: '#" . $app . "',data: {text: 'Sample Text',link: 'http://www.optimeconsulting.com/',img: 'https://placekitten.com/700/200',number: 1998,date: '2016-11-08',datetime: '2016-11-08T00:00',time: '14:18',email: 'kvasquez@optimeconsulting.com',tel: '+572572238',url: 'http://www.optimeconsulting.com/',textarea: 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. At consequuntur eaque facere nulla provident, quidem temporibus. Ad dicta dignissimos dolorum est fugiat ipsum, non possimus, similique vel vitae!',},})</script>";
        return $app;
    }

    public function discoverControlsFunction(Twig_Environment $twig, $template)
    {

        $controls = $this->convertToControls($template);

        $response = '<div class="page-heaer"><h4>Template Controllers</h4></div>';
        $response .= implode('', $controls);

        $response = $twig->createTemplate($response);

        return $response->render([]);

    }

    public function generateScriptHandlersFunction(Twig_Environment $twig, $app_name, $template){
        $response = "<script>new Vue({el: '".$app_name."' ,data: {";

        $handlers = $this->convertToControls($template);

        foreach ($handlers as $handler) {
            preg_match("/\'(?P<type>\w+)\'\,(\'|[[:blank:]])*\'(?P<name>\w+)\'/", $handler, $matches);
            $response .= $matches['name'].": '".$this->getDefaultValue($matches['type'])."', ";
        }

        $response .= "}, }) </script>";

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

    private function getDefaultValue($type)
    {
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
            'url' => 'http://www.optimeconsulting.com/',
            'textarea' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. At consequuntur eaque facere nulla provident, quidem temporibus. Ad dicta dignissimos dolorum est fugiat ipsum, non possimus, similique vel vitae!',
        ];

        return $defaults[$type];
    }
}