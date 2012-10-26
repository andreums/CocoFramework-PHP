<?php
class news_has_tag extends FW_ActiveRecord_Model {
    
    protected $id_news;
    protected $id_tag;
    
    public static $has_many = array (
        array(
            "property" => "news",
            "table"    => "news",
            "srcColumn"=> "id_news",
            "dstColumn"=> "id",
            "update"   => "restrict",
            "delete"   => "restrict"
        ),
        array(
            "property" => "tag",
            "table"    => "tag",
            "srcColumn"=> "id_tag",
            "dstColumn"=> "id",
            "update"   => "restrict",
            "delete"   => "restrict"
        )        
    );

};
?>
