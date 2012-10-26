<?php
    class invoice extends FW_ActiveRecord_Model {
        
        protected $id;
        protected $id_client;
        protected $id_order;
        protected $id_payment;
        
        protected $created_at;        
        protected $status;
        
        protected $subtotal;
        protected $tax;
        protected $total;
        protected $comments;
        
        public static $has_one = array( 
            array(
                    "property"    => "client",
                    "table"            => "client",
                    "srcColumn" => "id_client",
                    "dstColumn" => "id",
                    "update"         => "restrict",
                    "delete"          => "restrict"
            ),
            array(
                    "property"    => "payment",
                    "table"            => "payment",
                    "srcColumn" => "id_payment",
                    "dstColumn" => "id",
                    "update"         => "restrict",
                    "delete"          => "restrict"
            ),
            array(
                    "property"    => "order",
                    "table"            => "order",
                    "srcColumn" => "id_order",
                    "dstColumn" => "id",
                    "update"         => "restrict",
                    "delete"          => "restrict"
            )
        );
        
        public static $has_many = array( 
            array(
                    "property"     => "items",
                    "table"            => "invoice_item",
                    "srcColumn" => "id",
                    "dstColumn" => "id_invoice",
                    "update"        => "restrict",
                    "delete"         => "restrict"
            )
        );
        
        public function getId() {
            return $this->id;
        }
        
        public function getClient() {
            return $this->client->first();
        }
        
        public function getTax() {
            return "{$this->tax} %";
        }
        
        public function getTotal() {            
            return doubleval($this->total);
        }
        
        public function getSubtotal() {
            return doubleval($this->subtotal);
        }
        
        public function getItems() {
            return $this->items;            
        }
        
        public function getQty() {
            return count($this->items);
        }
        
        public function getPayment() {
            return $this->payment->first();
        }
        
        public function getStatusJSON() {
            $status = intval($this->status);
            if ($status===0) {
                $status = _("No pagada");
            }
            if ($status===1) {
                $status = _("Pagada");
            }
            if ($status===2) {
                $status = _("Cancelado (error en el pago)");
            }            
            return htmlentities($status,ENT_QUOTES,"UTF-8");
        }
        
        
        
        public function getQtyJSON() {
            $qty = $this->getQty();
            $qty = "{$qty} artículos";
            return htmlentities($qty,ENT_QUOTES,"UTF-8");            
        }
        
        public function getIVAJSON() {
            $total         = $this->getTotal();
            $subtotal = $this->getSubtotal();
            $iva             = round(($total-$subtotal),2);
            $iva             = "{$iva} &euro;";
            return $iva;
        }
        
        public function getSubtotalJSON() {
            $total = "{$this->getSubtotal()} &euro;";
            return $total;
        }
        
        public function getTotalJSON() {
            $total = "{$this->getTotal()} &euro;";
            return $total;
        }
        
        public function getDateJSON() {
            return date("d/m/Y H:i:s",strtotime($this->created_at));
        }
        
        public function getDate() {
            return date("d/m/Y H:i:s",strtotime($this->created_at));
        }
        
        
                
    };
?>    
