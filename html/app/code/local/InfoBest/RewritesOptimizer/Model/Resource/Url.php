<?php
/**
 * @version 		0.1.0
 * @license         http://opensource.org/licenses/MIT - MIT License
 */

class InfoBest_RewritesOptimizer_Model_Resource_Url extends Mage_Catalog_Model_Resource_Url {
    
    /**
    * Retrieve product data objects
    *
    * @param int|array $productIds
    * @param int $storeId
    * @param int $entityId
    * @param int $lastEntityId
    * @return array
    */
    protected function _getProducts($productIds = null, $storeId, $entityId = 0, &$lastEntityId) {
        $products = array();
        $websiteId = Mage::app()->getStore($storeId)->getWebsiteId();
        if (!is_null($productIds)) {
            if (!is_array($productIds)) {
                $productIds = array($productIds);
            }
        }
        $select = $this->_getWriteAdapter()->select()
            ->useStraightJoin(true)
            ->from(array('e' => $this->getTable('catalog/product')), array('entity_id'))
            ->join(
                array('w' => $this->getTable('catalog/product_website')),
                $this->_getWriteAdapter()->quoteInto('e.entity_id=w.product_id AND w.website_id=?', $websiteId),
                array()
            )
            ->where('e.entity_id>?', $entityId)
            ->order('e.entity_id')
            ->limit($this->_productLimit);
        if (!is_null($productIds)) {
            $select->where('e.entity_id IN(?)', $productIds);
        }

        $query = $this->_getWriteAdapter()->query($select);
        while ($row = $query->fetch()) {
            $product = new Varien_Object($row);
            $product->setIdFieldName('entity_id');
            $product->setCategoryIds(array());
            $product->setStoreId($storeId);
            $products[$product->getId()] = $product;
            $lastEntityId = $product->getId();
        }

        unset($query);

        if ($products) {
            $select = $this->_getReadAdapter()->select()
                ->from(
                    $this->getTable('catalog/category_product'),
                    array('product_id', 'category_id'))
                ->where('product_id IN(?)', array_keys($products));
            $categories = $this->_getReadAdapter()->fetchAll($select);
            foreach ($categories as $category) {
                $productId = $category['product_id'];
                $categoryIds = $products[$productId]->getCategoryIds();
                $categoryIds[] = $category['category_id'];
                $products[$productId]->setCategoryIds($categoryIds);
            }

            foreach (array('name', 'url_key', 'url_path','visibility',"status") as $attributeCode) {
                $attributes = $this->_getProductAttribute($attributeCode, array_keys($products), $storeId);
                foreach ($attributes as $productId => $attributeValue) {
                    $products[$productId]->setData($attributeCode, $attributeValue);
                }
            }
        }

        return $products;
    }

}