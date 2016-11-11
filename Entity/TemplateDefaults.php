<?php

namespace killoblanco\TemplateManagerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TemplateDefaults
 *
 * @ORM\Table(name="tm_template_defaults")
 * @ORM\Entity(repositoryClass="killoblanco\TemplateManagerBundle\Repository\TemplateDefaultsRepository")
 */
class TemplateDefaults
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Template", inversedBy="templateDefaults")
     * @ORM\JoinColumn(name="template", referencedColumnName="id")
     */
    private $template;

    /**
     * @var string
     *
     * @ORM\Column(name="data", type="text")
     */
    private $data;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * @param mixed $template
     */
    public function setTemplate($template)
    {
        $this->template = $template;
    }

    /**
     * Set data
     *
     * @param string $data
     * @return TemplateDefaults
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Get data
     *
     * @return string 
     */
    public function getData()
    {
        return $this->data;
    }
}
