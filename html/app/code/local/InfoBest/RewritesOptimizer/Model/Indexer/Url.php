<?php
/**
 * @version 		0.1.0
 * @license         http://opensource.org/licenses/MIT - MIT License
 */

class InfoBest_RewritesOptimizer_Model_Indexer_Url extends Mage_Catalog_Model_Indexer_Url {
    
    /**
    * Register event data during product save process
    * Added additional check for when product changes status or visibility, because depending on value, those products need to be excluded by Indexer
    *
    * @param Mage_Index_Model_Event $event
    */
    protected function _registerProductEvent(Mage_Index_Model_Event $event) {
        $product = $event->getDataObject();
        $dataChange2 = false;

        if (($product->dataHasChangedFor('status') && $product->getData('status') == "1") || ($product->dataHasChangedFor('visibility') && $product->getData('visibility') != "1")) {
        	$dataChange2 = true;
        }

        $dataChange = $product->dataHasChangedFor('url_key') || $product->getIsChangedCategories() || $product->getIsChangedWebsites() || $dataChange2;
            
        if (!$product->getExcludeUrlRewrite() && $dataChange) {
            $event->addNewData('rewrite_product_ids', array($product->getId()));
        }
    }
}