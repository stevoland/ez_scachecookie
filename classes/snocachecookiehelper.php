<?php

class sNoCacheCookieHelper
{

    private function __construct()
    {
    }

	static public function setCookie()
	{
		$ini   = eZINI::instance( 'snocachecookie.ini' );
		$hasUserData = false;
		$displayedData = $ini->variable( 'NoCacheCookieSettings', 'DisplayedData' ) || array();
		$cookieValue = $ini->variable( 'NoCacheCookieSettings', 'CookieValue' ) || 'true';
		if ( $cookieValue === true )
		{
			$cookieValue = 'true';
		}
		$useDetailedValue = ( $ini->variable( 'NoCacheCookieSettings', 'DetailedCookieValue' ) == 'enabled' );
		$detailedValue = '';

		if ( in_array('basket', $displayedData) )
		{
			$http = eZHTTPTool::instance();
            $sessionID = $http->sessionID();
			$basket = eZBasket::fetch($sessionID);
			if ( $basket )
			{
				if ( !$basket->isEmpty() )
				{
					$hasUserData = true;
					if ( $useDetailedValue )
					{
						$detailedValue .= ',basket';
					}
				}
			}
		}
		
		if ( ( $hasUserData === false || $useDetailedValue ) && in_array('wishlist', $displayedData) )
		{
			$user = eZUser::currentUser();
			$userID = $user->attribute( 'contentobject_id' );
			$WishListArray = eZPersistentObject::fetchObjectList( eZWishList::definition(),
															  null, array( "user_id" => $userID
																		   ),
															  null, null,
															  true );
			if ( count( $WishListArray ) > 0 )
			{
				if ( $WishListArray[0]->itemCount() > 0 )
				{
					$hasUserData = true;
					if ( $useDetailedValue )
					{
						$detailedValue .= ',wishlist';
					}
				}
			}
		}
		
		if ( $hasUserData === false || $useDetailedValue )
		{
			$prefs = eZPreferences::values();
			$hasPrefs = false;
			foreach ( $prefs as $key => $val )
			{			
				if ( $key != '' )
				{
					if ( in_array('preferences', $displayedData) || in_array($key, $displayedData)  )
					{
						if ( $val != '' )
						{
							$hasUserData = true;
							
							if ( $useDetailedValue )
							{
								if ( !$hasPrefs )
								{
									$detailedValue .= ',preferences';
								}
								$detailedValue .= ",$key:$val";
							}
							$hasPrefs = true;
						}
					}
				}
			}
		}
		
		
		$value  = ( $hasUserData !== false ) ? $cookieValue . $detailedValue : false;
		
		$wwwDir = eZSys::wwwDir();
	    $cookiePath = $wwwDir != '' ? $wwwDir : '/';
        setcookie( $ini->variable( 'NoCacheCookieSettings', 'CookieName' ),
                   $value,
                   (int)$ini->variable( 'NoCacheCookieSettings', 'CookieDuration' ),
                   $cookiePath );

	}
}

?>
