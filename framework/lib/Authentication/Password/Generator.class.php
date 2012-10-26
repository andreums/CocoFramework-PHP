<?php
/**
 *  Andrés Ignacio Martínez Soto
 *  andresmartinezsoto@gmail.com  
 * Coco-PHP
 * License: Copyright (c) 2010-2012 Andrés Ignacio Martínez Soto    
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */
/**
 * @author andreu
 *
 */
class FW_Authentication_Password_Generator extends FW_Singleton {

    /**
     * Generates a random password
     * 
     * @param int $length Length of the password
     * @param int $strength Strength of the password
     * 
     * @return string The generated password
     */
    public function generateSimplePassword($length=10,$strength=9) {
 
        $password   = '';
        $vowels     = "aeiou";
        $consonants = "bcdfghjklmnpqrstvwxyz";

        if ($strength&1) {
            $consonants .= "BCDFGHJKLMNPQRSTVWXYZ";
        }
        if ($strength&2) {
            $vowels     .= "AEIOU";
        }
        if ($strength&4) {
            $consonants .= '0123456789';
        }
        if ($strength&8) {
            $consonants .= '@#$%';
        }
        if ($strength&9) {
            $consonants .= "_-";
        }         
        $alt   = (time()%rand());
        for ($i=0;$i<$length;$i++) {
            if ($alt===1) {
                $password .= $consonants[(rand() % strlen($consonants))];
                $alt = 0;
            } else {
                $password .= $vowels[(rand() % strlen($vowels))];
                $alt = 1;
            }
        }
        return $password;
    }



};
?>