<?php
/**
 * Fuel
 *
 * Fuel is a fast, lightweight, community driven PHP5 framework.
 *
 * @package    Fuel
 * @version    1.8
 * @author     Fuel Development Team
 * @license    MIT License
 * @copyright  2010 - 2016 Fuel Development Team
 * @link       http://fuelphp.com
 */


class Twig_Fuel_Extension extends Twig_Extension
{
	/**
	 * Gets the name of the extension.
	 *
	 * @return  string
	 */
	public function getName()
	{
		return 'fuel';
	}
    
    /**
	 * Sets up all of the Gloabls this extension makes available.
	 *
	 * @return  array
	 */
     public function getGlobals()
     {
        return array(
            'timestamp' => time()
        );
     }
    

	/**
	 * Sets up all of the functions this extension makes available.
	 *
	 * @return  array
	 */
     
	public function getFunctions()
	{
		return array(
			'fuel_version'      => new Twig_Function_Method($this, 'fuel_version'),
			'url'               => new Twig_Function_Method($this, 'url'),

			'base_url'          => new Twig_Function_Function('Uri::base'),
			'current_url'       => new Twig_Function_Function('Uri::current'),
			'uri_segment'       => new Twig_Function_Function('Uri::segment'),
			'uri_segments'      => new Twig_Function_Function('Uri::segments'),

			'config'            => new Twig_Function_Function('Config::get'),

			'dump'              => new Twig_Function_Function('Debug::dump'),

			'lang'              => new Twig_Function_Function('Lang::get'),

			'form_open'         => new Twig_Function_Function('Form::open'),
			'form_close'        => new Twig_Function_Method($this, 'twig_form_close_extension'),
			'form_input'        => new Twig_Function_Function('Form::input'),
			'form_password'     => new Twig_Function_Function('Form::password'),
			'form_hidden'       => new Twig_Function_Function('Form::hidden'),
			'form_radio'        => new Twig_Function_Function('Form::radio'),
			'form_checkbox'     => new Twig_Function_Function('Form::checkbox'),
			'form_textarea'     => new Twig_Function_Function('Form::textarea'),
			'form_file'         => new Twig_Function_Function('Form::file'),
			'form_button'       => new Twig_Function_Function('Form::button'),
			'form_reset'        => new Twig_Function_Function('Form::reset'),
			'form_submit'       => new Twig_Function_Function('Form::submit'),
			'form_select'       => new Twig_Function_Function('Form::select'),
			'form_label'        => new Twig_Function_Function('Form::label'),
            
            'securelink'        => new Twig_Function_Function('Securelink::generate'),
            
			'form_val'          => new Twig_Function_Function('Input::param'),
			'input_get'         => new Twig_Function_Function('Input::get'),
			'input_post'        => new Twig_Function_Function('Input::post'),

			'asset_add_path'    => new Twig_Function_Function('Asset::add_path'),
			'asset_css'         => new Twig_Function_Function('Asset::css'),
			'asset_js'          => new Twig_Function_Function('Asset::js'),
			'asset_img'         => new Twig_Function_Function('Asset::img'),
			'asset_render'      => new Twig_Function_Function('Asset::render'),
			'asset_find_file'   => new Twig_Function_Function('Asset::find_file'),

			'theme_asset_css'   => new Twig_Function_Method($this, 'theme_asset_css'),
			'theme_asset_js'    => new Twig_Function_Method($this, 'theme_asset_js'),
			'theme_asset_img'   => new Twig_Function_Method($this, 'theme_asset_img'),

			'html_anchor'       => new Twig_Function_Function('Html::anchor'),
			'html_mail_to_safe' => new Twig_Function_Function('Html::mail_to_safe'),

			'session_get'       => new Twig_Function_Function('Session::get'),
			'session_get_flash' => new Twig_Function_Function('Session::get_flash'),

			'markdown_parse'    => new Twig_Function_Function('Markdown::parse'),

			'auth_has_access'   => new Twig_Function_Function('Auth::has_access'),
			'auth_check'        => new Twig_Function_Function('Auth::check'),
			'auth_get'          => new Twig_Function_Function('Auth::get'),
            
            'form_value'        => new Twig_Function_Method($this, 'twig_form_value_extension'),
            
            'sum_format'        => new Twig_Function_Method($this, 'twig_sum_format_extension'),
            
            'mathcaptcha'       => new Twig_Function_Function('Mathcaptcha::generate'),
            'askcaptcha'       => new Twig_Function_Function('Askcaptcha::generate'),
            
            'on_tv'                  => new Twig_Function_Function(function($channel_id){
                return \EPG::nowOnTV($channel_id);
            }),
            
            'next_on_tv'             => new Twig_Function_Function(function($channel_id){
                return \EPG::nextOnTV($channel_id);
            }),
            
            'continuous_channel_programm_list'             => new Twig_Function_Function(function($channel_id, $limit){
                return \EPG::programme($channel_id, $limit);
            }),
            
            'calculate_minutes' => new Twig_Function_Function(function($start, $end){
                return (($end-$start)/60);
            }),
            
            'calculate_status' => new Twig_Function_Function(function($start, $end){
                
                if($start == 0 | $end == 0) return 0;
                
                $total = ($end-$start); # 100% 
                $onepercent = ($total/100); # 1%  
                $left = (time()-$start); # seconds left
                
                return ($left/$total*100);
               
            }),
            
            'does_user_record_this_channel' => new Twig_Function_Function(function($channel_id, $user_id, $start, $end){
                
                $recording = Model_Recording::query()->where('channel_id', '=', $channel_id)->where('start', '=', $start)->where('end', '=', $end)->get_one();
                if(!$recording) return false;
                
                if(Model_User_Recording::query()->where('recording_id', '=', $recording->id)->where('user_id', '=', $user_id)->count())
                {
                    return true;
                }
                else
                {
                    return false;
                }
        
                
            }),
            
            't'    => new Twig_Function_Function('i18n::t'),
            
            
		);
	}
    
