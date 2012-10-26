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
 *  Andrés Ignacio Martínez Soto
 *  andresmartinezsoto@gmail.com  
 * Coco-PHP
 * License: Copyright (c) 2010-2012 Andrés Ignacio Martínez Soto    
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */
class timeHelper extends FW_Singleton {

    private $_config;
    private $_dateParts;
    private $_year;
    private $_month;
    private $_day;
    private $_date;
    private $_timestamp;
    private $_dateSeparator;
    private $_dateFormat;
    private $_filter;

    public function __construct ()     {
        $this->_config = FW_Config::getInstance();
    }

    private function _setUp() {
        $this->_dateSeparator = $this->_config->getParameter("teSeparator");
        $this->_dateFormat = $this->_config->getParameter("teFormat");
        $this->_year = date("Y");
        $this->_month = date("m");
        $this->_day = date("d");
        $this->_timestamp = time();
        $this->_date = $this->_dateFormat;
    }

    private function _setDate() {
        $this->_year = date("Y");
        $this->_month = date("m");
        $this->_day = date("d");
        $this->_timestamp = time();
    }


    public static function getMonthName($month) {
        if (!is_numeric($month)) {
            return false;
        }
        else {
            switch ($month) {

                case 01:
                case 1:
                    return _("January");
                    break;

                case 02:
                case 2:
                    return _("February");
                    break;

                case 03:
                case 3:
                    return _("March");
                    break;

                case 04:
                case 4:
                    return _("April");
                    break;

                case 05:
                case 5:
                    return _("May");
                    break;

                case 06:
                case 6:
                    return _("June");
                    break;

                case 07:
                case 7:
                    return _("July");
                    break;

                case 08:
                case 8:
                    return _("August");
                    break;

                case 09:
                case 9:
                    return _("September");
                    break;

                case 10:
                    return _("October");
                    break;

                case 11:
                    return _("November");
                    break;

                case 12:
                    return _("December");
                    break;

            };
        }
    }

    private function _getDayName($dayName) {
        if (!$dayName) {
            return "";
        }
        else {
            switch ($dayName) {

                case "Monday":
                    return _("Monday");
                    break;

                case "Tuesday":
                    return _("Tuesday");
                    break;

                case "Wednesday":
                    return _("Wednesday");
                    break;

                case "Thursday":
                    return _("Thursday");
                    break;

                case "Friday":
                    return _("Friday");
                    break;

                case "Saturday":
                    return _("Saturday");
                    break;

                case "Sunday":
                    return _("Sunday");
                    break;

            };
        }
    }



    private function _getDate($format="numeric") {

        if ($format=="numeric") {
            $this->_date = str_replace("%/%",$this->_dateSeparator,$this->_date);
            $this->_date = str_replace("dd",$this->_day,$this->_date);
            $this->_date = str_replace("mm",$this->_month,$this->_date);
            $this->_date = str_replace("yyyy",$this->_year,$this->_date);
            return $this->_date;
        }

        if ($format=="text") {
            $this->_dateFormat = $this->_config->getParameter("medDateFormat");

            $dayName = i18n::translate(date("l"));
            $monthName = i18n::translate(date("F"));

            $this->_date = $this->_dateFormat;
            $this->_date = str_replace("dd",date("d"),$this->_date);
            $this->_date = str_replace("Dname",$dayName,$this->_date);
            $this->_date = str_replace("Mname",$monthName,$this->_date);
            $this->_date = str_replace("yyyy",date("Y"),$this->_date);
            return $this->_date;
        }
    }

    public function getDate() {
        return $this->_getDate("numeric");
    }

    public function getTextDate() {
        return $this->_getDate("text");
    }

    /**
     * Returns a UNIX timestamp from a textual datetime description. Wrapper for PHP function strtotime().
     *
     * @param string $dateString Datetime string to be represented as a Unix timestamp
     * @param int $userOffset User's offset from GMT (in hours)
     * @return integer Unix timestamp
     */
    public function toUnix($dateString, $userOffset = null) {
        $ret = $this->fromString($dateString, $userOffset);
        return $rest;
    }
    /**
     * Returns a date formatted for Atom RSS feeds.
     *
     * @param string $dateString Datetime string or Unix timestamp
     * @param int $userOffset User's offset from GMT (in hours)
     * @return string Formatted date string
     */
    public function toAtom($dateString, $userOffset = null) {
        $date = $this->fromString($dateString, $userOffset);
        $ret = date('Y-m-d\TH:i:s\Z', $date);
        return $rest;
    }
    /**
     * Formats date for RSS feeds
     *
     * @param string $dateString Datetime string or Unix timestamp
     * @param int $userOffset User's offset from GMT (in hours)
     * @return string Formatted date string
     */
    public function toRSS($dateString, $userOffset = null) {
        $date = $this->fromString($dateString, $userOffset);
        $ret = date("r", $date);
        return $ret;
    }

    /**
     * Returns a UNIX timestamp, given either a UNIX timestamp or a valid strtotime() date string.
     *
     * @param string $dateString Datetime string
     * @param int $userOffset User's offset from GMT (in hours)
     * @return string Parsed timestamp
     */
    public function fromString($dateString, $userOffset = null) {
        if (empty($dateString)) {
            return false;
        }
        if (is_int($dateString) || is_numeric($dateString)) {
            $date = intval($dateString);
        } else {
            $date = strtotime($dateString);
        }
        if ($userOffset !== null) {
            return $this->convert($date, $userOffset);
        }
        return $date;
    }


    public function fromUnix($format,$unix) {
        if (empty($unix)) {
            return false;
        }
        else {
            if (empty($format)) {
                $format = "l dS \of F Y H:i:s ";
            }
            $date = date($format,$unix);
            return $date;
        }

    }

    public static function toEuropeanDate($date) {
        $dt = date("d-m-Y",strtotime($date));
        return $dt;
    }

    public function getNow() {
        $time = time();
        $date = date("Y-m-d H:i:s", $time);
        return $date;
    }

    public static function getFullHumanDate($date) {
        $format    = "l d F \d\e Y H:i:s";
        setlocale (LC_TIME,FW_Locale::getInstance()->getLocale());
        $humanDate = strftime('%A %d de %B del %Y %H:%M:%S',strtotime($date));

        return $humanDate;
    }

}

?>