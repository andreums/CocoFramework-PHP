<?php
    class order_item extends FW_ActiveRecord_Model {
        
        protected $id;
        protected $id_order;
        protected $item_id;
        protected $item_text;
        protected $item_option_id;
        protected $unit_price;
        protected $qty;
        protected $tax;
        protected $price;
        
        
        /*public static $has_one = array( 
            array(
                    "property"    => "item",
                    "table"            => "client",
                    "srcColumn" => "id_client",
                    "dstColumn" => "id",
                    "update"         => "restrict",
                    "delete"          => "restrict"
            )
        );*/
        
        public function getId() {
            // TODO: Change when services link is active
            return $this->item_id;
        }
        
        public function getDescription() {
            // TODO: Change when services link is active
            return html_entity_decode($this->item_text,ENT_QUOTES,"UTF-8");            
        }
        
        public function getUnitPriceWithoutTax() {
            $unit = floatval($this->unit_price);
            return "{$unit} &euro;";                        
        }
        
        public function getPrice() {
            $total             = 0.0;
            $qty                = intval($this->qty);
            $subtotal     = floatval((floatval($this->unit_price)*$qty));            
            $total             = floatval($subtotal+($this->tax*($subtotal/100)));            
            $total             = round($total,2);
            $this->price = $total;
            return $total;                         
        }
        
        public function getQty() {
            return intval($this->qty);
        }
        
        public function getItem() {
            return $this->item->first();
        }
        
        public function getItemId() {
            return $this->item_id;
        }
        
        public function getItemText() {
            return html_entity_decode($this->item_text,ENT_QUOTES,"UTF-8");
        }
            
    };
?>    
        