<?php
/**
 *  Andrés Ignacio Martínez Soto
 *  andresmartinezsoto@gmail.com  
 * Coco-PHP
 * License: Copyright (c) 2010-2012 Andrés Ignacio Martínez Soto    
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */
/*include "framework/lib/external/phpmailer/class.phpmailer.php";
include "framework/lib/external/phpmailer/class.smtp.php";
include "framework/lib/external/phpmailer/class.pop3.php";*/

/**
 * @author andreu
 *
 */
class FW_Mailer extends FW_Singleton {

    private $_mailer;
    private $_subject;
    private $_template;
    private $_templateData;
    private $templateType;
    private $_mailData;
    private $_type;
    private $_altText;
    private $_mailError;
    private $_contents;
    
    private $_templateDir;


    public function __construct() {
        $this->_configure();        
    }

    /**
     * Configures the Mailer with the default account
     * 
     * @return void 
     */
    private function _configure() {
        $this->_config  = FW_Config::getInstance();
        $this->_mailer  = new PHPMailer();
        $account        = $this->_config->mail->getDefaultAccount();
        if ($account!==null) {
            $this->_configureMailer($account);
        }
        $templateDir    = $this->_config->mail->getTemplateDir();
        if ($templateDir!==null) {
            $this->_templateDir = trim($templateDir,'/');            
        }
    }


    /**
     * Configures the Mailer with an account
     * 
     * @param array $account Data about an account
     * 
     * @return void
     */
    private function _configureMailer(array $account=array()) {
        $mailer = $this->_mailer;        
        if ($account!==null) {
            $mailer->IsSMTP();
            $mailer->Host     = $account["smtp"]["host"];
            $mailer->Port     = $account["smtp"]["port"];
            if (isset($account["smtp"]["authentication"])) {
                $mailer->Username = $account["smtp"]["authentication"]["username"];
                $mailer->Password = $account["smtp"]["authentication"]["password"];
                $mailer->SMTPAuth = true;
            }
            
            if (isset($account["identity"])) {
                $mailer->From     = utf8_decode($account["identity"]["email"]);
                $mailer->FromName = utf8_decode($account["identity"]["from"]);
                $mailer->Sender   = utf8_decode($mailer->FromName);
            }
            
            if ($mailer->Port!==25) {
                if ($mailer->Port === 465) {
                    $mailer->SMTPDebug  = true;                    
                    $mailer->SMTPSecure = "ssl";                    
                }
            }
        }
        $this->_mailer = $mailer;
    }
    
    /**
     * Uses an account with the mailer
     * 
     * @param string $name The name of the account
     * 
     * @return bool
     */
    public function useAccount($name) {
        $result = false;
        $account = $this->_config->mail->getAccount($name);
        if ($account!==null) {
            $this->_configureMailer($account);
            $result = true;
        }      
        return $result;  
    }


    private function _renderMailTemplate($templateFile,array $data=array()) {

        try {            
            $contents     = file_get_contents($templateFile);
            foreach ($data as $key=>$value) {                                
                $expr     = "{{".$key."}}";
                $contents = str_replace($expr,$value,$contents);
            }
            return $contents;

        }
        catch (Exception $ex) {
            trigger_error("Plugin | EmailPlugin cannot load the template {$templateName} to render it",E_USER_NOTICE);
        }
    }

    /**
     * Gets images from an html content
     * 
     * @param string $content The content
     * 
     * @return array
     */
    private function _getImagesFromHTML($content) {
        $matches = array();
        if (preg_match_all("/\s*<img.+?src=\"(.+?)\"/",$content,$matches) ) {
            array_shift($matches);
        }
        return $matches;
    }

	/**
     * Sends the mail
     * 
     * @return bool
     */
    private function _sendMail() {
        
        $result = false;
        
        if ($this->_mailer!==null) {            

            $message = $this->_mailer->Body;
            /*$message = utf8_decode($message);
            $message = str_replace('\n','\r\n', "<body text='%body%'>");*/
            
            

            if ($this->_type==="text") {               
                $this->_mailer->Body     = $message;
                //$this->_mailer->WordWrap = 80;
            }
            
            if ($this->_type==="html") {                
                $contents   = $this->_contents;
                $this->_mailer->MsgHTML($contents);

                if (!empty($this->_altText)) {
                    $this->_altText         = utf8_decode($this->_altText);
                    $this->_mailer->AltBody = $this->_altText;
                }                
            }
            
            if ($this->_type=="template") {
                $template  = "{$this->_templateDir}/{$this->_template}.php";
                $pinfo          = pathinfo($template);
                               
                
                $extension = $pinfo["extension"];                
                $contents   = $this->_renderMailTemplate($template,$this->_templateData);
                $contents   = utf8_decode($contents);

                $this->_mailer->MsgHTML($contents);

                if (!empty($this->_altText)) {
                    $this->_altText         = utf8_decode($this->_altText);
                    $this->_mailer->AltBody = $this->_altText;
                }
            }

            $this->_mailerError = "";

            if(!$this->_mailer->Send()) {
                $this->_mailerError =  $this->_mailer->ErrorInfo;                                
                $result = false;
            }
            else {
                $result = true;
            }
        }
        return $result;
    }

    
    /**
     * Adds an attachment
     * 
     * @param string $attachmentFile File of the attachment
     * @param string $name Name for this attachment
     * @param string $encoding Encoding for the attachment (default is Base64)
     * @param string $type The mime type of the attachment
     * 
     * @return $this
     */
    public function addAttachment($attachmentFile,$name="",$encoding="base64",$type="application/octet-stream") {

        if (!$this->_mailer) {
            $this->create();
        }

        if (is_file($attachmentFile)) {            
            if (empty($name)) {
                $fileInfo = pathinfo($attachmentFile);
                if ($fileInfo) {
                    $name = $fileInfo["basename"];
                }
                else {
                    throw new FW_Mailer_Exception("Mailer cannot obtain information about the attachment file {$attachmentFile}");                    
                }
            }
            $this->_mailer->AddAttachment($attachmentFile,$name);
        }
        else {
            throw new FW_Mailer_Exception("Mailer cannot locate the attachment file or it hasn't got permissions to read  {$attachmentFile}");            
        }
        return $this;
    }


