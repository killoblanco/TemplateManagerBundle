<?php

namespace killoblanco\TemplateManagerBundle\Twig\Extension;

use Twig_SimpleFunction;

class TemplateManagerExtension extends \Twig_Extension
{

    public function getFunctions()
    {
        return [
            new Twig_SimpleFunction('bindControl', [$this, 'bindControlFunction'], ['is_safe' => ['html']]),
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
                return '<textarea';
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
            $bootstrap .= '<label for="' . $name . '">' . $name . ':</label>';
        }

        $bootstrap .= $controller . '</div>';

        return $bootstrap;

    }

    private function expandAttrs($attrs)
    {
        if ($attrs) {
            $r = '';
            foreach ($attrs as $key => $value) {
                $r .= ' ' . $key . '="' . $value . '"';
            }
            return $r;
        } else {
            return '';
        }
    }
}