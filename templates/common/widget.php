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

$class = trim($this->fetch('class')) ? sprintf('widget %s', trim($this->fetch('class'))) : 'widget';
?>

<?php if (trim($this->fetch('content'))) : ?>
<div class="<?= $class ?> mb-4">
    <?php if (trim($this->fetch('title'))) : ?>
    <h4 class="widget-title"><?= trim($this->fetch('title')) ?></h4>
    <?php endif; ?>

    <div class="widget-content">
        <?= trim($this->fetch('content')) ?>

    </div>
</div>
<?php endif; ?>
