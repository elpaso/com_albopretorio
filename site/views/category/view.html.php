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

defined ( '_JEXEC' ) or die ();

JLoader::register('AlbopretorioViewAlbopretorio', JPATH_SITE . '/components/com_albopretorio/views/albopretorio/view.html.php');


/**
 * HTML View class for the Albopretorio component
 *
 * @package Joomla.Site
 * @subpackage com_albopretorio
 * @since 1.0
 */
class AlbopretorioViewCategory extends AlbopretorioViewAlbopretorio {

	/**
	 * Constructor
	 *
	 * @param array $config
	 *        	A named configuration array for object construction.<br/>
	 *        	name: the name (optional) of the view (defaults to the view class name suffix).<br/>
	 *        	charset: the character set to use for display<br/>
	 *        	escape: the name (optional) of the function to use for escaping strings<br/>
	 *        	base_path: the parent path (optional) of the views directory (defaults to the component folder)<br/>
	 *        	template_plath: the path (optional) of the layout directory (defaults to base_path + /views/ + view name<br/>
	 *        	helper_path: the path (optional) of the helper files (defaults to base_path + /helpers/)<br/>
	 *        	layout: the layout (optional) to use to display the view<br/>
	 *
	 * @since 12.2
	 */
	public function __construct($config = array()) {
		$config = array (
				'template_path' => dirname ( __FILE__ ) . '/../albopretorio/tmpl'
		);
		parent::__construct ( $config );
	}
	/**
	 * Execute and display a template script.
	 *
	 * @param string $tpl
	 *        	The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return mixed A string if successful, otherwise a Error object.
	 *
	 * @since 1.6
	 */
	public function display($tpl = null) {

		$catid = JRequest::getInt ( 'catid' );
        if(!$catid){
            $catid = JRequest::getInt ( 'id' );
        }

        $app  = JFactory::getApplication();

		$categories = JCategories::getInstance ( 'Albopretorio' );
		$category = $categories->get ( $catid );

        $tagsHelper = new JHelperTags;
        $category->tags = $tagsHelper->getItemTags( 'com_albopretorio.category',  $catid);

		$state = $this->get ( 'State' );

		$state->set ('filter.category_id', $catid);

		$this->assignRef('category', $category );

		parent::display ( $tpl );
		$app = JFactory::getApplication ();
		$pathway = $app->getPathway ();
		$menus = $app->getMenu ();

		// Because the application sets a default page title,
		// we need to get it from the menu item itself
		$menu = $menus->getActive ();
		$path = array (
				array (
						'title' => $category->title,
						'link' => ''
				)
		);

		$category = $category->getParent ();
		while ( ($menu->query ['option'] != 'com_albopretorio' || $catid != $category->id) && $category->id > 1 ) {
			$path [] = array (
					'title' => $category->title,
					'link' => AlbopretorioHelperRoute::getCategoryRoute ( $category->id )
			);
			$category = $category->getParent ();
		}
		$path = array_reverse ( $path );
		foreach ( $path as $item ) {
			$pathway->addItem ( $item ['title'], $item ['link'] );
		}
	}
}
