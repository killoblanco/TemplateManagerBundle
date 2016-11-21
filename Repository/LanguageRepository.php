<?php

namespace killoblanco\TemplateManagerBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * LanguageRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class LanguageRepository extends EntityRepository
{
    /**
     * @return array
     */
    public function findAllActives()
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT l FROM TemplateManagerBundle:Language l WHERE l.active = 1'
            )
            ->getResult();

    }
}