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
 * @author      Mirko Pagliai <mirko.pagliai@gmail.com>
 * @copyright   Copyright (c) 2016, Mirko Pagliai for Nova Atlantis Ltd
 * @license     http://www.gnu.org/licenses/agpl.txt AGPL License
 * @link        http://git.novatlantis.it Nova Atlantis Ltd
 */
$this->extend('/Admin/Common/form');
$this->assign('title', $title = __d('me_cms', 'Edit banner'));
?>

<?= $this->Form->create($banner); ?>
<div class='float-form'>
    <?php
        echo $this->Form->control('position_id', [
            'label' => __d('me_cms', 'Position'),
        ]);
        echo $this->Form->control('active', [
            'label' => sprintf('%s?', __d('me_cms', 'Published')),
        ]);
        echo $this->Form->control('thumbnail', [
            'label' => __d('me_cms', 'Thumbnail'),
            'help' => __d('me_cms', 'The banner is displayed as a  thumbnail. ' .
                'You should disable this, if the banner is an animated gif'),
        ]);
    ?>
</div>
<fieldset>
    <p><?= $this->Html->strong(__d('me_cms', 'Preview')) ?></p>
    <?php
    if ($banner->thumbnail) {
        echo $this->Thumb->resize($banner->path, ['width' => 1186], ['class' => 'img-thumbnail margin-15']);
    } else {
        echo $this->Html->img($banner->www, ['class' => 'img-thumbnail margin-15']);
    }

    echo $this->Form->control('filename', [
        'disabled' => true,
        'label' => __d('me_cms', 'Filename'),
    ]);
    echo $this->Form->control('target', [
        'label' => __d('me_cms', 'Web address'),
        'help' => __d('me_cms', 'The address should begin with {0}', $this->Html->em('http://')),
    ]);
    echo $this->Form->control('description', [
        'label' => __d('me_cms', 'Description'),
        'rows' => 3,
        'type' => 'textarea',
    ]);
    ?>
</fieldset>
<?= $this->Form->submit($title) ?>
<?= $this->Form->end() ?>