<?php
    class ad extends  FW_ActiveRecord_Model {
                
        protected $id;
        protected $id_category;
        protected $name;
        protected $description;
        protected $image;
        protected $title;
        protected $contents;
        protected $start_date;
        protected $end_date;
        protected $position;
        protected $status;        
        protected $created_at;        
        protected $link;
        protected $flash_bg_color;
        protected $flash_width;
        protected $flash_height;
        protected $username;     
        protected $owner;
        protected $slug;
        protected $type;
        protected $max_impressions;
        
        protected $category;
        
        public static $belongs_to = array(
             array(
                "property" => "category",
                "table" => "ad_category",
                "srcColumn" => "id_category",
                "dstColumn" => "id",
                "update" => "restrict",
                "delete" => "restrict"
            ),
            array(
                    "property"    => "username",
                    "table"            => "user",
                    "srcColumn" => "username",
                    "dstColumn" => "username",
                    "update"         => "restrict",
                    "delete"          => "restrict"
            ),
            array(
                    "property"    => "owner",
                    "table"            => "user",
                    "srcColumn" => "owner",
                    "dstColumn" => "username",
                    "update"         => "restrict",
                    "delete"          => "restrict"
            )
        );        
        
        
        public function getCategory() {            
            if ($this->category!==null) {
                return $this->category->first();
            }
        }
        
        public function getDescription() {
            return html_entity_decode($this->description,ENT_QUOTES,"UTF-8");
        }
        
        public function getAuthor() {
            return $this->username->first()->getDisplayName();
        }
        
        public function getDate() {
            return date("d/m/Y H:i:s",strtotime($this->created_at));
        }
        
        public function getOwner() {            
            if (is_a($this->owner,"FW_ActiveRecord_Relation")) {
                return $this->owner->first()->username;
            }
        }
        
        public  function registerImpression() {
            $request  = FW_Request::getInstance();
            $browser = FW_Browser::getInstance();
            $user         = FW_Authentication::getUser();                        
            if ($user!==null) {
                $user = $user->username;
            }   
            
            $country = "";
            $region    = "";
            $city          = "";
                    
            $geo  = @geoip_record_by_name($request->getClientIP());            
            if ($geo!==null) {
                $countryCode  = $geo["country_code"];
                $country            = $geo["country_name"];
                $region              = $geo["region"];                
                $city                   = $geo["city"];
                if (strlen($region)>0) {
                    $region             = geoip_region_name_by_code($countryCode,$region);                    
                }          
            }            
            
            $click                                  = new ad_impression();
            $click->ip                           = $request->getClientIP();
            $click->url                          = $request->getServerURL();
            $click->timestamp         = date("Y-m-d H:i:s");
            $click->referrer               = $browser->getReferrer();            
            $click->browser                 = $browser->getBrowserName();
            $click->browserversion = $browser->getBrowserVersion();
            $click->os                             = $browser->getPlatformName();
            $click->osversion            = $browser->getPlatformVersion();
            $click->id_ad                    = $this->id;
            
            if ($user!==null) {
                $click->user             = $user;
            }
            $click->country = $country;
            $click->region   = $region;
            $click->city        = $city;            
            
            return $click->save();           
            
        }
        
        
        public  function registerClick() {
            $request  = FW_Request::getInstance();
            $browser = FW_Browser::getInstance();
            $user         = FW_Authentication::getUser();                        
            if ($user!==null) {
                $user = $user->username;
            }   
            
            $country = "";
            $region    = "";
            $city          = "";
                    
            $geo  = @geoip_record_by_name($request->getClientIP());            
            if ($geo!==null) {
                $countryCode  = $geo["country_code"];
                $country            = $geo["country_name"];
                $region              = $geo["region"];                
                $city                   = $geo["city"];
                if (strlen($region)>0) {
                    $region             = geoip_region_name_by_code($countryCode,$region);                    
                }          
            }            
            
            $click                                  = new ad_click();
            $click->ip                           = $request->getClientIP();
            $click->url                          = $request->getServerURL();
            $click->timestamp         = date("Y-m-d H:i:s");
            $click->referrer               = $browser->getReferrer();            
            $click->browser                 = $browser->getBrowserName();
            $click->browserversion = $browser->getBrowserVersion();
            $click->os                             = $browser->getPlatformName();
            $click->osversion            = $browser->getPlatformVersion();
            $click->id_ad                    = $this->id;
            
            if ($user!==null) {
                $click->user             = $user;
            }
            $click->country = $country;
            $click->region   = $region;
            $click->city        = $city;            
            
            return $click->save();           
            
        }

        public function getName() {
            return html_entity_decode($this->name,ENT_QUOTES,"UTF-8");
        }                 


        public function getLink() {
            return $this->link;
        }
        
        
        public function getClicksByDay() {
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
        $query = "SELECT COUNT(id) AS qty,DAY(timestamp) AS day FROM {$prefix}ad_click WHERE id_ad='{$this->id}' AND MONTH(timestamp)=MONTH(NOW()) AND YEAR(timestamp)=YEAR(NOW()) GROUP BY DAY(timestamp) ORDER BY DAY(timestamp) ASC";        
        $database->query($query);
        while ($row=$database->fetchAssoc()) {
            $results [$row["day"]] = $row["qty"];
        }
        return $results;        
    }
    
    public function getClicksByMonth() {
        $results     = array_fill_keys(range(1,12),0);        
        $database = FW_Database::getInstance();
        $prefix       = $database->getPrefix();
        $query = "SELECT COUNT(id) AS qty,MONTH(timestamp) AS month FROM {$prefix}ad_click WHERE id_ad='{$this->id}' AND YEAR(timestamp)=YEAR(NOW()) GROUP BY MONTH(timestamp) ORDER BY MONTH(timestamp) ASC";        
        $database->query($query);        
        while ($row=$database->fetchAssoc()) {
            $results [$row["month"]] = $row["qty"];
        }
        return $results;        
    }
        
        
        public function getClicks() {
            $clicks = ad_click::find("id_ad='{$this->id}' ");
            return $clicks;
        }
        
        public function getHits() {
        $url  = "/index.php/paginas/{$this->slug}-{$this->id}";
        $hits = page_hit::find("url='{$url}' ");
        return $hits;
    }
    
    
    /* CLICKS */
    public function getTotalClicks() {        
        $hits = ad_click::count("id","id_ad='{$this->id}' ");
        if ($hits!==null) {
            return $hits;
        }
    }
    
    
    public function getAvgClicksByDay() {        
        $database = FW_Database::getInstance();
        $prefix       = $database->getPrefix();
        
        $query = "SELECT COUNT(id) AS qty, DATE(timestamp) AS date FROM {$prefix}ad_click WHERE id_ad='{$this->id}'  GROUP BY DATE(timestamp)";                
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
    
    public function getClicksByCountry() {
        $hits             = array();
        $database = FW_Database::getInstance();
        $prefix        = $database->getPrefix();        
        $query        = "SELECT DISTINCT(country), COUNT(id) AS qty FROM {$prefix}ad_click WHERE id_ad='{$this->id}' GROUP BY country ORDER BY qty DESC";       
                
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
    
      
    public function getClicksByPlace() {
        $hits             = array();
        $database = FW_Database::getInstance();
        $prefix        = $database->getPrefix();
        
        $query         = "SELECT COUNT(id) AS qty,CONCAT(city,',',region,',',country) AS place FROM {$prefix}ad_click WHERE id_ad='{$this->id}' GROUP BY city,region,country HAVING country IS NOT NULL ORDER BY qty DESC";
        $database->query($query);
        while ($hit=$database->fetchAssoc()) {
            $hit["place"] = trim(rtrim(rtrim(trim($hit["place"],','))));
            $hits             []= array("place"=>$hit["place"],"qty"=>$hit["qty"]);            
        }        
        return $hits;                
    }
    
    public function getClicksByRegion() {
        $hits           = array();
        $database = FW_Database::getInstance();
        $prefix       = $database->getPrefix();
        
        $query = "SELECT DISTINCT(region), COUNT(id) AS qty FROM {$prefix}ad_click WHERE id_ad='{$this->id}' GROUP BY region ORDER BY qty DESC";        
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
    
    public function getClicksByCity() {
        $hits           = array();
        $database = FW_Database::getInstance();
        $prefix       = $database->getPrefix();
        
        $query = "SELECT DISTINCT(city), COUNT(id) AS qty FROM {$prefix}ad_click WHERE id_ad='{$this->id}' GROUP BY city ORDER BY qty DESC";        
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
    
    public function getClicksByBrowser() {
        $hits           = array();
        $database = FW_Database::getInstance();
        $prefix       = $database->getPrefix();
        
        $query = "SELECT DISTINCT(browser), browserversion, COUNT(id) AS qty FROM {$prefix}ad_click WHERE id_ad='{$this->id}' GROUP BY browser,browserversion ORDER BY qty ASC";        
        $database->query($query);
        if ($database->numRows()>0) {
            while ($row=$database->fetchAssoc()) {
                $hits [] = array("browser"=>$row["browser"]." ".$row["browserversion"],"qty"=>$row["qty"]);
            }
        }        
        return $hits;
    }
    
    public function getClicksByOs() {
        $hits           = array();
        $database = FW_Database::getInstance();
        $prefix       = $database->getPrefix();
        
        $query = "SELECT DISTINCT(os),osversion, COUNT(id) AS qty FROM {$prefix}ad_click WHERE id_ad='{$this->id}' GROUP BY os,osversion ORDER BY qty DESC";        
        $database->query($query);
        if ($database->numRows()>0) {
            while ($row=$database->fetchAssoc()) {
                $hits [] = array("os"=>$row["os"]." ".$row["osversion"],"qty"=>$row["qty"]);
            }
        }        
        return $hits;
    }
    
    public function getClicksByReferrer() {
        $hits              = array();
        $database = FW_Database::getInstance();
        $prefix        = $database->getPrefix();
        
        $query = "SELECT DISTINCT(referrer), COUNT(id) AS qty FROM {$prefix}ad_click WHERE id_ad='{$this->id}' GROUP BY referrer ORDER BY qty ASC";        
        $database->query($query);
        if ($database->numRows()>0) {
            while ($row=$database->fetchAssoc()) {
                if ($row["referrer"]===null || $row["referrer"]==="") {
                    $row["referrer"] = _("Clicks directos");                     
                }
                else {
                    $row["referrer"] = "<a href=\"{$row["referrer"]}\" title=\"{$row["referrer"]}\">{$row["referrer"]}</a>";                    
                }
                $hits [] = array("referrer"=>$row["referrer"],"qty"=>$row["qty"]);
            }
        }        
        return $hits;
    }
    
    
    public function getTodayClicks() {        
        $hits = ad_click::count("id","id_ad='{$this->id}'  AND DATE(timestamp)=DATE(NOW()) ");
        if ($hits!==null) {
            return $hits[0];
        } 
    }
    
    public function getYesterdayClicks() {        
        $hits = ad_click::count("id","id_ad='{$this->id}'  AND DATE(timestamp)=DATE(NOW()-INTERVAL 1 DAY)  ");        
        if ($hits!==null) {
            return $hits[0];
        } 
    }
        
    
    /* END CLICKS */
   
   
   /* IMPRESIONES */
  public function getTotalImpressions() {                
        $hits = ad_impression::count("id","id_ad='{$this->id}' ");                
        if ($hits!==null) {
            return intval($hits);
        }
    }  
  
  public function getTodayImpressions() {        
        $hits = ad_impression::count("id","id_ad='{$this->id}'  AND DATE(timestamp)=DATE(NOW()) ");
        if ($hits!==null) {
            return $hits[0];
        } 
    }
    
    public function getYesterdayImpressions() {        
        $hits = ad_impression::count("id","id_ad='{$this->id}'  AND DATE(timestamp)=DATE(NOW()-INTERVAL 1 DAY)  ");        
        if ($hits!==null) {
            return $hits[0];
        } 
    }
    
    public function getAvgImpressionsByDay() {        
        $database = FW_Database::getInstance();
        $prefix       = $database->getPrefix();
        
        $query = "SELECT COUNT(id) AS qty, DATE(timestamp) AS date FROM {$prefix}ad_impression WHERE id_ad='{$this->id}'  GROUP BY DATE(timestamp)";                
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
    
    public function getImpressionsByPlace() {
        $hits             = array();
        $database = FW_Database::getInstance();
        $prefix        = $database->getPrefix();
        
        $query         = "SELECT COUNT(id) AS qty,CONCAT(city,',',region,',',country) AS place FROM {$prefix}ad_impression WHERE id_ad='{$this->id}' GROUP BY city,region,country HAVING country IS NOT NULL ORDER BY qty DESC";
        $database->query($query);
        while ($hit=$database->fetchAssoc()) {
            $hit["place"] = trim(rtrim(rtrim(trim($hit["place"],','))));
            $hits             []= array("place"=>$hit["place"],"qty"=>$hit["qty"]);            
        }        
        return $hits;                
    }
    
    
    public function getImpressionsByCountry() {
        $hits             = array();
        $database = FW_Database::getInstance();
        $prefix        = $database->getPrefix();        
        $query        = "SELECT DISTINCT(country), COUNT(id) AS qty FROM {$prefix}ad_impression WHERE id_ad='{$this->id}' GROUP BY country ORDER BY qty DESC";       
                
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
    
    public function getImpressionsByRegion() {
        $hits           = array();
        $database = FW_Database::getInstance();
        $prefix       = $database->getPrefix();
        
        $query = "SELECT DISTINCT(region), COUNT(id) AS qty FROM {$prefix}ad_impression WHERE id_ad='{$this->id}' GROUP BY region ORDER BY qty DESC";        
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
    
    public function getImpressionsByCity() {
        $hits           = array();
        $database = FW_Database::getInstance();
        $prefix       = $database->getPrefix();
        
        $query = "SELECT DISTINCT(city), COUNT(id) AS qty FROM {$prefix}ad_impression WHERE id_ad='{$this->id}' GROUP BY city ORDER BY qty DESC";        
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
    
    public function getImpressionsByDay() {
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
        $query = "SELECT COUNT(id) AS qty,DAY(timestamp) AS day FROM {$prefix}ad_impression WHERE id_ad='{$this->id}' AND MONTH(timestamp)=MONTH(NOW()) AND YEAR(timestamp)=YEAR(NOW()) GROUP BY DAY(timestamp) ORDER BY DAY(timestamp) ASC";        
        $database->query($query);
        while ($row=$database->fetchAssoc()) {
            $results [$row["day"]] = $row["qty"];
        }
        return $results;        
    }
    
    public function getImpressionsByMonth() {
        $results     = array_fill_keys(range(1,12),0);        
        $database = FW_Database::getInstance();
        $prefix       = $database->getPrefix();
        $query = "SELECT COUNT(id) AS qty,MONTH(timestamp) AS month FROM {$prefix}ad_impression WHERE id_ad='{$this->id}' AND YEAR(timestamp)=YEAR(NOW()) GROUP BY MONTH(timestamp) ORDER BY MONTH(timestamp) ASC";        
        $database->query($query);        
        while ($row=$database->fetchAssoc()) {
            $results [$row["month"]] = $row["qty"];
        }
        return $results;        
    }
    
    
    public function getImpressionsByBrowser() {
        $hits           = array();
        $database = FW_Database::getInstance();
        $prefix       = $database->getPrefix();
        
        $query = "SELECT DISTINCT(browser), browserversion, COUNT(id) AS qty FROM {$prefix}ad_impression WHERE id_ad='{$this->id}' GROUP BY browser,browserversion ORDER BY qty ASC";        
        $database->query($query);
        if ($database->numRows()>0) {
            while ($row=$database->fetchAssoc()) {
                $hits [] = array("browser"=>$row["browser"],"version"=>$row["browserversion"],"qty"=>$row["qty"]);
            }
        }        
        return $hits;
    }
    
    public function getImpressionsByOs() {
        $hits           = array();
        $database = FW_Database::getInstance();
        $prefix       = $database->getPrefix();
        
        $query = "SELECT DISTINCT(os),osversion, COUNT(id) AS qty FROM {$prefix}ad_impression WHERE id_ad='{$this->id}' GROUP BY os,osversion ORDER BY qty ASC";        
        $database->query($query);
        if ($database->numRows()>0) {
            while ($row=$database->fetchAssoc()) {
                $hits [] = array("os"=>$row["os"],"version"=>$row["osversion"],"qty"=>$row["qty"]);
            }
        }        
        return $hits;
    }
    
    public function getImpressionsByReferrer() {
        $hits              = array();
        $database = FW_Database::getInstance();
        $prefix        = $database->getPrefix();
        
        $query = "SELECT DISTINCT(referrer), COUNT(id) AS qty FROM {$prefix}ad_impression WHERE id_ad='{$this->id}' GROUP BY referrer ORDER BY qty ASC";        
        $database->query($query);
        if ($database->numRows()>0) {
            while ($row=$database->fetchAssoc()) {
                if ($row["referrer"]===null || $row["referrer"]==="") {
                    $row["referrer"] = _("Clicks directos");                     
                }
                else {
                    $row["referrer"] = "<a href=\"{$row["referrer"]}\" title=\"{$row["referrer"]}\">{$row["referrer"]}</a>";                    
                }
                $hits [] = array("referrer"=>$row["referrer"],"qty"=>$row["qty"]);
            }
        }        
        return $hits;
    } 
  
  /* END IMPRESIONES */
 
     public function getContents() {
         return html_entity_decode($this->contents,ENT_QUOTES,"UTF-8");
     }
     
     public function getTitle() {
         return html_entity_decode($this->title,ENT_QUOTES,"UTF-8");
     }
     
     public function getId() {
         return $this->id;
     }
     
     public function getSlug() {
         return $this->slug;
     }
     
     public function  getMaxImpressions() {
         return intval($this->max_impressions);
     }
     
     
     private function _getImage($width=null,$height=null) {
         $image   = "";
         $code     = "";
         
         if ($width===null && $height===null) {
            $width   = $this->getCategory()->getWidth();
            $height = $this->getCategory()->getHeight();
         }
         
         if (!FW_Util_String::getInstance()->isValidUrl($this->image)) {
             $base    = FW_Config::getInstance()->get("core.global.baseURL");
             $image = "{$base}/{$this->image}";                           
         }
         else {
             $image = $this->image;
         }
         
        $code .= "<img src=\"{$image}\" style=\"width: {$width}px; height: {$height}px;\"    alt=\"{$this->getTitle()}\" title=\"{$this->getTitle()}\" />";
        return $code;
     }
     
     public function hasImage() {
         return ($this->getImage()!=="");
     }
     
     public function getImage() {
         $image   = "";         
         
         $img = trim($this->image);
                           
         if (!FW_Util_String::getInstance()->isValidUrl($img)) {
             $base    = FW_Config::getInstance()->get("core.global.baseURL");
             $image = "{$base}/{$img}";                           
         }
         else {
             $image = $img;
         }         
         return $image;
     }
         
     public function getImageHTML($width=null,$height=null) {
         $image   = "";
         $code     = "";
         
         if ($width===null && $height===null) {
            $width   = $this->getCategory()->getWidth();
            $height = $this->getCategory()->getHeight();
         }
         
         if (!FW_Util_String::getInstance()->isValidUrl($this->image)) {
             $base    = FW_Config::getInstance()->get("core.global.baseURL");
             $image = "{$base}/{$this->image}";                           
         }
         else {
             $image = $this->image;
         }
         
         
         
        $code .= "<img src=\"{$image}\" style=\"width: {$width}px; height: {$height}px;\"    alt=\"{$this->getTitle()}\" title=\"{$this->getTitle()}\" />";
        return $code;
     }
     
     public function getFlashHTML ($width=null,$height=null) {
         $code   ="";
        
        if ($width===null && $height===null) {         
            $width   = $this->getCategory()->getWidth();
            $height = $this->getCategory()->getHeight();
        }
         
         $title     = $this->getTitle();
         $image = $this->image;
         $link      = html::link_for_internal("ads","ad","adClick",array("slug"=>$this->getSlug(),"id"=>$this->getId()));
                  
         if (!FW_Util_String::getInstance()->isValidUrl($image)) {             
             $base    = FW_Config::getInstance()->get("core.global.baseURL");
             $image = "{$base}/{$this->image}";                           
         }         
         
         
         $code = '<object style="z-index: 1;" width="'.$width.'px" height="'.$height.'px" wmode="transparent" bgcolor="#FFFFFF" loop="false" data="'.$image.'" name="movie"></object>';         
         
         return $code;
     }
     
     
 
    private function _generateTransparentImage($width=null,$height=null) {
        if ($width===null && $height===null) {
            $width   = $this->getCategory()->getWidth();
            $height = $this->getCategory()->getHeight();
        }
        
        $image  = imagecreatetruecolor($width,$height);                
        $colour = imagecolorallocate($image, 0, 0, 0);
        imagecolortransparent($image,$colour);
        ob_start();        
        imagegif($image);
        imagedestroy($image);
        $imageData = ob_get_clean();
        return 'data:image/gif;base64,'.base64_encode($imageData);         
    }
 
     public function isImage() {
         return (intval($this->type)===1);
     }
     
     public function isText() {
         return (intval($this->type)===0);
     }
     public function isFlash() {
         return (intval($this->type)===2);
     }
     
     public function display($width=null,$height=null) {                   
         $code = "";         
         if ($this->isText()) {
             $code .= $this->_displayTextAd($width,$height);             
         }   
         else if ($this->isImage()) {             
             $code .= $this->_displayImageAd($width,$height);
         }   
         
         else if ($this->isFlash()) {
             $code .= $this->_displayFlashAd($width,$height);
         }             
         return $code;
     }
     
     private function _displayFlashAd($width=null,$height=null) {
         $code   ="";
        
        if ($width===null && $height===null) {         
            $width   = $this->getCategory()->getWidth();
            $height = $this->getCategory()->getHeight();
        }         
         
         $title     = $this->getTitle();
         $image = $this->image;
         $link      = html::link_for_internal("ads","ad","adClick",array("slug"=>$this->getSlug(),"id"=>$this->getId()));
         
                  
         if (!FW_Util_String::getInstance()->isValidUrl($image)) {             
             $base    = FW_Config::getInstance()->get("core.global.baseURL");
             $image = "{$base}/{$this->image}";                           
         }
         
         
         $code .= '<div style="position: relative; z-index:1; background: #FFFFFF;">';
         $code .= '<object style="z-index: 1;" width="'.$width.'px" height="'.$height.'px" wmode="transparent" bgcolor="#FFFFFF" loop="false" data="'.$image.'" name="movie"></object>';
         $code .='<a href="'.$link.'" target="_self" style="display:block;position:absolute;width: "'.$width.'px";height:"'.$height.'px" ;z-index:9999;top:0px;left:0px;border:none;background:none;">';
         $code .='<img src="'.$this->_generateTransparentImage().'"  style="width:'.$width.'px;height:'.$height.'px;" alt="'.$title.'" title="'.$title.'"  />';
         $code .= '</a></div>';
         return $code;
     }
     
     private function _displayImageAd($width=null,$height=null) {
         $code  = "";
         $image = $this->_getImage($width,$height);
         $title     = $this->getTitle();
         $link     = html::link_for_internal("ads","ad","adClick",array("slug"=>$this->getSlug(),"id"=>$this->getId()));
         $code .= "<a href=\"{$link}\" title=\"{$this->getTitle()}\" alt=\"{$title}\">{$image}</a>";
         return $code;         
     }
     
     private function _displayTextAd($width=null,$height=null) {
         $code  = "";
         $link     = html::link_for_internal("ads","ad","adClick",array("slug"=>$this->getSlug(),"id"=>$this->getId()));
         $code .= "<p>{$this->getContents()}<br/><a href=\"{$link}\" title=\"{$this->getTitle()}\" alt=\"{$this->getTitle()}\">{$this->getTitle()}</a></p>";         
         return $code;
     }
     
     public function getStatus() {
         return intval($this->status);
     }
     
     public function getStatusAsText() {
         $status = $this->getStatus();
         if ($status===1) {
             return _("Activo");
         }
         else {
             return _("Inactivo");
         }
     }
     
     public function getDateStart() {
         return date("d/m/Y",strtotime($this->start_date));
     }
     
     public function getDateEnd() {
         return date("d/m/Y",strtotime($this->end_date));
     }
    
    public function getType() {
        return intval($this->type);
    }
    
    
    public function beforeDelete() {
        $id                      = $this->id;
        $clicks              = ad_click::find(" id_ad='{$id}' ");
        $impressions = ad_impression::find(" id_ad='{$id}' ");
        
        if ($clicks->hasResult()) {  foreach ($clicks as $click) { $click->delete(); } }
        if ($impressions->hasResult()) {  foreach ($impressions as $impression) { $impression->delete(); } }
        return true;
    }
    
    public function stop() {
        $this->status = 0;
        return $this->save();
    }
    
    public function start() {
        $this->status = 1;
        return $this->save();
    }
    
    public function isFirstImpression() {
        if ($this->getTotalImpressions()===0) {
            return true;
        }
        return false;
    }
    
    
        
    };
?>