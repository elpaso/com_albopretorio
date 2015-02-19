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
defined ( '_JEXEC' ) or die ();

JLoader::register ( 'AlbopretorioHelper', JPATH_ADMINISTRATOR . '/components/com_albopretorio/helpers/albopretorio.php' );

/**
 * Albopretorio model.
 *
 * @package Joomla.Administrator
 * @subpackage com_albopretorio
 * @since 1.6
 */
class AlbopretorioModelAffissione extends JModelAdmin {

	/**
	 * The type alias for this content type.
	 *
	 * @var string
	 * @since 3.2
	 */
	public $typeAlias = 'com_albopretorio.affissione';

	/**
	 *
	 * @var string The prefix to use with controller messages.
	 * @since 1.6
	 */
	protected $text_prefix = 'COM_ALBOPRETORIO';

	/**
	 * Batch copy items to a new category or current.
	 *
	 * @param integer $value
	 *        	The new category.
	 * @param array $pks
	 *        	An array of row IDs.
	 * @param array $contexts
	 *        	An array of item contexts.
	 *
	 * @return mixed An array of new IDs on success, boolean false on failure.
	 *
	 * @since 11.1
	 */
	protected function batchCopy($value, $pks, $contexts) {
		$categoryId = ( int ) $value;

		$i = 0;

		if (! parent::checkCategoryId ( $categoryId )) {
			return false;
		}

		// Parent exists so we let's proceed
		while ( ! empty ( $pks ) ) {
			// Pop the first ID off the stack
			$pk = array_shift ( $pks );

			$this->table->reset ();

			// Check that the row actually exists
			if (! $this->table->load ( $pk )) {
				if ($error = $this->table->getError ()) {
					// Fatal error
					$this->setError ( $error );

					return false;
				} else {
					// Not fatal error
					$this->setError ( JText::sprintf ( 'JLIB_APPLICATION_ERROR_BATCH_MOVE_ROW_NOT_FOUND', $pk ) );
					continue;
				}
			}

			// Alter the title & alias
			$data = $this->generateNewTitle ( $categoryId, $this->table->alias, $this->table->name );
			$this->table->name = $data ['0'];
			$this->table->alias = $data ['1'];

			// Reset the ID because we are making a copy
			$this->table->id = 0;

			// New category ID
			$this->table->catid = $categoryId;

			// TODO: Deal with ordering?
			// $this->table->ordering = 1;

			// Check the row.
			if (! $this->table->check ()) {
				$this->setError ( $this->table->getError () );
				return false;
			}

			parent::createTagsHelper ( $this->tagsObserver, $this->type, $pk, $this->typeAlias, $this->table );

			// Store the row.
			if (! $this->table->store ()) {
				$this->setError ( $this->table->getError () );
				return false;
			}

			// Get the new item ID
			$newId = $this->table->get ( 'id' );

			// Add the new ID to the array
			$newIds [$i] = $newId;
			$i ++;
		}

		// Clean the cache
		$this->cleanCache ();

		return $newIds;
	}


    /**
    * @brief Check pub dates and publish state
    *
    * @return
    */
    public function isPublished($item) {
        return     $item->published == '1'
                && $item->publish_up < date('Y-m-d H:i:s')
                && $item->publish_down > date('Y-m-d H:i:s');
    }

	/**
	 * Method to test whether a record can be deleted.
	 *
	 * @param
	 *        	object A record object.
	 * @return boolean True if allowed to delete the record. Defaults to the permission set in the component.
	 * @since 1.6
	 */
	protected function canDelete($record) {
		if (! empty ( $record->id )) {
			if ($record->published != - 2) {
				return;
			}
			$user = JFactory::getUser ();

			if (! empty ( $record->catid )) {
				$canDelete = $user->authorise ( 'core.delete', 'com_albopretorio.category.' . ( int ) $record->catid );
			} else {
				$canDelete = parent::canDelete ( $record );
			}
		}
        // ABP: Additional check on pub dates: deny if published, only superadmin can change
        if ( $canDelete && ! $user->authorise('core.admin')  && $record->id )
        {
            $item = $this->getItem ( $record->id );
            $canDelete = ! $this->isPublished($item);
        }
        return $canDelete;
	}

