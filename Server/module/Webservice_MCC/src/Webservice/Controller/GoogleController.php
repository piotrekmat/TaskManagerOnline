<?php

namespace Webservice\Controller;

use Application\MccController;
use Template\Model\TemplateManager;
use Webservice\Form\Google\CategoryGoogleAssignForm;
use Webservice\Model\GoogleIntegrations;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class GoogleController extends MccController
{
    public function indexAction() {

    }

    public function getGoogleCategoriesAction() {
        return $this->getData('Webservice\Entity\Google\GoogleCategory');
    }

    public function googleMatchingAction() {
        $entityManager = $this->getEntityManager();
        $categoryRepository = $entityManager->getRepository('Product\Entity\Category');
        $googleCategoryRepository = $entityManager->getRepository('Webservice\Entity\Google\GoogleCategory');
        $ids = null;
        $search = $this->params()->fromQuery('search');
        if (!empty($search)) {
            if (isset($search['category'])) {
                $ids = $search['category'];
            }
        }
        $categories = $categoryRepository->getCategories(null, null, null, $ids);
        $categoryRoots = $categoryRepository->makeTree($categories);
        $data = $googleCategoryRepository->getGoogleMatchingGrid($categoryRoots, 0);

        return new JsonModel([
            'count' => count($data),
            'data'  => $data,
        ]);
    }

    public function assignGoogleCategoryAction() {
        $entityManager = $this->getEntityManager();
        $categoryId = $this->params('id');
        $category = $entityManager->getRepository('Product\Entity\Category')->find($categoryId);
        $form = new CategoryGoogleAssignForm($entityManager, $this);
        $form->bind($category);

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());
            if ($form->isValid()) {
                $entityManager->flush();

                return new JsonModel([
                    'result'   => true,
                    'msg'      => _t('Operacja została zakończona pomyślnie.'),
                    'callback' => 'Mcc.Grid.updateActiveGrid();',
                ]);
            }
            return new JsonModel([
                'result' => true,
                'msg'    => _t('Operacja nie została zakończona pomyślnie.'),
                'error'  => true,
            ]);
        }

        $this->setModal([
            'label'   => _t('Łączenie kategorii Google'),
            'buttons' => 'close, saveAndClose',
        ]);

        return new ViewModel([
            'form' => $form,
        ]);
    }

    public function addGoogleCategoryAction() {
        $entityManager = $this->getEntityManager();
        $googleCategory = new \Webservice\Entity\Google\GoogleCategory();
        $form = new \Webservice\Form\Google\GoogleCategoryForm($entityManager, $this);
        $form->setAttribute('action', $this->getCurrentUrl(true, true));
        $form->setAttribute('data-autocomplete-google-category', $this->url()->fromRoute('webservice/google', ["action" => "find-google-categories"]));
        $form->bind($googleCategory);

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());
            if ($form->isValid()) {
                $entityManager->persist($googleCategory);
                $entityManager->flush();
                return new JsonModel([
                    'result'   => true,
                    'msg'      => _t('Operacja została zakończona pomyślnie.'),
                    'callback' => 'Mcc.Grid.updateActiveGrid();',
                ]);
            }
            return new JsonModel([
                'result' => true,
                'msg'    => _t('Operacja nie została zakończona pomyślnie.'),
                'error'  => true,
            ]);
        }

        $this->setModal([
            'label'   => _t('Dodawanie kategorii Google'),
            'buttons' => 'close, saveAndClose',
        ]);

        return (new ViewModel([
            'form' => $form,
        ]))->setTemplate('webservice/google/google-category-form');
    }

    public function editGoogleCategoryAction() {
        $id = $this->params('id');
        $entityManager = $this->getEntityManager();
        $googleCategory = $entityManager->getRepository('Webservice\Entity\Google\GoogleCategory')->find($id);
        $form = new \Webservice\Form\Google\GoogleCategoryForm($entityManager, $this);
        $form->setAttribute('action', $this->getCurrentUrl(true, true));
        $form->setAttribute('data-autocomplete-google-category', $this->url()->fromRoute('webservice/google', ["action" => "find-google-categories"]));
        $form->bind($googleCategory);

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());
            if ($form->isValid()) {
                $entityManager->persist($googleCategory);
                $entityManager->flush();
                return new JsonModel([
                    'result'   => true,
                    'msg'      => _t('Operacja została zakończona pomyślnie.'),
                    'callback' => 'Mcc.Grid.updateActiveGrid();',
                ]);
            }
            return new JsonModel([
                'result' => true,
                'msg'    => _t('Operacja nie została zakończona pomyślnie.'),
                'error'  => true,
            ]);
        }

        $this->setModal([
            'label' => _t('Edycja kategorii Google'),
            'buttons' => 'close, save, saveAndClose',
        ]);

        return (new ViewModel([
            'form' => $form,
        ]))->setTemplate('webservice/google/google-category-form');
    }

    public function removeGoogleCategoryAction() {
        $entityManager = $this->getEntityManager();
        $repository = $entityManager->getRepository('Webservice\Entity\Google\GoogleCategory');
        return $this->simpleOperations([
            'modal' => [
                'label' => 'Usuwanie kategorii Google',
            ],
            'operation' => function($ids) use ($entityManager, $repository) {
                foreach ($ids as $id) {
                    $entity = $repository->find($id);
                    $entityManager->remove($entity);
                }
                $entityManager->flush();
            },
            'callback' => 'Mcc.Grid.updateActiveGrid();',
        ]);
    }

    public function findGoogleCategoriesAction() {
        $entityManager = $this->getEntityManager();
        $googleCategoryRepository = $entityManager->getRepository('Webservice\Entity\Google\GoogleCategory');
        $results = $googleCategoryRepository->findGoogleCategory($this->params()->fromQuery());
        return new JsonModel($results);
    }

    public function generateGoogleShoppingXMLAction() {
        $logger = $this->getLogger();
        $em = $this->getEntityManager();
        $result = $logger->log(
            function () use ($em) {
                $googleIntegrations = new GoogleIntegrations($em);
                $channel = $googleIntegrations->getDefaultChannel();
                $products = $googleIntegrations->getProductsToGoogleXML($channel['id']);
                $templateBody = file_get_contents(__DIR__ . '/../../../view/webservice/google/google-xml.twig');
                $description = trim(TemplateManager::getHTML($templateBody, [
                    'products' => $products,
                    'service' => $channel['service']
                ]));

                $file = __DIR__.'/../../../../../../../www/lapado/data/GoogleXML/google-products.xml';

                file_put_contents($file, $description);
            },
            [
                'type' => 'GoogleShopping',
                'subtype' => 'generateGoogleShoppingXML',
                'concurrentAllowed' => false,
            ]
        );
    }
}