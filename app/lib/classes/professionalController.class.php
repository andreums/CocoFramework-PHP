<?php
    class professionalController extends FW_mvc_BaseController {
        
        public function beforeRender() {
            $this->setBreadcrumb();
        }
        
        protected function _getUserType() {
           $type             = false;
            $username = $this->user()->username;
            $user             = $this->model()->getUser($username);            
            if ($user!==null) {
                if ($user->type==="2" && $user->hasRole("promotor")) {
                    $type = "promotor";
                }                
                if ($user->type==="3" && $user->hasRole("inmobiliaria")) {
                    $type = "inmobiliaria";                    
                }
                if ($user->type==="4" && $user->hasRole("banco") ) {
                    $type = "banco";
                }
            }           
            return $type;
       }

    /* Método privado para obtener el promotor inmobiliario: REFACTORING */
        protected function _getPromotor() {
            $username = $this->user()->username;
            $promotor = $this->getPromotorByUsername($username);
            return $promotor;
        }
        
        protected function _getInmobiliaria() {
            $username    = $this->user()->username;
            $inmobiliaria = $this->getInmobiliariaByUsername($username);
            return $inmobiliaria;
        }
        
        protected function _getBanco() {
            $username  = $this->user()->username;
            $banco          = $this->getBancoByUsername($username);
            return $banco;
        }
        
        
        protected function _getProfesionalUser() {
            $user             = null;
            $type            = $this->_getUserType();
            $username = $this->user()->username;            
                        
            switch ($type) {
                    
                case "promotor":
                    $user = $this->model()->getPromotorByUsername($username);
                 break;
                 
                case "inmobiliaria":
                    $user = $this->model()->getInmobiliariaByUsername($username);
                 break;
                 
                 case "banco":
                    $user = $this->model()->getBancoByUsername($username);
                 break;
            };
            
            return $user;
        }
        
    };
?>    
