<?php
    class blockHelper extends FW_Singleton {
        
        public function getBlocks() {
            $blocks = cms_block::find(" status='1' ",array(array("type"=>"ASC","column"=>"block_order")));            
            return $blocks;
        }

        
        
    };
?>    
