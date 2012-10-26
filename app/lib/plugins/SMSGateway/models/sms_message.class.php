<?php

class sms_message extends FW_ActiveRecord_Model {

    protected $id;
    protected $account;
    protected $username;
    protected $text;
    protected $date;
    protected $telephone;
    protected $status;
    protected $type;
    protected $readed;
    protected $batch_id;
    protected $delivered;
    protected $in_reply_to;
    protected $failed_to_deliver;


    public function getContactName() {

        $telephone = $this->telephone;
        $contact = contact::findByColumn("cellTel",$telephone,array(array("column"=>"id","type"=>"ASC")));
        $contact = $contact->first();
        if ($telephone[0]==3 && $telephone[1]==4) {
            $telephone2 = substr($telephone,2);
        }
        $contact2 = contact::findByColumn("cellTel",$telephone,array(array("column"=>"id","type"=>"ASC")));
        $contact2 = $contact2->first();



        if ($contact!=null && $contact2!=null) {
            $contact = $contact;
            return ($contact->getDisplayName());
        }
        if ($contact==null && $contact2!=null) {
            $contact = $contact2;
            return ($contact->getDisplayName());
        }
        if ($contact!=null && $contact2==null) {
            $contact = $contact;
            return ($contact->getDisplayName());
        }
        if ($contact==null && $contact2==null) {
            return $this->telephone;
        }
        return $this->telephone;
    }



    public function getType() {
        if ($type=="1") {
            return $this->_filter->decodeUTF8(_("Recibido"));
        }
        else {
            return $this->_filter->decodeUTF8(_("Enviado"));
        }
    }
    public function getText() {
        $text = str_replace("\n"," ",$this->text);
        $text = trim($text);
        $text = chunk_split($text);
        $text = utf8_encode($text);
        return $text;
    }

    public function getStatus() {
        if ($this->type=="1") {
            if ($this->readed=="0") {
                return $this->_filter->decodeUTF8(_("No leído"));
            }
            else {
                return $this->_filter->decodeUTF8(_("Leído"));
            }
        }
        if ($this->type=="0") {
            if ( ($this->delivered=="0") && ($this->failed_to_deliver=="0") ) {
                return $this->_filter->decodeUTF8(_("En progreso"));
            }
            else if ( ($this->delivered=="1") && ($this->failed_to_deliver=="0") ) {
                return $this->_filter->decodeUTF8(_("Entregado"));
            }
            else if ( ($this->delivered=="0") && ($this->failed_to_deliver=="1") ) {
                return $this->_filter->decodeUTF8(_("Error"));
            }
            else {
                return $this->_filter->decodeUTF8(_("Desconocido"));
            }
        }
    }

    public function getDate() {
        return (date("d/m/Y H:i:s",strtotime($this->date)));
    }

    public function markAsReaded($var=false) {

        if (!$var) {
            if ($this->readed==1) {
                $this->readed=0;
            }
            else {
                $this->readed=1;
            }
        }
        else {
            if ($this->readed==0) {
                $this->readed = 1;
            }
        }
        return $this->save();
    }

};
?>