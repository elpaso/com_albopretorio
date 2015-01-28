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
 * Albopretorio component helper.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_albopretorio
 * @since       1.6
 */
class AlbopretorioHelper extends JHelperContent
{
	public static $extension = 'com_albopretorio';

	/**
	 * Configure the Linkbar.
	 *
	 * @param   string  $vName  The name of the active view.
	 *
	 * @return  void
	 */
	public static function addSubmenu($vName)
	{
		JHtmlSidebar::addEntry(
			JText::_('COM_ALBOPRETORIO_SUBMENU_AFFISSIONI'),
			'index.php?option=com_albopretorio&view=albopretorio',
			$vName == 'albopretorio'
		);

        // Check for custom ACL rule category.create
        // This works, but categories can still be reached from the menu!
        // This needs a plugin
        $canDo  = JHelperContent::getActions('com_albopretorio');
        if($canDo->get('category.create', true)){
            JHtmlSidebar::addEntry(
                JText::_('COM_ALBOPRETORIO_SUBMENU_CATEGORIE'),
                'index.php?option=com_categories&extension=com_albopretorio',
                $vName == 'categories'
            );
        }
	}

	public static function getSignature(){
		?>
		<div>Albopretorio per Joomla! 3 &copy; <?php echo date('Y') ?> <a title="Sviluppo e assistenza Joomla! FAP e software open-source" target="_blank" href="http://www.itopen.it">Alessandro Pasotti &mdash; ItOpen</a></div>
		<?php
	}

    public static function getCategoryPath($category, $glue = ' <em class="icon icon-arrow-right-3"></em> '){

        $pieces = array();
        $category_pathway = array();
        $category_pathway[] = (object) array('name' => $category->title, 'link' => AlbopretorioHelperRoute::getCategoryRoute($category->id));
        while($category = $category->getParent()){
			if ($category->title != 'ROOT'){
        		$category_pathway[] = (object) array('name' => $category->title, 'link' => AlbopretorioHelperRoute::getCategoryRoute($category->id));
        	}
        }
        $category_pathway = array_reverse($category_pathway);
        foreach($category_pathway as $path){
			 $pieces[] = "<a class=\"hasTooltip\" href=\"$path->link\" title=\"" . JText::_('COM_ALBOPRETORIO_FILTER_BY_CATEGORY') . "\">$path->name</a>";
		}
		return implode($glue, $pieces);
    }
}