     /**
     * 
     */
    public function twig_sum_format_extension($sum, $decimals = 2)
    {
        /*
        setlocale(LC_MONETARY, 'de_DE.UTF-8');
        return money_format('%!n', $sum);
        */
        return number_format($sum, $decimals, '.', '.');
    }

	/**
	 * Provides the url() functionality.  Generates a full url (including
	 * domain and index.php).
	 *
	 * @param   string  URI to make a full URL for (or name of a named route)
	 * @param   array   Array of named params for named routes
	 * @return  string
	 */
	public function url($uri = '', $named_params = array())
	{
		if ($named_uri = \Router::get($uri, $named_params))
		{
			$uri = $named_uri;
		}

		return \Uri::create($uri);
	}
    
    /**
     *  
     */     
    public function twig_form_close_extension()
    {
        return "<input type=\"hidden\" name=\"".\Config::get('security.csrf_token_key')."\" value=\"".\Security::fetch_token()."\" />\n</form>";
    }
    
    /**
     *  
     */ 
    public function twig_form_value_extension($field, $default = '', $repopulate = true)
    {
        return \Arr::get(Session::get_flash('input.old', array()), $field, \Input::post($field, $default));
    }
    
	public function fuel_version()
	{
		return \Fuel::VERSION;
	}

	public function theme_asset_css($stylesheets = array(), $attr = array(), $group = null, $raw = false)
	{
		return \Theme::instance()->asset->css($stylesheets, $attr, $group, $raw);
	}

	public function theme_asset_js($scripts = array(), $attr = array(), $group = null, $raw = false)
	{
		return \Theme::instance()->asset->js($scripts, $attr, $group, $raw);
	}

	public function theme_asset_img($images = array(), $attr = array(), $group = null)
	{
		return \Theme::instance()->asset->img($images, $attr, $group);
	}
}