	/**
	 * Method to test whether a record can have its state changed.
	 *
	 * @param
	 *        	object A record object.
	 * @return boolean True if allowed to change the state of the record. Defaults to the permission set in the component.
	 * @since 1.6
	 */
	protected function canEditState($record) {
		$user = JFactory::getUser ();

		if (! empty ( $record->catid )) {
			$canEditState = $user->authorise ( 'core.edit.state', 'com_albopretorio.category.' . ( int ) $record->catid );
		} else {
			$canEditState = parent::canEditState ( $record );
		}

        // ABP: Additional check on pub dates: deny if published, only superadmin can change
        if ( $canEditState && ! $user->authorise('core.admin') && $record->id )
        {
            $item = $this->getItem ($record->id);
            $canEditState = ! $this->isPublished($item);
        }
        return $canEditState;
	}

	/**
	 * Returns a Table object, always creating it.
	 *
	 * @param
	 *        	type The table type to instantiate
	 * @param
	 *        	string A prefix for the table class name. Optional.
	 * @param
	 *        	array Configuration array for model. Optional.
	 * @return JTable A database object
	 */
	public function getTable($type = 'Albopretorio', $prefix = 'AlbopretorioTable', $config = array()) {
		return JTable::getInstance ( $type, $prefix, $config );
	}

