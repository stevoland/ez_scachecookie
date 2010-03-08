<?php

class sCacheCookieHelper
{

    private function __construct()
    {
    }

	static public function setCookie()
	{
		$ini = eZINI::instance( 'scachecookie.ini' );
		$hasUserData = false;
		$displayedData = $ini->variable( 'CacheCookieSettings', 'DisplayedData' );
		$cookieValue = $ini->variable( 'CacheCookieSettings', 'CookieValue' ) || 'true';
		if ( $cookieValue === true )
		{
			$cookieValue = 'true';
		}
		$useDetailedValue = ( $ini->variable( 'CacheCookieSettings', 'DetailedCookieValue' ) == 'enabled' );
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
		
		if ( ( !$hasUserData || $useDetailedValue ) && in_array('wishlist', $displayedData) )
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
		
		if ( !$hasUserData || $useDetailedValue )
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
								if ( in_array('preferences', $displayedData) && !$hasPrefs  )
								{
									$detailedValue .= ',preferences';
								}
								if ( in_array($key, $displayedData) )
								{
									$detailedValue .= ",$key:$val";
								}
							}
							$hasPrefs = true;
						}
					}
				}
			}
		}
		
		
		$value  = ( $hasUserData ) ? $cookieValue . $detailedValue : false;
		
		$wwwDir = eZSys::wwwDir();
	    $cookiePath = $wwwDir != '' ? $wwwDir : '/';
        setcookie( $ini->variable( 'CacheCookieSettings', 'CookieName' ),
                   $value,
                   (int)$ini->variable( 'CacheCookieSettings', 'CookieDuration' ),
                   $cookiePath );

	}
}

?>