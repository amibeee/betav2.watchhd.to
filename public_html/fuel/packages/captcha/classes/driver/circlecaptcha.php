<?php
/**
 * Captcha - a driver based captcha package for fuelphp
 * 
 * @package Captcha
 * @version v1.0
 * @author Carl Craig
 * @license MIT License
 * @copyright 2012 Carl Craig
 */
namespace Captcha;

class Driver_Circlecaptcha
{


    public $error = '';

/** Construct
 * 
 * Loads the config file and adds it to the config property
 */
	public function __construct()
	{
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
	
/** Set
 * 
 * @param string or array
 * @param any
 * 
 * Sets variables into the class properties or config properties.
 * example set('captcha_width', 50) would set the captcha_width property to 50
 * 
 * example set(array('captcha_width' => 50, 'captcha_height' => 10))
 * This would set the captcha width to 50 and the height to 10
 * 
 * If a property doesnt exist, the config array will be checked to see if
 * a relevant key exists
 */
	public function set($config, $value = null)
	{		
		if (is_array($config))
		{
			foreach ($config as $property => $value)
			{
				if (property_exists(__CLASS__, $property))
				{
					$this->$property = $value;
				}
				else if (array_key_exists($property, $this->config))
				{
					$this->config[$property] = $value;
				}
			}
		}
		elseif ($value)
		{
			if (property_exists(__CLASS__, $config))
			{
				$this->$config = $value;
			}
			elseif (array_key_exists($config, $this->config))
			{
				$this->config[$config] = $value;
			}
		}
	}

/** Image
 * 
 * @param array
 * @return Image - Response Object
 * 
 * Sets the environment and then generates the captcha image
 * If config is provided it will run it through the set() function
 */
	public function image($config = null)
	{
		if ($config)
		{
			$this->set($config);
		}
		
		$image = new Driver_Circlecaptcha_Image();
		return $image->create();
	}

/** Check
 * 
 * @param null or string
 * @return Bool
 * 
 * Checks a Key against the one stored in the session
 * Returns true on a match
 * If no key is given, Key will be retrieved from post
 * using the post_key_name specified in the config file
 */
	public function check($key = null)
	{
	   
        
        $session = \Session::get('circlecaptcha', false);
        if(!$session)
        {
            $this->error = 'Session crashed.';
            return false;
        }
        
        if(!isset($session['circle_x']) || !isset($session['circle_y']) || !isset($session['circle_r']))
        {
            $this->error = 'Session Values not found.';
            return false;
        }
        
        $button_x = \Input::post('button_x', 0);
        $button_y = \Input::post('button_y', 0);
    
        
        if($button_x >= $session['circle_x']-$session['circle_r'] 
        && $button_x <= $session['circle_x']+$session['circle_r']
        && $button_y >= $session['circle_y']-$session['circle_r']
        && $button_y <= $session['circle_y']+$session['circle_r'])
        {
            return true;
        }
        else
        {
            $this->error = 'You have not clicked into the open circle.';
            return false;
        }
        

	}

/** Html
 * 
 * @param array
 * @return Html object
 * 
 * Returns the html for the captcha, the image tag and input field;
 * Can be given configuration when called
 * e.g. Captcha::html('simplecaptcha', array('view' => 'simplecaptcha/custom', 'captcha_error' => 'oops'))
 * This would set the view to simplecaptcha/custom, and set an error message variable to 'oops'
 * view = the view to be displayed, will default to simplecaptcha/default if none given
 * captcha_route = the route to where the captcha image is created, will default to the one specified in config
 */
	public function html($config = null)
	{
		$html = \View::forge('circlecaptcha/default', array());
			return $html;
    
	}

/** Set_Font
 * 
 * Selects a random font from font_list, and creates a path to is using font_directory
 * It calculates the new height and width based on font_height
 */
	protected function set_font()
	{
		$this->font = $this->config['font_directory'].$this->config['font_list'][array_rand($this->config['font_list'], 1)];
		$this->font_height = $this->config['font_height'];
		$this->font_width = (int) ceil($this->calculate_size($this->config['font_png_width']));
		return true;
	}

/** Set_Symbol
 * 
 * Runs through the symbol_list, removes blacklisted symbols
 * It adjusts the width for each symbol
 * It creates the x coordinate for each symbol
 */
	protected function set_symbol()
	{
		$x = 0;
		foreach ($this->config['symbol_list'] as $index => $data)
		{
			$data['width'] = $this->calculate_size($data['width']);
			if ( ! in_array($data['symbol'], $this->config['symbol_blacklist']))
			{
				$data['x'] = $x;
				$this->symbol_list[] = $data;
			}
			$x += $data['width'];
		}
		$this->symbol_total = count($this->symbol_list);
		return true;
	}

/** Set_Key
 * 
 * Generates a random key for the captcha
 */
	protected function set_key()
	{
		for ($i = 0; $i < $this->captcha_length; $i++)
		{
			$key = $this->symbol_list[array_rand($this->symbol_list)];
			if ($key['width'] + $this->key_width > $this->captcha_width)
			{
				break;
			}
			$this->key_array[] = $key;
			$this->key_string .= $key['symbol'];
			$this->key_width += $key['width'];
		}
		$this->store_key($this->key_string);
		return true;
	}
    
    public function error()
    {
        return $this->error;
    }
    
/** Store_Key
 * 
 * Stores a key in the session
 */
	protected function store_key($key)
	{
		$key_hashed = $this->hash_key($key);
		\Session::set($this->config['session_key_name'], $key_hashed);
	}

/** Hash_Key
 * 
 * Hashes a key with the salt specified in the config file
 */
	protected function hash_key($key)
	{
		$key_salted = $this->config['salt'].$key.$this->config['salt'];
		return md5((string) $key_salted);
	}

/** Calculate_Size
 * 
 * calculates a new size based on the new font size;
 * This will keep all x coordinates and symbol widths relative to the font size;
 */	
	protected function calculate_size($value)
	{
		if (! $this->percent)
		{
			$this->percent = $this->config['font_height']/$this->config['font_png_height'];
		}
		return $value * $this->percent;
	}
}

/* end of file simplecaptcha.php */