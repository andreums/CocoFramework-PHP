<?php
    class professionalModel extends FW_mvc_BaseModel {
        
        public function getProfessionalUser($id,$slug) {
            $conditions = " id='{$id}' AND slug='{$slug}' AND status='1' ";
            $user             = professional::find($conditions);
            if ($user->hasResult()) {
                return $user->first();
            }
        }
        
         // BEGIN: Métodos para obtener usuarios
        public function getUser($username) {
            $user = user::find("username='{$username}'");
            if ( $user->hasResult() ) {
                return $user->first();
            }
        }

        public function getPromotorByUsername($username) {
            $promotor = promotor::find("creator='{$username}'");
            if ( $promotor->hasResult() ) {
                return $promotor->first();
            }
        }

        public function getInmobiliariaByUsername($username) {
            $inmobiliaria = inmobiliaria::find("creator='{$username}'");
            if ( $inmobiliaria->hasResult() ) {
                return $inmobiliaria->first();
            }
        }

        public function getBancoByUsername($username) {
            $banco = banco::find("creator='{$username}'");
            if ( $banco->hasResult() ) {
                return $banco->first();
            }
        }
        
        
    };
?>    
