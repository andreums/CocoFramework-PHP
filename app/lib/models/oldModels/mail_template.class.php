<?php
    class mail_template extends FW_ActiveRecord_Model {
        protected $id;
        protected $title;
        protected $description;
        protected $filename;
        protected $username;
        protected $created_at;
        protected $status;      
        private $templateData;
        
        public static $belongs_to = array (
             array(
                "property" => "username",
                "table"    => "user",
                "srcColumn"=> "username",
                "dstColumn"=> "username",
                "update"   => "restrict",
                "delete"   => "restrict"
             )
        ); 
        
        public function afterFind() {            
            $this->setTemplateData();
        }
        public function beforeSave() {
            // guardar la plantilla al disc dur
            print "<h1>BeforeSave!</h1>";
            $data = $this->getTemplateData();
            $path =  FW_Config::getInstance()->mail->getTemplateDir();
            if (strlen($this->filename)===0) {
                $this->filename = FW_Util_Url::seoUrl($this->title).".php";              
            }
            $file   = "{$path}/{$this->filename}";                        
            if (file_put_contents($file,$data)) {                
                return true;
            }            
            return false;            
        }
        
       public function setTemplateData($data="") {            
            if (strlen($data)===0) {
                $path =  FW_Config::getInstance()->mail->getTemplateDir();
                $file   = "{$path}/{$this->filename}";                                
                if (is_file($file)) {
                    $data = file_get_contents($file);
                }
                else {
                    $data = "";
                }                    
            }            
            $this->templateData = $data;            
        }
        
        public function getTemplateData() {
            return html_entity_decode($this->templateData,ENT_QUOTES,"UTF-8");
        }
        
        public function getAuthor() {
            return $this->username->first()->getDisplayName();
        }
        
        
        public function getId() {
            return $this->id;
        }  
        
        public function getTitle() {
            return html_entity_decode($this->title,ENT_QUOTES,"UTF-8");
        }
        
        public function getTitleJSON() {
            return htmlentities($this->getTitle(),ENT_QUOTES,"UTF-8");
        }
        
        public function getDescription() {
            return html_entity_decode($this->description,ENT_QUOTES,"UTF-8");
        }

        public function getDescriptionJSON() {
            return strip_tags(htmlentities($this->getDescription(),ENT_QUOTES,"UTF-8"));
        }        
        
        public function getStatus() {
            return (intval($this->status));
        }
        
        public function getDate() {
            return date("d/m/Y H:i:s",strtotime($this->created_at));
        }
        
        public function getStatusAsText() {
            if (intval($this->status)===0) {
                return _("Inactiva");
            }
            return _("Activa");
        }
        
        public function getFilename() {
            $path         = rtrim(FW_Config::getInstance()->get("mail.global.paths.templates"),'/');
            $filename = "{$path}/{$this->filename}";
            if (is_file($filename)) {
                return $filename;
            }
            return false;
        }
        
        public function getVariables() {
            $variables = array();
            if (preg_match_all('#\{\{(.*)\}\}#',$this->getTemplateData(),$matches)) {
                $variables = $matches[1];
            }    
            $variables = array_unique($variables,SORT_STRING);
            return $variables;
        }
        
        public function renderTemplate(mailing_list_suscriber $suscriber,array $variables=array()) {                
            $header     = "<html> <head> <title>{$this->getTitle()}</title> ";    
            $header    .= '<meta content="text/html; charset=iso-8859-1" http-equiv="Content-Type" /></head>';
            $header    .= '<body marginheight="0" topmargin="0" marginwidth="0" leftmargin="0">';
            
            $footer      = "</body></html>";
                        
            // insert the tracking code here
            $rand           = strval(rand(0,999));
            $key             = new FW_Util_Crypt();
            $key             = $key->getUniqId("mailer_{$rand}");
            $link             = html::link_for_internal("newsletter","email","trackEmail",array("key"=>$key));
            $code          = "<img src=\"{$link}\" />";
            
            $template = $this->getTemplateData();
            $template = "{$header}{$code}{$template}{$footer}";            
            
            $vars           = array();            
            if (preg_match_all('/\{\{(((?!\}\}).)+)/',$template,$matches)) {                
                foreach ($matches[1] as $var) {
                    $vars [$var]= "";                    
                    if (isset($variables[$var])) {
                        $vars [$var]= $variables[$var];
                    }
                }
            }                                 
            if (count($vars)) {
                foreach ($vars as $key=>$value) {                    
                    
                    if (is_array($value) || is_object($value)) {
                        continue;
                    }                    
                    $keyExploded = explode('.',$key);                                        
                    if (count($keyExploded)>1) {
                        
                        if ($keyExploded[0]==="system") {
                            $value = $this->_getSystemValue($keyExploded[1]);
                        }                        
                        if ($keyExploded[0]==="suscriber") {                                                                                    
                            $value        = $this->_getSuscriberValue($suscriber, $keyExploded[1]);                            
                        }
                        if ($keyExploded[0]==="campaign") {
                            if (!isset($variables["objects"]["campaign"])) {
                                throw new FW_Exception("Can't access mailing list campaign object");
                            }
                            $campaign = $variables["objects"]["campaign"];
                            $value          = $this->_getCampaignValue($campaign,$keyExploded[1]);
                        }                                                
                    }
                                        
                    if ($key!=="id_template") {
                        $var = "{{{$key}}}";                        
                        $template = str_replace($var,$value,$template);
                    }                                                    
                }                
            }            
            $template = utf8_decode($template);            
            return array("template"=>$template,"key"=>$key);            
        }
        
        private function _getCampaignValue(mail_campaign $campaign,$variable) {
            $value = "";
            if ($variable==="title") {
                $value = $campaign->getTitle();
            }
            if ($variable==="subject") {
                $value =  $campaign->getSubject();                
            }
            if ($variable==="from") {
                $value = $campaign->getMailFrom();
            }                        
            
            return $value;
        }
        
        private function _getSuscriberValue(mailing_list_suscriber $suscriber,$variable) {
            $value = "";
            if ($variable==="name") {
                $value = $suscriber->getName();
            }
            if ($variable==="username") {
                $value =  $suscriber->getUsername();                
            }
            if ($variable==="email") {
                $value = $suscriber->getEmail();
            }                        
            return $value;
        }
        
        private function _getSystemValue($variable) {
            $value = "";
            
            if ($variable==="time") {
                $value = date("H:i:s");                
            }
            if ($variable==="date") {
                $value = date("d/m/Y");                
            }
            if ($variable==="datetime") {
                $value = date("d/m/Y H:i:s");                
            }
            
            return $value;
        }
        
    };
?>