<?php
namespace Webservice\Form\Google;

use Doctrine\ORM\EntityManager;
use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;

class CategoryGoogleAssignForm extends Form implements InputFilterProviderInterface
{
    protected $entityManager;
    protected $controller;

    public function __construct(EntityManager $entityManager, $controller)
    {
        parent::__construct('search');
        $this->entityManager = $entityManager;
        $this->controller = $controller;

        $this->setHydrator(new \Application\Hydrator\MagicHydrator($entityManager, 'Product\Entity\Category'))
            ->setObject(new \Product\Entity\Category());

        $this->setAttribute('method', 'post')
            ->setAttribute('action', $controller->getCurrentUrl(true, true))
            ->setAttribute('data-autocomplete-category', $controller->url()->fromRoute('product/category', ['action' => 'findCategory']))
            ->setAttribute('data-autocomplete-google-category', $controller->url()->fromRoute('webservice/google', ['action' => 'findGoogleCategories']))
        ;

        $this->add([
            'name' => 'id',
            'type' => 'Application\Form\DoctrineHidden',
            'options' => [
                'label' => _t('Kategoria'),
                'object_manager' => $entityManager,
                'target_class'   => 'Product\Entity\Category',
            ],
            'attributes' => [
                'class'                    => 'select2 input-large',
                'readonly'                 => true,
                'data-autocomplete'        => 'category',
                'data-autocomplete-minlen' => 2,
                'data-allow-clear'         => true,
                'data-placeholder'         => _t('Wybierz kategorię'),
            ],
        ]);

        $this->add([
            'name' => 'googleCategory',
            'type' => 'Application\Form\DoctrineHidden',
            'options' => [
                'label' => _t('Kategoria Google'),
                'object_manager' => $entityManager,
                'target_class'   => 'Webservice\Entity\Google\GoogleCategory',
            ],
            'attributes' => [
                'class'                    => 'select2 input-large',
                'data-autocomplete'        => 'google-category',
                'data-autocomplete-minlen' => 2,
                'data-allow-clear'         => true,
                'data-placeholder'         => _t('Wybierz kategorię'),
            ],
        ]);
    }

    public function getInputFilterSpecification() {
        return [
            'id' => [
                'required' => false,
            ],
            'googleCategory' => [
                'required' => false,
            ],
        ];
    }
}
