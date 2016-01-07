<?php
/**
*
* @package      COM_ALBOPRETORIO
* @copyright    Copyright (C) 2014-2015 Alessandro Pasotti http://www.itopen.it All rights reserved.
* @license      GNU/GPL

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as
    published by the Free Software Foundation, either version 3 of the
    License, or (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.

*/
defined('_JEXEC') or die;

require_once dirname(__FILE__) . '/albopretorio.php';

/**
 * Methods supporting a list of affissione records.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_albopretorio
 * @since       1.6
 */
class AlbopretorioModelCategory extends AlbopretorioModelAlbopretorio
{

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		$app = JFactory::getApplication('administrator');

		// Load the filter state.
		$search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$accessId = $this->getUserStateFromRequest($this->context . '.filter.access', 'filter_access', null, 'int');
		$this->setState('filter.access', $accessId);

		$state = $this->getUserStateFromRequest($this->context . '.filter.published', 'filter_published', '', 'string');
		$this->setState('filter.published', $state);

		$document_date = $this->getUserStateFromRequest($this->context . '.filter.document_date', 'filter_document_date', '', 'date');
		$this->setState('filter.document_date', $document_date);

		$hide = $this->getUserStateFromRequest($this->context . '.filter.hide', 'filter_hide', null, 'int');
		$this->setState('filter.hide', $hide);

		$document_number = $this->getUserStateFromRequest($this->context . '.filter.document_number', 'filter_document_number', '', 'string');
		$this->setState('filter.document_number', $document_number);

		$categoryId = $this->getUserStateFromRequest($this->context . '.filter.category_id', 'filter_category_id', null);
		$this->setState('filter.category_id', $categoryId);

		$language = $this->getUserStateFromRequest($this->context . '.filter.language', 'filter_language', '');
		$this->setState('filter.language', $language);

		// force a language
		$forcedLanguage = $app->input->get('forcedLanguage');
		if (!empty($forcedLanguage))
		{
			$this->setState('filter.language', $forcedLanguage);
			$this->setState('filter.forcedLanguage', $forcedLanguage);
		}

		$tag = $this->getUserStateFromRequest($this->context . '.filter.tag', 'filter_tag', '');
		$this->setState('filter.tag', $tag);

		// Load the parameters.
		$params = JComponentHelper::getParams('com_albopretorio');
		$this->setState('params', $params);

		// List state information.
		parent::populateState('a.name', 'asc');
	}


}
