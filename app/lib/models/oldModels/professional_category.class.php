<?php
class professional_category extends FW_ActiveRecord_Tree {

    protected $id;
    protected $id_parent;
    protected $name;
    protected $slug;
    protected $description;
    protected $status;
    protected $type;
    protected $created_at;
    protected $author;

     public static $has_and_belongs_to_many = array (
        array(
                "property"  => "news",
                "srcTable"  => "professional_category",
                "srcColumn" => "id",
                "dstTable"  => "professional_news",
                "dstColumn" => "id",
                "throughTable" => "professional_news_has_category",
                "throughTableSrcColumn" => "id_category",
                "throughTableDstColumn" => "id_news",
                "update" => "restrict",
                "delete" => "restrict"
            )
    );

 	public static $belongs_to = array (
        array(
            "property" => "author",
            "table"    => "user",
            "srcColumn"=> "author",
            "dstColumn"=> "username",
            "update"   => "restrict",
            "delete"   => "restrict"
        )
    );

    public static $acts_as_tree = array (
    	"idColumn"     => "id",
        "parentColumn" => "id_parent",
        "siblingOrder" => array (
            array (
            	"column" => "title",
                "type"   => "ASC"
            )
        )
    );
    
    public function getAuthor() {
        return $this->author->first()->getDisplayName();
    }
    public function getName() {
        return html_entity_decode($this->name,ENT_QUOTES,"UTF-8");
    }
    public function getCountNews() {
        return count($this->getPublishedNews());
    }
    
    public function getSlug() {
        return $this->slug;
    }
    
    public function getStatus() {
        $status = intval($this->status);
        if ($status===1) {
            return _("Activa");
        }
        return _("Inactiva");
    }
    
    public function getDescription() {
        return html_entity_decode($this->description,ENT_QUOTES,'UTF-8');
    }

    public function getDate() {
        return date("d/m/Y H:i:s",strtotime($this->created_at));
    }
    
    public function hasNews() {
        return $this->news->hasResult();
    }
    
    public function getPublishedNews() {
        return $this->news->find("b.status='1'");
    }

    public function getNews() {
        return $this->news;
    }
    
    public function getId() {
        return $this->id;
    }
    
    public function hasImage() {
        $result       = false;        
        if (is_file("images/cms/categories/{$this->image}")) {
            $result = true;                        
        }        
        else {
            $result = false;
        }
        return $result;        
    }
    
    public function getImage() {
        $path           = rtrim($this -> _getBaseURL(), '/');
        if ($this->hasImage()) {
            $image    = "{$path}/images/cms/categories/{$this->image}";
        }
        else {
            $image = "{$path}/images/cms/categories/noimage.jpg";
        }
        return $image;
    }
    
    /*    
        $posts       = array();
        $children = $this->getChildren();

        if ($this->posts->hasResult()) {
            foreach ($this->posts as $post) {
                $posts []=  $post->id;
            }
        }

        if ($children->hasResult()) {
            foreach ($children as $childCategory) {
                $childCategoryPosts = $childCategory->getPosts();
                if ($childCategoryPosts!==null) {
                    foreach ($childCategoryPosts as $post) {
                        $posts []=  $post->id;
                    }
                }
            }
        }
        $p          = array();
        $posts      = array_unique($posts);
        foreach ($posts as $post) {
            $p []= "'{$post}'";
        }
        $posts      = implode(',',$p);

        $conditions = array (
            array (
                "name"     => "id",
                "operator" => "IN",
                "value"	   => $posts
            )
        );
        $posts      = post::find($conditions);
        return $posts;
    }     
     */

};
?>