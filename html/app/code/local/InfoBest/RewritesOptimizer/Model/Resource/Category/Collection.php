<?php
/**
 * @version         0.1.0
 * @license         http://opensource.org/licenses/MIT - MIT License
 */

class InfoBest_RewritesOptimizer_Model_Resource_Category_Collection extends Mage_Catalog_Model_Resource_Category_Collection {

    /**
     * Joins url rewrite rules to collection
     * @return Mage_Catalog_Model_Resource_Category_Collection
     */
    public function joinUrlRewrite() {
        // $storeId = Mage::app()->getStore()->getId();
        $storeId = Mage::app()->getWebsite()->getDefaultStore()->getId();
        $this->joinTable('core/url_rewrite', 'category_id=entity_id', array('request_path'), "{{table}}.is_system=1" . " AND {{table}}.product_id IS NULL" . " AND {{table}}.store_id='{$storeId}'" . " AND id_path LIKE 'category/%'", 'left');
        return $this;
    }

}