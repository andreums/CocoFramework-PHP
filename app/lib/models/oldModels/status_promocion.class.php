<?php
    class status_promocion extends FW_ActiveRecord_Model {
        protected $id;
        protected $id_promocion;
        protected $datetime;
        protected $status;
        protected $description;
        
        /* OJO: Description es VARCHAR(512) */
    };
?>