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
 * Class to identify the user_agent
 * and the operating system of the client.
 *
 * @author andreu
 *
 */
class FW_Browser extends FW_Singleton implements IComponent {


    /**
     * The user agent
     * 
     * @var string
     */
    private $_userAgent;
    
    /**
     * The name of the browser
     * 
     * @var string
     */
    private $_browserName;
    
    /**
     * The version of the browser
     * 
     * @var string
     */
    private $_browserVersion;
    
    /**
     * The name of the platform
     * 
     * @var string
     */
    private $_platformName;
    
    /**
     * The version of the platform
     * 
     * @var string
     */
    private $_platformVersion;
    
    /**
     * HTTP Referrer
     * 
     * @var string
     */
    private $_referrer;


    /**
     * The constructor of Browser component
     * @return null
     */
    public function __construct() {
        $this->configure(null);
        $this->initialize(array());
    }


    /* 
     * Configures the Browser component
     * 
     * @return void
     */
    public function configure(FW_Container_Parameter $parameters=null) {        
        $this->_userAgent = FW_Request::getInstance()->getUserAgent();
        $this->_referrer  = FW_Request::getInstance()->getHttpReferrer();
    }

    /* 
     * Initializes the browser component
     * 
     * @return void     
     */
    public function initialize(array $arguments=array()) {
        $this->_detectPlatform();
        $this->_detectBrowser();
    }
    
    
    /**
     * Gets the referrer from the HTTP
     * request
     * 
     * @return string
     */
    public function getReferrer() {
        return $this->_referrer;
    }
    
    /**
     * Gets the user agent HTTP header
     * 
     * @return string
     */    
    public function getUserAgent() {
        return $this->_userAgent;
    }
    
    public function getBrowserName() {
        return $this->_browserName;
    } 
    
    public function getBrowserVersion() {
        return $this->_browserVersion;        
    }

    /**
     * Gets the browser name
     *
     * @return void
     */
    public function getBrowser() {
        return $this->_browserName.' '.$this->_browserVersion;
    }
    
    public function getPlatformName() {
        return $this->_platformName;
    }
    
    public function getPlatformVersion() {
        return $this->_platformVersion;        
    }

    /**
     * Gets the platform name
     *
     * @return string
     */
    public function getPlatform() {
        return $this->_platformName.' '.$this->_platformVersion;
    }


    /**
     * Tries to identify the platform of the USER_AGENT
     * 
     * @return void
     */
    private function _detectPlatform() {
        $this->_checkForWindowsPlatform();
        $this->_checkForUnixPlatform();
        $this->_checkForMacPlatform();
        $this->_checkForWindowsPlatform();
        $this->_checkForMobilePlatform();
    }

    /**
     * Tries to detect a Windows platform
     *
     * @return void
     */
    private function _checkForWindowsPlatform()  {
        if (preg_match('/Windows 95/i',$this->_userAgent) || preg_match('/Win95/', $this->_userAgent)) {
            $this->_platformName = "Windows";
            $this->_platformVersion = "95";
        }

        if (preg_match('/Windows NT 4.0/i', $this->_userAgent) || preg_match('/WinNT4.0/i', $this->_userAgent)) {
            $this->_platformName = "Windows";
            $this->_platformVersion = "NT 4.0";
        }

        if (preg_match('/Windows NT/i', $this->_userAgent) || preg_match('/WinNT/i', $this->_userAgent)) {
            $this->_platformName = "Windows";
            $this->_platformVersion = "NT";
        }

        if (preg_match('/Windows NT 5.0/i', $this->_userAgent) || preg_match('/Windows 2000/i', $this->_userAgent)) {
            $this->_platformName = "Windows";
            $this->_platformVersion = "2000";
        }
        if (preg_match('/Win 9x 4.90/i', $this->_userAgent) || preg_match('/Windows ME/i', $this->_userAgent)) {
            $this->_platformName = "Windows";
            $this->_platformVersion = "ME";
        }
        if (preg_match('/Windows.98/i', $this->_userAgent) || preg_match('/Win98/i', $this->_userAgent)) {
            $this->_platformName = "Windows";
            $this->_platformVersion = "98";
        }
        if (preg_match('/Windows NT 6.0/i', $this->_userAgent)) {
            $this->_platformName = "Windows";
            $this->_platformVersion = "Vista";
        }
        if (preg_match('/Windows NT 6.1/i', $this->_userAgent)) {
            $this->_platformName = "Windows";
            $this->_platformVersion = "7";
        }
        if (preg_match('/Windows NT 5.1/i', $this->_userAgent)) {
            $this->_platformName = "Windows";
            $this->_platformVersion = "XP";
        }
        if (preg_match('/Windows NT 5.2/i', $this->_userAgent)) {
            $this->_platformName = "Windows";
            if (preg_match('/Win64/i', $this->_userAgent)) {
                $this->_platformVersion = "XP 64 bit";
            }
            else {
                $this->_platformVersion = "Server 2003";
            }
        }
    }

