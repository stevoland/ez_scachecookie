<?php

class sNoCacheCookieType extends eZWorkflowEventType
{
    const WORKFLOW_TYPE_STRING = 'snocachecookie';
    const WORKFLOW_TYPE_CLASS = 'sNoCacheCookieType';
    const WORKFLOW_TYPE_DESC = 'Update No Cache Cookie';
    
    function sNoCacheCookieType()
    {
        $this->eZWorkflowEventType( self::WORKFLOW_TYPE_STRING,  self::WORKFLOW_TYPE_DESC );
        $this->setTriggerTypes( array( 'user' =>  array(  'preferences' => array( 'before', 'after' ) ),
                                       'shop' =>  array(  'addtobasket' => array( 'before', 'after' ),
                                                          'updatebasket' => array( 'before', 'after' ) )
        
         ) );
    }

    function execute( $process, $event )
    {
        sNoCacheCookieHelper::setCookie();
        
        return eZWorkflowType::STATUS_ACCEPTED;
    }
}

eZWorkflowEventType::registerEventType( sNoCacheCookieType::WORKFLOW_TYPE_STRING, sNoCacheCookieType::WORKFLOW_TYPE_CLASS );

?>