<?php
    class adHelper extends FW_Singleton {
        
        public function existsCategory($name) {
            $category = $this->_getAdCategory($name);
            return ($category!==null);            
        }
        
        
        public function getAdsForCategory($name) {            
            $category = $this->_getAdCategory($name);
            if ($category!==null) {
                if ($category->hasAds()) {
                    $ads        = $category->getActiveAds();                    
                    $count  = count($ads);
                    if ($count===0) {
                        return;
                    }
                    $rand     = mt_rand(0,($count-1));
                    if (isset($ads[$rand]))  {
                        $ads[$rand]->registerImpression();
                        return $ads[$rand]->display();                        
                    }
                    else {
                        throw new FW_Exception("This exception should never be raised!");                        
                    }
                }
                else {
                    throw new FW_Exception("Ad category {$name} hasn't got any ads. Please, create an ad and add to it!");
                }
                                
            }
            else {
                throw new FW_Exception("Ad category {$name} doesn't exists");
            }
        }
        
        
      private function _getAdCategory($name) {
        $category = ad_category::find(" name='{$name}' ");
        if ($category->hasResult()) {
            return $category->first();
        }                        
    }
        
    };
?>    
