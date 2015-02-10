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
 * Albopretorio Component Controller
 *
 * @package     Joomla.Site
 * @subpackage  com_albopretorio
 * @since       1.5
 */
class AlbopretorioController extends JControllerLegacy
{
	/**
	 * Method to show a albopretorio view
	 *
	 * @param   boolean			If true, the view output will be cached
	 * @param   array  An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return  JController		This object to support chaining.
	 * @since   1.5
	 */
	public function display($cachable = false, $urlparams = false)
	{
		$cachable = true;

		// Set the default view name and format from the Request.
		$vName = $this->input->get('view', 'albopretorio');
		$this->input->set('view', $vName);

        // It's a download?
        $isDownload = $this->input->get('field', false);

		$user = JFactory::getUser();

		if ($user->get('id') )
		{
			$cachable = false;
		}

		$safeurlparams = array('id' => 'INT', 'limit' => 'UINT', 'limitstart' => 'UINT', 'filter_order' => 'CMD', 'filter_order_Dir' => 'CMD', 'lang' => 'CMD');

		parent::display($cachable && !$isDownload, $safeurlparams);
	}
}
