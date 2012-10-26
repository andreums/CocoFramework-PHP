<?php
class widgetCategories extends FW_Widget_Base {
    
    private $_categories;

    
    public function initialize(array $parameters=array()) {
        $this->_categories = $this->_getCategories();                    
    }

    
    public function render() {
        $code = "";
        if (count($this->_categories)>0) {
            $code .= "<ul>";
            foreach ($this->_categories as $category) {
                $title = "{$category["name"]} ({$category["qty"]})";
                $link  = html::link_to_internal("cms","news","displayNewsByCategory",$title,array("id"=>$category["id"],"slug"=>$category["slug"]));
                $code .= "<li>{$link}</li>";                
            }
            $code .= "</ul>";            
        }                         
        return $code;
    }
    
    private function _getCategories() {
        $results        = array();
        $categories = category::find(" status='1' ",array(array("type"=>"ASC","column"=>"name")));
        if ($categories->hasResult()) {
            foreach ($categories as $category) {
                $results []= array("id"=>$category->getId(),"slug"=>$category->getSlug(),"name"=>$category->getName(),"qty"=>$category->getCountNews());
            }
        }
        return $results;
    }

};
?>