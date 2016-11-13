<?php

namespace Webservice\Repository\Google;

use Application\Repository\MccEntityRepository;

class GoogleCategoryRepository extends MccEntityRepository
{
    public function getGoogleMatchingGrid($roots, $level = 0) {
        $result = [];
        foreach ($roots as $key => $row) {
            $sort[$key] = $row->sort;
            $name[$key] = count($row->translations) ? $row->translations[0]->name : '---';
        }
        array_multisort($name, SORT_ASC, SORT_STRING, $roots);

        foreach ($roots as $root) {
            $tempResult = [
                'name'            => count($root->translations) ? $root->translations->first()->name : '---',
                'id'              => $root->id,
                'lvl'             => $level,
                '_parent'         => isset($root->parent) ? $root->parent->id : null,
                '_children'       => (count($root->children) > 0),
                'googleNumber'        => ($root->googleCategory) ? $root->googleCategory->numberGoogleCategory : null,
                'googleName'      => ($root->googleCategory) ? $root->googleCategory->nameGoogleCategory : null,
            ];

            $result[] = $tempResult;
            if (count($root->children)) {
                $childrenResults = $this->getGoogleMatchingGrid($root->children, $level+1);
                foreach ($childrenResults as $childResult) {
                    $result[] = $childResult;
                }
            }
        }
        return $result;
    }

    public function findEditGoogleCategory($id)
    {
        $qb = $this->_em->createQueryBuilder()
            ->select('gc')
            ->from('Webservice\Entity\Google\GoogleCategory', 'gc', 'gc.id')
            ->where('gc.id = :id')
            ->setParameter('id', $id);

        $googleCategories = $qb->getQuery()->getSingleResult();
        return $googleCategories;
    }

    public function findGoogleCategory($parameters)
    {
        if (!empty($parameters['id'])) {
            $googleCategory = $this->find($parameters['id']);
            return [
                'id' => $googleCategory->id,
                'name' => $googleCategory->nameGoogleCategory
            ];
        } else {
            $results = $this->_em->createQueryBuilder()
                ->select('ic.id', 'ic.nameGoogleCategory name')
                ->from('Webservice\Entity\Google\GoogleCategory', 'ic')
                ->where('ic.numberGoogleCategory LIKE :query')
                ->orWhere(('ic.nameGoogleCategory LIKE :query'))
                ->setParameter('query', '%' . $parameters['q'] . '%')
                ->getQuery()->getArrayResult();
            return [
                'total' => count($results),
                'results' => $results
            ];
        }
    }
}