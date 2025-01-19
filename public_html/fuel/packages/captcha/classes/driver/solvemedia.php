<?php
namespace Captcha;

class Driver_Solvemedia
{
    	/** Config
	 * (array)
	 * The config array
	 */
	private $config;

	/** Error
	 * (string)
	 * Contains any errors generated whilst checking a recaptcha attempt
	 */
	protected $error;

/** Construct
 * 
 * Loads the config file and adds it to the config property
 */
	public function __construct()
	{
		\Config::load('solvemedia', true, false, true);
		$this->config = \Config::get('solvemedia');
		if ( ! $this->config['challenge_key'])
		{
			throw new Captcha_Exception('Solvemedia Captcha needs a challenge key to be specified in the config file');
		}
		if ( ! $this->config['verify_key'])
		{
			throw new Captcha_Exception('Solvemedia Captcha needs a verify key to be specified in the config file');
		}
        if ( ! $this->config['hash_key'])
		{
			throw new Captcha_Exception('Solvemedia Captcha needs a hash key to be specified in the config file');
		}
	}	

/** Forge
 * 
 * Returns the current instance, or forges a new one if none exist
 */
	public static function forge()
	{
		static $instance = null;

		if ($instance === null)
		{
			$instance = new static;
		}
		
		return $instance;
	}
	
/** Check
 * 
 * Calls an HTTP POST function to verify if the user's guess was correct
 * 
 * @param string $remote_ip
 * @param string $challenge
 * @param string $response
 * @param array $extra_params an array of extra variables to post to the server
 * @return bool
 */
	public function check($remote_ip = null, $response = null, $extra_params = array())
	{
		$remote_ip = \Input::real_ip();
		if ($remote_ip == '0.0.0.0' or $remote_ip == '')
		{
			throw new Captcha_Exception('Solvemedia Captcha needs a valid Remote IP');
		}
		if (is_null($response))
		{
			$response = \Input::post('adcopy_response');
		}
		
		$response = (string) e($response);
		
		if ($response === '')
		{
			$this->error = 'Please provide a answer';
			return false;
		}
        
        $data['privatekey'] = $this->config['verify_key'];
        $data['challenge'] = \Input::post('adcopy_challenge');
        $data['response'] = $response;
        $data['remoteip'] = $remote_ip;
        
        $solvemedia_response = $this->_http_post('verify.solvemedia.com', '/papi/verify', $data);
        \Debug::dump($solvemedia_response);
        $answers = explode ("\n", $solvemedia_response[1]);
        
        
        if( strlen($this->config['hash_key']) ){
                # validate message authenticator
                $hash = sha1( $answers[0] . $data['challenge'] .$this->config['hash_key'] );

                if( $hash != $answers[2] ){
                        $this->error = 'hash-fail';
                        return false;
                }
        }

        if (trim ($answers [0]) == 'true') {
                return true;
        }
        else {
                $this->error = $answers[1];
                return false;
        }
        
	}

/** Html
 * 
 * Gets the challenge HTML (javascript and non-javascript version).
 * 
 * @param string $view The view to load (optional, default is null)
 * @param string $error The error given by reCAPTCHA (optional, default is null)
 * @param boolean $use_ssl Should the request be made over ssl? (optional, default is false)
 * @return string - The HTML to be embedded in the user's form.
 */
	public function html($view = null)
	{
	   
		if (is_null($view))
		{
			$view = $this->config['default_view'];
		}
		
		$data = array();
		$data['challenge_key'] = $this->config['challenge_key'];
		return \View::forge($view, $data);
	}

/** Qsencode
 * 
 * Encodes the given data into a query string format
 * 
 * @param array $data - array of string elements to be encoded
 * @return  string - encoded request
 */
	private function _qsencode($data)
	{
		return http_build_query($data);
	}

/** Http Post
 * 
 * Submits an HTTP POST to a reCAPTCHA server
 * 
 * @param string $host
 * @param string $path
 * @param array $data
 * @param int port
 * @return array response
 */
	private function _http_post($host, $path, $data, $port = 80)
	{
		$req = $this->_qsencode($data);
		
		$http_request = implode('',array(
			"POST $path HTTP/1.0\r\n",
			"Host: $host\r\n",
			"Content-Type: application/x-www-form-urlencoded;\r\n",
			"Content-Length:".strlen($req)."\r\n",
			"User-Agent: SolvemediaCAPTCHA/PHP\r\n",
			"\r\n",
			$req));
		
		$response = '';
		if( false == ( $fs = @fsockopen($host, $port, $errno, $errstr, 10) ) )
		{
			throw new Captcha_Exception('Solvemedia Captcha could not open Socket');
			return false;
		}
		
		fwrite($fs, $http_request);
		while (!feof($fs))
		{
			$response .= fgets($fs, 1160); // One TCP-IP packet
		}
		fclose($fs);
		$response = explode("\r\n\r\n", $response, 2);
		return $response;
	}

/** Error
 * Returns error
 * @return string
 */
	public function error()
	{
		if ($this->error)
		{
			return $this->error;
		}
		else
		{
			return false;
		}
	}	
}