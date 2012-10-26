<?php
class FW_Twitter_Api extends FW_Singleton {

    public $http_code;
    public $url;
    public $host = "https://api.twitter.com/1/";
    public $timeout = 30;
    public $connecttimeout = 30;
    public $ssl_verifypeer = FALSE;
    public $format = "json";
    public $decode_json = TRUE;
    public $http_info;
    public $useragent = "TwitterOAuth v0.2.0-beta2";
    //public $retry = TRUE;

    /**
     * Set API URLS
     */
    public function accessTokenURL() {
        return 'https://api.twitter.com/oauth/access_token';
    }

    public function authenticateURL() {
        return 'https://twitter.com/oauth/authenticate';
    }

    public function authorizeURL() {
        return 'https://twitter.com/oauth/authorize';
    }

    public function requestTokenURL() {
        return 'https://api.twitter.com/oauth/request_token';
    }

    /**
     * Debug helpers
     */
    public function lastStatusCode() {
        return $this -> http_status;
    }

    public function lastAPICall() {
        return $this -> last_api_call;
    }

    /**
     * construct TwitterOAuth object
     */
    public function __construct($consumer_key, $consumer_secret, $oauth_token = NULL, $oauth_token_secret = NULL) {
        $this -> sha1_method = new FW_OAuth_SignatureMethod_HmacSHA1();
        $this -> consumer = new FW_OAuth_Consumer($consumer_key, $consumer_secret);

        if (!empty($oauth_token) && !empty($oauth_token_secret)) {
            $this -> token = new FW_OAuth_Consumer($oauth_token, $oauth_token_secret);
        } else {
            $this -> token = NULL;
        }
    }

    /**
     * Get a request_token from Twitter
     *
     * @returns a key/value array containing oauth_token and oauth_token_secret
     */
    public function getRequestToken($oauth_callback = NULL) {
        $parameters = array();

        if (!empty($oauth_callback)) {
            $parameters['oauth_callback'] = $oauth_callback;
        }

        $request = $this->oAuthRequest($this->requestTokenURL(),'GET',$parameters);        
        $token = FW_OAuth_Util::parse_parameters($request);

        if (isset($token['oauth_token'])) {
            $this -> token = new FW_OAuth_Consumer($token['oauth_token'], $token['oauth_token_secret']);
        }
        return $token;
    }

    /**
     * Get the authorize URL
     *
     * @returns a string
     */
    public function getAuthorizeURL($token, $sign_in_with_twitter = TRUE) {
        if (is_array($token)) {
            $token = $token['oauth_token'];
        }

        if (empty($sign_in_with_twitter)) {
            return $this -> authorizeURL() . "?oauth_token={$token}";
        } else {
            return $this -> authenticateURL() . "?oauth_token={$token}";
        }
    }

    /**
     * Exchange request token and secret for an access token and
     * secret, to sign API calls.
     *
     * @returns array("oauth_token" => "the-access-token",
     *                "oauth_token_secret" => "the-access-secret",
     *                "user_id" => "9436992",
     *                "screen_name" => "abraham")
     */
    public function getAccessToken($oauth_verifier = FALSE) {
        $parameters = array();

        if (!empty($oauth_verifier)) {
            $parameters['oauth_verifier'] = $oauth_verifier;
        }

        $request = $this->oAuthRequest($this->accessTokenURL(),'GET',$parameters);
        var_dump($request);
        $token = FW_OAuth_Util::parse_parameters($request);
        var_dump($token);
        
        $this -> token = new FW_OAuth_Consumer($token['oauth_token'], $token['oauth_token_secret']);

        return $token;
    }

