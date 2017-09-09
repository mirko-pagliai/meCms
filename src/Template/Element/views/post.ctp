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
?>

<div class="post-container content-container mb-4">
    <div class="content-header mb-3 pl-3">
        <?php if (getConfig('post.category') && $post->category->title && $post->category->slug) : ?>
            <h5 class="content-category mb-1">
                <?= $this->Html->link($post->category->title, ['_name' => 'postsCategory', $post->category->slug]) ?>
            </h5>
        <?php endif; ?>

        <h2 class="content-title mb-1">
            <?= $this->Html->link($post->title, ['_name' => 'post', $post->slug]) ?>
        </h2>

        <?php if ($post->subtitle) : ?>
            <h4 class="content-subtitle mb-1">
                <?= $this->Html->link($post->subtitle, ['_name' => 'post', $post->slug]) ?>
            </h4>
        <?php endif; ?>

        <div class="content-info mt-2 text-muted">
            <?php
            if (getConfig('post.author')) {
                echo $this->Html->div(
                    'content-author',
                    __d('me_cms', 'Posted by {0}', $post->user->full_name),
                    ['icon' => 'user']
                );
            }

            if (getConfig('post.created')) {
                echo $this->Html->div('content-date', __d(
                    'me_cms',
                    'Posted on {0}',
                    $post->created->i18nFormat()
                ), ['icon' => 'clock-o']);
            }
            ?>
        </div>
    </div>

    <div class="content-text text-justify">
        <?php
        //Executes BBCode on the text
        $text = $this->BBCode->parser($post->text);

        //Truncates the text if the "<!-- read-more -->" tag is present
        $strpos = strpos($text, '<!-- read-more -->');

        if (!$this->request->isAction(['view', 'preview']) && $strpos) {
            echo $truncatedText = $this->Text->truncate($text, $strpos, [
                'ellipsis' => false,
                'exact' => true,
                'html' => false,
            ]);
        //Truncates the text if requested by the configuration
        } elseif (!$this->request->isAction(['view', 'preview'])) {
            $truncatedText = $this->Text->truncate($text, getConfigOrFail('default.truncate_to'), [
                'exact' => false,
                'html' => true,
            ]);

            echo $truncatedText;
        } else {
            echo $text;
        }
        ?>
    </div>

    <?php if (getConfig('post.tags') && $post->tags) : ?>
        <div class="content-tags mt-2">
            <?php foreach ($post->tags as $tag) : ?>
                <?= $this->Html->link($tag->tag, ['_name' => 'postsTag', $tag->slug], ['icon' => 'tags']) ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="content-buttons mt-2 text-right">
        <?php
        //If it was requested to truncate the text and that has been
        //truncated, it shows the "Read more" link
        if (!empty($truncatedText) && $truncatedText !== $post->text) {
            echo $this->Html->button(
                __d('me_cms', 'Read more'),
                ['_name' => 'post', $post->slug],
                ['class' => ' readmore']
            );
        }
        ?>
    </div>

    <?php
    if (getConfig('post.shareaholic') && $this->request->isAction('view', 'Posts') && !$this->request->isAjax()) {
        echo $this->Html->shareaholic(getConfigOrFail('shareaholic.app_id'));
    }
    ?>
</div>