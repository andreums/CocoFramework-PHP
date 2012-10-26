<?php
    class client extends FW_ActiveRecord_Model {
        
        protected $id;
        protected $username;
        protected $name;                
        protected $email;
        protected $telephone;        
        protected $fax;
        protected $document;
        protected $street;
        protected $number;
        protected $city;
        protected $province;
        protected $state;
        protected $country;
        protected $postal_code;
        protected $bussiness;
        protected $bussiness_id;
        protected $vat_number;
        protected $is_professional;
        protected $status;
        
        
        public function getId() {
            return $this->id;
        }
        
        public function isProfessional() {
            return (intval($this->is_professional)===1);
        }
        
        public function getUsername() {
            return html_entity_decode($this->username,ENT_QUOTES,"UTF-8");
        }        
        
        public function getName() {
            return html_entity_decode($this->name,ENT_QUOTES,"UTF-8");
        }
        
        public function getEmail() {
            return html_entity_decode($this->email,ENT_QUOTES,"UTF-8");
        }
        
        public function getDocument() {
            return html_entity_decode($this->document,ENT_QUOTES,"UTF-8");
        }
        
        public function getTelephone() {
            return html_entity_decode($this->telephone,ENT_QUOTES,"UTF-8");
        }
        
        public function getFax() {
            return html_entity_decode($this->fax,ENT_QUOTES,"UTF-8");
        }
        
        public function getAddress() {
            $address = "{$this->street} {$this->number}, {$this->postal_code} {$this->city} ,{$this->province} ,{$this->state} ,{$this->country} ";
            return html_entity_decode($address,ENT_QUOTES,"UTF-8");
        }
        
        public function getNumber() {
            return html_entity_decode($this->number,ENT_QUOTES,"UTF-8");
        }
        
        public function getPostalCode() {
            return html_entity_decode($this->postal_code,ENT_QUOTES,"UTF-8");
        }
        
        public function getStreet() {
            return html_entity_decode($this->street,ENT_QUOTES,"UTF-8");
        }
        
        public function getCity() {
            return html_entity_decode($this->city,ENT_QUOTES,"UTF-8");
        }
        
        public function getProvince() {
            return html_entity_decode($this->province,ENT_QUOTES,"UTF-8");
        }
        
        public function getState() {
            return html_entity_decode($this->state,ENT_QUOTES,"UTF-8");
        }
        
        public function getCountry() {
            return html_entity_decode($this->country,ENT_QUOTES,"UTF-8");
        }
        
        public function getBussiness() {
            return html_entity_decode($this->bussiness,ENT_QUOTES,"UTF-8");
        }
        
        public function getBussinessId() {
            return html_entity_decode($this->bussiness_id,ENT_QUOTES,"UTF-8");
        }
        
        public function getVatNumber() {
            return html_entity_decode($this->vat_number,ENT_QUOTES,"UTF-8");
        }
        
        public function hasFax() {
            return (strlen($this->fax)>0);
        }
        
    };
?>    
