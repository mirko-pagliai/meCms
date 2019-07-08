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
$this->extend('/Common/index');
$this->assign('title', $title = __d('me_cms', 'Search posts'));

/**
 * Breadcrumb
 */
$this->Breadcrumbs->add($title, ['_name' => 'postsSearch']);

echo $this->Form->create(null, ['type' => 'get', 'url' => ['_name' => 'postsSearch']]);
echo $this->Form->control('p', [
    'button' => $this->Form->submit(__d('me_cms', 'Search'), ['class' => 'btn-primary', 'icon' => 'search']),
    'default' => $this->request->getQuery('p'),
    'label' => false,
    'placeholder' => sprintf('%s...', __d('me_cms', 'Search')),
]);
echo $this->Form->end();
?>

<?php if (!empty($pattern)) : ?>
    <div class="bg-info text-white mt-3 mb-3 p-2">
        <?= __d('me_cms', 'You have searched for: {0}', $this->Html->em($pattern)) ?>
    </div>
<?php endif; ?>

<?php if (!empty($posts)) : ?>
    <div class="as-table">
        <?php foreach ($posts as $post) : ?>
            <div class="mb-3 p-1">
                <h6>
                    <?= $this->Html->link($post->title, ['_name' => 'post', $post->slug]) ?>
                    <span class="small text-muted">
                        (<?= $post->created->i18nFormat(getConfigOrFail('main.datetime.short')) ?>)
                    </span>
                </h6>

                <div class="text-justify">
                <?php
                    //Executes BBCode on the text, strips tags, extracts
                    //  an excerpt from `$pattern` and highlights `$pattern`
                    $text = $this->Text->excerpt($post->plain_text, $pattern, 350);
                    echo $this->Text->highlight($text, $pattern);
                ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <?= $this->element('MeTools.paginator') ?>
<?php endif; ?>