<?php
    class event extends FW_ActiveRecord_Model {

        protected $id;
        protected $title;
        protected $description;
        protected $created_at;
        protected $author;
        protected $status;
        protected $all_day;
        protected $start;
        protected $end;
        protected $url;
        protected $place;
        protected $permalink;



        public static $belongs_to = array (
            array(
                "property" => "author",
                "table"    => "user",
                "srcColumn"=> "author",
                "dstColumn"=> "username",
                "update"   => "restrict",
                "delete"   => "restrict"
            )
        );


        public function getAllDay() {
            if ($this->all_day==='0') {
                return "false";
            }
            return "true";
        }

        public function getUrl() {
            if (strlen($this->permalink)===0) {
                $url = html::link_for_internal("events","event","getEventById",array("id"=>($this->id)),false);
            }
            else {
                $url = html::link_for_internal("events","event","getEventByPermalink",array("permalink"=>($this->permalink)),false);
            }
            return $url;
        }

        public function getDate() {
            return timeHelper::getFullHumanDate($this->created_at);
        }

    };
?>