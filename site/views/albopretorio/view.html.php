<?php
/**
*
* @package      COM_ALBOPRETORIO
* @copyright    Copyright (C) 2014 Alessandro Pasotti http://www.itopen.it All rights reserved.
* @license      GNU/AGPL

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU Affero General Public License as
    published by the Free Software Foundation, either version 3 of the
    License, or (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU Affero General Public License for more details.

    You should have received a copy of the GNU Affero General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.

*/

defined('_JEXEC') or die;



/**
 * HTML View class for the Albopretorio component
 *
 * @package     Joomla.Site
 * @subpackage  com_albopretorio
 * @since       1.0
 */
class AlbopretorioViewAlbopretorio extends JViewLegacy
{
	/**
	 * @var     object
	 * @since   1.6
	 */
	protected $state;

	/**
	 * @var     object
	 * @since   1.6
	 */
	protected $item;

	/**
	 * @var     boolean
	 * @since   1.6
	 */
	protected $print;

	/**
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise a Error object.
	 *
	 * @since   1.6
	 */
	public function display($tpl = null)
	{
		$app  = JFactory::getApplication();
		$user = JFactory::getUser();

		// Needed in template
		$this->addHelperPath(JPATH_COMPONENT_ADMINISTRATOR . '/helpers');
		$this->loadHelper('albopretorio');

        // Set FE filters
        $state = $this->get ( 'State' );

		$state->set ('filter.published', '1');
        $state->set ('filter.publish_up', date('Y-m-d'));
        $state->set ('filter.publish_down', date('Y-m-d'));


		// Get model data.
		$items		= $this->get('Items');
		$pagination	= $this->get('Pagination');
		$state		= $this->get('State');

		$active	= $app->getMenu()->getActive();
		$menus		= $app->getMenu();
		$pathway	= $app->getPathway();
		$title		= null;

		// Merge params
		// Get the current menu item
		$params = $app->getParams();

		// Check for errors.
		// @TODO: Maybe this could go into JComponentHelper::raiseErrors($this->get('Errors'))
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseWarning(500, implode("\n", $errors));

			return false;
		}

		if(!isset($this->category)){
			$this->assign('category', $f = false );
		}
		$this->assignRef('pagination', $pagination);
		$this->assignRef('params', $params);
        $this->assignRef('state', $state);
		$this->assignRef('items', $items);
		$this->assignRef('user', $user);
		if (!empty($msg))
		{
			$this->assignRef('msg', $msg);
		}

		return parent::display($tpl);
	}

	/**
	 * Returns an array of fields the table can be sorted by
	 *
	 * @return  array  Array containing the field name to sort by as the key and display text as value
	 *
	 * @since   3.0
	 */
	protected function getSortFields()
	{
		return array(
			'a.ordering' => JText::_('JGRID_HEADING_ORDERING'),
			'a.published' => JText::_('JSTATUS'),
			'a.document_number' => JText::_('JSTATUS'),
			'a.official_number' => JText::_('JSTATUS'),
			'a.name' => JText::_('JGLOBAL_TITLE'),
			'category_title' => JText::_('JCATEGORY'),
			'a.access' => JText::_('JGRID_HEADING_ACCESS'),
			'a.language' => JText::_('JGRID_HEADING_LANGUAGE'),
			'a.id' => JText::_('JGRID_HEADING_ID')
		);
	}}
