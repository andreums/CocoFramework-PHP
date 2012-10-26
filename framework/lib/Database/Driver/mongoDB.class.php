<?php
    class FW_Database_Driver_mongoDB extends FW_Database_Driver_Base {
        
        private static $_link;
        private $_result;
        private $_host;
        private $_collection;
        private $_username;
        private $_password;
        private $_persistence;
        private $_database;
        
        
        public function initialize(array$arguments= array()) {        
            $this->_result      = null;
            $this->_host        = $this->_configuration->getParameter("host");
            $this->_database    = $this->_configuration->getParameter("database");
            $this->_username    = $this->_configuration->getParameter("username");
            $this->_password    = $this->_configuration->getParameter("password");
            $this->_persistence = $this->_configuration->getParameter("persistence");            
            $this->connect();
        }


    
    
        /**
         * Gets information about the current driver
         *
         * @access public
         * @return string
         */
        public function info() {
            
        }

        /**
         * Connects to the database
         *
         * @access public
         * @return bool
         */
        public function connect() {
            $dsn = "";
            if ( (strlen($this->_username)>0) && (strlen($this->_password)>0) ) {
                $dsn  = "mongodb://{$this->_username}:{$this->_password}@{$this->_host}/{$this->_database}";
            }
            else {
                $dsn  = "mongodb://{$this->_host}/{$this->_database}";
            }
            
            try {
                if ($this->_persistence===true) {                                                    
                    $link = new Mongo($dsn,array("persist" => "x"));
                }
                else {
                    $link = new Mongo($dsn);
                }
                
                if ($link!==null) {
                    self::$_link = $link;
                    //$this->select($this->_database);
                    var_dump($link);
                    var_dump(self::$_link);
                                       
                    $article = array("title"=>"Foobar","content"=>"FoobarTar","saved_at"=>new MongoDate());
                    var_dump($article);
                    $database = $link->selectDB($this->_database);
                    $collection = $database->selectCollection("movies");
                    $cursor = ($collection->find());
                    var_dump(iterator_to_array($cursor));

                     
                    return true;
                }            
                return false;
            }
            catch (Exception $exception) {
                return false;
            }
            
        }

        /**
         * Disconnects from the database
         *
         * @access public
         * @return bool
         */
        public function disconnect() {
            self::$_link = null;
            return true;
        }
        
        public function select($database) {
            try {
                self::$_link->selectDB($database);                
            }
            catch (InvalidArgumentException $exception) {
                self::$_link = null;
                return false;
            }
            return true;
        }
        
        public function selectCollection($collection) {
            if (self::$_link!==null) {            
                self::$_link->$collection;
            }            
        }

        /**
         * Queries the database
         *
         * @param  string The SQL query
         * @access public
         * @return mixed The result
         */
        public function query($query) {
            $database   = $this->_database;
            $database   = self::$_link->database;
            $collection = $this->_collection;
            
            if (is_array($query)) {
                $collection = $query[0];   
                $query      = $query[1];            
               
            }
            //$this->selectCollection($collection);
            //$database->selectCollection($collection);
            var_dump($database->$collection->$query);
            
        }
        
        /**
         * Gets the affected rows for a query
         *
         * @access public
         * @return int Number of affected rows
         */
        public function affectedRows() { }

        /**
         * Gets the number of rows from a result
         *
         * @access public
         * @return int Number of rows of the result
         */
        public function numRows() { }            

        /**
         * Fetches the result of a query as array
         *
         * @access public
         * @return Array
         */
        public function fetchArray() { }

        /**
         * Fetches the result of a query as Row
         *
         * @access public
         * @return Array
         */
        public function fetchRow() { }

        /**
         * Fetches the result of a query as an associative array
         *
         * @access public
         * @return Array
         */
        public function fetchAssoc() { }

        /**
         * Fetches the result of a query as an object
         *
         * @access public
         * @return object
         */
        public function fetchObject() { }
        
        public function existsCollection($name) {
            $exists          = false;
            $database        = $this->_database;
            $collections     = self::$_link->$database->listCollections();
            $collectionNames = array();
            foreach ($collections as $collection) {
                $collectionNames[] = $collection->getName();
            }
            return in_array($name, $collectionNames);
        }
        

}
?>