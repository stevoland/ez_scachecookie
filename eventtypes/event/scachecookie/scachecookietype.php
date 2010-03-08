<?php

class sCacheCookieType extends eZWorkflowEventType
{
    const WORKFLOW_TYPE_STRING = 'scachecookie';
    const WORKFLOW_TYPE_CLASS = 'sCacheCookieType';
    const WORKFLOW_TYPE_DESC = 'Update Cache Cookie';
    
    function __construct()
    {
        $this->eZWorkflowEventType( self::WORKFLOW_TYPE_STRING,  self::WORKFLOW_TYPE_DESC );
        $this->setTriggerTypes( array( 'user' =>  array(  'preferences' => array( 'before', 'after' ) ),
                                       'shop' =>  array(  'addtobasket' => array( 'before', 'after' ),
                                                          'updatebasket' => array( 'before', 'after' ) )
        
         ) );
    }

    function execute( $process, $event )
    {
        sCacheCookieHelper::setCookie();
        
        return eZWorkflowType::STATUS_ACCEPTED;
    }
}

eZWorkflowEventType::registerEventType( sCacheCookieType::WORKFLOW_TYPE_STRING, sCacheCookieType::WORKFLOW_TYPE_CLASS );

?>