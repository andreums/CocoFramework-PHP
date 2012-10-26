<?php
class Account {

    private $name;
    private $provider;
    private $username;
    private $password;
    private $sender;
    private $active;
    private $description;

    public function fromArray(array $data=array()) {
        if (!empty($data)) {
            foreach ($data as $key=>$value) {
                $this->$key = $value;
            }
        }
    }

    public function toArray() {
        $data       = array();
        $reflect    = new ReflectionObject($this);
        $properties = $reflect->getProperties();
        foreach ($properties as $property) {
            $data [$property->getName()] = $property->getValue($this);
        }
        return $data;
    }

    public function getProvider() {
        return $this->provider;
    }

    public function getName() {
        return $this->name;
    }

    public function getUsername() {
        return $this->username;
    }

    public function getPassword() {
        return $this->password;
    }

    public function getActive() {
        return $this->active;
    }

    public function getSender() {
        return $this->sender;
    }

    public function getDescription() {
        return $this->description;
    }

    public function activate() {
        $this->active = true;
    }

    public function deactivate() {
        $this->active = false;
    }


    public function setDescription($description) {
        $this->description = $description;
    }

    public function setUsername($username) {
        $this->username = $username;
    }

    public function setPassword($password) {
        $this->passowrd = $password;
    }

    public function setSender($sender) {
        $this->sender = $sender;
    }

    public function setProvider($provider) {
        $this->provider = $provider;
    }



};
?>