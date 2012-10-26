<?php
class professional_page extends FW_ActiveRecord_Model {

    protected $id;    
    protected $title;
    protected $slug;
    protected $author;
    protected $created_at;
    protected $content;
    protected $type;
    protected $status;

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
        
    public function getId() {
        return $this->id;
    }

    
    public function getStatus() {
        return intval($this->status);
    }
    
    
    public function getStatusJSON() {
        $status = $this->getStatus();        
        if ($status===1) {
            return _("Publicada");
        }
        return _("No publicada");
    }
    
    public function getTitle() {
        return html_entity_decode($this->title,ENT_QUOTES,"UTF-8");
    }
    
    public function getTitleJSON() {
        return htmlentities($this->getTitle(),ENT_QUOTES,"UTF-8");
    }

    public function getContents() {
        return html_entity_decode($this->content,ENT_QUOTES,"UTF-8");
    }

    public function getAuthor() {
        return $this -> author -> first() -> getDisplayName();
    }

    public function getDate() {
        return date("d/m/Y H:i:s",strtotime($this->created_at));
    }

    public function hasRelatedPages() {
                
        $database = FW_Database::getInstance(); 
        $prefix       = $database->getPrefix();
        
        $query        = "SELECT COUNT(id_page_b) FROM {$prefix}professional_related_pages WHERE id_page_a='{$this->id}' ";          
        $database->query($query);
        $result         = $database->fetchRow();        
        $result         = intval($result[0]);                
        if ($result>0) {            
            return true;
        }
        return false;
    }
    
    public function getSlug() {
        return $this->slug;
    }

    public function getRelatedPages() {
        $pages       = array();
        
        $database = FW_Database::getInstance(); 
        $prefix       = $database->getPrefix();
        
        $query        = "SELECT id_page_b FROM {$prefix}professional_related_pages WHERE id_page_a='{$this->id}' ";        
        $database->query($query);
        while ($row=$database->fetchRow()) {                        
            $id         = intval($row[0]);            
            $page  = professional_page::find("id='{$id}' ");                        
            if ($page->hasResult()) {
                if (intval($page->first()->getStatus())===1) {
                    $pages []= $page->first();
                }
            }            
        }
        return $pages;
    }
    
    public function getRelatedPagesIds() {
        $pages       = array();
        
        $database = FW_Database::getInstance(); 
        $prefix       = $database->getPrefix();
        
        $query        = "SELECT id_page_b FROM {$prefix}professional_related_pages WHERE id_page_a='{$this->id}' ";
        $database->query($query);
        while ($row=$database->fetchRow()) {            
            $pages []= $row[0];                        
        }
        return $pages;
    }
    
    public function getRelatedPagesList($user) {
        $user   = professional::find(" creator='{$user}' AND status='1' ");
        $user   = $user->first();        
        $code  = "<ul>";        
        $pages = $this -> getRelatedPages();        
        if (count($pages)) {
            foreach ($pages as $page) {
                //$type, $slug, $id, $slugcontent, $idcontent
                $code .= "<li>".html::link_to_internal("profesionales","cmsPage","displayPage",$page->getTitle(),array("id"=>$page->id))."</li>";
            }
        }
        $code .= "</ul>";
        return $code;
    }
    
    
    
    public function getHits() {
        $url  = "/index.php/paginas/{$this->slug}-{$this->id}";
        $hits = professional_page_hit::find("url='{$url}' ");
        return $hits;
    }
    
    public function getTotalHits() {        
        $hits = professional_page_hit::count("id","id_page='{$this->id}' ");
        if ($hits!==null) {
            return $hits;
        }
    }
    
    public function hitsThisMonth() {        
        $hits = professional_page_hit::count("id","id_page='{$this->id}' AND MONTH(timestamp)=MONTH(NOW()) AND YEAR(timestamp)=YEAR(NOW())");       
        if ($hits!==null) {
            return $hits;
        }
    }
    
    public function hitsByMonth() {
        $results     = array(1=>0,2=>0,3=>0,4=>0,5=>0,6=>0,7=>0,8=>0,9=>0,10=>0,11=>0,12=>0);
        $database = FW_Database::getInstance();
        $prefix       = $database->getPrefix();
        $query = "SELECT COUNT(id) AS qty,MONTH(timestamp) AS month FROM {$prefix}professional_page_hit WHERE id_page='{$this->id}' AND YEAR(timestamp)=YEAR(NOW()) GROUP BY MONTH(timestamp) ORDER BY MONTH(timestamp) ASC";        
        $database->query($query);
        while ($row=$database->fetchAssoc()) {
            $results [$row["month"]] = $row["qty"];
        }
        return $results;        
    }
    
