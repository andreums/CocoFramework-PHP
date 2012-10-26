<?php
class EmailPlugin extends FW_Plugin_Base {

    private $_phpmailerDir = "";
    private $_templateDir = "";
    private $_mailer = null;
    protected $_filter;
    private static $_status = false;

    private $_mail;
    private $_subject;
    private $_template;
    private $_templateData;
    private $templateType;
    private $_mailData;
    private $_type;
    private $_altText;
    private $_mailError;

    protected function _setOptions($options) {
        $this->_options = $options;
    }

    public function _setUp() {

        $this->_filter = Filter::getInstance();
        $this->_phpmailerDir = $this->getPluginPath().'/'."phpmailer".DS;
        $this->_templateDir = getcwd().'/'."framework".'/'."mailTemplates".DS;

        $this->_loadClasses();
        $this->_mail = new PHPMailer(true);

        $mailType = $this->_getOption("mailMethod");

        if ( ($mailType!=null) && ($mailType=="smtp")) {
            $this->_mail->IsSMTP();
            $this->_mail->SMTPAuth   = $this->_getOption("enableSMTPAuthentication");
            $this->_mail->SMTPSecure = $this->_getOption("smtpSecurity");
            $this->_mail->Host       = $this->_getOption("smtpHost");
            $this->_mail->Port       = $this->_getOption("smtpPort");
            $this->_mail->Username   = $this->_getOption("smtpUsername");
            $this->_mail->Password   = $this->_getOption("smtpPassword");

            $from = $this->_getOption("from");
            $from = explode("|",$from);
            $fromMail = $from[0];
            $fromName = $from[1];
            $this->_mail->SetFrom($fromMail,$fromName);

            $replyTo = $this->_getOption("replyTo");
            if ($replyTo!=null) {
                $replyTo = explode("|",$replyTo);
                $replyToMail = $replyTo[0];
                $replyToName = $replyTo[1];
                $this->_mail->AddReplyTo($replyToMail,$replyToName);
            }
        }

        if ( ($mailType!=null) && ($mailType=="sendmail")) {

            $from = $this->_getOption("from");
            $from = explode("|",$from);
            $fromMail = $from[0];
            $fromName = $from[1];
            $this->_mail->SetFrom($fromMail,$fromName);

            $replyTo = $this->_getOption("replyTo");
            if ($replyTo!=null) {
                $replyTo = explode("|",$replyTo);
                $replyToMail = $replyTo[0];
                $replyToName = $replyTo[1];
                $this->_mail->AddReplyTo($replyToMail,$replyToName);
            }
        }

    }


    private function _loadClasses() {
        try {
            $route = $this->_phpmailerDir.'/'."class.phpmailer.php";
            require_once $route;

            $this->_mail = new PHPMailer();
        }
        catch (Exception $ex) {
            trigger_error("Plugin | EmailPlugin cannot load the classes that it needs",E_USER_NOTICE);
        };
    }




    private function _renderMailTemplate($templateName,$data) {

        try {
            if ($this->_templateType=="html") {
                $templateFile = $this->_templateDir."{$templateName}.php";
            }
            if ($this->_templateType=="text") {
                $templateFile = $this->_templateDir."{$templateName}.txt";
            }

            $contents = file_get_contents($templateFile);
            $toReplace = array();
            foreach (explode("\n",$contents) as $line) {
                $tagOpensAt=strpos($line,"{%");
                $tagClosesAt=strpos($line,"%}");
                if ( ($tagOpensAt!==false) && ($tagClosesAt!==false) ) {
                    $len = intval($tagClosesAt)-intval($tagOpensAt);
                    $param = (substr($line,$tagOpensAt,$len+2));
                    $toReplace[] =$param;
                }
            }

            if (count($toReplace)>0) {
                foreach ($toReplace as $pattern) {
                    $name = substr($pattern,2,strlen($pattern)-4);
                    $value = $data[$name];
                    $contents = str_replace($pattern,$value,$contents,$count);
                }
            }
            return $contents;

        }
        catch (Exception $ex) {
            trigger_error("Plugin | EmailPlugin cannot load the template {$templateName} to render it",E_USER_NOTICE);
        }
    }

    private function _getImagesFromHTML($content) {
        if (preg_match_all("/\s*<img.+?src=\"(.+?)\"/",$content,$matches) ) {
            return $matches[1];
        }
    }


    public function setEncoding($encoding) {

        if ($encoding=="8bit") {
            $this->_mail->Encoding = "8bit";
        }
        if ($encoding=="7bit") {
            $this->_mail->Encoding = "7bit";
        }
    }

