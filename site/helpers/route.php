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
 * Albopretorio Component Route Helper
 *
 * @package     Joomla.Site
 * @subpackage  com_albopretorio
 * @since       1.5
 */
abstract class AlbopretorioHelperRoute
{
	protected static $lookup;

	protected static $lang_lookup = array();

	/**
	 * @param   integer  The route of the affissione
	 */
	public static function getAlbopretorioRoute()
	{

		//Create the link
		$link = 'index.php?option=com_albopretorio';
		if ($item = self::findItem(array()))
		{
			$link .= '&Itemid='.$item;
		}
		return $link;
	}


	/**
	 * @param   integer  The route of the affissione
	 */
	public static function getAffissioneRoute($id, $catid, $language = 0)
	{
		$needles = array(
			'affissione'  => array((int) $id)
		);

		//Create the link
		$link = 'index.php?option=com_albopretorio&view=affissione&id='. $id;

		if ((int) $catid > 1)
		{
			$categories = JCategories::getInstance('Albopretorio');
			$category = $categories->get((int) $catid);

			if ($category)
			{
				//TODO Throw error that the category either not exists or is unpublished
				$needles['category'] = array_reverse($category->getPath());
				$needles['categories'] = $needles['category'];
				$link .= '&catid='.$catid;
			}
		}

		if ($language && $language != "*" && JLanguageMultilang::isEnabled())
		{
			self::buildLanguageLookup();

			if (isset(self::$lang_lookup[$language]))
			{
				$link .= '&lang=' . self::$lang_lookup[$language];
				$needles['language'] = $language;
			}
		}

		if ($item = self::findItem($needles))
		{
			$link .= '&Itemid='.$item;
		}
		return $link;
	}

	public static function getCategoryRoute($catid, $language = 0)
	{
		if ($catid instanceof JCategoryNode)
		{
			$id = $catid->id;
			$category = $catid;
		}
		else
		{
			$id = (int) $catid;
			$category = JCategories::getInstance('Albopretorio')->get($id);
		}

		if ($id < 1 || !($category instanceof JCategoryNode))
		{
			$link = '';
		}
		else
		{
			$needles = array();

			// Create the link
			$link = 'index.php?option=com_albopretorio&view=category&id='.$id.'&catid='.$id;

			$catids = array_reverse($category->getPath());
			$needles['category'] = $catids;
			$needles['categories'] = $catids;

			if ($language && $language != "*" && JLanguageMultilang::isEnabled())
			{
				self::buildLanguageLookup();

				if (isset(self::$lang_lookup[$language]))
				{
					$link .= '&lang=' . self::$lang_lookup[$language];
					$needles['language'] = $language;
				}
			}

			if ($item = self::findItem($needles))
			{
				$link .= '&Itemid='.$item;
			}
		}

		return $link;
	}

	protected static function buildLanguageLookup()
	{
		if (count(self::$lang_lookup) == 0)
		{
			$db    = JFactory::getDbo();
			$query = $db->getQuery(true)
				->select('a.sef AS sef')
				->select('a.lang_code AS lang_code')
				->from('#__languages AS a');

			$db->setQuery($query);
			$langs = $db->loadObjectList();

			foreach ($langs as $lang)
			{
				self::$lang_lookup[$lang->lang_code] = $lang->sef;
			}
		}
	}

	public static function findItem($needles = null)
	{
		$app		= JFactory::getApplication();
		$menus		= $app->getMenu('site');
		$language	= isset($needles['language']) ? $needles['language'] : '*';

		// Prepare the reverse lookup array.
		if (!isset(self::$lookup[$language]))
		{
			self::$lookup[$language] = array();

			$component	= JComponentHelper::getComponent('com_albopretorio');

			$attributes = array('component_id');
			$values = array($component->id);

			if ($language != '*')
			{
				$attributes[] = 'language';
				$values[] = array($needles['language'], '*');
			}

			$items = $menus->getItems($attributes, $values);

			foreach ($items as $item)
			{
				if (isset($item->query) && isset($item->query['view']))
				{
					$view = $item->query['view'];
                    if (!isset(self::$lookup[$language][$view]))
					{
						self::$lookup[$language][$view] = array();
					}
					if (isset($item->query['id']))
					{

						// here it will become a bit tricky
						// language != * can override existing entries
						// language == * cannot override existing entries
						if (!isset(self::$lookup[$language][$view][$item->query['id']]) || $item->language != '*')
						{
							self::$lookup[$language][$view][$item->query['id']] = $item->id;
						}
					}
				}
			}
		}

		if ($needles)
		{
			foreach ($needles as $view => $ids)
            {

				if (isset(self::$lookup[$language][$view]))
				{
					foreach ($ids as $id)
					{
						if (isset(self::$lookup[$language][$view][(int) $id]))
						{
							return self::$lookup[$language][$view][(int) $id];
						}
					}
				}
			}
		}


		// Check if the active menuitem matches the requested language
		$active = $menus->getActive();

        // If it's a category filtered view or not albo view, build link to main albo view
        if( ! $active || $active && 'category' == $active->query['view']) {
            $component	= JComponentHelper::getComponent('com_albopretorio');
			$attributes = array('component_id');
			$values = array($component->id);
            $items = $menus->getItems($attributes, $values);
            foreach($items as $item){
                if (isset($item->query) && isset($item->query['view']) && 'albopretorio' == $item->query['view']){
                    return $item->id;
                }
            }
        }

		if ($active && ($language == '*' || in_array($active->language, array('*', $language)) || !JLanguageMultilang::isEnabled()))
		{
			return $active->id;
		}

        // ABP: patch for affissione -> albopretorio
        if( $needles && array_key_exists('affissione', $needles )){
            if (isset(self::$lookup[$language]['albopretorio'][0]))
            {
                return self::$lookup[$language]['albopretorio'][0];
            }
        }

		// If not found, return language specific home link
		$default = $menus->getDefault($language);
		return !empty($default->id) ? $default->id : null;
	}

	/**
	 * @param   integer  The route of the attachment
	 */
	public static function getAttachmentRoute($id, $catid, $field)
	{
		$link = self::getAffissioneRoute($id, $catid);
		$link .= '&field=' .$field;
		return $link;
	}
}