	/**
	 * Method to get the record form.
	 *
	 * @param array $data
	 *        	Data for the form.
	 * @param boolean $loadData
	 *        	True if the form is to load its own data (default case), false if not.
	 * @return JForm A JForm object on success, false on failure
	 * @since 1.6
	 */
	public function getForm($data = array(), $loadData = true) {

		// Get the form.
		$form = $this->loadForm ( 'com_albopretorio.affissione', 'affissione', array (
				'control' => 'jform',
				'load_data' => $loadData
		) );
		if (empty ( $form )) {
			return false;
		}

        // ABP: now implemented in allowEdit at the controller level - Joomla! Sucks! (TM)
        /*/ Checks if canEdit
        if($form->getData()->get('id'))
        {
            if(! $this->canEdit( (object) $form->getData()->toObject( ) ))
            {
                $app = JFactory::getApplication ();
                // Add a message to the message queue
                $app->enqueueMessage ( JText::_ ( 'COM_ALBOPRETORIO_CANNOT_EDIT_PUBLISHED_DOCUMENT' ), 'warning' );
                $this->checkin($form->getData()->get('id'));
                $link = JRoute::_('index.php?option=com_albopretorio');
                $app->redirect($link);
                return;
            }
        }*/

		// Determine correct permissions to check.
		if ($this->getState ( 'affissione.id' )) {
			// Existing record. Can only edit in selected categories.
			$form->setFieldAttribute ( 'catid', 'action', 'core.edit' );
		} else {
			// New record. Can only create in selected categories.
			$form->setFieldAttribute ( 'catid', 'action', 'core.create' );
		}

		// Modify the form based on access controls.
		if (! $data ){
            $data = (object) $form->getData()->toObject( ) ;
        }

		if ( ! $this->canEditState ( ( object ) $data )) {

            // Disable fields for display.
            $form->setFieldAttribute ( 'ordering', 'disabled', 'true' );
            $form->setFieldAttribute ( 'published', 'disabled', 'true' );

            // Disable fields while saving.
            // The controller has already verified this is a record you can edit.
            $form->setFieldAttribute ( 'ordering', 'filter', 'unset' );
            $form->setFieldAttribute ( 'published', 'filter', 'unset' );

            // Only disable fields if it's an existing record

            if ( !is_object( $data ) ) {
                $data = ( object ) $data;
            }
            if  ( $data->id) {
                $form->setFieldAttribute ( 'publish_up', 'disabled', 'true' );
                $form->setFieldAttribute ( 'publish_down', 'disabled', 'true' );

                $form->setFieldAttribute ( 'publish_up', 'filter', 'unset' );
                $form->setFieldAttribute ( 'publish_down', 'filter', 'unset' );
                // ABP: these are required fields... unset required to allow saving
                $form->setFieldAttribute ( 'publish_up', 'required', 'false' );
                $form->setFieldAttribute ( 'publish_down', 'required', 'false' );
            }
		}

		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return mixed The data for the form.
	 * @since 1.6
	 */
	protected function loadFormData() {
		// Check the session for previously entered form data.
		$data = JFactory::getApplication ()->getUserState ( 'com_albopretorio.edit.affissione.data', array () );
		if (empty ( $data )) {
			$data = $this->getItem ();

			// Prime some default values.
			if ($this->getState ( 'affissione.id' ) == 0) {
				$app = JFactory::getApplication ();
				$data->set ( 'catid', $app->input->get ( 'catid', $app->getUserState ( 'com_albopretorio.affissione.filter.category_id' ), 'int' ) );
			}
		}

		$this->preprocessData ( 'com_albopretorio.affissione', $data );

		return $data;
	}

	public function makeFolder($folder) {
		if (! JFolder::exists ( $folder )) {
			if (! JFolder::create ( $folder )) {
				$app = JFactory::getApplication ();
				// Add a message to the message queue
				$app->enqueueMessage ( JText::_ ( 'COM_ALBOPRETORIO_CANNOT_CREATE_UPLOAD_FOLDER' ) . ' ' . $folder, 'warning' );
				return false;
			}
		}
		return true;
	}

	/**
	 * Method to save the form data.
	 *
	 * @param
	 *        	array The form data.
	 *
	 * @return boolean True on success.
	 * @since 3.0
	 */
	public function save($data) {
		$app = JFactory::getApplication ();

		// Alter the title for save as copy
		if ($app->input->get ( 'task' ) == 'save2copy') {
			list ( $name, $alias ) = $this->generateNewTitle ( $data ['catid'], $data ['alias'], $data ['name'] );
			$data ['name'] = $name;
			$data ['alias'] = $alias;
			$data ['published'] = 0;
		}

		// Handle files
		$jinput = JFactory::getApplication ()->input;
		$files = $jinput->files->get ( 'jform' );
		if (parent::save ( $data )) {
			$table = $this->getTable ();
			$key = $table->getKeyName ();
			if ($data [$key]) {
				$item = parent::getItem ( $data [$key] );
			} else {
				$item = false;
			}

			// Store files
			jimport ( 'joomla.filesystem.file' );
			jimport ( 'joomla.filesystem.folder' );

			// TODO: move to helper
			$base_path = JPATH_ROOT . "/albopretorio_uploads";

			if (! $this->makeFolder ( $base_path )) {
				return false;
			}

			$uploaded = 0;
			foreach ( $files as $fname => $file ) {
				// Clean up filename to get rid of strange characters like spaces etc
				$uploaded_filename = JFile::makeSafe ( $file ['name'] );
				if (! $uploaded_filename) {
					continue;
				}
				// Set up the source and destination of the file
				$src = $file ['tmp_name'];

				// Create sub folders
				$today = getdate ();
				$month = $today ['mon'];
				$year = $today ['year'];

				$filename = $year . '/' . $month . '/' . $uploaded_filename;
				$dest = $base_path . '/' . $filename;

				// Checks if there's a file with the same name
				while ( file_exists($dest) ) {
                    $ext = pathinfo($uploaded_filename, PATHINFO_EXTENSION);
                    $base = pathinfo($uploaded_filename, PATHINFO_FILENAME);
                    $number = '' . ((int) preg_replace("/.*?(\d+)$/", '\1', $base) + 1);
                    $prefix = preg_replace("/(.*?)\d+$/", '\1', $base);
                    $uploaded_filename = $prefix . $number . '.' . $ext;
                    $filename = $year . '/' . $month . '/' . $uploaded_filename;
                    $dest = $base_path . '/' . $filename;
                }

				// First check if the file has the right extension, we deny php
				// TODO: make this configurable!
				if (strtolower ( JFile::getExt ( $filename ) ) != 'php') {
					if (! JFile::upload ( $src, $dest )) {
						// Add a message to the message queue
						$app->enqueueMessage ( JText::_ ( 'COM_ALBOPRETORIO_CANNOT_UPLOAD_ATTACHMENT' ) . ' ' . $dest, 'warning' );
					} else {
						// Add a message to the message queue
						// Delete old file if any
						if ($item && $item->$fname && $item->$fname != $filename) {
							if (! JFile::delete ( $base_path . '/' . $item->$fname )) {
								$app->enqueueMessage ( JText::_ ( 'COM_ALBOPRETORIO_DELETE_ATTACHMENT_ERROR' ) . ' ' . $item->$fname, 'warning' );
							}
							$app->enqueueMessage ( JText::_ ( 'COM_ALBOPRETORIO_OVERWRITE_ATTACHMENT_SUCCESS' ) . ' ' . $filename, 'message' );
						} else {
							$app->enqueueMessage ( JText::_ ( 'COM_ALBOPRETORIO_UPLOAD_ATTACHMENT_SUCCESS' ) . ' ' . $filename, 'message' );
						}
						// Save new file name
						$data [$fname] = $filename;
						$uploaded ++;
					}
				}
			}

			// Remove attachments on user request
			$removed = 0;
			if ($item) {
				$form_data = $jinput->get ( 'jform', '', 'array' );
				for($i = 0; $i < 12; $i ++) {
					$fname = "filename$i";
					if (array_key_exists ( $fname . '_remove', $form_data )) {
						if (! $files [$fname] ['size'] && $item->$fname) {
							if (! JFile::delete ( $base_path . '/' . $item->$fname )) {
								$app->enqueueMessage ( JText::_ ( 'COM_ALBOPRETORIO_DELETE_ATTACHMENT_ERROR' ) . ' ' . $item->$fname, 'error' );
							} else {
								$app->enqueueMessage ( JText::_ ( 'COM_ALBOPRETORIO_DELETE_ATTACHMENT_SUCCESS' ) . ' ' . $item->$fname, 'notice' );
								$removed ++;
								$data [$fname] = '';
							}
						}
					}
				}
			}


			// Update attachments
			if ($uploaded || $removed) {
				// If isNew
				if (! $item) {
					$data [$key] = $this->getState ( $this->getName () . '.id' );
				}
				parent::save ( $data );
			}

            // Check that we have at least one attachment...
            $attachments = 0;
            if ( ! $item )
            {
                for($i = 0; $i < 12; $i++)
                {
                    $fname = "filename$i";
                    if( isset($data[$fname]) && $item[$fname] ) {
                        $attachments++;
                    }
                }
            } else {
                // Reload item ...
                $item = parent::getItem ( $data [$key] );
                for($i = 0; $i < 12; $i++)
                {
                    $fname = "filename$i";
                    if( isset($item->$fname) && $item->$fname) {
                        $attachments++;
                    }
                }
            }
            if ( ! $uploaded && ! $attachments )
            {
                $app->enqueueMessage ( JText::_ ( 'COM_ALBOPRETORIO_NO_ATTACHMENTS' ) , 'warning' );
            }


			$assoc = JLanguageAssociations::isEnabled ();
			if ($assoc) {
				$id = ( int ) $this->getState ( $this->getName () . '.id' );
				$item = $this->getItem ( $id );

				// Adding self to the association
				$associations = $data ['associations'];

				foreach ( $associations as $tag => $id ) {
					if (empty ( $id )) {
						unset ( $associations [$tag] );
					}
				}

				// Detecting all item menus
				$all_language = $item->language == '*';

				if ($all_language && ! empty ( $associations )) {
					JError::raiseNotice ( 403, JText::_ ( 'COM_ALBOPRETORIO_ERROR_ALL_LANGUAGE_ASSOCIATED' ) );
				}

				$associations [$item->language] = $item->id;

				// Deleting old association for these items
				$db = JFactory::getDbo ();
				$query = $db->getQuery ( true )->delete ( '#__associations' )->where ( $db->quoteName ( 'context' ) . ' = ' . $db->quote ( 'com_albopretorio.item' ) )->where ( $db->quoteName ( 'id' ) . ' IN (' . implode ( ',', $associations ) . ')' );
				$db->setQuery ( $query );
				$db->execute ();

				if ($error = $db->getErrorMsg ()) {
					$this->setError ( $error );
					return false;
				}

				if (! $all_language && count ( $associations )) {
					// Adding new association for these items
					$key = md5 ( json_encode ( $associations ) );
					$query->clear ()->insert ( '#__associations' );

					foreach ( $associations as $id ) {
						$query->values ( $id . ',' . $db->quote ( 'com_albopretorio.item' ) . ',' . $db->quote ( $key ) );
					}

					$db->setQuery ( $query );
					$db->execute ();

					if ($error = $db->getErrorMsg ()) {
						$this->setError ( $error );
						return false;
					}
				}
			}

			return true;
		} else {
            // vardie() for debugging
            //foreach( $this->getErrors () as $error ){
            //    $app->enqueueMessage ( $error, 'error' );
            //}
		}

		return false;
	}

	/**
	 * Method to get a single record.
	 *
	 * @param
	 *        	integer The id of the primary key.
	 *
	 * @return mixed Object on success, false on failure.
	 * @since 1.6
	 */
	public function getItem($pk = null) {
		if ($item = parent::getItem ( $pk )) {
			// vardie($item);
			// Convert the params field to an array.
			$registry = new JRegistry ();
			$registry->loadString ( $item->metadata );
			$item->metadata = $registry->toArray ();

			/*
			 * / Convert the images field to an array. $registry = new JRegistry; $registry->loadString($item->images); $item->images = $registry->toArray();
			 */
		}
		if (! empty ( $item->id )) {
			// Load user (author)
			$item->author = JUser::getInstance ( $item->created_by )->name;

			// Load category
			$db = JFactory::getDBO ();
			$db->setQuery ( "SELECT title FROM #__categories WHERE id = " . $item->catid . " LIMIT 1;" );
			$item->category_title = $db->loadResult ();
		} else {

            // Get latest number
            $db = JFactory::getDBO ();
            $db->setQuery ( "SELECT COUNT(id) FROM #__albopretorio" );
            if ( $db->loadResult () > 0) {
                // If numerical ordering:
                // Autoincrement?
                jimport('joomla.application.component.helper');
                if (  JComponentHelper::getParams('com_albopretorio')->get('autoincrement_sort_numerically') != '0' ) {
                    $db->setQuery ( "SELECT official_number FROM #__albopretorio ORDER BY CAST(official_number as SIGNED INTEGER) DESC LIMIT 1;" );
                } else {
                    $db->setQuery ( "SELECT official_number FROM #__albopretorio ORDER BY official_number DESC LIMIT 1;" );
                }
                $item->official_number = (int) $db->loadResult () + 1;
            } else {
                $item->official_number = 1;
            }

        }

		// Load associated albopretorio items
		$app = JFactory::getApplication ();
		$assoc = JLanguageAssociations::isEnabled ();

		if ($assoc) {
			$item->associations = array ();

			if ($item->id != null) {
				$associations = JLanguageAssociations::getAssociations ( 'com_albopretorio', '#__albopretorio', 'com_albopretorio.item', $item->id );

				foreach ( $associations as $tag => $association ) {
					$item->associations [$tag] = $association->id;
				}
			}
		}

		if (! empty ( $item->id )) {
			$item->tags = new JHelperTags ();
			$item->tags->getTagIds ( $item->id, 'com_albopretorio.affissione' );
			$item->metadata ['tags'] = $item->tags;
		}

		return $item;
	}

	/**
	 * Prepare and sanitise the table prior to saving.
	 */
	protected function prepareTable($table) {
		$date = JFactory::getDate ();
		$user = JFactory::getUser ();

		$table->name = htmlspecialchars_decode ( $table->name, ENT_QUOTES );
		$table->alias = JApplication::stringURLSafe ( $table->alias );

		if (empty ( $table->alias )) {
			$table->alias = JApplication::stringURLSafe ( $table->name );
		}

		if (empty ( $table->id )) {
			// Set the values
			$table->created = $date->toSql ();

			// Set ordering to the last item if not set
			if (empty ( $table->ordering )) {
				$db = JFactory::getDbo ();
				$query = $db->getQuery ( true )->select ( 'MAX(ordering)' )->from ( $db->quoteName ( '#__albopretorio' ) );
				$db->setQuery ( $query );
				$max = $db->loadResult ();

				$table->ordering = $max + 1;
			}
		} else {
			// Set the values
			$table->modified = $date->toSql ();
			$table->modified_by = $user->get ( 'id' );
		}

        // Make sure publish_down date is included:
        if (!empty( $table->publish_down ))
        {
            $table->publish_down = substr( $table->publish_down, 0, 10 ) . ' 23:59:59';
        }

		// Increment the content version number.
		// $table->version++;

	}

	/**
	 * A protected method to get a set of ordering conditions.
	 *
	 * @param
	 *        	object A record object.
	 * @return array An array of conditions to add to add to ordering queries.
	 * @since 1.6
	 */
	protected function getReorderConditions($table) {
		$condition = array ();
		$condition [] = 'catid = ' . ( int ) $table->catid;
		return $condition;
	}

	protected function preprocessForm(JForm $form, $data, $group = 'content') {
		// Association albopretorio items
		$app = JFactory::getApplication ();
		$assoc = JLanguageAssociations::isEnabled ();
		if ($assoc) {
			$languages = JLanguageHelper::getLanguages ( 'lang_code' );
			$addform = new SimpleXMLElement ( '<form />' );
			$fields = $addform->addChild ( 'fields' );
			$fields->addAttribute ( 'name', 'associations' );
			$fieldset = $fields->addChild ( 'fieldset' );
			$fieldset->addAttribute ( 'name', 'item_associations' );
			$fieldset->addAttribute ( 'description', 'COM_ALBOPRETORIO_ITEM_ASSOCIATIONS_FIELDSET_DESC' );
			$add = false;
			foreach ( $languages as $tag => $language ) {
				if (empty ( $data->language ) || $tag != $data->language) {
					$add = true;
					$field = $fieldset->addChild ( 'field' );
					$field->addAttribute ( 'name', $tag );
					$field->addAttribute ( 'type', 'modal_albopretorio' );
					$field->addAttribute ( 'language', $tag );
					$field->addAttribute ( 'label', $language->title );
					$field->addAttribute ( 'translate_label', 'false' );
					$field->addAttribute ( 'edit', 'true' );
					$field->addAttribute ( 'clear', 'true' );
				}
			}
			if ($add) {
				$form->load ( $addform, false );
			}
		}

		parent::preprocessForm ( $form, $data, $group );
	}

	/**
	 * Method to change the title & alias.
	 *
	 * @param integer $parent_id
	 *        	The id of the parent.
	 * @param string $alias
	 *        	The alias.
	 * @param string $title
	 *        	The title.
	 *
	 * @return array Contains the modified title and alias.
	 *
	 * @since 3.1
	 */
	protected function generateNewTitle($category_id, $alias, $name) {
		// Alter the title & alias
		$table = $this->getTable ();
		while ( $table->load ( array (
				'alias' => $alias,
				'catid' => $category_id
		) ) ) {
			if ($name == $table->name) {
				$name = JString::increment ( $name );
			}
			$alias = JString::increment ( $alias, 'dash' );
		}

		return array (
				$name,
				$alias
		);
	}
}