    public function getAvgHitsByDay() {        
        $database = FW_Database::getInstance();
        $prefix       = $database->getPrefix();
        
        $url  = "/index.php/paginas/{$this->slug}-{$this->id}";
        $query = "SELECT COUNT(id) AS qty, DATE(timestamp) AS date FROM {$prefix}professional_page_hit WHERE id_page='{$this->id}'  GROUP BY DATE(timestamp)";                
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
        $hits           = array();
        $database = FW_Database::getInstance();
        $prefix       = $database->getPrefix();
        
        $url  = "/index.php/paginas/{$this->slug}-{$this->id}";
        $query = "SELECT DISTINCT(country), COUNT(id) AS qty FROM {$prefix}professional_page_hit WHERE id_page='{$this->id}' GROUP BY country ORDER BY qty DESC";       
                
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
        
        $url  = "/index.php/paginas/{$this->slug}-{$this->id}";
        $query = "SELECT DISTINCT(region), COUNT(id) AS qty FROM {$prefix}professional_page_hit WHERE id_page='{$this->id}' GROUP BY region ORDER BY qty DESC";        
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
        
        $url  = "/index.php/paginas/{$this->slug}-{$this->id}";
        $query = "SELECT DISTINCT(city), COUNT(id) AS qty FROM {$prefix}professional_page_hit WHERE id_page='{$this->id}' GROUP BY city ORDER BY qty DESC";        
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
    
    public function getHitsByBrowser() {
        $hits           = array();
        $database = FW_Database::getInstance();
        $prefix       = $database->getPrefix();
        
        $url  = "/index.php/paginas/{$this->slug}-{$this->id}";
        $query = "SELECT DISTINCT(browser), browserversion, COUNT(id) AS qty FROM {$prefix}professional_page_hit WHERE id_page='{$this->id}' GROUP BY browser,browserversion ORDER BY qty ASC";        
        $database->query($query);
        if ($database->numRows()>0) {
            while ($row=$database->fetchAssoc()) {
                $hits [] = array("browser"=>"{$row["browser"]}{$row["browserversion"]}","qty"=>$row["qty"]);
            }
        }        
        return $hits;
    }
    
    public function getHitsByReferrer() {
        $hits              = array();
        $database = FW_Database::getInstance();
        $prefix        = $database->getPrefix();
        
        $query = "SELECT DISTINCT(referrer), COUNT(id) AS qty FROM {$prefix}professional_page_hit WHERE id_page='{$this->id}' GROUP BY referrer ORDER BY qty ASC";        
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
    
    public function getHitsByOs() {
        $hits           = array();
        $database = FW_Database::getInstance();
        $prefix       = $database->getPrefix();
        
        $query = "SELECT DISTINCT(os),osversion, COUNT(id) AS qty FROM {$prefix}professional_page_hit WHERE id_page='{$this->id}' GROUP BY os,osversion ORDER BY qty ASC";        
        $database->query($query);
        if ($database->numRows()>0) {
            while ($row=$database->fetchAssoc()) {
                $hits [] = array("os"=>"{$row["os"]}{$row["osversion"]}","qty"=>$row["qty"]);
            }
        }        
        return $hits;
    }
    
    
    public function getTodayHits() {
        $url  = "/index.php/paginas/{$this->slug}-{$this->id}";
        $hits = professional_page_hit::count("id","id_page='{$this->id}'  AND DATE(timestamp)=DATE(NOW()) ");
        if ($hits!==null) {
            return $hits;
        }
    }
    
    public function hitsByPlatform() {
        $hits           = array();
        $database = FW_Database::getInstance();
        $prefix       = $database->getPrefix();
        
        $url  = "/index.php/paginas/{$this->slug}-{$this->id}";
        $query = "SELECT COUNT(id) AS qty,os AS platform FROM {$prefix}professional_page_hit WHERE url='{$url}'  GROUP BY os";        
        $database->query($query);
        while ($hit=$database->fetchAssoc()) {
            $hits []= $hit;            
        }
        
        return $hits;        
    }
    
    public function getYesterdayHits() {        
        $hits = professional_page_hit::count("id","id_page='{$this->id}'  AND DATE(timestamp)=DATE(NOW()-INTERVAL 1 DAY)  ");                
        if ($hits!==null) {
            return $hits[0];
        } 
    }
    
    public function getHitsByPlace() {
        $hits             = array();
        $database = FW_Database::getInstance();
        $prefix        = $database->getPrefix();
        
        $query         = "SELECT COUNT(id) AS qty,CONCAT(country,',',region,',',city) AS place FROM {$prefix}professional_page_hit WHERE id_page='{$this->id}' GROUP BY city,region,country HAVING country IS NOT NULL"; 
        $database->query($query);
        while ($hit=$database->fetchAssoc()) {
            $hits []= array("place"=>$hit["place"],"qty"=>$hit["qty"]);            
        }
        return $hits;                
    }
    
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
        $query = "SELECT COUNT(id) AS qty,DAY(timestamp) AS day FROM {$prefix}professional_page_hit WHERE id_page='{$this->id}' AND MONTH(timestamp)=MONTH(NOW()) AND YEAR(timestamp)=YEAR(NOW()) GROUP BY DAY(timestamp) ORDER BY DAY(timestamp) ASC";        
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
        $query = "SELECT COUNT(id) AS qty,MONTH(timestamp) AS month FROM {$prefix}professional_page_hit WHERE id_page='{$this->id}' AND YEAR(timestamp)=YEAR(NOW()) GROUP BY MONTH(timestamp) ORDER BY MONTH(timestamp) ASC";        
        $database->query($query);        
        while ($row=$database->fetchAssoc()) {
            $results [$row["month"]] = $row["qty"];
        }
        return $results;        
    }
    
    public function registerHit() {
        return professional_page_hit::registerHit($this->id);
    }

    

};
?>