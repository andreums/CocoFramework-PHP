<?php
class faq_question extends FW_ActiveRecord_Model {

    protected $id;
    protected $id_category;
    protected $title;
    protected $author;
    protected $slug;
    protected $created_at;    
    protected $content;
    protected $status;    
    
    public static $belongs_to = array (
        array(
            "property" => "author",
            "table"    => "user",
            "srcColumn"=> "author",
            "dstColumn"=> "username",
            "update"   => "restrict",
            "delete"   => "restrict"
        ),
        array(
            "property" => "category",
            "table"    => "faq_category",
            "srcColumn"=> "id_category",
            "dstColumn"=> "id",
            "update"   => "restrict",
            "delete"   => "restrict"
        )
    );
    
    public function getCategory() {
        if ($this->category->hasResult()) {
            return $this->category->first();
        }
    }
    
    public function getIdCategory() {
        return $this->id_category;
    }

    
    public function getId() {
        return $this->id;
    }
    
    public function getSlug() {
        return $this->slug;
    }

    public function getTitle() {
        return html_entity_decode($this->title,ENT_QUOTES,"UTF-8");
    }
    
    public function getTitleJSON() {
        return htmlentities($this->getTitle(),ENT_QUOTES,"UTF-8");
    }
    
    public function getAuthorJSON() {
        return htmlentities($this->author->first()->getDisplayName(),ENT_QUOTES,"UTF-8");
    }
    
    public function getStatusAsJSON() {
        $status = "";
        if ($this->status==="1") {
            $status =  _("Publicada");
        }
        else {
            $status =  _("No publicada");
        }
        return htmlentities($status,ENT_QUOTES,"UTF-8");
    }
    
    public function getStatus() {
        return intval($this->status);
    }
    
    public function getComments() {
        $comments = comment::find("id_news='{$this->id}'");
        return $comments;        
    }
    
    public function getApprovedComments() {
        $comments = comment::find("id_news='{$this->id}' AND status='1' ");
        return $comments;        
    }
    
 
    public function getIntro() {
        return utf8_encode($this->intro);
    }
    
    public function getIntroduction() {
        return utf8_encode(html_entity_decode($this -> intro,ENT_QUOTES,"UTF-8"));
    }

    public function getContents() {
        return utf8_encode(html_entity_decode($this -> content,ENT_QUOTES,"UTF-8"));
    }

    public function getAuthor() {
        return $this -> author -> first() -> getDisplayName();
    }
    
    

    public function getDate() {
        return date("d/m/Y H:i:s", strtotime($this -> created_at));
    }
    
    public function getDisplayImage() {
            $path       = $this->_getBaseURL();
            $pattern = '/<\s*img [^\>]*src\s*=\s*[\""\']?([^\""\'\s>]*)/i';
            if (preg_match($pattern,(string) $this->content, $matches)) {
                $image = $matches[1];
                if (FW_Util_Url::isValidUrl($image)) {
                    $path = $image;
                    return $path;
                }                                
            }
            if (empty($image)) {
                $image = "/images/cms/news/noimage.png";                
            }   
            $path  .= $image;                     
        return $path;
    }
    
    public function allowComments() {
        return (intval($this->allow_comments)===1);
    }

};
?>