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



if (!empty($this->msg))
{
	echo $this->msg;
}
else
{
	$lang      = JFactory::getLanguage();
    $lang->load('com_albopretorio', JPATH_ADMINISTRATOR .'/components/com_albopretorio', $lang->getTag());

	?>
<div class="albopretorio<?php echo $this->params->get('pageclass_sfx') ?>">
    <?php if ($this->category) :  ?>
        <div class="clearfix">
            <h1>
                <?php echo $this->escape($this->category->title); ?>
            </h1>
        <?php if ($this->params->get('show_cat_tags', 1) && !empty($this->category->tags)) : ?>
            <?php $this->category->tagLayout = new JLayoutFile('joomla.content.tags'); ?>
            <?php echo $this->category->tagLayout->render($this->category->tags); ?>
        <?php endif; ?>
            <a class="btn btn-primary pull-right" href="<?php echo AlbopretorioHelperRoute::getAlbopretorioRoute() ?>"><?php echo JText::_('COM_ALBOPRETORIO_ALL'); ?></a>
        </div>
        <?php if ($this->params->get('show_description')) :  ?>
            <?php echo $this->category->description; ?>
        <?php endif; ?>
    <?php elseif ($this->params->get('show_page_heading')) :  ?>
        <h1>
            <?php echo $this->escape($this->params->get('page_heading')); ?>
        </h1>
    <?php else: ?>
        <h1>
            <?php echo $this->escape($this->params->get('page_title')); ?>
        </h1>
    <?php endif; ?>
    <?php if ($this->params->get('description')) :  ?>
        <?php echo $this->params->get('description'); ?>
    <?php endif; ?>
    <?php echo $this->loadTemplate('items');
    AlbopretorioHelper::getSignature(); ?>
</div>
<?php } ?>

