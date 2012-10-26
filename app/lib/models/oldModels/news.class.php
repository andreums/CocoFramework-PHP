<?php
class news extends FW_ActiveRecord_Model {

    protected $id;
    protected $title;
    protected $author;
    protected $slug;
    protected $created_at;
    protected $intro;
    protected $content;
    protected $status;
    protected $allow_comments;
    protected $type;

    /*public static $has_and_belongs_to_many = array( array("property" => "categories", "srcTable" => "news", "srcColumn" => "id", "dstTable" => "category", "dstColumn" => "id", "throughTable" => "news_has_category", "throughTableSrcColumn" => "id_news", "throughTableDstColumn" => "id_category", "update" => "restrict", "delete" => "restrict"));*/

    public static $belongs_to = array( array("property" => "author", "table" => "user", "srcColumn" => "author", "dstColumn" => "username", "update" => "restrict", "delete" => "restrict"));
    
    public function getId() {
        return $this->id;
    }
    
    public function getSlug() {
        return $this->slug;
    }

    public function getTitle() {
        return utf8_encode($this -> title);
    }
    
    public function getStatus() {
        if ($this->status==="1") {
            return _("Publicada");
        }
        return _("No publicada");
    }
    
    public function getComments() {
        $comments = comment::find("id_news='{$this->id}'");
        return $comments;        
    }
    
    public function hasComments() {
        if (!$this->allowComments()) {
            return false;
        }
        return (count(comment::find("id_news='{$this->id}'"))>0);            
    }
    
    public function getApprovedComments() {
        $comments = comment::find("id_news='{$this->id}' AND status='1' ");
        return $comments;        
    }
    
    public function getCategories() {
        $id                  = $this->id;
        $categories  = array();
        $database     = $this->database();
        $prefix           = $database->getPrefix();
        $query           = "SELECT id_category FROM {$prefix}news_has_category WHERE id_news='{$id}' ";
        
        $database->query($query);
        while ($row=$database->fetchRow()) {
            $categories []= $row[0];
        }
        return $categories;
    }
    
    
    public function getCategoriesFull() {
        $id                  = $this->id;
        $categories  = array();
        $database     = $this->database();
        $prefix           = $database->getPrefix();
        $query           = "SELECT id_category FROM {$prefix}news_has_category WHERE id_news='{$id}' ";
        $database->query($query);        
        while ($row=$database->fetchRow()) {            
            $categories []= category::find("id='{$row[0]}'  ")->first();
        }        
        return $categories;
    }
    
    public function getCategoriesArchive() {
        $code            = "<ul class=\"categoryList\">";
        $categories = $this->getCategoriesFull();
        if (count($categories)) {
            foreach ($categories as $category) {
                $link    = html::link_to_internal("cms","news","displayNewsByCategory",$category->getName(),array("slug"=>$category->getSlug(),"id"=>$category->getId()));
                $code .= "<li>{$link}</li>";                
            }
        }
        $code           .= "</ul>";
        return $code;
    }
    
