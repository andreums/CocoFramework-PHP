<?php
/**
 *  Andrés Ignacio Martínez Soto
 *  andresmartinezsoto@gmail.com  
 * Coco-PHP
 * License: Copyright (c) 2010-2012 Andrés Ignacio Martínez Soto    
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */
    class FW_Cart extends FW_Singleton implements Countable {
        
        private $_session;
        private $_contents;
        
        public function __construct() {
            $this->_contents = array();            
            
            $this->_session = FW_Session::getInstance();
            $contents           = FW_Session::get("cart","cart");
            if ($contents!==null) {
                $this->_contents = $contents;
            }
            else {                
                $this->_contents["total_items"] = 0;
                $this->_contents["cart_total"]    = 0;
            }          
        }
        
        public function __destruct() {            
            $this->_saveCart();
        }
        
        public function insert($item) {
            $result = false;                
            if (!empty($item)) {                                                
                if ($this->_insert($item)) {
                    $result = true;
                }
            }
            else {
                return false;
            }
            
            if ($result===true) {
                $this->_saveCart();
            }
            return true;                     
        }
        
        
        private function _insert(array $item=array()) {            
            $rowid = null;
            
            if (empty($item) || count($item)===0) {                
                return false;
            }
            if ( (!isset($item['id'])) || (!isset($item['qty'])) || (!isset($item['price'])) || (!isset($item['name'])) ) {                
                return false;
            }
            
            if (!is_numeric($item["qty"])) {                
                return false;
            }
            if (!is_numeric($item["price"])) {                
                return false;
            }            
            
            $item["qty"]     = intval($item["qty"]);
            $item["price"] = floatval($item["price"]);
                        
            if (isset($item["options"])  && count($item["options"])>0 ) {
                $rowid = md5($item["id"].implode('',$item["options"]));
            }            
            else {
                $rowid = md5($item["id"]);
            }
            
            unset($this->_contents[$rowid]["rowid"]);
            $this->_contents[$rowid]["rowid"] = $rowid;
            
            foreach ($item as $key=>$value) {
                $this->_contents[$rowid][$key] = $value;
            }            
            return true;
        }


        public function update(array $item=array()) {            
            if (count($item)===0) {
                return false;
            }
            if ( isset($item["rowid"]) && isset($item["qty"]) ) {                
                if ($this->_update($item)) {                    
                    $this->_saveCart();
                }
            }            
        }
        
        private function _update(array $item=array()) {            
                        
            if ( (!isset($item["qty"])) || (!isset($item["rowid"])) || (!isset($this->_contents[$item["rowid"]])) ) {                
                return false;
            }
            
            if (!is_numeric($item["qty"])) {                
                return false;                
            }
            
            if ( intval($this->_contents[$item["rowid"]]["qty"]) === intval($item["qty"]) ) {                
                return false;                
            }
            
            if (intval($item["qty"])===0) {                
                $this->remove($item["rowid"]);                
            }
            else {                                
                $this->_contents[$item["rowid"]]["qty"] = intval($item["qty"]);                
            }
            return true;
        }
        
        public function remove($id) {
            if (isset($this->_contents[$id])) {
                unset($this->_contents[$id]);
                $this->_saveCart();
            }
        }
        
        public function hasOptions($id) {
            if (!isset($this->_contents[$id]["options"]) || count($this->_contents[$id]["options"])===0) {
                return false;
            }
            return true;
        }
        
        public function getProductOptions($id) {
            if ($this->hasOptions($id)) {
                return $this->_contents[$id]["options"];
            }
        }



        private function _saveCart() {
            unset($this->_contents["total_items"]);
            unset($this->_contents["cart_total"]);
            
            $total  = 0;
            $items = 0;
            foreach ($this->_contents as $key=>$value) {
                if ( isset($value["price"]) && isset($value["qty"]) ) {
                    
                    $qty     = intval($value["qty"]);
                    $price = floatval($value["price"]);
                    
                    $total  += ($price*$qty);
                    $items += $qty;
                    
                    $this->_contents[$key]["subtotal"] = (floatval($this->_contents[$key]["price"])*intval($this->_contents[$key]["qty"])  );
                }
            }
            
            $this->_contents["total_items"] = $items;
            $this->_contents["cart_total"]    = $total;
            
            FW_Session::set("cart",$this->_contents,"cart");
            return true;            
        }  
        
        
        public function total() {
            return floatval($this->_contents["total"]);
        }           
        
        public function count() {
            return intval($this->_contents["total_items"]);
        }
        
        public function getContents() {
            $cart = $this->_contents;
            unset($cart["total_items"]);
            unset($cart["cart_total"]);
            
            return $cart;
        }
        
        
        public function clear() {
            unset($this->_contents);
            $this->_contents                                 = array();
            $this->_contents["cart_total"]    = 0;
            $this->_contents["total_items"] = 0;
            
            FW_Session::unsetData("cart","cart");
        }
        
        public function getItemCount() {
            return intval($this->_contents["total_items"]);
        }
        
        
        public function getSubtotal() {
            return $this->_contents["cart_total"];
        }
        
        public function getTaxAmount($tax) {
            $subtotal = floatval($this->getSubtotal());
            $total         = floatval($this->getTotal($tax));
            $tax            = ($total-$subtotal);
            return floatval($tax);
        }
        
        public function getTotal($tax) {
            $subtotal = floatval($this->getSubtotal());
            $total         = $subtotal+(($subtotal*$tax)/100);
            return floatval($total);            
        }
           
    };
?>    
