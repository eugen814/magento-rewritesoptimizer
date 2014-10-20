<?php
/**
 * @version         0.1.0
 * @license         http://opensource.org/licenses/MIT - MIT License
 */

class InfoBest_RewritesOptimizer_Model_Core_Url_Rewrite extends Mage_Core_Model_Url_Rewrite {

    /**
    * Load rewrite information by id path, feed it the default store ID always
    *
    * @param   mixed $path
    * @return  Mage_Core_Model_Url_Rewrite
    */
    public function loadByIdPath($path) {
        $storeId = $this->getStoreId();
        $this->setStoreId(Mage::app()->getWebsite()->getDefaultStore()->getId());
        
        parent::loadByIdPath($path);
        
        $this->setStoreId($storeId);
        return $this;
    }
    
    /**
    * Load rewrite information for request, feed it the default store ID always
    * If $path is array - we must load possible records and choose one matching earlier record in array
    *
    * @param   mixed $path
    * @return  Mage_Core_Model_Url_Rewrite
    */
    public function loadByRequestPath($path) {
        parent::loadByRequestPath($path);
        
        if (!$this->getData('request_path')) {
            $storeId = $this->getStoreId();
            $this->setStoreId(Mage::app()->getWebsite()->getDefaultStore()->getId());
            
            parent::loadByRequestPath($path);
            
            $this->setStoreId($storeId);
        }
        return $this;
    }

}
