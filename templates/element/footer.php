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
?>
<footer id="footer">
    <?php
    $links = [
        $this->Html->link(__d('me_cms', 'Search'), ['_name' => 'postsSearch']),
        $this->Html->link(__d('me_cms', 'Cookies policy'), ['_name' => 'page', 'cookies-policy']),
        $this->Html->link(__d('me_cms', 'Feed RSS'), '/posts/rss'),
    ];

    if (getConfig('default.contact_us')) {
        $links[] = $this->Html->link(__d('me_cms', 'Contact us'), ['_name' => 'contactUs']);
    }

    echo $this->Html->ul($links);
    ?>

    <p><?= __d('me_cms', 'Powered by {0}. Copyright {1}', 'MeCms', date('Y')) ?></p>
    <p>
        <?= __d('me_cms', 'Developed by {0}', $this->Html->link('Mirko Pagliai', '//github.com/mirko-pagliai', ['target' => '_blank'])) ?>
    </p>
</footer>
