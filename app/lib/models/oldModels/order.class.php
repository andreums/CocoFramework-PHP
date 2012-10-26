<?php
    class order extends FW_ActiveRecord_Model {
        
        protected $id;
        protected $id_client;
        protected $created_at;
        protected $completed_at;
        protected $status;
        protected $id_payment;
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
            )
        );
        
        public static $has_many = array( 
            array(
                    "property"     => "items",
                    "table"            => "order_item",
                    "srcColumn" => "id",
                    "dstColumn" => "id_order",
                    "update"        => "restrict",
                    "delete"         => "restrict"
            )
        );
        
        public function getId() {
            return $this->id;
        }
        
        public function getViewLink() {
            $result = html::link_to_internal("orders","userOrders","displayOrder",_("Ver pedido"),array("id"=>$this->id));
            return $result;            
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
        
        public function getOrderPrice() {
            $price  =  $this->_normalizar($this->getTotal());
            $price  =  "M978{$price}";
            return $price;            
        }
        
        public function generateOrderContents() {            
            $items                   = $this->getItems();
            $orderAmount  =  $this->_normalizar($this->getTotal());
            $contents           = "M978{$orderAmount}\n";
            $contents         .= "{$this->getQty()}\n";
            if (count($items)) {
                foreach ($items as $item) {
                    $id                  = $item->getItemId();
                    $text            = $item->getItemText();
                    $qty              = $item->getQty();
                    $price          = $this->_normalizar($item->getPrice());                    
                    $contents .= "{$id}\n{$text}\n{$qty}\n{$price}\n";                                                            
                }
            }
            return $contents;
        }
        
        public function getComments() {
            return html_entity_decode($this->comments,ENT_QUOTES,"UTF-8");
        }
        
        
         private function _normalizar ($precio) {
            //convertimos un precio "XX.XX" a "XXXX"
            //si no hay . añadimos dos ceros al final
            $result = "";
            if (!strpos($precio,'.')) {
                $result = $precio . "00";
            }
            else {
                $pos                           = strpos($precio,'.');
                $decimales             = substr($precio,(1+$pos));
                $numDecimales  = strlen($decimales);
                $result = substr($precio,0,$pos) .( $numDecimales < 2 ? ( $numDecimales==0 ? "00":$decimales . "0") : substr($decimales,0,2) );
            }
            return $result;
        }
        
    };
?>    
