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

JHtml::_ ( 'bootstrap.tooltip' );

if (!empty($this->msg))
{
	echo $this->msg;
}
else
{
	$lang      = JFactory::getLanguage();
    $lang->load('com_albopretorio', JPATH_ADMINISTRATOR .'/components/com_albopretorio', $lang->getTag());

	?>
	<div class="affissione">
	<div class="clearfix">
	<h2 class="pull-left">
		<?php if ($this->item->published == 0) : ?>
			<span class="label label-warning"><?php echo JText::_('JUNPUBLISHED'); ?></span>
		<?php endif; ?>
		<?php echo str_replace('&apos;', "'", $this->item->name); ?>

	</h2>
	<a class="btn btn-primary pull-right" href="<?php echo AlbopretorioHelperRoute::getAlbopretorioRoute() ?>"><?php echo JText::_('COM_ALBOPRETORIO_ALL'); ?></a>
	</div>

	<?php if ($this->params->get('show_tags', 1)) : ?>
		<?php $this->item->tagLayout = new JLayoutFile('joomla.content.tags'); ?>
		<?php echo $this->item->tagLayout->render($this->item->tags->itemTags); ?>
	<?php endif; ?>

    <?php if ($this->params->get('show_item_description', 1)) : ?>
	<!-- Show Description from Component -->
	<?php echo $this->item->description; ?>
    <?php endif; ?>

    <dl class="dl-horizontal albopretorio">
        <dt><?php echo JText::_('COM_ALBOPRETORIO_HEADING_CATEGORY') ?></dt>
        <dd><?php

        $category = JCategories::getInstance('Albopretorio')->get($this->item->catid);
        echo AlbopretorioHelper::getCategoryPath($category);

		?></dd>

        <dt><?php echo JText::_('COM_ALBOPRETORIO_HEADING_DOCUMENT_DATE') ?></dt>
        <dd><?php echo JHtml::_('date', $this->item->document_date, JText::_('DATE_FORMAT_LC3')); ?></dd>

        <dt><?php echo JText::_('COM_ALBOPRETORIO_HEADING_OFFICIAL_NUMBER') ?></dt>
        <dd><?php echo $this->item->official_number; ?></dd>

        <dt><?php echo JText::_('COM_ALBOPRETORIO_HEADING_DOCUMENT_NUMBER') ?></dt>
        <dd><?php echo $this->item->document_number; ?></dd>


        <dt><?php echo JText::_('COM_ALBOPRETORIO_HEADING_PUBLISH_DATE') ?></dt>
        <dd><?php echo JHtml::_('date', $this->item->publish_up, JText::_('DATE_FORMAT_LC3')); ?></dd>

        <dt><?php echo JText::_('COM_ALBOPRETORIO_HEADING_SUSPEND_DATE') ?></dt>
        <dd><?php echo JHtml::_('date',  $this->item->publish_down, JText::_('DATE_FORMAT_LC3')); ?></dd>
    </dl>


    <?php  if( ! count($this->item->attachments) ) { ?>
        <div class="alert alert-warning"><?php echo JText::_('COM_ALBOPRETORIO_NO_ATTACHMENTS') ?></div>
    <?php  } else { ?>
        <h3><?php echo JText::_('COM_ALBOPRETORIO_ATTACHMENTS') ?></h3>
        <table class="table">
            <thead>
                <th><?php echo JText::_('COM_ALBOPRETORIO_ATTACHMENTS_HEADING_NAME') ?></th>
                <th><?php echo JText::_('COM_ALBOPRETORIO_ATTACHMENTS_HEADING_SIZE') ?></th>
                <!-- th><?php echo JText::_('COM_ALBOPRETORIO_ATTACHMENTS_HEADING_DOWNLOAD') ?></th> -->
            </thead>
            <tbody>
        <?php foreach( $this->item->attachments as $attachment ){ ?>
            <tr>
            <td><a class="hasTooltip" title="<?php echo JText::_('COM_ALBOPRETORIO_ATTACHMENTS_DOWNLOAD') . ' ' . $attachment->display_name ?>" class="hasTooltip" href="<?php echo $attachment->link ?>" title="<?php echo JText::_('COM_ALBOPRETORIO_ATTACHMENTS_DOWNLOAD') . ' ' . $attachment->display_name ?>"><i class="icon icon-download"></i> <?php echo $attachment->display_name ?></a></td>
            <td><?php echo $attachment->size ?></td>
            <!-- <td><a href="<?php echo $attachment->link ?>" title="<?php echo JText::_('COM_ALBOPRETORIO_ATTACHMENTS_DOWNLOAD') . ' ' . $attachment->display_name ?>"><i class="icon icon-download"></i></a></td>-->
            </tr>
        <?php } ?>
            </tbody>
        </table>
    <?php }
    AlbopretorioHelper::getSignature();
    ?>


	</div>

<?php } ?>
