<?php

namespace Ens\JobBundle\Utils;

class Job
{
    static public function slugify($text, $link = '-')
    {
    	
    	if(empty($text))
    	{
    		return 'n-a';
    	}else
    	{
		    	// replace non letter or digits by -
		  		$text = preg_replace('#[^\\pL\d]+#u', '-', $text);

		  		// trim
		  		$text = trim($text, '-');

		  		// transliterate
				if (function_exists('iconv'))
				{
				    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
				}

		  		// remove unwanted characters
		  		$text = preg_replace('#[^-\w]+#', '', $text);

			    // trim and lowercase
			    $text = strtolower(trim($text, $link));    		
	 
		 		if(empty($text))
		 		{
		 			return 'n-a';
		 		}

	 				return $text;		
    	}    
    }

}

?>