<?php
/**
 * @version 		0.1.0
 * @license         http://opensource.org/licenses/MIT - MIT License
 */
 
class InfoBest_RewritesOptimizer_Model_Url extends Mage_Catalog_Model_Url {
 
    /**
    * Refresh all product rewrites in accordance to extension config + ignore store code
    *
    * @param int $storeId
    * @return Mage_Catalog_Model_Url
    */
    public function refreshProductRewrites($storeId) {
        $this->_categories = array();
        $storeRootCategoryId = $this->getStores($storeId)->getRootCategoryId();
        $this->_categories[$storeRootCategoryId] = $this->getResource()->getCategory($storeRootCategoryId, $storeId);

        $lastEntityId = 0;
        $process = true;

        $enableOptimisation = Mage::getStoreConfigFlag('dev/index/onoff_toggle');
        $excludeProductsDisabled = Mage::getStoreConfigFlag('dev/index/disabled_products_toggle');
        $excludeProductsNotVisible = Mage::getStoreConfigFlag('dev/index/notvisible_products_toggle');
        $useCategoriesInUrl = Mage::getStoreConfig('catalog/seo/product_use_categories');

        while ($process == true) {
            $products = $this->getResource()->getProductsByStore($storeId, $lastEntityId);
            if (!$products) {
                $process = false;
                break;
            }

            $this->_rewrites = array();
            $this->_rewrites = $this->getResource()->prepareRewrites($storeId, false, array_keys($products));

            $loadCategories = array();
            foreach ($products as $product) {
                foreach ($product->getCategoryIds() as $categoryId) {
                    if (!isset($this->_categories[$categoryId])) {
                        $loadCategories[$categoryId] = $categoryId;
                    }
                }
            }

            if ($loadCategories) {
                foreach ($this->getResource()->getCategories($loadCategories, $storeId) as $category) {
                    $this->_categories[$category->getId()] = $category;
                }
            }
            
            
            foreach ($products as $product) {
           	 	if ($enableOptimisation && $excludeProductsDisabled && $product->getData("status") == 2) {
	           	 	continue;
           	 	}
           	 	if ($enableOptimisation && $excludeProductsNotVisible && $product->getData("visibility") == 1) {
	           	 	continue;
           	 	}            	

           	 	// always reindex short url
                $this->_refreshProductRewrite($product, $this->_categories[$storeRootCategoryId]);
            	if ($useCategoriesInUrl != "0" || !$enableOptimisation) {
	            	foreach ($product->getCategoryIds() as $categoryId) {
                    	if ($categoryId != $storeRootCategoryId && isset($this->_categories[$categoryId])) {
                        	$this->_refreshProductRewrite($product, $this->_categories[$categoryId]);
                        }
                    }
            	}
            }

            unset($products);
            $this->_rewrites = array();
        }

        $this->_categories = array();
        return $this;
    }


    /**
    * Refresh all rewrite URLs
    * Overrides original by using only the default store code when refreshing URLs
    * Used to make full reindexing of url rewrites
    *
    * @param int $storeId
    * @return Mage_Catalog_Model_Url
    */
    public function refreshRewrites($storeId = null) {
        if (is_null($storeId)) {
            foreach (Mage::app()->getWebsites() as $website) {
                if ($website->getDefaultStore())
                    $this->refreshRewrites($website->getDefaultStore()->getId());
            }
            return $this;
        }
        return parent::refreshRewrites($storeId);
    }

    /**
    * Refresh category rewrite URLs
    * Overrides original by using only the default store code when refreshing URLs
    *
    * @param Varien_Object $category
    * @param string $parentPath
    * @param bool $refreshProducts
    * @return Mage_Catalog_Model_Url
    */
    public function refreshCategoryRewrite($categoryId, $storeId = null, $refreshProducts = true) {
        if (is_null($storeId)) {
            foreach (Mage::app()->getWebsites() as $website) {
                if ($website->getDefaultStore())
                    $this->refreshCategoryRewrite($categoryId, $website->getDefaultStore()->getId(), $refreshProducts);
            }
            return $this;
        }
        return parent::refreshCategoryRewrite($categoryId, $storeId, $refreshProducts);
    }


    /**
    * Refresh product rewrite URL
    * Overrides original method by using only the default store code when refreshing URL
    *
    * @param Varien_Object $product
    * @param Varien_Object $category
    * @return Mage_Catalog_Model_Url
    */
    public function refreshProductRewrite($productId, $storeId = null) {
        if (is_null($storeId)) {
            foreach (Mage::app()->getWebsites() as $website) {
                if ($website->getDefaultStore())
                    $this->refreshProductRewrite($productId, $website->getDefaultStore()->getId());
            }
            return $this;
        }
        return parent::refreshProductRewrite($productId, $storeId);
    }


    /**
    * Deletes old rewrites for default store
    *
    * @param int $storeId
    * @return Mage_Catalog_Model_Url
    */
    public function clearStoreInvalidRewrites($storeId = null) {
        if (is_null($storeId)) {
            foreach (Mage::app()->getWebsites() as $website) {
                if ($website->getDefaultStore())
                    $this->clearStoreInvalidRewrites($website->getDefaultStore()->getId());
            }
            return $this;
        }
        return parent::clearStoreInvalidRewrites($storeId);
    }


    // if used, this would remove category path from product URLs; there's a switch in magento for that; 
    // but might implement it so that the user has one extension config area that handles all these settings
    // protected function _refreshProductRewrite(Varien_Object $product, Varien_Object $category) {
        // if ($this->getStoreRootCategory($category->getStoreId())->getId() != $category->getId()) {
            // return $this;
        // }
        // return parent::_refreshProductRewrite($product, $category);
    // }

}