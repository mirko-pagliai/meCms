<?php
/**
 * This file is part of MeCms.
 *
 * MeCms is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * MeCms is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with MeCms.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author		Mirko Pagliai <mirko.pagliai@gmail.com>
 * @copyright	Copyright (c) 2015, Mirko Pagliai for Nova Atlantis Ltd
 * @license		http://www.gnu.org/licenses/agpl.txt AGPL License
 * @link		http://git.novatlantis.it Nova Atlantis Ltd
 * @package		MeCms\View\Photos
 */
?>
	
<div class="photos index">
	<?php echo $this->Html->h2(__d('me_cms', 'Photos')); ?>
	<div class='clearfix'>
		<?php foreach($photos as $photo): ?>
			<div class="col-sm-6 col-md-4 col-lg-3">
				<div class="photo-box">
					<?php 
						echo $this->Html->div('title', $photo['Photo']['filename']);
						echo $this->Html->thumb($photo['Photo']['path'], array('side' => 400));

						$actions = array($this->Html->link(__d('me_cms', 'Edit'), array('action' => 'edit', $id = $photo['Photo']['id']), array('icon' => 'pencil')));

						//Only admins and managers can delete photos
						if($this->Auth->isManager())
							$actions[] = $this->Form->postLink(__d('me_cms', 'Delete'), array('action' => 'delete', $id), array('class' => 'text-danger', 'icon' => 'trash-o'), __d('me_cms', 'Are you sure you want to delete this?'));

						$actions[] = $this->Html->link(__d('me_cms', 'Open'), array('action' => 'view', $id, 'admin' => FALSE), array('icon' => 'external-link', 'target' => '_blank'));

						echo $this->Html->ul($actions, array('class' => 'actions'));
					?>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
	<?php echo $this->element('MeTools.paginator'); ?>
</div>