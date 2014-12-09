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
 * View class for a list of albopretorio.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_albopretorio
 * @since       1.6
 */
class AlbopretorioViewAlbopretorio extends JViewLegacy
{
	protected $items;

	protected $pagination;

	protected $state;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');

        $this->loadHelper('albopretorio');
		AlbopretorioHelper::addSubmenu('albopretorio');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		$this->addToolbar();
		$this->sidebar = JHtmlSidebar::render();
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since   1.6
	 */
	protected function addToolbar()
	{
		$state	= $this->get('State');
		$canDo	= JHelperContent::getActions('com_albopretorio', 'category', $state->get('filter.category_id'));
		$user	= JFactory::getUser();

		// Get the toolbar object instance
		$bar = JToolBar::getInstance('toolbar');
		JToolbarHelper::title(JText::_('COM_ALBOPRETORIO_MANAGER'), 'calendar-3');

		if (count($user->getAuthorisedCategories('com_albopretorio', 'core.create')) > 0)
		{
			JToolbarHelper::addNew('affissione.add');
		}
		if ($canDo->get('core.edit'))
		{
			JToolbarHelper::editList('affissione.edit');
		}
		if ($canDo->get('core.edit.state'))
		{
			JToolbarHelper::publish('albopretorio.publish', 'JTOOLBAR_PUBLISH', true);
			JToolbarHelper::unpublish('albopretorio.unpublish', 'JTOOLBAR_UNPUBLISH', true);
			JToolbarHelper::archiveList('albopretorio.archive');
		}
		if ($canDo->get('core.admin'))
		{
			JToolbarHelper::checkin('affissione.checkin');
			}
		if ($state->get('filter.published') == -2 && $canDo->get('core.delete'))
		{
			JToolbarHelper::deleteList('', 'albopretorio.delete', 'JTOOLBAR_EMPTY_TRASH');
		} elseif ($canDo->get('core.edit.state'))
		{
			JToolbarHelper::trash('albopretorio.trash');
		}
		// Add a batch button
		if ($user->authorise('core.create', 'com_albopretorio') && $user->authorise('core.edit', 'com_albopretorio') && $user->authorise('core.edit.state', 'com_albopretorio'))
		{
			JHtml::_('bootstrap.modal', 'collapseModal');
			$title = JText::_('JTOOLBAR_BATCH');

			// Instantiate a new JLayoutFile instance and render the batch button
			$layout = new JLayoutFile('joomla.toolbar.batch');

			$dhtml = $layout->render(array('title' => $title));
			$bar->appendButton('Custom', $dhtml, 'batch');
		}
		if ($user->authorise('core.admin', 'com_albopretorio'))
		{
			JToolbarHelper::preferences('com_albopretorio');
		}
		JToolbarHelper::help('JHELP_COMPONENTS_ALBOPRETORIO');

		JHtmlSidebar::setAction('index.php?option=com_albopretorio&view=albopretorio');

		JHtmlSidebar::addFilter(
			JText::_('JOPTION_SELECT_PUBLISHED'),
			'filter_published',
			JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), 'value', 'text', $this->state->get('filter.published'), true)
		);

		JHtmlSidebar::addFilter(
			JText::_('JOPTION_SELECT_CATEGORY'),
			'filter_category_id',
			JHtml::_('select.options', JHtml::_('category.options', 'com_albopretorio'), 'value', 'text', $this->state->get('filter.category_id'))
		);

		JHtmlSidebar::addFilter(
			JText::_('JOPTION_SELECT_ACCESS'),
			'filter_access',
			JHtml::_('select.options', JHtml::_('access.assetgroups'), 'value', 'text', $this->state->get('filter.access'))
		);

		JHtmlSidebar::addFilter(
			JText::_('JOPTION_SELECT_LANGUAGE'),
			'filter_language',
			JHtml::_('select.options', JHtml::_('contentlanguage.existing', true, true), 'value', 'text', $this->state->get('filter.language'))
		);

		JHtmlSidebar::addFilter(
		JText::_('JOPTION_SELECT_TAG'),
		'filter_tag',
		JHtml::_('select.options', JHtml::_('tag.options', true, true), 'value', 'text', $this->state->get('filter.tag'))
		);
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
	}
}
