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

class Driver_Circlecaptcha_Image
{


/** Construct
 * 
 * @param array
 * 
 * Runs the Config through the set() function
 */
	public function __construct($config = array())
	{
		$this->set($config);
	}

/** Set
 * 
 * @param string or array
 * @param any
 * 
 * Sets variables into the class properties.
 * example set('captcha_width', 50) would set the captcha_width property to 50
 * 
 * example set(array('captcha_width' => 50, 'captcha_height' => 10))
 * This would set the captcha width to 50 and the height to 10
 * 
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
			}
		}
		elseif ($value)
		{
			if (property_exists(__CLASS__, $config))
			{
				$this->$config = $value;
			}
		}
	}

/** Create
 * 
 * @return image response object
 * 
 * Creates the captcha response
 */
	public function create()
	{
	   
       
       $width = 400;	
        $height = 150;
        $circle = 15;
        
        $im	= imagecreatetruecolor($width, $height);	//Create image / Erzeuge das Bild	

        $target_circle = rand(1, $circle);	//choose an opened circle / wähle einen geöffneten Kreis aus
        
        $background = imagecolorallocate($im, 255, 255, 255);			//Background color / Hintergrundfarbe
        $border = imagecolorallocate($im, 0, 0, 0);								//Border color / Rahmenfarbe
        imagefilledrectangle($im, 0, 0, $width - 1, $height - 1, $background);	//Fill image with background color / Fülle Bild mit Hintergrundfarbe
        imagerectangle($im, 0, 0, $width - 1, $height - 1, $border);
        
        $color = array();
        $color[] = imagecolorallocatealpha($im, 0, 0, 0, 90);			//black
	    $color[] = imagecolorallocatealpha($im, 128, 0, 0, 90);		//brown
	    $color[] = imagecolorallocatealpha($im, 255, 0, 0, 80);		//red
	    $color[] = imagecolorallocatealpha($im, 0, 255, 0, 70);		//green
	    $color[] = imagecolorallocatealpha($im, 0, 0, 255, 90);		//blue
	    $color[] = imagecolorallocatealpha($im, 255, 153, 0, 70);	//orange
	    $color[] = imagecolorallocatealpha($im, 153, 0, 255, 90);	//purple
	    $color[] = imagecolorallocatealpha($im, 0, 255, 255, 70);	//cyan
        shuffle($color);	//shuffle color array / mische die Farben im Array

        for($i=0; $i < $circle; $i++){
		
            $diameter = mt_rand(30,50);							
            $radius = ($diameter / 2)+2;					
            $x = mt_rand($radius,$width-$radius);	
            $y = mt_rand($radius,$height-$radius);	
		
            $start = 0;	
            $end = 360; 
            
            if($target_circle == $i)
            {		
                $start = rand(0, 360);	
                $end = $start-45;
			 \Session::set('circlecaptcha', array('circle_x' => $x, 'circle_y' => $y, 'circle_r' => $radius));
            }
		
            imagesetthickness($im, 2);	
            imagearc($im, $x, $y, $diameter, $diameter, $start, $end, $color[rand(0, count($color)-1)]);
        
        }
        
        $response = new \Fuel\Core\Response();
		$response->set_header('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate');
		$response->set_header('Expires', 'Mon, 26 Jul 1997 05:00:00 GMT');
		$response->set_header('Pragma', 'no-cache');
        	
        $response->set_header('Content-Type','image/png');
        $response->body = imagepng($im);
		imagedestroy($im);
		
        
        return $response;
	
    }
	

}

/* end of file image.php */
