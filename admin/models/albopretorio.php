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
 * Methods supporting a list of affissione records.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_albopretorio
 * @since       1.6
 */
class AlbopretorioModelAlbopretorio extends JModelList
{
	/**
	 * Constructor.
	 *
	 * @param   array  An optional associative array of configuration settings.
	 * @see     JController
	 * @since   1.6
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id', 'a.id',
				'name', 'a.name',
				'alias', 'a.alias',
				'checked_out', 'a.checked_out',
				'checked_out_time', 'a.checked_out_time',
				'catid', 'a.catid', 'category_title',
				'published', 'a.published',
				'access', 'a.access', 'access_level',
				'created', 'a.created',
				'created_by', 'a.created_by',
				'ordering', 'a.ordering',
				'language', 'a.language',
				'publish_up', 'a.publish_up',
				'publish_down', 'a.publish_down',
                'hide', 'a.hide',
                'document_date', 'a.document_date',
                'document_number', 'a.document_number',
                'official_number', 'a.official_number',
                'description', 'a.description',
                'filename0', 'a.filename0',
                'filename1', 'a.filename1',
                'filename2', 'a.filename2',
                'filename3', 'a.filename3',
                'filename4', 'a.filename4',
                'filename5', 'a.filename5',
                'filename6', 'a.filename6',
                'filename7', 'a.filename7',
                'filename8', 'a.filename8',
                'filename9', 'a.filename9',
                'filename10', 'a.filename10',
                'filename11', 'a.filename11'
			);

			$app = JFactory::getApplication();
			$assoc = JLanguageAssociations::isEnabled();
			if ($assoc)
			{
				$config['filter_fields'][] = 'association';
			}
		}

        // Automatic archiver
        $db =  JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->update($db->quoteName('#__albopretorio'))
            ->set($db->quoteName('published') . '=\'2\'')
            ->where($db->quoteName('published') . '= 1 AND publish_down < "'. date('Y-m-d') . '"');
        $db->setQuery($query);
        $db->execute();

		parent::__construct($config);
	}

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

		$hide = $this->getUserStateFromRequest($this->context . '.filter.hide', 'filter_hide', 0, 'int');
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

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param   string    A prefix for the store id.
	 *
	 * @return  string    A store id.
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id .= ':' . $this->getState('filter.search');
		$id .= ':' . $this->getState('filter.access');
		$id .= ':' . $this->getState('filter.published');
		$id .= ':' . $this->getState('filter.document_date');
		$id .= ':' . $this->getState('filter.hide');
		$id .= ':' . $this->getState('filter.document_number');
		$id .= ':' . $this->getState('filter.category_id');
		$id .= ':' . $this->getState('filter.category_root_id');
		$id .= ':' . $this->getState('filter.language');
		$id .= ':' . $this->getState('filter.publish_up');
		$id .= ':' . $this->getState('filter.publish_down');

		return parent::getStoreId($id);
	}


	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return  JDatabaseQuery
	 */
	protected function getListQuery()
	{
		// Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$user = JFactory::getUser();
		$app = JFactory::getApplication();

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select',
				'a.id, a.name, a.alias, a.checked_out, a.checked_out_time, a.catid,' .
					' a.document_date, a.document_number, a.official_number, a.hide,' .
					' a.published, a.access, a.ordering, a.language, a.publish_up, a.publish_down, a.created_by'
			)
		);
        /*/ Add files
        for($i = 0; $i < 12; $i++){
            $query->select('filename'.$i);
        }*/

		$query->from($db->quoteName('#__albopretorio') . ' AS a');

		// Join over the language
		$query->select('l.title AS language_title')
			->join('LEFT', $db->quoteName('#__languages') . ' AS l ON l.lang_code = a.language');

		// Join over the users for the checked out user.
		$query->select('uc.name AS editor')
			->join('LEFT', '#__users AS uc ON uc.id=a.checked_out');

		// Join over the users for the author.
		$query->select('ua.name AS author')
			->join('LEFT', '#__users AS ua ON ua.id=a.created_by');


		// Join over the asset groups.
		$query->select('ag.title AS access_level')
			->join('LEFT', '#__viewlevels AS ag ON ag.id = a.access');

		// Join over the categories.
		$query->select('c.title AS category_title')
			->join('LEFT', '#__categories AS c ON c.id = a.catid');

		// Add the slug
		$query->select(	'CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(":", a.id, a.alias) ELSE a.id END as slug');

		// Join over the associations.
		$assoc = JLanguageAssociations::isEnabled();
		if ($assoc)
		{
			$query->select('COUNT(asso2.id)>1 as association')
				->join('LEFT', '#__associations AS asso ON asso.id = a.id AND asso.context=' . $db->quote('com_albopretorio.item'))
				->join('LEFT', '#__associations AS asso2 ON asso2.key = asso.key')
				->group('a.id');
		}

		// Filter by access level.
		if ($access = $this->getState('filter.access'))
		{
			$query->where('a.access = ' . (int) $access);
		}

		// Implement View Level Access
		if (!$user->authorise('core.admin'))
		{
			$groups = implode(',', $user->getAuthorisedViewLevels());
			$query->where('a.access IN (' . $groups . ')');
		}

		// Filter by published state.
		$published = $this->getState('filter.published');
		if (is_numeric($published))
		{
			$query->where('a.published = ' . (int) $published);
		}
		elseif ($published === '')
		{
			$query->where('(a.published IN (0, 1))');
		}

		// Filter by document_date.
		$document_date = $this->getState('filter.document_date');
		if ($document_date)
		{
			$query->where('a.document_date = ' . $db->escape(trim($document_date), true));
		}

		// Filter by publish_up.
		$publish_up = $this->getState('filter.publish_up');
		if ($publish_up)
		{
			$query->where('a.publish_up <= "' . $db->escape(trim($publish_up), true) . '"');
		}

		// Filter by publish_down.
		$publish_down = $this->getState('filter.publish_down');
		if ($publish_down)
		{
			$query->where('a.publish_down >= "' . $db->escape(trim($publish_down), true) . '"');
		}

		// Filter by hide.
		$hide = $this->getState('filter.hide');
		if ($hide)
		{
			$query->where('a.hide = ' . (int) $hide);
		}

		// Filter by document_number.
		$document_number = $this->getState('filter.document_number');
		if ($document_number)
		{
			$query->where('a.document_number = ' . $db->escape(trim($document_number), true));
		}


		// Filter by a single or group of categories.
		$baselevel = 1;
		$categoryId = $this->getState('filter.category_id');

		if (is_numeric($categoryId))
		{
			$cat_tbl = JTable::getInstance('Category', 'JTable');
			$cat_tbl->load($categoryId);
			$rgt = $cat_tbl->rgt;
			$lft = $cat_tbl->lft;
			$baselevel = (int) $cat_tbl->level;
			$query->where('c.lft >= ' . (int) $lft)
			->where('c.rgt <= ' . (int) $rgt);
		}
		elseif (is_array($categoryId))
		{
			JArrayHelper::toInteger($categoryId);
			$categoryId = implode(',', $categoryId);
			$query->where('a.catid IN (' . $categoryId . ')');
		}

		// Filter by search in title
		$search = $this->getState('filter.search');
		if (!empty($search))
		{
			if (stripos($search, 'id:') === 0)
			{
				$query->where('a.id = ' . (int) substr($search, 3));
			}
			else
			{
				$search = $db->quote('%' . str_replace(' ', '%', $db->escape(trim($search), true) . '%'));
				$query->where('(a.name LIKE ' . $search . ' OR a.alias LIKE ' . $search . ')');
			}
		}

		// Filter on the language.
		if ($language = $this->getState('filter.language'))
		{
			$query->where('a.language = ' . $db->quote($language));
		}

		// Filter by a single tag.
		$tagId = $this->getState('filter.tag');
		if (is_numeric($tagId))
		{
			$query->where($db->quoteName('tagmap.tag_id') . ' = ' . (int) $tagId)
				->join(
					'LEFT', $db->quoteName('#__contentitem_tag_map', 'tagmap')
					. ' ON ' . $db->quoteName('tagmap.content_item_id') . ' = ' . $db->quoteName('a.id')
					. ' AND ' . $db->quoteName('tagmap.type_alias') . ' = ' . $db->quote('com_albopretorio.affissione')
				);
		}

		// Add the list ordering clause.
		$orderCol = $this->state->get('list.ordering');
		$orderDirn = $this->state->get('list.direction');
		if ($orderCol == 'a.ordering' || $orderCol == 'category_title')
		{
			$orderCol = 'c.title ' . $orderDirn . ', a.ordering';
		}

        if ( $orderCol == 'a.official_number' && JComponentHelper::getParams('com_albopretorio')->get('autoincrement_sort_numerically') == '1' ) {
            $orderCol = " CAST(official_number as SIGNED INTEGER) ";
        }

        if($orderCol && $orderDirn){
            $query->order($db->escape($orderCol . ' ' . $orderDirn));

        }

		//echo nl2br(str_replace('#__','jos_',$query));
		return $query;
	}

	/**
	 * Returns a Table object, always creating it.
	 *
	 * @param   type      The table type to instantiate
	 * @param   string    A prefix for the table class name. Optional.
	 * @param   array     Configuration array for model. Optional.
	 * @return  JTable    A database object
	 */
	public function getTable($type = 'Albopretorio', $prefix = 'AlbopretorioTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

}
