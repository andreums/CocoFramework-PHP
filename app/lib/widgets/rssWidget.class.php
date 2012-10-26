<?php
class rssWidget extends FW_Widget_Base {

    private $_rss;
    private $_items;
    private $_reader;

    public function initialize(array $parameters=array()) {
        $rss = $this->get("rss");
        if ($rss!==null) {
            $this->_rss = $rss;
        }

        $options                  = new FW_Container_Parameter();
        $options->cache           = new FW_Container_Parameter();
        $options->cache->lifetime = 300;
        
        $this->_reader   = new FW_Util_RSS_Reader($this->_rss,$options);
        $this->_items    = $this->_reader->getItems();        
    }

    private function _processItems() {
        
    }

    public function render() {
        $html  = "";
        $items = array();
        foreach ($this->_items as $item) {
            $items []= html::link_to($item->link,$item->title,array());
        }
        $html .= html::unorderedList($items);        
        return $html;
    }

};
?>