    /**
     * Tries to detect an *Nix platform
     *
     * @return void
     */
    private function _checkForUnixPlatform()  {
        if (preg_match('/Linux/i', $this->_userAgent)) {
            $this->_platformName = "Linux";
            if (preg_match('/Debian/i', $this->_userAgent)) {
                $this->_platformName = "Debian GNU/Linux";

            }
            if (preg_match('/Ubuntu\/\d+\.\d+/i', $this->_userAgent,$matches)) {
                $this->_platformName = "Ubuntu GNU/Linux";
                if (!empty($matches)) {
                    $version = explode('/',$matches[0]);
                    $this->_platformVersion = $version[1];
                }
                 

            }
            if (preg_match('/Mandrake/i', $this->_userAgent)) {
                $this->_platformName = "Mandrake Linux";

            }
            if (preg_match('/SuSE/i', $this->_userAgent)) {
                $this->_platformName = "SuSE Linux";

            }
            if (preg_match('/Novell/i', $this->_userAgent)) {
                $this->_platformName = "Novell Linux";

            }
            if (preg_match('/Red ?Hat/i', $this->_userAgent)) {
                $this->_platformName = "RedHat Linux";

            }
            if (preg_match('/Gentoo/i', $this->_userAgent)) {
                $this->_platformName = "Gentoo Linux";

            }
            if (preg_match('/Fedora/i', $this->_userAgent)) {
                $this->_platformName = "Fedora Linux";

            }
            if (preg_match('/MEPIS/i', $this->_userAgent)) {
                $this->_platformName = "MEPIS Linux";

            }
            if (preg_match('/Knoppix/i', $this->_userAgent)) {
                $this->_platformName = "Knoppix Linux";

            }
            if (preg_match('/Slackware/i', $this->_userAgent)) {
                $this->_platformName = "Slackware Linux";

            }
            if (preg_match('/Xandros/i', $this->_userAgent)) {
                $this->_platformName = "Xandros Linux";

            }
            if (preg_match('/Kanotix/i', $this->_userAgent)) {
                $this->_platformName = "Kanotix Linux";

            }
            if (preg_match('/Pardus/i', $this->_userAgent)) {
                $this->_platformName = "Pardus Linux";

            }
        }
        if (preg_match('/FreeBSD/i', $this->_userAgent)) {
            $this->_platformName = "FreeBSD";

        }
        if (preg_match('/NetBSD/i', $this->_userAgent)) {
            $this->_platformName = "NetBSD";

        }
        if (preg_match('/OpenBSD/i', $this->_userAgent)) {
            $this->_platformName = "OpenBSD";

        }
        if (preg_match('/IRIX/i', $this->_userAgent)) {
            $this->_platformName = "SGI IRIX";

        }
        if (preg_match('/SunOS/i', $this->_userAgent)) {
            $this->_platformName = "Solaris";

        }
        if (preg_match('/Unix/i', $this->_userAgent)) {
            $this->_platformName = "UNIX";

        }


    }

