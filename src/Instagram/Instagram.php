<?php
namespace Instagram;

/**
 * Description of Instagram
 *
 * @author gaetan
 */
class Instagram {
    
    private $app_id;
    private $url_api;
    
    function __construct($app_id, $url_api) 
    {
        $this->app_id = $app_id;
        $this->url_api = $url_api;
    }
    
    public  function get($endpoint, $options = null)
    {
		$ch = curl_init();
		$strUrl = $this->url_api . $endpoint . '?';
		
		if (!is_null($options)) {
			$options['client_id'] = $this->app_id;
		}
		foreach($options as $strName => $strKey){
			$strUrl .= sprintf('%s=%s&', $strName, urlencode($strKey));
		}
		
		curl_setopt($ch, CURLOPT_URL, $strUrl);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		
		$body = curl_exec($ch);
		curl_close($ch);
		
		$json = json_decode($body, true);
		return $json;
	}
    
}

?>
