<?php

namespace Webservice\Model;

class GoogleIntegrations extends \SoapClient
{

    protected $entityManager;

    /**
     * GoogleIntegrations constructor.
     *
     * @param mixed $entityManager
     */
    public function __construct($entityManager) {
        $this->entityManager = $entityManager;
    }

    public function getDefaultChannel() {
        $connection = $this->entityManager->getConnection();
            $selectClause = "
            SELECT sc.id AS id, ss.service AS service
            FROM system.channels sc
            JOIN system.services ss ON sc.service_id = ss.id
            WHERE (sc.is_default = true)
		";

        $statement = $connection->prepare($selectClause);
        $statement->execute();
        $result = $statement->fetch();

        return $result;
    }

    public function getProductsToGoogleXML($channel) {
        $connection = $this->entityManager->getConnection();
        $selectClause = "
			SELECT
			  prod.product_id AS id,
			  prod.product_name AS name,
			  prod.product_producer AS brand,
			  substring(prod.product_long_descr from 0 for 5000) AS html,
			  prod.product_photo AS image,
			  links.link_value AS link,
			  spc.presale_allowed AS presale_allowed,
			  spc.presale_stock AS presale_stock,
			  spc.stock AS stock,
			  sp.sellprice AS price,
			  pean.pe_ean AS gtin,
			  ccat.category_name AS product_type,
              gc.numbergooglecategory AS google_product_category
			FROM cache.products_".$channel." AS prod
			LEFT JOIN cache.links_de_de AS links ON prod.product_id = links.link_id_object
			LEFT JOIN cache.product_versions_".$channel." AS versions ON prod.product_id = versions.pv_price
			LEFT JOIN products.product_versions pv ON (prod.product_id = pv.pv_id_product)
			LEFT JOIN warehouses.stocks_per_channel spc ON (pv.pv_id = spc.version_id)
			LEFT JOIN cache_dtre.sale_price sp ON (sp.productversion_id = pv.pv_id)
            JOIN products.product_eans pean ON (prod.product_id = pean.productversion_id)
			LEFT JOIN cache.categories_".$channel." ccat ON (prod.product_id_category = ccat.category_id)
			LEFT JOIN products.categories cat ON (prod.product_id_category = cat.category_id)
            LEFT JOIN products.google_categories gc ON (cat.category_id = gc.id)
			WHERE (spc.channel_id = ".$channel.") AND
				  (sp.channel_id = ".$channel.") AND
				  (prod.product_photo IS NOT NULL) AND
				  (prod.product_long_descr IS NOT NULL) AND
				  (pean.is_default = true)
		";

        $statement = $connection->prepare($selectClause);
        $statement->execute();
        $items = $statement->fetchAll();
        return $items;
    }
}
?>