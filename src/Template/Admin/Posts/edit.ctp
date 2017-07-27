<?php
/**
 * This file is part of me-cms.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright   Copyright (c) Mirko Pagliai
 * @link        https://github.com/mirko-pagliai/me-cms
 * @license     https://opensource.org/licenses/mit-license.php MIT License
 */
$this->extend('/Admin/Common/form');
$this->assign('title', $title = __d('me_cms', 'Edit post'));

$this->Library->ckeditor();
$this->Library->datetimepicker();
$this->Library->slugify();
$this->Asset->script(ME_CMS . '.admin/tags', ['block' => 'script_bottom']);
?>

<?= $this->Form->create($post); ?>
<div class='float-form'>
    <?php
    //Only admins and managers can edit posts on behalf of other users
    if ($this->Auth->isGroup(['admin', 'manager'])) {
        echo $this->Form->control('user_id', [
            'empty' => false,
            'label' => __d('me_cms', 'Author'),
        ]);
    }

    echo $this->Form->control('category_id', [
        'empty' => false,
        'label' => __d('me_cms', 'Category'),
    ]);
    echo $this->Form->datetimepicker('created', [
        'label' => __d('me_cms', 'Date'),
        'help' => [
            __d('me_cms', 'If blank, the current date and time will be used'),
            __d('me_cms', 'You can delay the publication by entering a future date'),
        ],
    ]);
    echo $this->Form->control('priority', [
        'label' => __d('me_cms', 'Priority'),
    ]);
    echo $this->Form->control('active', [
        'label' => sprintf('%s?', __d('me_cms', 'Published')),
        'help' => __d('me_cms', 'Disable this option to save as a draft'),
    ]);
    ?>
</div>
<fieldset>
    <?php
        echo $this->Form->control('title', [
            'id' => 'title',
            'label' => __d('me_cms', 'Title'),
        ]);
        echo $this->Form->control('subtitle', [
            'label' => __d('me_cms', 'Subtitle'),
        ]);
        echo $this->Form->control('slug', [
            'id' => 'slug',
            'label' => __d('me_cms', 'Slug'),
            'help' => __d('me_cms', 'The slug is a string identifying a resource. If ' .
                'you do not have special needs, let it be generated automatically'),
        ]);
    ?>
    <div class="form-group to-be-hidden">
        <?= $this->Form->control('tags_as_string', [
            'id' => 'tags-output-text',
            'label' => __d('me_cms', 'Tags'),
            'rows' => 2,
            'help' => __d('me_cms', 'Tags must be at least 3 chars and separated by a comma ' .
                'or a comma and a space. Only  lowercase letters, numbers, hyphen, space'),
        ]) ?>
    </div>
    <div class="form-group hidden to-be-shown">
        <div id="tags-preview">
            <?= sprintf('%s:', __d('me_cms', 'Tags')) ?>
        </div>
        <?php
            echo $this->Form->control('add_tags', [
                'button' => $this->Form->button(null, [
                    'class' => 'btn-success',
                    'icon' => 'plus',
                    'id' => 'tags-input-button',
                ]),
                'id' => 'tags-input-text',
                'label' => false,
                'help' => __d('me_cms', 'Tags must be at least 3 chars and separated by a comma ' .
                    'or a comma and a space. Only lowercase letters, numbers, hyphen, space'),
            ]);

            //Tags error
            if ($this->Form->isFieldError('tags')) {
                echo $this->Form->error('tags');
            }
        ?>
    </div>
    <?= $this->Form->ckeditor('text', [
        'label' => __d('me_cms', 'Text'),
        'rows' => 10,
    ]) ?>
    <?= $this->element('admin/bbcode') ?>
</fieldset>
<?= $this->Form->submit($title) ?>
<?= $this->Form->end() ?>