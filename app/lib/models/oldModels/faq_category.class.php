<?php
class faq_category extends FW_ActiveRecord_Tree {

    protected $id;
    protected $id_parent;
    protected $name;
    protected $slug;
    protected $description;    
    protected $image;
    protected $status;
    protected $created_at;
    protected $author;
    
    public static $has_many = array (
        array(
            "property" => "questions",
            "table"    => "faq_question",
            "srcColumn"=> "id",
            "dstColumn"=> "id_category",
            "update"   => "restrict",
            "delete"   => "restrict"
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
            	"column" => "name",
                "type"   => "ASC"
            )
        )
    );
    
    public function hasSubCategories() {
        return $this->hasChildren();        
    }
    
    public function hasQuestions() {
        return (count($this->questions)>0);
    }
    
    public function getSubCategories() {
        if ($this->hasChildren()) {
            return $this->getChildren();
        }
    }
    
    public function getAuthor() {
        return $this->author->first()->getDisplayName();
    }
    public function getName() {
        return html_entity_decode($this->name,ENT_QUOTES,"UTF-8");
    }
    
    public function getNameJSON() {
        return htmlentities($this->getName(),ENT_QUOTES,"UTF-8");
    }
    
    public function getAuthorJSON() {
        return htmlentities($this->author->first()->getDisplayName(),ENT_QUOTES,"UTF-8");
    }
    
    public function getCountNews() {
        return count($this->getPublishedNews());
    }
    
    public function getSlug() {
        return $this->slug;
    }
    
    
    public function getStatus() {
        return intval($this->status);
    }
    
    public function getStatusAsJSON() {
        $status = intval($this->enabled);
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
        if ($this->image!==null) {
            if (is_file("images/faq/categories/{$this->image}")) {
                $result = true;                        
            }
        }        
        return $result;        
    }
    
    public function getImage() {
        $path           = rtrim($this -> _getBaseURL(), '/');        
        if ($this->hasImage()) {
            $image    = "{$path}/images/faq/categories/{$this->image}";
        }
        else {
            $image = "{$path}/images/faq/categories/noimage.png";
        }
        return $image;
    }
    
    public function getQuestions() {
        return $this->questions->find(" status='1' ");
    }
    
    public function getQuestionsAdmin() {
        return $this->questions;
    }
    

};
?>