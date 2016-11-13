<?php
namespace Webservice\Form\Google;

use Doctrine\ORM\EntityManager;
use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;

class GoogleCategoryForm extends Form implements InputFilterProviderInterface
{
    protected $entityManager;
    protected $controller;

    public function __construct(EntityManager $entityManager, $controller)
    {
        parent::__construct('search');
        $this->entityManager = $entityManager;
        $this->controller = $controller;

        $this->setHydrator(new \Application\Hydrator\MagicHydrator($entityManager, 'Webservice\Entity\Google\GoogleCategory'))
            ->setObject(new \Webservice\Entity\Google\GoogleCategory($entityManager));

        $this->setAttribute('method', 'post');

        $this->add([
            'name'	=> 'numberGoogleCategory',
            'type'	=> 'text',
            'options' => [
                'label' => _t('Numer kategorii Google'),
            ],
            'attributes' => [
                'class' => 'input-medium',
                'data-rule-required' => true,
            ],
        ]);

        $this->add([
            'name' => 'parent',
            'type' => 'Application\Form\DoctrineHidden',
            'options' => [
                'label' => _t('Rodzic kategorii Google'),
                'object_manager' => $entityManager,
                'target_class'   => 'Webservice\Entity\Google\GoogleCategory',
                'property'       => 'id',
                'find_method'    => [
                    'name'   => 'findBy',
                    'params' => [
                        'criteria' => ['id' => null],
                    ]
                ],
            ],
            'attributes' => [
                'class'                    => 'select2 input-large',
                'data-autocomplete'        => 'google-category',
                'data-autocomplete-minlen' => 2,
                'data-allow-clear'         => true,
                'data-placeholder'         => _t('Wybierz kategorię'),
            ],
        ]);

        $this->add([
            'name'	=> 'nameGoogleCategory',
            'type'	=> 'text',
            'options' => [
                'label' => _t('Nazwa kategorii'),
            ],
            'attributes' => [
                'class' => 'input-medium',
                'data-rule-required' => true,
            ],
        ]);
    }

    public function getInputFilterSpecification() {
        return [
            'numberGoogleCategory' => [
                'required' => true,
            ],
            'nameGoogleCategory' => [
                'required' => true,
            ],
        ];
    }
}