    /**
     * Tries to detect a Mac OS Platform
     *
     * @return void
     */
    private function _checkForMacPlatform()  {
        if (preg_match('/Mac_PowerPC/i', $this->_userAgent)) {
            $this->_platformName = "Mac OS";

        }
        if (preg_match('/Mac OS X/i', $this->_userAgent)) {
            $this->_platformName = "Mac OS";
            $this->_platformVersion = "X";

        }
        if (preg_match('/Macintosh/i', $this->_userAgent)) {
            $this->_platformName = "Mac OS";

        }
    }

    /**
     * Tries to detect a mobile operating system
     * platform
     *
     * @return void
     */
    private function _checkForMobilePlatform()  {

        if (preg_match('/iPad/i',$this->_userAgent)) {
            $this->_platformName = "Apple iPad";

        }

        if (preg_match('/(iPhone|iPod)(.*?)AppleWebKit\//i', $this->_userAgent, $matches)) {
            $this->_platformName = "Apple iPhone";
            $ver = preg_match('/CPU (iPhone|iPod) OS(.*?)like/i',$matches[2],$mmatches);
            if ($ver) {
                $this->_platformVersion     = $mmatches[2];
            }
        }

        if (preg_match('/iPod/i',$this->_userAgent)) {
            $this->_platformName = "Apple iPod";

        }

        if (preg_match('/Android/i',$this->_userAgent)) {
            $this->_platformName = "Android";

        }

        if (preg_match('/Blackberry/i', $this->_userAgent)) {
            $this->_platformName = "Blackberry";

        }

        if (preg_match('/(pre\/|palm os|palm|hiptop|avantgo|plucker|xiino|blazer|elaine)/i',$this->_userAgent)) {
            $this->_platformName = "Palm OS";

        }

        if (preg_match('/(iris|3g_t|windows ce|opera mobi|windows ce; smartphone;|windows ce; iemobile)/i',$this->_userAgent)) {
            $this->_platformName = "Windows Mobile Smartphone";

        }

        if (preg_match('/PalmOS/i', $this->_userAgent)) {
            $this->_platformName = "Palm OS";

        }

        if (preg_match('/Windows CE/i', $this->_userAgent)) {
            $this->_platformName = "Windows CE";

        }
        if (preg_match('/QtEmbedded/i', $this->_userAgent)) {
            $this->_platformName = "Qtopia";

        }
        if (preg_match('/Zaurus/i', $this->_userAgent)) {
            $this->_platformName = "Linux Zaurus";

        }
        if (preg_match('/Symbian/i', $this->_userAgent)) {
            $this->_platformName = "Symbian OS";

        }
        if (preg_match('/PalmOS\/sony\/model/i', $this->_userAgent)) {
            $this->_platformName = "Sony Clie";

        }
        if (preg_match('/Zaurus ([a-zA-Z0-9.]+)/i', $this->_userAgent, $matches)) {
            $this->_platformName = "Sharp Zaurus " . $matches[1];

        }
        if (preg_match('/Series ([0-9]+)/i', $this->_userAgent, $matches)) {
            $this->_platformName = "Nokia";

        }
        if (preg_match('/Nokia ([0-9]+)/i', $this->_userAgent, $matches)) {
            $this->_platformName = "Nokia";

        }
        if (preg_match('/SIE-([a-zA-Z0-9]+)/i', $this->_userAgent, $matches)) {
            $this->_platformName = "Siemens";

        }
        if (preg_match('/dopod([a-zA-Z0-9]+)/i', $this->_userAgent, $matches)) {
            $this->_platformName = "Dopod";

        }
        if (preg_match('/o2 xda ([a-zA-Z0-9 ]+);/i', $this->_userAgent, $matches)) {
            $this->_platformName = "O2 XDA";

        }
        if (preg_match('/SEC-([a-zA-Z0-9]+)/i', $this->_userAgent, $matches)) {
            $this->_platformName = "Samsung";

        }
        if (preg_match('/SonyEricsson ?([a-zA-Z0-9]+)/i', $this->_userAgent, $matches)) {
            $this->_platformName = "SonyEricsson";

        }

    }



