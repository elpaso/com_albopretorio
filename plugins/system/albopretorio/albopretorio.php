<?php
/**
* @version		$Id:$
* @package		plgSystemAlbopretorio
* @copyright	Copyright (C) 2015 ItOpen. All rights reserved.
* @licence      GNU/GPL
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


// Check if component is installed
if (! file_exists(JPATH_ADMINISTRATOR . '/components/com_albopretorio/albopretorio.php'))
{
    return;
}

// Check if is not enabled..
if (!JComponentHelper::getComponent('com_albopretorio', true)->enabled)
{
    return;
}

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
        // Check if component is installed
        if (! file_exists(JPATH_ADMINISTRATOR . '/components/com_albopretorio/albopretorio.php'))
        {
            return;
        }
        // Check if is not enabled..
        if (! JComponentHelper::getComponent('com_albopretorio', true)->enabled)
        {
            return;
        }
        $this->loadLanguage('com_albopretorio', JPATH_ADMINISTRATOR . '/components/com_albopretorio/');
        $this->_component = JComponentHelper::getComponent('com_albopretorio');
    }

    /*
    * adds additional fields to the user editing form
    *
    * @param JForm $form The form to be altered.
    * @param mixed $data The associated data for the form.
    *
    * @return boolean
    *
    * @since 1.6
    *
    public function onContentPrepareForm($form, $data)
    {
        $app = JFactory::getApplication();
        if (!$app->isAdmin())
        {
            return true;
        }

        if (!($form instanceof JForm))
        {
            $this->_subject->setError('JERROR_NOT_A_FORM');
            return false;
        }
        // Check we are manipulating a valid form.
        $name = $form->getName();
        if ('com_categories.categorycom_albopretorio' != $name)
        {
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

    }
    //*/

    public function onAfterRoute(){

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
