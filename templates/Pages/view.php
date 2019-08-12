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
$this->extend('/common/view');
$this->assign('title', $page->title);

/**
 * Userbar
 */
$class = 'badge badge-warning';
if (!$page->active) {
    $this->userbar($this->Html->span(I18N_DRAFT, compact('class')));
}
if ($page->created->isFuture()) {
    $this->userbar($this->Html->span(I18N_SCHEDULED, compact('class')));
}
$this->userbar($this->Html->link(
    __d('me_cms', 'Edit page'),
    ['action' => 'edit', $page->id, 'prefix' => ADMIN_PREFIX],
    ['class' => 'nav-link', 'icon' => 'pencil-alt', 'target' => '_blank']
));
$this->userbar($this->Form->postLink(
    __d('me_cms', 'Delete page'),
    ['action' => 'delete', $page->id, 'prefix' => ADMIN_PREFIX],
    ['class' => 'nav-link text-danger', 'icon' => 'trash-alt', 'confirm' => I18N_SURE_TO_DELETE, 'target' => '_blank']
));

/**
 * Breadcrumb
 */
if (getConfig('page.category') && $page->has('category') && $page->category->has(['slug', 'title'])) {
    $this->Breadcrumbs->add($page->category->title, ['_name' => 'pagesCategory', $page->category->slug]);
}
$this->Breadcrumbs->add($page->title, ['_name' => 'page', $page->slug]);

/**
 * Meta tags
 */
if ($this->getRequest()->isAction('view', 'Pages')) {
    $this->Html->meta(['content' => 'article', 'property' => 'og:type']);

    if ($page->has('modified')) {
        $this->Html->meta(['content' => $page->modified->toUnixString(), 'property' => 'og:updated_time']);
    }

    if ($page->has('preview')) {
        foreach ($page->preview as $preview) {
            $this->Html->meta(['href' => $preview->url, 'rel' => 'image_src']);
            $this->Html->meta(['content' => $preview->url, 'property' => 'og:image']);
            $this->Html->meta(['content' => $preview->width, 'property' => 'og:image:width']);
            $this->Html->meta(['content' => $preview->height, 'property' => 'og:image:height']);
        }
    }

    if ($page->has('text')) {
        $this->Html->meta([
            'content' => $this->Text->truncate($page->plain_text, 100, ['html' => true]),
            'property' => 'og:description',
        ]);
    }
}

echo $this->element('views/page', compact('page'));