	/**
     * Sets the From header
     * 
     * @param string $email The email
     * @param string $name The name
     * 
     * @return $this
     */
    public function setFrom($address,$name) {
         
        if (!$this->_mailer) {
            $this->create();
        }
        $this->_mailer->SetFrom($address,$name,true);
        return $this;
    }

    /**
     * Adds an address to the mail
     * 
     * @param string $email The email
     * @param string $name The name
     * 
     * @return $this
     */
    public function addAddress($email,$name="") {
        if (!$this->_mailer) {
            $this->create();
        }
        
        $name  = utf8_decode($name);
        $email = utf8_decode($email);
        
        $this->_mailer->AddAddress($email,$name);
        return $this;
    }

    /**
     * Adds a ReplyTo header
     * 
     * @param string $email The email
     * @param string $name The name
     * 
     * @return $this
     */
    public function addReplyTo($email,$name="") {
        if (!$this->_mailer) {
            $this->create();
        }
        
        $name  = utf8_decode($name);
        $email = utf8_decode($email);
        
        $this->_mailer->AddReplyTo($email,$name);
        return $this;
    }


    /**
     * Adds a BCC header
     * 
     * @param string $email The email
     * @param string $name The name
     * 
     * @return $this
     */
    public function addBCC($email,$name="") {
        if (!$this->_mailer) {
            $this->create();
        }
        
        $name  = utf8_decode($name);
        $email = utf8_decode($email);
        
        $this->_mailer->AddBCC($email,$name);
        return $this;
    }

    /**
     * Adds a CC header
     * 
     * @param string $email The email
     * @param string $name The name
     * 
     * @return $this
     */
    public function addCC($email,$name="") {
        if (!$this->_mailer) {
            $this->create();
        }
        
        $name  = utf8_decode($name);
        $email = utf8_decode($email);
        
        $this->_mailer->AddCC($email,$name);
        return $this;
    }

    /**
     * Clears all the addresses
     * 
     * @return void
     */
    public function clearAddresses() {
        if (!$this->_mailer) {
            $this->create();
        }
        $this->_mailer->ClearAddresses();        
    }

    
    /**
     * Clears all the CCs headers
     * 
     * @return void
     */
    public function clearCC() {
        if (!$this->_mailer) {
            $this->create();
        }
        $this->_mailer->ClearCCs();        
    }

    /**
     * Clears the ReplyTo header
     * 
     * @return void
     */
    public function clearReplyTo() {
        if (!$this->_mailer) {
            $this->create();
        }
        $this->_mailer->ClearReplyTos();        
    }


    /**
     * Clears all the recipients 
     * 
     * @return void
     */
    public function clearAllRecipients() {
        if (!$this->_mailer) {
            $this->create();
        }
        $this->_mailer->ClearAllRecipients();        
    }

    /**
     * Clears all the attachments
     * 
     * @return void
     */
    public function clearAttachments() {
        if (!$this->_mailer) {
            $this->create();
        }
        $this->_mailer->ClearAttachments();        
    }

    /**
     * Sends the mail 
     * 
     * @return bool
     */
    public function send() {
        if (!$this->_mailer) {
            return false;
        }        
        return $this->_sendMail();
    }


    public function composeTextMail ($subject,$text) {

        if (!$this->_mailer) {
            $this->create();
        }
        $this->_type                       =  "text";
        $this->_mailer->Subject = $subject;
        $this->_mailer->Body      = $text;
        return $this;

    }

    public function composeTemplateMail ($subject,$templateFile,$templateType,$templateData,$altText) {

        if (!$this->_mailer) {
            $this->create();
        }

        $this->_type            = "template";
        $this->_mailer->Subject = $subject;
        $this->_template        = $templateFile;
        $this->_templateData    = $templateData;
        $this->_altText         = $altText;
        $this->_templateType    = $templateType;
        return $this;

    }
    
    public function composeHTMLMail ($subject,$contents,$altText) {
        if (!$this->_mailer) {
            $this->create();
        }

        $this->_type                         = "html";
        $this->_mailer->Subject = $subject;
        $this->_contents               = $contents;        
        $this->_altText                   = $altText;        
        return $this;
    }
    

    public function create() {
        unset($this->_mailer);
        $this->_mailer = new PHPMailer();
        return $this;
    }
    
    public function createTrakingImage() {
        header('Content-Type: image/png');
        $im = @imagecreate(1, 1);
        $background_color = imagecolorallocate($im, 255, 255, 255);  // make it white
        imagepng($im,"image.png");
        imagedestroy($im);
    }

};
?>