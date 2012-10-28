<?php
    /**
     * A class to represent a collection of 
     * FW_API_Google_Geocoder_Result items
     * as a result of a Geocoder API call 
     */
    class FW_API_Google_Geocoder_Results implements Countable {
        
        /**
         * An array of FW_API_Google_Geocoder_Result items
         *
         * @var array
         */
        private $_items;
        
        
        public function __construct($data) {            
            $this->_items   = array();            
            if ( ($data!==null) && ($data->status!==null) && ($data->status!=="ZERO_RESULTS") ) {            
                if (count($data->results)) {
                    foreach ($data->results as $result) {
                        $result = new FW_API_Google_Geocoder_Result($result);
                        $this->add($result);
                    }
                }
            }
        }
        
        public function add(FW_API_Google_Geocoder_Result $result) {
            $this->_items []= $result;            
        }
        
        public function count() {
            return count($this->_items);
        }
        
        public function first() {
            if (count($this->_items)>0) {
                return ($this->_items[0]);
            }            
        }
        
        public function last() {
            if (count($this->_items)>0) {
                return ($this->_items[(count($this->_items)-1)]);
            }
        }
       
        public function get($position) {
            if ( (count($this->_items)>0) && ($this->_items[$position]!==null) ) {
                return $this->_items[$position];
            }            
        }
        
    };
?>