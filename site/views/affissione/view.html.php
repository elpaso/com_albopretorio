<?php
/**
*
* @package      COM_ALBOPRETORIO
* @copyright    Copyright (C) 2014 Alessandro Pasotti http://www.itopen.it All rights reserved.
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

/**
 * HTML View class for the Albopretorio component
 *
 * @package     Joomla.Site
 * @subpackage  com_albopretorio
 * @since       1.0
 */
class AlbopretorioViewAffissione extends JViewLegacy
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

    public function formatbytes($file, $type='GB')
    {

       switch($type){
          case "GB":
             $filesize = ((filesize($file) * .0009765625) * .0009765625) * .0009765625; // bytes to GB
             if($filesize >= 1)
          break;
          case "MB":
             $type = 'MB';
             $filesize = (filesize($file) * .0009765625) * .0009765625; // bytes to MB
             if($filesize >= 1)
          break;
          case "KB":
             $type = 'KB';
             $filesize = filesize($file) * .0009765625; // bytes to KB
          break;
       }
       if($filesize <= 0){
          return $filesize = 'unknown file size';}
       else{return round($filesize, 2).' '.$type;}
    }

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
		// TODO: move to helper
		$base_path = JPATH_ROOT . "/albopretorio_uploads";


		// Get view related request variables.
		$print = $app->input->getBool('print');

		// Get model data.
		$state = $this->get('State');
		$item  = $this->get('Item');

		// Check for errors.
		// @TODO: Maybe this could go into JComponentHelper::raiseErrors($this->get('Errors'))
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseWarning(500, implode("\n", $errors));

			return false;
		}

		// Download
		if($item && $field = $app->input->getString('field')){
			if(file_exists($base_path . '/' . $item->$field ) && is_file($base_path . '/' . $item->$field )){
				ob_end_clean();
				$file_basename = basename($item->$field);
				$ext = substr(strrchr($file_basename, '.'), 1);
				switch (strtolower($ext) == 'pdf') {
					case 'pdf':
						header('Content-type: application/pdf');
						header('Content-Disposition: inline; filename="' . $file_basename . '"');
					break;
					case 'txt':
					case 'rst':
						header('Content-type: text/plain');
						header('Content-Disposition: attachment; filename="' . $file_basename . '"');
					break;
					default:
						header('Content-type: application/octet-stream');
						header('Content-Disposition: attachment; filename="' . $file_basename . '"');
				}
				readfile($base_path . '/' . $item->$field );
				exit();
			}
			JError::raiseError(500, JText::_('COM_ALBOPRETORIO_ATTACHMENT_NOT_FOUND'));
		}


		// Merge affissione params. If this is single-affissione view, menu params override affissione params
		// Otherwise, affissione params override menu item params
		$active	= $app->getMenu()->getActive();
		$menus		= $app->getMenu();
		$pathway	= $app->getPathway();
		$title		= null;
		// Get the current menu item
		$params = $app->getParams();
		
        // Additional CSS
        if ( $params->get('custom_css', false) ){
            $app->getDocument()->addStyleDeclaration($params->get('custom_css'));
        }

		// Check the access to the affissione
		$levels = $user->getAuthorisedViewLevels();

		if (!in_array($item->access, $levels))
		{
			JError::raiseWarning(403, JText::_('JERROR_ALERTNOAUTHOR'));
			return;
		}

		$this->assignRef('params', $params);
		$this->assignRef('state', $state);
		$this->assignRef('item', $item);
		$this->assignRef('user', $user);
		if (!empty($msg))
		{
			$this->assignRef('msg', $msg);
		}

		$item->tags = new JHelperTags;
		$item->tags->getItemTags('com_albopretorio.affissione', $item->id);

		$this->_prepareDocument();

        // Attachments
        $item->attachments = array();
        for($i = 0; $i < 12; $i++){
            $fname = 'filename' . $i;
            if($item->$fname){
                $item->attachments[$fname] = new JObject(array(
                    'name' => $item->$fname,
                    'display_name' => str_replace('_', ' ', substr($item->$fname, strrpos($item->$fname, '/') + 1)),
                    'size' => $this->formatbytes($base_path . '/' . $item->$fname),
                    'link' => AlbopretorioHelperRoute::getAttachmentRoute($item->id, $item->catid, $fname)
                ));
            }
        }

		return parent::display($tpl);
	}

	/**
	 * Prepares the document
	 *
	 * @return  void
	 * @since   1.6
	 */
	protected function _prepareDocument()
	{
		$app		= JFactory::getApplication();
		$menus		= $app->getMenu();
		$pathway	= $app->getPathway();
		$title		= null;

		// Because the application sets a default page title,
		// we need to get it from the menu item itself
		$menu = $menus->getActive();

		if ($menu)
		{
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		}
		else
		{
			$this->params->def('page_heading', JText::_('COM_ALBOPRETORIO_DEFAULT_PAGE_TITLE'));
		}

		$title = $this->params->get('page_title', '');

		$id = (int) @$menu->query['id'];

		// if the menu item does not concern this affissione
		if ($menu && ($menu->query['option'] != 'com_albopretorio' || $menu->query['view'] != 'affissione' || $id != $this->item->id))
		{
			// If this is not a single affissione menu item, set the page title to the affissione title
			if ($this->item->name)
			{
				$title = $this->item->name;
			}

			$path = array(array('title' => $this->item->name, 'link' => ''));
			$category = JCategories::getInstance('Albopretorio')->get($this->item->catid);
			while (($menu->query['option'] != 'com_albopretorio' || $menu->query['view'] == 'affissione' || $id != $category->id) && $category->id > 1)
			{
				$path[] = array('title' => $category->title, 'link' => AlbopretorioHelperRoute::getCategoryRoute($category->id));
				$category = $category->getParent();
			}
			$path = array_reverse($path);
			foreach ($path as $item)
			{
				$pathway->addItem($item['title'], $item['link']);
			}
		}

		if (empty($title))
		{
			$title = $app->get('sitename');
		}
		elseif ($app->get('sitename_pagetitles', 0) == 1)
		{
			$title = JText::sprintf('JPAGETITLE', $app->get('sitename'), $title);
		}
		elseif ($app->get('sitename_pagetitles', 0) == 2)
		{
			$title = JText::sprintf('JPAGETITLE', $title, $app->get('sitename'));
		}
		if (empty($title))
		{
			$title = $this->item->name;
		}
		$this->document->setTitle($title);

		if ($this->item->metadesc)
		{
			$this->document->setDescription($this->item->metadesc);
		}
		elseif (!$this->item->metadesc && $this->params->get('menu-meta_description'))
		{
			$this->document->setDescription($this->params->get('menu-meta_description'));
		}

		if ($this->item->metakey)
		{
			$this->document->setMetadata('keywords', $this->item->metakey);
		}
		elseif (!$this->item->metakey && $this->params->get('menu-meta_keywords'))
		{
			$this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
		}

		if ($this->params->get('robots'))
		{
			$this->document->setMetadata('robots', $this->params->get('robots'));
		}

		if ($app->get('MetaTitle') == '1')
		{
			$this->document->setMetaData('title', $this->item->name);
		}

		if ($app->get('MetaAuthor') == '1')
		{
			$this->document->setMetaData('author', $this->item->author);
		}

	}
}
