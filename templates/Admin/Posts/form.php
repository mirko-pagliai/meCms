<?php
declare(strict_types=1);

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
$this->extend('MeCms./common/form');
$this->Library->ckeditor();
$this->Library->datetimepicker();
$this->Library->slugify();
$this->Asset->script('MeCms.admin/tags', ['block' => 'script_bottom']);

$defaultCategory = $categories->count() < 2 ? $categories->first() : false;
$emptyCategory = !$defaultCategory && $this->getTemplate() !== 'edit';
?>

<?= $this->Form->create($post); ?>
<div class="row">
    <div class="col-lg-3 order-12">
        <div class="float-form">
        <?php
        //Only admins and managers can add posts on behalf of other users
        if ($this->Auth->isGroup(['admin', 'manager'])) {
            echo $this->Form->control('user_id', [
                'default' => $this->Auth->user('id'),
                'label' => I18N_AUTHOR,
            ]);
        }

        echo $this->Form->control('category_id', [
            'default' => $defaultCategory,
            'empty' => $emptyCategory,
            'label' => I18N_CATEGORY,
        ]);
        echo $this->Form->datetimepicker('created', [
            'help' => [I18N_USE_CURRENT_DATETIME, I18N_DELAY_PUBLICATION],
            'label' => I18N_DATE,
        ]);
        echo $this->Form->control('priority', [
            'default' => '3',
            'label' => I18N_PRIORITY,
        ]);
        echo $this->Form->control('active', [
            'help' => I18N_HELP_DRAFT,
            'label' => I18N_PUBLISHED,
        ]);
        echo $this->Form->control('enable_comments', [
            'help' => I18N_HELP_ENABLE_COMMENTS,
            'label' => I18N_ENABLE_COMMENTS,
        ]);
        ?>
        </div>
    </div>
    <fieldset class="col-lg-9">
    <?php
    echo $this->Form->control('title', [
        'id' => 'title',
        'label' => I18N_TITLE,
    ]);
    echo $this->Form->control('subtitle', [
        'label' => I18N_SUBTITLE,
    ]);
    echo $this->Form->control('slug', [
        'help' => I18N_HELP_SLUG,
        'id' => 'slug',
        'label' => I18N_SLUG,
    ]);
    ?>
    <div class="form-group to-be-hidden">
        <?= $this->Form->control('tags_as_string', [
            'help' => __d('me_cms', 'Tags must be at least 3 chars and separated by a comma ' .
                'or a comma and a space. Only lowercase letters, numbers, hyphen, space'),
            'id' => 'tags-output-text',
            'label' => I18N_TAGS,
            'rows' => 2,
        ]) ?>
    </div>
    <div class="form-group hidden to-be-shown">
        <div id="tags-preview">
            <?= $this->Form->label(sprintf('%s:', I18N_TAGS)) ?>
        </div>
        <?php
        echo $this->Form->control('add_tags', [
            'button' => $this->Form->button(null, [
                'class' => 'btn-success',
                'icon' => 'plus',
                'id' => 'tags-input-button',
            ]),
            'help' => __d('me_cms', 'Tags must be at least 3 chars and separated by a comma ' .
                'or a comma and a space. Only lowercase letters, numbers, hyphen, space'),
            'id' => 'tags-input-text',
            'label' => false,
        ]);

        //Tags error
        if ($this->Form->isFieldError('tags')) {
            echo str_replace(PHP_EOL, '<br />', $this->Form->error('tags'));
        }
        ?>
    </div>
    <?= $this->Form->ckeditor('text', ['label' => I18N_TEXT, 'rows' => 10]) ?>
    <?= $this->element('admin/bbcode') ?>
    </fieldset>
</div>
<?= $this->Form->submit($title) ?>
<?= $this->Form->end() ?>
