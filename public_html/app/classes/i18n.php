<?php

class i18n

{

    

    public static function t($identifier)

    {

        

        $language = Session::get('language', null);
	if(is_null($language))
	{
	
		// trying to detect browser language
		$language = substr(Input::server('HTTP_ACCEPT_LANGUAGE', 'de'), 0, 2);
		
	}

        if($string = Model_Ts::query()->where('identifier', '=', $identifier)->where('language', '=', $language)->get_one())

        {

            return $string->translation;

        }

        else

        {

            

            if($language == 'en')

            {

                

                $ts = new Model_Ts;

                $ts->identifier = $identifier;

                $ts->translation = $identifier;

                $ts->language = $language;

                $ts->save();

                   

            }

            

            return $identifier;

            

        }

    

    }

    

}