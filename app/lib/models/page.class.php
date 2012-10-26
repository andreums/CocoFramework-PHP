<?php
class page extends FW_ActiveRecord_Model {

    public $id;    
    public $title;
    public $slug;
    public $author;
    public $created_at;
    public $content;    
    public $status;

    /*public static $belongs_to = array (
         array(
            "property" => "author",
            "table"    => "user",
            "srcColumn"=> "author",
            "dstColumn"=> "username",
            "update"   => "restrict",
            "delete"   => "restrict"
         )
     );*/ 
    
    public function getId() {
        return $this->id;
    }

    
    public function getStatusAsInteger() {
        return intval($this->status);
    }
    
    
    public function getStatus() {        
        return intval($this->status);
    }
    
    public function getStatusJSON() {        
        $status = intval($this->status);
        if ($status===1) {
            $status = _("Publicada"); 
        }
        else {
            $status = _("No publicada");
        }
        return htmlentities($status,ENT_QUOTES,"UTF-8");
    }

    public function getURL() {
        return $this -> url;
    }

    public function getTitle() {
        return html_entity_decode($this->title,ENT_QUOTES,"UTF-8");
    }
    
    public function getTitleJSON() {
        return htmlentities($this->getTitle(),ENT_QUOTES,"UTF-8");
    }

    public function getContents() {
        return html_entity_decode($this -> content,ENT_QUOTES,"UTF-8");
    }

    public function getAuthor() {
        return $this -> author -> first() -> getDisplayName();
    }
    
    public function getAuthorJSON() {
        return htmlentities($this->getAuthor(),ENT_QUOTES,"UTF-8");
    }

    public function getDate() {
        return date("d/m/Y H:i:s", strtotime($this -> created_at));
    }

    public function hasRelatedPages() {
        return (count($this->getRelatedPages())>0);
    }
    
    public function getSlug() {
        return $this->slug;
    }

    public function getRelatedPages() {
        $pages       = array();
        
        $database = FW_Database::getInstance(); 
        $prefix       = $database->getPrefix();
        
        $query        = "SELECT id_page_b FROM {$prefix}related_pages WHERE id_page_a='{$this->id}' ";
        $database->query($query);
        while ($row=$database->fetchRow()) {
            $id        = $row[0];
            $page  = page::find("id='{$id}' ");
            if ($page->hasResult()) {
                if ($page->first()->status==="1") {
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
        
        $query        = "SELECT id_page_b FROM {$prefix}related_pages WHERE id_page_a='{$this->id}' ";
        $database->query($query);
        while ($row=$database->fetchRow()) {            
            $pages []= intval($row[0]);                        
        }
        return $pages;
    }

    public function getRelatedPagesList() {
        $code  = "<ul>";        
        $pages = $this -> getRelatedPages();
        if (count($pages)) {
            foreach ($pages as $page) {
                $code .= "<li>".html::link_to_internal("cms","page","displayPage",$page->getTitle(),array("id"=>$page->id,"slug"=>$page->slug))."</li>";
            }
        }
        $code .= "</ul>";
        return $code;
    }    
    
    public function registerHit() {
        return page_hit::registerHit($this->id);
    }
    
    public function getHits() {
        $url  = "/index.php/paginas/{$this->slug}-{$this->id}";
        $hits = page_hit::find("url='{$url}' ");
        return $hits;
    }
    
    public function getTotalHits() {        
        $hits = page_hit::count("id","id_page='{$this->id}' ");
        if ($hits!==null) {
            return $hits;
        }
    }
    
    public function getYesterdayHits() {        
        $hits = page_hit::count("id","id_page='{$this->id}'  AND DATE(timestamp)=DATE(NOW()-INTERVAL 1 DAY)  ");        
        if ($hits!==null) {
            return $hits[0];
        } 
    }
    
    public function hitsThisMonth() {        
        $hits = page_hit::count("id","id_page='{$this->id}' AND MONTH(timestamp)=MONTH(NOW()) AND YEAR(timestamp)=YEAR(NOW())");       
        if ($hits!==null) {
            return $hits;
        }
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
        $query = "SELECT COUNT(id) AS qty,DAY(timestamp) AS day FROM {$prefix}page_hit WHERE id_page='{$this->id}' AND MONTH(timestamp)=MONTH(NOW()) AND YEAR(timestamp)=YEAR(NOW()) GROUP BY DAY(timestamp) ORDER BY DAY(timestamp) ASC";        
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
        $query = "SELECT COUNT(id) AS qty,MONTH(timestamp) AS month FROM {$prefix}page_hit WHERE id_page='{$this->id}' AND YEAR(timestamp)=YEAR(NOW()) GROUP BY MONTH(timestamp) ORDER BY MONTH(timestamp) ASC";        
        $database->query($query);        
        while ($row=$database->fetchAssoc()) {
            $results [$row["month"]] = $row["qty"];
        }
        return $results;        
    }
    
    public function getAvgHitsByDay() {        
        $database = FW_Database::getInstance();
        $prefix       = $database->getPrefix();
        
        $query = "SELECT COUNT(id) AS qty, DATE(timestamp) AS date FROM {$prefix}page_hit WHERE id_page='{$this->id}'  GROUP BY DATE(timestamp)";                
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
    
  public function getHitsByBrowser() {
        $hits           = array();
        $database = FW_Database::getInstance();
        $prefix       = $database->getPrefix();
        
        $query = "SELECT DISTINCT(browser), browserversion, COUNT(id) AS qty FROM {$prefix}page_hit WHERE id_page='{$this->id}' GROUP BY browser,browserversion ORDER BY qty DESC";        
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
        
        $query = "SELECT DISTINCT(referrer), COUNT(id) AS qty FROM {$prefix}page_hit WHERE id_page='{$this->id}' GROUP BY referrer ORDER BY qty DESC";        
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
        
        $query = "SELECT DISTINCT(os),osversion, COUNT(id) AS qty FROM {$prefix}page_hit WHERE id_page='{$this->id}' GROUP BY os,osversion ORDER BY qty DESC";        
        $database->query($query);
        if ($database->numRows()>0) {
            while ($row=$database->fetchAssoc()) {
                $hits [] = array("os"=>"{$row["os"]}{$row["osversion"]}","qty"=>$row["qty"]);
            }
        }        
        return $hits;
    }
    
    
    
    
    public function getTodayHits() {        
        $hits = page_hit::count("id","id_page='{$this->id}'  AND DATE(timestamp)=DATE(NOW()) ");
        if ($hits!==null) {
            return $hits;
        }
    }
    
    public function getHitsByPlace() {
        $hits             = array();
        $database = FW_Database::getInstance();
        $prefix        = $database->getPrefix();
        
        $query         = "SELECT COUNT(id) AS qty,CONCAT(city,',',region,',',country) AS place FROM {$prefix}page_hit WHERE id_page='{$this->id}' GROUP BY city,region,country HAVING country IS NOT NULL ORDER BY qty DESC";
        $database->query($query);
        while ($hit=$database->fetchAssoc()) {
            $hit["place"] = trim(rtrim(rtrim(trim($hit["place"],','))));
            $hits             []= array("place"=>$hit["place"],"qty"=>$hit["qty"]);            
        }        
        return $hits;                
    }
    

};
?>