    public function getIntro() {
        return utf8_encode(html_entity_decode($this -> intro,ENT_QUOTES,"UTF-8"));
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
    
    public function getAuthorJSON() {
        return htmlentities($this -> author -> first() -> getDisplayName(),ENT_QUOTES,"UTF-8");
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
    
    
    /* STATS */
    
    public function getHitsByDay() {
        $results     = array();
        $month      = intval(date('m'));
        if (in_array($month,array(1,3,5,7,8,10,12))) {
            $results = array_fill_keys(range(1,31),0);                        
        }
        if (in_array($month,array(4,6,9,11))) {
            $results = array_fill_keys(range(1,30),0);
        }
        if ($month===2) {
            if (intval($date('L'))===1) {
                $results = array_fill_keys(range(1,29),0);
            }
            else {
                $results = array_fill_keys(range(1,28),0);
            }
        }
        $database = FW_Database::getInstance();
        $prefix       = $database->getPrefix();
        $query = "SELECT COUNT(id) AS qty,DAY(timestamp) AS day FROM {$prefix}news_hit WHERE id_news='{$this->id}' AND MONTH(timestamp)=MONTH(NOW()) AND YEAR(timestamp)=YEAR(NOW()) GROUP BY DAY(timestamp) ORDER BY DAY(timestamp) ASC";        
        $database->query($query);
        while ($row=$database->fetchAssoc()) {
            $results [$row["day"]] = $row["qty"];
        }
        return $results;        
    }
    
    public function getHitsByMonth() {
        $results     = array_fill_keys(range(1,12),0);        
        $database = FW_Database::getInstance();
        $prefix       = $database->getPrefix();
        $query = "SELECT COUNT(id) AS qty,MONTH(timestamp) AS month FROM {$prefix}news_hit WHERE id_news='{$this->id}' AND YEAR(timestamp)=YEAR(NOW()) GROUP BY MONTH(timestamp) ORDER BY MONTH(timestamp) ASC";        
        $database->query($query);        
        while ($row=$database->fetchAssoc()) {
            $results [$row["month"]] = $row["qty"];
        }
        return $results;        
    }
    
    public function getTotalHits() {        
        $hits = news_hit::count("id","id_news='{$this->id}' ");        
        if ($hits!==null) {
            return $hits;
        }
    }
    
    
    public function getAvgHitsByDay() {        
        $database = FW_Database::getInstance();
        $prefix       = $database->getPrefix();
        
        $query = "SELECT COUNT(id) AS qty, DATE(timestamp) AS date FROM {$prefix}news_hit WHERE id_news='{$this->id}'  GROUP BY DATE(timestamp)";                        
        $database->query($query);        
        if ($database->numRows()>0) {
            $total = array();
            while($row=$database->fetchAssoc()) {
                $total []= $row["qty"];                
            }
            $sum = array_sum($total);
            return ($sum/count($total));            
        }       
        return 0;
    }
    
    public function getHitsByCountry() {
        $hits             = array();
        $database = FW_Database::getInstance();
        $prefix        = $database->getPrefix();        
        $query        = "SELECT DISTINCT(country), COUNT(id) AS qty FROM {$prefix}news_hit WHERE id_news='{$this->id}' GROUP BY country ORDER BY qty DESC";       
                
        $database->query($query);
        if ($database->numRows()>0) {
            while ($row=$database->fetchAssoc()) {
                if ($row["country"]===null) {
                    $row["country"] = _("Desconocido");
                }
                $hits []= $row;
            }
        }
        return $hits;
    }
    
    public function getHitsByRegion() {
        $hits           = array();
        $database = FW_Database::getInstance();
        $prefix       = $database->getPrefix();
        
        $query = "SELECT DISTINCT(region), COUNT(id) AS qty FROM {$prefix}news_hit WHERE id_news='{$this->id}' GROUP BY region ORDER BY qty DESC";        
        $database->query($query);
        if ($database->numRows()>0) {
            while ($row=$database->fetchAssoc()) {
                if ($row["region"]===null || $row["region"]==="") {
                    $row["region"] = _("Desconocido");
                }
                $hits []= $row;
            }
        }
        
        return $hits;
    }
    
    public function getHitsByCity() {
        $hits           = array();
        $database = FW_Database::getInstance();
        $prefix       = $database->getPrefix();
        
        $query = "SELECT DISTINCT(city), COUNT(id) AS qty FROM {$prefix}news_hit WHERE id_news='{$this->id}' GROUP BY city ORDER BY qty DESC";        
        $database->query($query);
        if ($database->numRows()>0) {
            while ($row=$database->fetchAssoc()) {
                if ($row["city"]===null || $row["city"]==="") {
                    $row["city"] = _("Desconocido");
                }
                $hits []= $row;
            }
        }
        
        return $hits;
    }
    
    public function getHitsByPlace() {
        $hits             = array();
        $database = FW_Database::getInstance();
        $prefix        = $database->getPrefix();
        
        $query         = "SELECT COUNT(id) AS qty,CONCAT(city,',',region,',',country) AS place FROM {$prefix}news_hit WHERE id_news='{$this->id}' GROUP BY city,region,country HAVING country IS NOT NULL ORDER BY qty DESC";
        $database->query($query);
        while ($hit=$database->fetchAssoc()) {
            $hit["place"] = trim(rtrim(rtrim(trim($hit["place"],','))));
            $hits             []= array("place"=>$hit["place"],"qty"=>$hit["qty"]);            
        }        
        return $hits;                
    }
    
    public function getHitsByBrowser() {
        $hits           = array();
        $database = FW_Database::getInstance();
        $prefix       = $database->getPrefix();
        
        $query = "SELECT DISTINCT(browser), browserversion, COUNT(id) AS qty FROM {$prefix}news_hit WHERE id_news='{$this->id}' GROUP BY browser,browserversion ORDER BY qty ASC";        
        $database->query($query);
        if ($database->numRows()>0) {
            while ($row=$database->fetchAssoc()) {
                $hits [] = array("browser"=>$row["browser"],"version"=>$row["browserversion"],"qty"=>$row["qty"]);
            }
        }        
        return $hits;
    }
    
    public function getHitsByOs() {
        $hits           = array();
        $database = FW_Database::getInstance();
        $prefix       = $database->getPrefix();
        
        $query = "SELECT DISTINCT(os),osversion, COUNT(id) AS qty FROM {$prefix}news_hit WHERE id_news='{$this->id}' GROUP BY os,osversion ORDER BY qty ASC";        
        $database->query($query);
        if ($database->numRows()>0) {
            while ($row=$database->fetchAssoc()) {
                $hits [] = array("os"=>$row["os"],"version"=>$row["osversion"],"qty"=>$row["qty"]);
            }
        }        
        return $hits;
    }
    
    public function getHitsByReferrer() {
        $hits              = array();
        $database = FW_Database::getInstance();
        $prefix        = $database->getPrefix();
        
        $query = "SELECT DISTINCT(referrer), COUNT(id) AS qty FROM {$prefix}news_hit WHERE id_news='{$this->id}' GROUP BY referrer ORDER BY qty ASC";        
        $database->query($query);
        if ($database->numRows()>0) {
            while ($row=$database->fetchAssoc()) {
                if ($row["referrer"]===null || $row["referrer"]==="") {
                    $row["referrer"] = _("Hits directos");                     
                }
                else {
                    $row["referrer"] = "<a href=\"{$row["referrer"]}\" title=\"{$row["referrer"]}\">{$row["referrer"]}</a>";                    
                }
                $hits [] = array("referrer"=>$row["referrer"],"qty"=>$row["qty"]);
            }
        }        
        return $hits;
    }
    
    
    public function getTodayHits() {                                
        $hits = news_hit::count("id","id_news='{$this->id}'  AND DATE(timestamp)=DATE(NOW()) ");        
        if ($hits!==null) {
            return $hits[0];
        } 
    }
    
    public function getYesterdayHits() {        
        $hits = news_hit::count("id","id_news='{$this->id}'  AND DATE(timestamp)=DATE(NOW()-INTERVAL 1 DAY)  ");        
        if ($hits!==null) {
            return $hits[0];
        } 
    }
    
    public function registerHit() {
        return news_hit::registerHit($this->id);
    }
        
    
    /* END STATS */

};
?>