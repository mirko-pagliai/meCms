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
$this->extend('/Admin/Common/index');
$this->assign('title', __d('me_cms', 'Pages'));

$this->append('actions', $this->Html->button(
    __d('me_cms', 'Add'),
    ['action' => 'add'],
    ['class' => 'btn-success', 'icon' => 'plus']
));
$this->append('actions', $this->Html->button(
    __d('me_cms', 'Add category'),
    ['controller' => 'PagesCategories', 'action' => 'add'],
    ['class' => 'btn-success', 'icon' => 'plus']
));

$this->Library->datepicker('#created', ['format' => 'MM-YYYY', 'viewMode' => 'years']);
?>

<?= $this->Form->createInline(false, ['class' => 'filter-form', 'type' => 'get']) ?>
    <fieldset>
        <?= $this->Html->legend(__d('me_cms', 'Filter'), ['icon' => 'eye']) ?>
        <?php
        echo $this->Form->control('id', [
            'default' => $this->request->getQuery('id'),
            'placeholder' => __d('me_cms', 'ID'),
            'size' => 2,
        ]);
        echo $this->Form->control('title', [
            'default' => $this->request->getQuery('title'),
            'placeholder' => __d('me_cms', 'title'),
            'size' => 16,
        ]);
        echo $this->Form->control('active', [
            'default' => $this->request->getQuery('active'),
            'empty' => sprintf('-- %s --', __d('me_cms', 'all status')),
            'options' => [
                'yes' => __d('me_cms', 'Only published'),
                'no' => __d('me_cms', 'Only drafts'),
            ],
        ]);
        echo $this->Form->control('category', [
            'default' => $this->request->getQuery('category'),
            'empty' => sprintf('-- %s --', __d('me_cms', 'all categories')),
        ]);
        echo $this->Form->control('priority', [
            'default' => $this->request->getQuery('priority'),
            'empty' => sprintf('-- %s --', __d('me_cms', 'all priorities')),
        ]);
        echo $this->Form->datepicker('created', [
            'data-date-format' => 'YYYY-MM',
            'default' => $this->request->getQuery('created'),
            'placeholder' => __d('me_cms', 'month'),
            'size' => 5,
        ]);
        echo $this->Form->submit(null, ['icon' => 'search']);
        ?>
    </fieldset>
<?= $this->Form->end() ?>

<table class="table table-hover">
    <thead>
        <tr>
            <th class="text-center"><?= $this->Paginator->sort('id', __d('me_cms', 'ID')) ?></th>
            <th><?= $this->Paginator->sort('title', __d('me_cms', 'Title')) ?></th>
            <th class="text-center"><?= $this->Paginator->sort('Categories.title', __d('me_cms', 'Category')) ?></th>
            <th class="min-width text-center"><?= $this->Paginator->sort('priority', __d('me_cms', 'Priority')) ?></th>
            <th class="min-width text-center"><?= $this->Paginator->sort('created', __d('me_cms', 'Date')) ?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($pages as $page) : ?>
            <tr>
                <td class="min-width text-center">
                    <code><?= $page->id ?></code>
                </td>
                <td>
                    <strong><?= $this->Html->link($page->title, ['action' => 'edit', $page->id]) ?></strong>
                    <?php
                    //If the page is not active (it's a draft)
                    if (!$page->active) {
                        echo $this->Html->span(
                            __d('me_cms', 'Draft'),
                            ['class' => 'record-label record-label-warning']
                        );
                    }

                    //If the page is scheduled
                    if ($page->created->isFuture()) {
                        echo $this->Html->span(
                            __d('me_cms', 'Scheduled'),
                            ['class' => 'record-label record-label-warning']
                        );
                    }

                    $actions = [];

                    //Only admins and managers can edit pages
                    if ($this->Auth->isGroup(['admin', 'manager'])) {
                        $actions[] = $this->Html->link(
                            __d('me_cms', 'Edit'),
                            ['action' => 'edit', $page->id],
                            ['icon' => 'pencil']
                        );
                    }

                    //Only admins can delete pages
                    if ($this->Auth->isGroup('admin')) {
                        $actions[] = $this->Form->postLink(
                            __d('me_cms', 'Delete'),
                            ['action' => 'delete', $page->id],
                            [
                                'class' => 'text-danger',
                                'icon' => 'trash-o',
                                'confirm' => __d('me_cms', 'Are you sure you want to delete this?'),
                            ]
                        );
                    }

                    //If the page is active and is not scheduled
                    if ($page->active && !$page->created->isFuture()) {
                        $actions[] = $this->Html->link(
                            __d('me_cms', 'Open'),
                            ['_name' => 'page', $page->slug],
                            ['icon' => 'external-link', 'target' => '_blank']
                        );
                    } else {
                        $actions[] = $this->Html->link(
                            __d('me_cms', 'Preview'),
                            ['_name' => 'pagesPreview', $page->slug],
                            ['icon' => 'external-link', 'target' => '_blank']
                        );
                    }

                    echo $this->Html->ul($actions, ['class' => 'actions']);
                    ?>
                </td>
                <td class="min-width text-center">
                    <?= $this->Html->link(
                        $page->category->title,
                        ['?' => ['category' => $page->category->id]],
                        ['title' => __d('me_cms', 'View items that belong to this category')]
                    ) ?>
                </td>
                <td class="min-width text-center">
                    <?php
                    switch ($page->priority) {
                        case '1':
                            $priority = 1;
                            $class = 'priority-verylow';
                            $tooltip = __d('me_cms', 'Very low');
                            break;
                        case '2':
                            $priority = 2;
                            $class = 'priority-low';
                            $tooltip = __d('me_cms', 'Low');
                            break;
                        case '4':
                            $priority = 4;
                            $class = 'priority-high';
                            $tooltip = __d('me_cms', 'High');
                            break;
                        case '5':
                            $priority = 5;
                            $class = 'priority-veryhigh';
                            $tooltip = __d('me_cms', 'Very high');
                            break;
                        default:
                            $priority = 3;
                            $class = 'priority-normal';
                            $tooltip = __d('me_cms', 'Normal');
                            break;
                    }

                    echo $this->Html->badge($priority, compact('class', 'tooltip'));
                    ?>
                </td>
                <td class="min-width text-center">
                    <div class="hidden-xs">
                        <?= $page->created->i18nFormat(getConfigOrFail('main.datetime.long')) ?>
                    </div>
                    <div class="visible-xs">
                        <div><?= $page->created->i18nFormat(getConfigOrFail('main.date.short')) ?></div>
                        <div><?= $page->created->i18nFormat(getConfigOrFail('main.time.short')) ?></div>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?= $this->element('MeTools.paginator') ?>