<?php
    class invoice_item extends FW_ActiveRecord_Model {
        
        protected $id;
        protected $id_invoice;
        protected $item_id;
        protected $item_text;
        protected $item_option_id;
        
        protected $unit_price;        
        protected $qty;
        protected $tax;
        protected $price;
        
        public function getId() {
            // TODO: Change when services link is active
            return $this->item_id;
        }
        
        public function getDescription() {
            // TODO: Change when services link is active
            return html_entity_decode($this->item_text,ENT_QUOTES,"UTF-8");            
        }
        
        public function getUnitPriceWithoutTax() {
            $unit = round(floatval($this->unit_price),2);
            return "{$unit} &euro;";                        
        }
        
        public function getPrice() {
            $total           = 0.0;
            $qty              = intval($this->qty);
            $subtotal   = floatval((floatval($this->unit_price)*$qty));            
            $total          = floatval($subtotal+($this->tax*($subtotal/100)));            
            $total          = round($total,2);
            $total          = "{$total} &euro;"; 
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
