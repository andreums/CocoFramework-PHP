<?php
    class email extends FW_ActiveRecord_Model {
        protected $id;
        protected $id_campaign;
        protected $id_suscriber;
        protected $email;
        protected $subject;
        protected $content;
        protected $alt_text;
        protected $created_at;
        protected $sent_at;
        protected $opened_at;
        protected $agent;
        protected $agent_version;
        protected $os;
        protected $os_version;
        protected $ip;
        protected $country;
        protected $region;
        protected $city;
        protected $referrer;
        protected $status;
        protected $email_key;
        public function getId() {
            return $this->id;
        }

        public function getSubject() {            
            return html_entity_decode($this->subject, ENT_QUOTES, "UTF-8");
        }

        public function getSubjectJSON() {
            $link        = html::link_to_internal("newsletter","indexNewsletter","displayEmail",$this->getSubject(),array("id"=>$this->email_key));            
            return $link;
        }

        public function getDate() {
            return date("d/m/Y H:i:s", strtotime($this->created_at));
        }

        public function getCreatedDate() {
            return date("d/m/Y H:i:s", strtotime($this->created_at));
        }

        public function getSentDate() {
            if ( $this->sent_at === null ) {
                return htmlentities(_("No enviado"), ENT_QUOTES, "UTF-8");
            }
            return date("d/m/Y H:i:s", strtotime($this->sent_at));
        }

        public function getOpenedDate() {
            if ( $this->sent_at === null ) {
                return htmlentities(_("No enviado"), ENT_QUOTES, "UTF-8");
            }
            if ( $this->opened_at === null ) {
                return htmlentities(_("No abierto"), ENT_QUOTES, "UTF-8");
            }
            return date("d/m/Y H:i:s", strtotime($this->opened_at));
        }

        public function getStatus() {
            return intval($this->status);
        }

        public function getStatusJSON() {
            $return = "";
            $status = $this->getStatus();
            $sent = ($this->sent_at !== null);
            $open = ($this->opened_at !== null);
            if ( $sent === false ) {
                $return = _("No enviado");
            }
            if ( $sent !== false ) {
                if ( $open === true ) {
                    $return = _("Abierto");
                }
                else {
                    $return = _("No abierto");
                }
            }
            return htmlentities($return, ENT_QUOTES, "UTF-8");
        }
        
        
        public function getStatusText() {
            $return = "";
            $status = $this->getStatus();
            $sent = ($this->sent_at !== null);
            $open = ($this->opened_at !== null);
            if ( $sent === false ) {
                $return = _("No enviado");
            }
            if ( $sent !== false ) {
                if ( $open === true ) {
                    $return = _("Abierto");
                }
                else {
                    $return = _("No abierto");
                }
            }
            return html_entity_decode($return, ENT_QUOTES, "UTF-8");
        }

        public function getStatusUserJSON() {
            $return = "";
            $status = $this->getStatus();
            $sent = ($this->sent_at !== null);
            $open = ($this->opened_at !== null);
            if ( $sent === false ) {
                $return = _("No enviado");
            }
            if ( $sent !== false ) {
                if ( $open === true ) {
                    $return = _("Leído");
                }
                else {
                    $return = _("No leído");
                }
            }
            return htmlentities($return, ENT_QUOTES, "UTF-8");
        }

        public function getNewsletter() {
            return $this->id_campaign;
        }
        
         public function getNewsletterTitle() {
            $campaign = mail_campaign::find(" id='{$this->id_campaign}' ");
            if ($campaign->hasResult()){
                return $campaign->first()->getTitle();
            }
        }

        public function getSuscriberId() {
            return $this->id_suscriber;
        }

        public function getSuscriber() {
            $id = $this->id_suscriber;
            $suscriber = mailing_list_suscriber::find(" id='{$id}' ");
            if ( $suscriber->hasResult() ) {
                $suscriber = $suscriber->first();
                return $suscriber->getNameJSON();
            }
        }

        public function getName() {
            $id = $this->id_suscriber;
            $suscriber = mailing_list_suscriber::find(" id='{$id}' ");
            if ( $suscriber->hasResult() ) {
                $suscriber = $suscriber->first();
                return $suscriber->getName();
            }
        }

        public function getContents() {
            return html_entity_decode($this->content, ENT_QUOTES);
        }

        public function getEmail() {
            return htmlentities($this->email, ENT_QUOTES, "UTF-8");
        }

        public function getAltText() {
            return html_entity_decode($this->alt_text, ENT_QUOTES, "UTF-8");
        }

        public function getCampaign() {
            $campaign = mail_campaign::find(" id='{$this->id_campaign}' ");
            if ( $campaign->hasResult() ) {
                return $campaign->first();
            }
        }

        public function markAsReaded() {            
            $isOpened = ($this->opened_at!==null);                        
            if ( !$isOpened ) {
                $request  = FW_Request::getInstance();
                $browser = FW_Browser::getInstance();
                $this->opened_at = date("Y-m-d H:i:s");
                $this->status = 1;
                $this->ip = $request->getClientIP();
                $this->referrer = $request->getHttpReferrer();
                $this->agent = $browser->getBrowserName();
                $this->agent_version = $browser->getBrowserVersion();
                $this->os = $browser->getPlatform();
                $this->os_version = $browser->getPlatformVersion();
                $country = "";
                $region = "";
                $city = "";
                $geo = @geoip_record_by_name($request->getClientIP());                
                if ( $geo !== null ) {
                    $countryCode = $geo["country_code"];
                    $country = $geo["country_name"];
                    $region = $geo["region"];
                    $city = $geo["city"];
                    if ( strlen($region) > 0 ) {
                        $region = @geoip_region_name_by_code($countryCode, $region);
                    }
                }
                $this->country = $country;
                $this->region = $region;
                $this->city = $city;                
                
                $this->save();
            }

        }

    };
?>