<?php
    interface ISMSProvider {

        public function __construct();
        public function configure(array $parameters=array());
        public function getCredit();
        public function sendMessage($telephone,$text,$repliable=false);
        public function receiveMessages();
        public function checkNewMessages();
        public function updateSMSStatus();

    };
?>