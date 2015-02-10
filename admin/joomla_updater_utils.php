<?php
/**
*
* @copyright    Copyright (C) 2015 Alessandro Pasotti http://www.itopen.it
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

// No direct access to this file
defined('_JEXEC') or die('Restricted access');


if ( ! defined('JOOMLA_UPDATER_UTILS') ) {
    define ('JOOMLA_UPDATER_UTILS', 1);


    function add_joomla_updater_params ( ) {
        $option = JRequest::getString('option');
        if ( $option ) {
            jimport('joomla.application.component.helper');
            $key = JComponentHelper::getParams( $option )->get('itopen_update_server_key', '');
            $db     = JFactory::getDBO();
            $query  = $db->getQuery(true);

            $query->select('s.*')->from('#__update_sites AS s');
            $query->join('INNER', '#__update_sites_extensions AS b ON s.update_site_id = b.update_site_id');
            $query->join('INNER', '#__extensions AS e ON b.extension_id = e.extension_id');

            $query->where('e.element = ' . $db->quote( $option ));

            $db->setQuery($query);
            $rec = $db->loadObject();
            if ( $rec ){
                $extra = 'r=' . base64_encode(JURI::base());
                if ( $key ) {
                    $extra .= '&k=' . $key;
                }
                // Re-enable in any case
                // TODO: remove when https://github.com/joomla/joomla-cms/pull/3775 is in production
                if ( $rec->extra_query != $extra || $rec->enabled != '1') {
                    $rec->enabled = 1;
                    $rec->extra_query = $extra;
                    $db->updateObject('#__update_sites', $rec, 'update_site_id');
                }
            }
        }
    }
}

add_joomla_updater_params();
