<?php
    class ad_category extends  FW_ActiveRecord_Model {
        
        protected $id;
        protected $name;
        protected $description;
        protected $height;
        protected $width;
        protected $username;
        protected $created_at;
        protected $status;
        
        public static $belongs_to = array( 
            array(
                    "property"    => "username",
                    "table"            => "user",
                    "srcColumn" => "username",
                    "dstColumn" => "username",
                    "update"         => "restrict",
                    "delete"          => "restrict"
            )
        );
        
        public static $has_many = array( 
            array(
                    "property"     => "ads",
                    "table"            => "ad",
                    "srcColumn" => "id",
                    "dstColumn" => "id_category",
                    "update"         => "restrict",
                    "delete"          => "restrict"
            )
        );
        
        public function getId() {
            return $this->id;
        }
        
        public function getName() {
            return html_entity_decode($this->name,ENT_QUOTES,"UTF-8");
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
        
        public function getHeight() {
            return intval($this->height);
        }
        
        public function getWidth() {
            return intval($this->width);
        }
        
        public function getStatus() {
            return intval($this->status);
        }
        
        public function getAds() {
            return $this->ads;
        }
        
        public function hasAds() {
            return (count($this->ads)>0);
        }
        
        public function getAdCount() {
            return count($this->ads);
        }
        
        public function getActiveAds() {
            $ads = $this->ads->find("status='1' AND DATE(start_date)<=DATE(NOW()) AND DATE(end_date)>=DATE(NOW()) ");
            if (count($ads)) {
                foreach ($ads as $ad) {
                    $impressions           = $ad->getTotalImpressions();
                    $maxImpressions = $ad->getMaxImpressions();                    
                    if  (($impressions+1)>$maxImpressions) {
                        $ad->stop();
                    }
                }
            }
            return $ads;            
        }
                
    };
?>    
