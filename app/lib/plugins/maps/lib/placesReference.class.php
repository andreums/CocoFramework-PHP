<?php
class GooglePlaces
    {
        private $APIKey = "xxxxxxxxx";
 
        public $OutputType = "json";  //either json, xml or array
 
        public $Errors  = array();
 
        private $APIUrl = "https://maps.googleapis.com/maps/api/place";
        private $APICallType = "";
 
        private $IncludeDetails = false; 
 
        //all calls
        private $Language       = 'en';     //optional - https://spreadsheets.google.com/pub?key=p9pdwsai2hDMsLkXsoM05KQ&gid=1
 
        //Search
        private $Location;                  //REQUIRED  - This must be provided as a google.maps.LatLng object.
        private $Radius;                    //REQUIRED  
        private $Types;                     //optional - separate tyep with pipe symbol  http://code.google.com/apis/maps/documentation/places/supported_types.html  
 
        private $Name;                      //optional
 
        //Search, Details, 
        private $Sensor         = 'false';  //REQUIRED - is $Location coming from a sensor? like GPS?
 
        //Details & Delete
        private $Reference;
 
        //Add
        private $Accuracy;
 
        public function Search()
            {
                $this->APICallType = "search";
 
                return $this->APICall();
            }           
 
        public function Details()
            {
                $this->APICallType = "details";
 
                return $this->APICall();
            }
 
        public function Checkin()
            {
                $this->APICallType = "checkin-in";
 
                return $this->APICall();
            }
 
        public function Add()
            {
                $this->APICallType = "add";
 
                return $this->APICall();
            }
 
        public function Delete()
            {
                $this->APICallType = "delete";
 
                return $this->APICall();
            }
 
 
        public function SetLocation($Location)
            {
                $this->Location = $Location;
            }
        public function SetRadius($Radius)
            {
                $this->Radius = $Radius;
            }
        public function SetTypes($Types)
            {
                $this->Types = $Types;
            }
        public function SetLanguage($Language)
            {
                $this->Language = $Language;
            }
        public function SetName($Name)
            {
                $this->Name = $Name;
            }
        public function SetSensor($Sensor)
            {
                $this->Sensor = $Sensor;
            }
        public function SetReference($Reference)
            {
                $this->Reference = $Reference;
            }
        public function SetAccuracy($Accuracy)
            {
                $this->Accuracy = $Accuracy;
            }
        public function SetIncludeDetails($IncludeDetails)
            {
                $this->IncludeDetails = $IncludeDetails;
            }
 
        private function CheckForErrors()
            {
                if(empty($this->APICallType))
                    {
                        $this->Errors[] = "API Call Type is required but is missing.";
                    }
                if(empty($this->APIKey))
                    {
                        $this->Errors[] = "API Key is is required but is missing.";
                    }
 
                if(($this->OutputType!="json") && ($this->OutputType!="xml") && ($this->OutputType!="json"))
                    {
                        $this->Errors[] = "OutputType is required but is missing.";
                    }
 
 
 
            }
 
 
 
        private function APICall()
            {
                $this->CheckForErrors();
 
                if($this->APICallType=="add" || $this->APICallType=="delete")
                    {
                        $URLToPostTo = $this->APIUrl."/".$this->APICallType."/".$this->OutputType."?key=".$this->APIKey."&sensor=".$this->Sensor;
 
                        if($this->APICallType=="add")
                            {
                                $LocationArray = explode(",", $this->Location);
                                $lat = trim($LocationArray[0]);
                                $lng = trim($LocationArray[1]);
 
                                $paramstopost[location][lat]    = $lat;
                                $paramstopost[location][lng]    = $lng;
                                $paramstopost[accuracy]         = $this->Accuracy;
                                $paramstopost[name]             = $this->Name;
                                $paramstopost[types]            = explode("|", $this->Types);
                                $paramstopost[language]         = $this->Language;
                            }
                        if($this->APICallType=="delete")
                            {
                                $paramstopost[reference]        = $this->Reference;
                            }
 
                        $result = json_decode($this->CurlCall($URLToPostTo,json_encode($paramstopost)));
                        $result->errors = $this->Errors;
                        return $result;
 
                    }
 
                if($this->APICallType=="search")
                    {
                        $URLparams = "location=".$this->Location."&radius=".$this->Radius."&types=".$this->Types."&language=".$this->Language."&name=".$this->Name."&sensor=".$this->Sensor;
                    }
                if($this->APICallType=="details")
                    {
                        $URLparams = "reference=".$this->Reference."&language=".$this->Language."&sensor=".$this->Sensor;
                    }
                if($this->APICallType=="check-in")
                    {
                        $URLparams = "reference=".$this->Reference."&language=".$this->Language."&sensor=".$this->Sensor;
                    }
 
 
                $URLToCall = $this->APIUrl."/".$this->APICallType."/".$this->OutputType."?key=".$this->APIKey."&".$URLparams;
 
                $result = json_decode(file_get_contents($URLToCall),true);
 
                $result[errors] = $this->Errors;
 
                if($result[status]=="OK" && $this->APICallType=="details")
                    {
                        foreach($result[result][address_components] as $key=>$component)
                            {
                                if($component[types][0]=="street_number")
                                    {
                                        $address_street_number = $component[short_name];
                                    }
                                if($component[types][0]=="route")
                                    {
                                        $address_street_name = $component[short_name];
                                    }
                                if($component[types][0]=="locality")
                                    {
                                        $address_city = $component[short_name];
                                    }
                                if($component[types][0]=="administrative_area_level_1")
                                    {
                                        $address_state = $component[short_name];
                                    }
                                if($component[types][0]=="postal_code")
                                    {
                                        $address_postal_code = $component[short_name];
                                    }
                            }
 
                        $result[result][address_fixed][street_number]           = $address_street_number;
                        $result[result][address_fixed][address_street_name]     = $address_street_name;
                        $result[result][address_fixed][address_city]            = $address_city;
                        $result[result][address_fixed][address_state]           = $address_state;
                        $result[result][address_fixed][address_postal_code]     = $address_postal_code;
                    }
                return $result;
 
            }
 
        private function CurlCall($url,$topost)
            {
                $ch = curl_init(); 
                curl_setopt($ch, CURLOPT_URL, $url); 
                curl_setopt($ch, CURLOPT_HEADER, FALSE);  
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); 
                curl_setopt($ch, CURLOPT_POSTFIELDS, $topost);
                $body = curl_exec($ch); 
                curl_close($ch); 
 
                return $body;
 
            }
}

?>