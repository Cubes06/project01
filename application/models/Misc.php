<?php

	class Application_Model_Misc {
		
		
		
		public static function adjustURL($string) {
			$url = str_replace ( "'", '', $string );
			$url = str_replace ( '%20', ' ', $url );
			$url = preg_replace ( '~[^\\pL_]+~u', '', $url ); // substitutes anything but letters, numbers and '_' with separator
			$url = trim ( $url, "-" );
			$url = iconv ( "utf-8", "us-ascii//TRANSLIT", $url ); // you may opt for your own custom character map for encoding.
			//$url = strtolower ( $url );
			$url = preg_replace ( '~[^-a-zA-Z_]+~', '', $url ); // keep only letters, numbers, '_' and separator
			$url = preg_replace('~\b[a-zA-Z]{1,2}\b\s*~', '', $url);
			$url = str_replace ( '--', '', $url );
			$url = str_replace ( '--', '', $url );

			//$url = $this->cleanLastDart($url);

			return $url;
		}
		
		
		public static function cleanURL($string) {
			$url = str_replace ( "'", '', $string );
			$url = str_replace ( '%20', ' ', $url );
			$url = preg_replace ( '~[^\\pL_]+~u', '-', $url ); // substitutes anything but letters, numbers and '_' with separator
			$url = trim ( $url, "-" );
			$url = iconv ( "utf-8", "us-ascii//TRANSLIT", $url ); // you may opt for your own custom character map for encoding.
			//$url = strtolower ( $url );
			$url = preg_replace ( '~[^-a-zA-Z_]+~', '', $url ); // keep only letters, numbers, '_' and separator
			$url = preg_replace('~\b[a-zA-Z]{1,2}\b\s*~', '', $url);
			$url = str_replace ( '--', '-', $url );
			$url = str_replace ( '--', '-', $url );

			$url = $this->cleanLastDart($url);

			return $url;
		}
		
	}