    private function _sendMail() {

        if (!$this->_mail) {
            return false;
        }

        else {
            if ($this->_type=="text") {
                if ($this->_mail->Encoding=="7bit") {
                    $message = $this->_filter->decodeUTF8($this->_mailData);
                }
                else {

                }
                $this->_mailer->Body = $message;
                $this->_mailer->WordWrap = 80;
            }
            if ($this->_type=="template") {
                $pinfo = pathinfo($this->_template);
                $extension = $pinfo["extension"];
                $contents = $this->_renderMailTemplate($this->_template,$this->_templateData);

                if ($this->_mail->Encoding=="7bit") {
                    $contents = $this->_filter->decodeUTF8($contents);
                }

                $this->_mail->MsgHTML($contents);

                if (!empty($this->_altText)) {
                    $this->_altText = $this->_filter->decodeUTF8($this->_altText);
                    $this->_mail->AltBody = $this->_altText;
                }
            }

            $this->_mailError = "";

            if(!$this->_mail->Send()) {
                $this->_mailError =  $this->_mailer->ErrorInfo;
                return false;
            }
            else {
                return true;
            }
        }


    }

    public function addAttachment($attachmentFile,$name="",$encoding="base64",$type="application/octet-stream") {

        if (!$this->_mail) {
            return false;
        }

        if (file_exists($attachmentFile)) {

            if ($name=="") {
                $fileInfo = pathinfo($attachmentFile);
                if ($fileInfo) {
                    $name = $fileInfo["basename"];
                }
                else {
                    trigger_error("PLUGIN | Email Plugin cannot obtain information about the attachment {$attachmentfile}",E_USER_ERROR);
                    return false;
                }
            }
            return $this->_mail->AddAttachment($attachmentFile,$name);
        }
        else {
            trigger_error("PLUGIN | Email Plugin cannot locate  attachment file {$attachmentfile}",E_USER_ERROR);
            return false;
        }
    }



    public function setFrom($address,$name) {
        if (!$address || !$name) {
            return false;
        }
        else {
            if (!$this->_mail) {
                return false;
            }
            else {
                $this->_mail->SetFrom($address,$name,true);
            }
        }
    }

    public function addAddress($email,$name="") {
        if (!$this->_mail) {
            return false;
        }
        else {
            return $this->_mail->AddAddress($email,$name);
        }
    }

    public function addReplyTo($email,$name="") {
        if (!$this->_mail) {
            return false;
        }
        else {
            return $this->_mail->AddReplyTo($email,$name);
        }
    }


    public function addBCC($email,$name="") {
        if (!$this->_mail) {
            return false;
        }
        else {
            return $this->_mail->AddBCC($email,$name);
        }
    }

    public function addCC($email,$name="") {
        if (!$this->_mail) {
            return false;
        }
        else {
            return $this->_mail->AddCC($email,$name);
        }
    }

    public function clearAddresses() {
        if (!$this->_mail) {
            return false;
        }
        else {
            return $this->_mail->ClearAddresses();
        }
    }

    public function clearCC() {
        if (!$this->_mail) {
            return false;
        }
        else {
            return $this->_mail->ClearCCs();
        }
    }

    public function clearReplyTo() {
        if (!$this->_mail) {
            return false;
        }
        else {
            return $this->_mail->ClearReplyTos();
        }
    }


    public function clearAllRecipients() {
        if (!$this->_mail) {
            return false;
        }
        else {
            return $this->_mail->ClearAllRecipients();
        }
    }

    public function clearAttachments() {
        if (!$this->_mail) {
            return false;
        }
        else {
            return $this->_mail->ClearAttachments();
        }
    }

    public function send() {
        if (!$this->_mail) {
            return false;
        }
        else {
            return $this->_sendMail();
        }
    }


    public function composeTextMail ($subject,$text) {

        if (!$this->_mail) {
            return false;
        }

        $this->_type = "text";
        $this->_mail->Subject = $subject;
        $this->_mail->Body = $text;
        return true;

    }

    public function composeTemplateMail ($subject,$templateFile,$templateType,$templateData,$altText) {

        if (!$this->_mail) {
            return false;
        }

        $this->_type = "template";
        $this->_mail->Subject = $subject;
        $this->_template = $templateFile;
        $this->_templateData = $templateData;
        $this->_altText = $altText;
        $this->_templateType = $templateType;
        return true;

    }

    public function startMail() {
        unset($this->_mail);
        $this->_mail = new PHPMailer();
    }

}

?>