    /**
     * Tries to detect the browser using the user agent header
     *
     * @return void
     */
    private function _detectBrowser() {

        if (preg_match('/(iPhone|iPod)(.*?)AppleWebKit\//i', $this->_userAgent, $matches)) {
            $ver = preg_match('/CPU (iPhone|iPod) OS(.*?)like/i',$matches[2],$mmatches);
            if ($ver) {
                $this->_browserName     = $mmatches[1];
                $this->_browserVersion  = $mmatches[2];
            }


        }

        if (preg_match('/Netscape[0-9]?\/([a-zA-Z0-9.]+)/i', $this->_userAgent, $matches)) {
            $this->_browserName = 'Netscape';
            $this->_browserVersion = $matches[1];

        }

        if (preg_match('/^Mozilla\/5.0/i', $this->_userAgent) && preg_match('/rv:([a-zA-Z0-9.]+)/i', $this->_userAgent, $matches)) {
            $this->_browserName = 'Mozilla';
            $this->_browserVersion = $matches[1];

        }

        if (preg_match('/^Mozilla\/([a-zA-Z0-9.]+)/i', $this->_userAgent, $matches)) {
            $this->_browserName = 'Netscape Navigator';
            $this->_browserVersion = $matches[1];

        }

        if (preg_match('/MovableType[ \/]([a-zA-Z0-9.]+)/i', $this->_userAgent, $matches)) {
            $this->_browserName = 'MovableType';
            $this->_browserVersion = $matches[1];

        }

        if (preg_match('/WordPress[ \/]([a-zA-Z0-9.]*)/i', $this->_userAgent, $matches)) {
            $this->_browserName = 'WordPress';
            $this->_browserVersion = $matches[1];

        }
        if (preg_match('/typepad[ \/]([a-zA-Z0-9.]*)/i', $this->_userAgent, $matches)) {
            $this->_browserName = 'TypePad';
            $this->_browserVersion = $matches[1];

        }
        if (preg_match('/drupal/i', $this->_userAgent)) {
            $this->_browserName = 'Drupal';
            $this->_browserVersion = count($matches) > 0 ? $matches[1] : "";

        }

        if (preg_match('/(Camino|Chimera)[ \/]([a-zA-Z0-9.]+)/i', $this->_userAgent, $matches)) {
            $this->_browserName = 'Camino';
            $this->_browserVersion = $matches[2];

        }

        if (preg_match('/PHP/', $this->_userAgent, $matches)) {
            $this->_browserName = 'PHP';

        }

        if (preg_match('/w3m\/([a-zA-Z0-9.]+)/i', $this->_userAgent, $matches)) {
            $this->_browserName = 'W3M';
            $this->_browserVersion = $matches[1];

        }
        if (preg_match('/Dillo[ \/]([a-zA-Z0-9.]+)/i', $this->_userAgent, $matches)) {
            $this->_browserName = 'Dillo';
            $this->_browserVersion = $matches[1];

        }
        if (preg_match('/Epiphany\/([a-zA-Z0-9.]+)/i', $this->_userAgent, $matches)) {
            $this->_browserName = 'Epiphany';
            $this->_browserVersion = $matches[1];

        }
        if (preg_match('/(SeaMonkey)\/([a-zA-Z0-9.]+)/i', $this->_userAgent, $matches)) {
            $this->_browserName = 'Mozilla SeaMonkey';
            $this->_browserVersion = $matches[2];

        }
        if (preg_match('/Flock\/([a-zA-Z0-9.]+)/i', $this->_userAgent, $matches)) {
            $this->_browserName = 'Flock';
            $this->_browserVersion = $matches[1];

        }
        if (preg_match('/(Firefox|Phoenix|Firebird|BonEcho|GranParadiso|Minefield|Iceweasel)\/([a-zA-Z0-9.]+)/i', $this->_userAgent, $matches)) {
            $this->_browserName = 'Mozilla Firefox';
            $this->_browserVersion = $matches[2];

        }
        if (preg_match('/Minimo\/([a-zA-Z0-9.]+)/i', $this->_userAgent, $matches)) {
            $this->_browserName = 'Minimo';
            $this->_browserVersion = $matches[1];

        }
        if (preg_match('/PSP \(PlayStation Portable\)\; ([a-zA-Z0-9.]+)/', $this->_userAgent, $matches)) {
            $this->_browserName = "Sony PSP";
            $this->_browserVersion = $matches[1];

        }
        if (preg_match('/Galeon\/([a-zA-Z0-9.]+)/i', $this->_userAgent, $matches)) {
            $this->_browserName = 'Galeon';
            $this->_browserVersion = $matches[1];

        }
        if (preg_match('/K-Meleon\/([a-zA-Z0-9.]+)/i', $this->_userAgent, $matches)) {
            $this->_browserName = 'K-Meleon';
            $this->_browserVersion = $matches[1];

        }
        if (preg_match('/Lynx\/([a-zA-Z0-9.]+)/i', $this->_userAgent, $matches)) {
            $this->_browserName = 'Lynx';
            $this->_browserVersion = $matches[1];

        }
        if (preg_match('/Links \\(([a-zA-Z0-9.]+)/i', $this->_userAgent, $matches)) {
            $this->_browserName = 'Links';
            $this->_browserVersion = $matches[1];

        }
        if (preg_match('/ELinks[\/ ]([a-zA-Z0-9.]+)/i', $this->_userAgent, $matches)) {
            $this->_browserName = 'ELinks';
            $this->_browserVersion = $matches[1];

        }
        if (preg_match('/Konqueror\/([a-zA-Z0-9.]+)/i', $this->_userAgent, $matches)) {
            $this->_browserName = 'Konqueror';
            $this->_browserVersion = $matches[1];

        }
        if (preg_match('/Safari\/([a-zA-Z0-9.]+)/i', $this->_userAgent, $matches)) {
            $this->_browserName = 'Safari';
            $this->_browserVersion = $matches[1];

        }
        if (preg_match('/Chrome\/([a-zA-Z0-9.]+)/i', $this->_userAgent, $matches)) {
            $this->_browserName = 'Google Chrome';
            $this->_browserVersion = $matches[1];

        }
        if (preg_match('/Chromium\/([a-zA-Z0-9.]+)/i', $this->_userAgent, $matches)) {
            $this->_browserName = 'Google Chrome';
            $this->_browserVersion = $matches[1];

        }
        if (preg_match('/opera mini/i', $this->_userAgent)) {
            $this->_browserName = 'Opera Mini';
            preg_match('/Opera\/([a-zA-Z0-9.]+)/i', $this->_userAgent, $matches);
            $this->_browserVersion = $matches[1];

        }
        if (preg_match('/Opera[ \/]([a-zA-Z0-9.]+)/i', $this->_userAgent, $matches)) {
            $this->_browserName = 'Opera';
            $this->_browserVersion = $matches[1];

        }
        if (preg_match('/Blackberry([0-9]+)/i', $this->_userAgent, $matches)) {
            $this->_browserName = "Blackberry";
            $this->_browserVersion = $matches[1];

        }
        if (preg_match('/Blackberry([0-9]+)/i', $this->_userAgent, $matches)) {
            $this->_browserName = "Blackberry";
            $this->_browserVersion = $matches[1];

        }


        if (preg_match('/AOL(.*?)\s[0-9]\.[0-9](.*?)/i', $this->_userAgent, $matches)) {            
            $this->_browserName = 'AOL';
            $this->_browserVersion = $matches[1];

        }

        if (preg_match('/MSIE ([a-zA-Z0-9.]+)/i', $this->_userAgent, $matches)) {
            $this->_browserName = 'Internet Explorer';
            $this->_browserVersion = $matches[1];

        }

    }




};
?>