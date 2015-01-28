<?php
/**
* @version		$Id:$
* @package		plgSystemAlbopretorio
* @copyright	Copyright (C) 2015 ItOpen. All rights reserved.
* @licence      GNU/AGPL
*
* @description
*
* This plugin implements an ACL to prevent users enter Albo Pretorio
* category management, the ACL is 'category.create', 'com_albopretorio'
* defined in access.xml
*
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );

/**
 * Joomla! Albopretorio  plugin
 *
 * @package		COM_ALBOPRETORIO
 * @subpackage	System
 */
class plgSystemAlbopretorio extends JPlugin
{
    protected $_component;

    function __construct( $subject, $config)
    {
        parent::__construct($subject, $config);
        $this->loadLanguage('com_albopretorio', JPATH_ADMINISTRATOR . '/components/com_albopretorio/');
        $this->_component = JComponentHelper::getComponent('com_albopretorio');
    }

    function onAfterRoute(){

        $app = JFactory::getApplication();
        if (!$app->isAdmin()
            || JRequest::getString('option') != 'com_categories'
            || JRequest::getString('extension') != 'com_albopretorio'){
            return true;
        }

        if ( $this->_component ){
            $user    = JFactory::getUser();
            if (! $user->authorise('category.create', 'com_albopretorio') ) {
                JError::raiseWarning( 100, JText::_( 'COM_ALBOPRETORIO_CATEGORY_EDIT_DISALLOWED' ));
                $link = JRoute::_('index.php?option=com_albopretorio');
                JFactory::getApplication()->redirect($link);
            }
        }

        return true;
    }

}
