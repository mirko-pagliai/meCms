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
 * @see         http://api.cakephp.org/3.7/class-Cake.Mailer.Mailer.html Mailer
 */
namespace MeCms\Mailer;

use Cake\Mailer\Email;
use Cake\Mailer\Mailer as CakeMailer;

/**
 * Mailer classes let you encapsulate related Email logic into a reusable
 */
class Mailer extends CakeMailer
{
    /**
     * Gets the Email instance
     * @return \Cake\Mailer\Email Email instance
     * @since 2.15.3
     */
    public function getEmailInstance()
    {
        return $this->_email;
    }

    /**
     * Constructor
     * @param \Cake\Mailer\Email|null $email Email instance
     * @uses getEmailInstance()
     */
    public function __construct(?Email $email = null)
    {
        parent::__construct($email);

        $email = $this->getEmailInstance();
        $email->viewBuilder()->setHelpers(['MeTools.Html'], false);
        $email->setFrom(getConfigOrFail('email.webmaster'), getConfigOrFail('main.title'))
            ->setSender(getConfigOrFail('email.webmaster'), getConfigOrFail('main.title'))
            ->setEmailFormat('html');
    }
}