    /**
     * One time exchange of username and password for access token and secret.
     *
     * @returns array("oauth_token" => "the-access-token",
     *                "oauth_token_secret" => "the-access-secret",
     *                "user_id" => "9436992",
     *                "screen_name" => "abraham",
     *                "x_auth_expires" => "0")
     */
    public function getXAuthToken($username, $password) {
        $parameters = array();

        $parameters['x_auth_username'] = $username;
        $parameters['x_auth_password'] = $password;
        $parameters['x_auth_mode'] = 'client_auth';

        $request = $this -> oAuthRequest($this -> accessTokenURL(), 'POST', $parameters);
        $token = FW_OAuth_Util::parse_parameters($request);
        $this -> token = new FW_OAuth_Consumer($token['oauth_token'], $token['oauth_token_secret']);

        return $token;
    }

    /**
     * GET wrapper for oAuthRequest.
     */
    public function get($url, $parameters = array()) {
        $response = $this -> oAuthRequest($url, 'GET', $parameters);

        if ($this -> format === 'json' && $this -> decode_json) {
            return json_decode($response);
        }

        return $response;
    }

    /**
     * POST wrapper for oAuthRequest.
     */
    public function post($url, $parameters = array()) {
        $response = $this -> oAuthRequest($url, 'POST', $parameters);

        if ($this -> format === 'json' && $this -> decode_json) {
            return json_decode($response);
        }

        return $response;
    }

    /**
     * DELETE wrapper for oAuthReqeust.
     */
    public function delete($url, $parameters = array()) {
        $response = $this -> oAuthRequest($url, 'DELETE', $parameters);

        if ($this -> format === 'json' && $this -> decode_json) {
            return json_decode($response);
        }

        return $response;
    }

    /**
     * Format and sign an OAuth / API request
     */
    public function oAuthRequest($url, $method, $parameters) {
        if (strrpos($url, 'https://') !== 0 && strrpos($url, 'http://') !== 0) {
            $url = "{$this->host}{$url}.{$this->format}";
        }
                
        $request = FW_OAuth_Request::from_consumer_and_token($this->consumer,$this->token, $method, $url, $parameters);                
        $request->sign_request($this->sha1_method,$this->consumer,$this->token);
        
        switch ($method) {
            case 'GET' :                
                return $this->http($request->to_url(),'GET');
            default :
                return $this->http($request->get_normalized_http_url(),$method,$request->to_postdata());
        }
    }

    /**
     * Make an HTTP request
     *
     * @return API results
     */
    public function http($url, $method, $postfields = NULL) {
        $this -> http_info = array();
        $ci = curl_init();

        curl_setopt($ci, CURLOPT_USERAGENT, $this -> useragent);
        curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, $this -> connecttimeout);
        curl_setopt($ci, CURLOPT_TIMEOUT, $this -> timeout);
        curl_setopt($ci, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ci, CURLOPT_HTTPHEADER, array('Expect:'));
        curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, $this -> ssl_verifypeer);
        curl_setopt($ci, CURLOPT_HEADERFUNCTION, array($this, 'getHeader'));
        curl_setopt($ci, CURLOPT_HEADER, FALSE);
        
        switch($method) {
            case 'POST' :
                curl_setopt($ci, CURLOPT_POST, TRUE);

                if (!empty($postfields)) {
                    curl_setopt($ci, CURLOPT_POSTFIELDS, $postfields);
                }
                break;

            case 'DELETE' :
                curl_setopt($ci, CURLOPT_CUSTOMREQUEST, 'DELETE');

                if (!empty($postfields)) {
                    $url = "{$url}?{$postfields}";
                }
        }

        curl_setopt($ci, CURLOPT_URL, $url);
        var_dump($url);
        $response = curl_exec($ci);        
        
        $this->http_code = curl_getinfo($ci, CURLINFO_HTTP_CODE);
        $this->http_info = array_merge($this -> http_info, curl_getinfo($ci));

        $this->url = $url;
        
        curl_close($ci);
        

        return $response;
    }

    /**
     * Get the header info to store.
     */
    public function getHeader($ch, $header) {
        $i = strpos($header, ':');

        if (!empty($i)) {
            $key = str_replace('-', '_', strtolower(substr($header, 0, $i)));
            $value = trim(substr($header, $i + 2));
            $this -> http_header[$key] = $value;
        }

        return strlen($header);
    }

};
?>