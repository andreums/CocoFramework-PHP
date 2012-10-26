<?php
    class payment extends FW_ActiveRecord_Model {
        
        protected $id;
        protected $id_order;
        protected $order_contents;
        protected $order_amount;
        protected $order_item_count;
        protected $result;
        protected $transaction_type;
        protected $transaction_id;
        protected $transaction_date;
        protected $payment_method;        
        protected $store_id;        
        protected $authentication_code;
        protected $error_code;
        protected $error_description;
        protected $created_at;
        /*protected $client_ip;
        protected $client_id;
        protected $client_browser;
        protected $client_os;
        */
        protected $remote_address;
        protected $request_url;
        protected $mac;
        
        /*
         * $_SERVER['REMOTE_ADDR']." pide:".$_SERVER['REQUEST_URI'].":\n" .
            $_REQUEST['result'] . "\n" .
            $_REQUEST['pszPurchorderNum'] . "\n" .                      
            $_REQUEST['pszTxnDate'] . "\n" .
            $_REQUEST['tipotrans'] . "\n" .
            $_REQUEST['store'] . "\n" .
            $_REQUEST['pszApprovalCode'] . "\n" .            
            $_REQUEST['pszTxnID']. "\n".
            $_REQUEST['fpago'] . "\n" .
            $_REQUEST['mac'] . "\n"             
         */
        
        
        public function getId() {
            return $this->id;
        }
        
        
        public function getOrder() {
            $order = order::find(" id='{$this->id_order}' ");
            if (count($order)) {
                $order = $order->first();
                return $order;
            }            
        }
        
        
        public function generateOrderContents() {
            $order                   = $this->getOrder();
            $items                   = $order->getItems();
            $orderAmount = $this->_normalizar($order->getTotal());
            $contents           = "M978{$orderAmount}\n";
            $contents         .= "{$order->getQty()}\n";
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
