<?php
namespace Instagram;

/**
 * Description of Instagram
 *
 * @author gaetan
 */
class Instagram {
    
    protected $clientId, $clientSecret, $accessToken;
    protected $requestTokenUrl= 'https://api.instagram.com/oauth/authenticate';
    protected $accessTokenUrl = 'https://api.instagram.com/oauth/access_token';
    protected $authorizeUrl   = 'https://instagram.com/oauth/authorize';
    protected $apiUrl         = 'https://api.instagram.com';
    protected $userAgent      = 'EpiInstagram';
    protected $apiVersion     = 'v1';
    
    function __construct($clientId = null, $clientSecret = null, $accessToken = null) 
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->accessToken = $accessToken;
    }
    
    private function getApiUrl($endpoint)
    {
        if (!empty($this->apiVersion)) {
            return "{$this->apiUrl}/{$this->apiVersion}{$endpoint}";
        } else {
            return "{$this->apiUrl}{$endpoint}";
        }
    }
  
    public function getAccessToken($code, $redirectUri)
    {
        $params = array('client_id' => $this->clientId, 'client_secret' => $this->clientSecret, 'grant_type' => 'authorization_code', 'redirect_uri' => $redirectUri, 'code' => $code);
        $qs = http_build_query($params);
        return $this->request('POST', "{$this->accessTokenUrl}", $params);
    }

    public function getAuthorizeUrl($redirectUri)
    {
        $params = array('client_id' => $this->clientId, 'redirect_uri' => $redirectUri, 'response_type' => 'code', 'display' => 'touch');
        $qs = http_build_query($params);
        return "{$this->authorizeUrl}?{$qs}";
    }
    
    
    
    public function delete($endpoint, $params = null)
    {
        return $this->request('DELETE', $endpoint, $params);
    }

    public function get($endpoint, $params = null)
    {
        return $this->request('GET', $endpoint, $params);
    }

    public function post($endpoint, $params = null)
    {
        return $this->request('POST', $endpoint, $params);
    }
    
    

    private function request($method, $endpoint, $params = null)
    {
        if (preg_match('#^https?://#', $endpoint)) {
            $url = $endpoint;
        } else {
            $url = $this->getApiUrl($endpoint);
        }

        if ($this->accessToken) {
            $params['access_token'] = $this->accessToken;
        } else {
            $params['client_id'] = $this->clientId;
            $params['client_secret'] = $this->clientSecret;
        }

        if ($method === 'GET') {
            $url .= is_null($params) ? '' : '?'.http_build_query($params, '', '&');
        }

        if ($method === 'DELETE') {
            $paramToken['access_token'] = $this->accessToken;
            $url .= is_null($paramToken) ? '' : '?'.http_build_query($paramToken, '', '&');
        }

        $ch  = curl_init($url);
        curl_setopt($ch, CURLOPT_USERAGENT, $this->userAgent);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->requestTimeout);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        if (isset($_SERVER ['SERVER_ADDR']) && !empty($_SERVER['SERVER_ADDR']) && $_SERVER['SERVER_ADDR'] != '127.0.0.1') {
            curl_setopt($ch, CURLOPT_INTERFACE, $_SERVER ['SERVER_ADDR']);
        }
        if ($method === 'POST' && $params !== null) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        }

        $body = curl_exec($ch);
		curl_close($ch);
        
		return $body;
    }
    
}

?>
