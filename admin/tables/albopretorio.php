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
 * @package     Joomla.Administrator
 * @subpackage  com_albopretorio
 */
class AlbopretorioTableAlbopretorio extends JTable
{
	/**
	 * Ensure the params, metadata and images are json encoded in the bind method
	 *
	 * @var    array
	 * @since  3.3
	 */
	protected $_jsonEncode = array('params', 'metadata');

	/**
	 * Constructor
	 *
	 * @param   JDatabaseDriver  &$db  A database connector object
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__albopretorio', 'id', $db);

		JTableObserverTags::createObserver($this, array('typeAlias' => 'com_albopretorio.affissione'));
	}

	/**
	 * Overloaded check method to ensure data integrity.
	 *
	 * @return  boolean  True on success.
     * @TODO: add check for attachments and data
	 */
	public function check()
	{
		// Check for valid name.
		if (trim($this->name) == '')
		{
			$this->setError(JText::_('COM_ALBOPRETORIO_WARNING_PROVIDE_VALID_NAME'));
			return false;
		}

		if (empty($this->alias))
		{
			$this->alias = $this->name;
		}
		$this->alias = JApplication::stringURLSafe($this->alias);
		if (trim(str_replace('-', '', $this->alias)) == '')
		{
			$this->alias = JFactory::getDate()->format("Y-m-d-H-i-s");
		}

		// Check the publish down date is not earlier than publish up.
		if ((int) $this->publish_down > 0 && $this->publish_down < $this->publish_up)
		{
			$this->setError(JText::_('JGLOBAL_START_PUBLISH_AFTER_FINISH'));
			return false;
		}

        // Check this is not a new document and that the publish down date is > now
        if ( !$this->id && (int) $this->publish_down > 0 &&  $this->publish_down <= date('Y-m-d') )
        {
            $this->setError(JText::_('COM_ALBOPRETORIO_ERROR_PUBLISH_DOWN_DATE_IN_THE_PAST'));
            return false;
        }

        // Check this is not a new document and that the publish down date is > now
        if ( !$this->id && (int) $this->publish_up > 0 &&  $this->publish_up < date('Y-m-d') )
        {
            $this->setError(JText::_('COM_ALBOPRETORIO_ERROR_PUBLISH_UP_DATE_IN_THE_PAST'));
            return false;
        }

		// clean up keywords -- eliminate extra spaces between phrases
		// and cr (\r) and lf (\n) characters from string
		if (!empty($this->metakey))
		{
			// only process if not empty
			$bad_characters = array("\n", "\r", "\"", "<", ">"); // array of characters to remove
			$after_clean = JString::str_ireplace($bad_characters, "", $this->metakey); // remove bad characters
			$keys = explode(',', $after_clean); // create array using commas as delimiter
			$clean_keys = array();

			foreach ($keys as $key)
			{
				if (trim($key)) {  // ignore blank keywords
					$clean_keys[] = trim($key);
				}
			}
			$this->metakey = implode(", ", $clean_keys); // put array back together delimited by ", "
		}

		// clean up description -- eliminate quotes and <> brackets
		if (!empty($this->metadesc))
		{
			// only process if not empty
			$bad_characters = array("\"", "<", ">");
			$this->metadesc = JString::str_ireplace($bad_characters, "", $this->metadesc);
		}

		return true;
	}

	/**
	 * Overriden JTable::store to set modified data.
	 *
	 * @param   boolean	 $updateNulls  True to update fields even if they are null.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   1.6
	 */
	public function store($updateNulls = false)
	{
		$date	= JFactory::getDate();
		$user	= JFactory::getUser();
		if ($this->id)
		{
			// Existing item
			$this->modified		= $date->toSql();
			$this->modified_by	= $user->get('id');
		}
		else
		{
			// New albopretorio. A feed created and created_by field can be set by the user,
			// so we don't touch either of these if they are set.
			if (!(int) $this->created)
			{
				$this->created = $date->toSql();
			}
			if (empty($this->created_by))
			{
				$this->created_by = $user->get('id');
			}
		}
		// Verify that the alias is unique
		$table = JTable::getInstance('Albopretorio', 'AlbopretorioTable');
		if ($table->load(array('alias' => $this->alias, 'catid' => $this->catid)) && ($table->id != $this->id || $this->id == 0))
		{
			$this->setError(JText::_('COM_ALBOPRETORIO_ERROR_UNIQUE_ALIAS') . ' ' . $this->alias);
			return false;
		}

		return parent::store($updateNulls);
	}

	/**
	 * Method to delete a row from the database table by primary key value.
	 *
	 * @param   mixed  $pk  An optional primary key value to delete.  If not set the instance property value is used.
	 *
	 * @return  boolean  True on success.
	 *
	 * @link    http://docs.joomla.org/JTable/delete
	 * @since   11.1
	 * @throws  UnexpectedValueException
	 * @ABP: delete attachments
	 */
	public function delete($pk = null)
	{
		if (is_null($pk))
		{
			$pk = array();

			foreach ($this->_tbl_keys AS $key)
			{
				$pk[$key] = $this->$key;
			}
		}
		elseif (!is_array($pk))
		{
			$pk = array($this->_tbl_key => $pk);
		}

		foreach ($this->_tbl_keys AS $key)
		{
			$pk[$key] = is_null($pk[$key]) ? $this->$key : $pk[$key];

			if ($pk[$key] === null)
			{
				throw new UnexpectedValueException('Null primary key not allowed.');
			}
			$this->$key = $pk[$key];
		}

		// Implement JObservableInterface: Pre-processing by observers
		$this->_observers->update('onBeforeDelete', array($pk));

		// If tracking assets, remove the asset first.
		if ($this->_trackAssets)
		{
			// Get the asset name
			$name  = $this->_getAssetName();
			$asset = self::getInstance('Asset');

			if ($asset->loadByName($name))
			{
				if (!$asset->delete())
				{
					$this->setError($asset->getError());

					return false;
				}
			}
		}

		// ABP: remove attachments
		// TODO: move to helper
		jimport('joomla.filesystem.file');
		$application = JFactory::getApplication();
		$base_path = JPATH_ROOT . "/albopretorio_uploads";
		for($i = 0; $i < 12 ; $i++){
			$fname ="filename$i";
			if($this->$fname){
				if(!JFile::delete($base_path . '/' . $this->$fname)){
					$application->enqueueMessage(JText::_('COM_ALBOPRETORIO_DELETE_ATTACHMENT_ERROR') . ' ' . $this->$fname, 'warning');
				} else {
					$application->enqueueMessage(JText::_('COM_ALBOPRETORIO_DELETE_ATTACHMENT_SUCCESS') . ' ' . $this->$fname, 'message');
				}
			}
		}


		// Delete the row by primary key.
		$query = $this->_db->getQuery(true)
		->delete($this->_tbl);
		$this->appendPrimaryKeys($query, $pk);

		$this->_db->setQuery($query);

		// Check for a database error.
		$this->_db->execute();

		// Implement JObservableInterface: Post-processing by observers
		$this->_observers->update('onAfterDelete', array($pk));

		return true;
	}
}
