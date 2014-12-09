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
 * View to edit a albopretorio.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_albopretorio
 * @since       1.6
 */
class AlbopretorioViewAffissione extends JViewLegacy
{
	protected $item;

	protected $form;

	protected $state;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		$this->state	= $this->get('State');
		$this->item		= $this->get('Item');
		$this->form		= $this->get('Form');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		if ($this->getLayout() == 'modal')
		{
			$this->form->setFieldAttribute('language', 'readonly', 'true');
			$this->form->setFieldAttribute('catid', 'readonly', 'true');
		}
		$this->addToolbar();
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since   1.6
	 */
	protected function addToolbar()
	{
		JFactory::getApplication()->input->set('hidemainmenu', true);

		$user		= JFactory::getUser();
		$isNew		= ($this->item->id == 0);
		$checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == $user->get('id'));

		// Since we don't track these assets at the item level, use the category id.
		$canDo		= JHelperContent::getActions('com_albopretorio', 'category', $this->item->catid);

		JToolbarHelper::title(JText::_('COM_ALBOPRETORIO_MANAGER_AFFISSIONE'), 'affissione albopretorio');

		// If not checked out, can save the item.
		if (!$checkedOut && ($canDo->get('core.edit') || count($user->getAuthorisedCategories('com_albopretorio', 'core.create')) > 0))
		{
			JToolbarHelper::apply('affissione.apply');
			JToolbarHelper::save('affissione.save');
		}
		if (!$checkedOut && count($user->getAuthorisedCategories('com_albopretorio', 'core.create')) > 0)
		{
			JToolbarHelper::save2new('affissione.save2new');
		}
		// If an existing item, can save to a copy.
		if (!$isNew && $canDo->get('core.create'))
		{
			JToolbarHelper::save2copy('affissione.save2copy');
		}

		if (empty($this->item->id))
		{
			JToolbarHelper::cancel('affissione.cancel');
		}
		else
		{
			JToolbarHelper::cancel('affissione.cancel', 'JTOOLBAR_CLOSE');
		}

		JToolbarHelper::divider();
		JToolbarHelper::help('JHELP_COMPONENTS_ALBOPRETORIO_EDIT');
	}
}
