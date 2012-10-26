<?php
    class ad_impression extends FW_ActiveRecord_Model {
        
        protected $id;
        protected $id_ad;        
        protected $ip;
        protected $browser;        
        protected $browserversion;
        protected $os;
        protected $osversion;
        protected $timestamp;        
        protected $referrer;
        protected $user;                
        protected $country;
        protected $region;
        protected $city;
                
    };
?>