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

namespace MeCms\View\View;

use Cake\Routing\Router;
use MeCms\View\View;

/**
 * Application view class for all views, except the admin views
 */
class AppView extends View
{
    /**
     * Internal property to set the userbar elements
     * @var array
     */
    protected $userbar = [];

    /**
     * Internal method to set some blocks
     * @return void
     * @uses \MeCms\View\View::getTitleForLayout()
     */
    protected function setBlocks(): void
    {
        //Sets the "theme color" (the toolbar color for some mobile browser)
        if (getConfig('default.toolbar_color')) {
            $this->Html->meta('theme-color', getConfig('default.toolbar_color'));
        }

        //Sets the meta tag for RSS posts
        if (getConfig('default.rss_meta')) {
            $this->Html->meta(__d('me_cms', 'Latest posts'), '/posts/rss', ['type' => 'rss']);
        }

        //Sets scripts for Google Analytics
        if (getConfig('default.analytics')) {
            echo $this->Library->analytics(getConfig('default.analytics'));
        }

        //Sets scripts for Shareaholic
        if (getConfig('shareaholic.site_id')) {
            echo $this->Library->shareaholic(getConfig('shareaholic.site_id'));
        }

        //Sets some Facebook's tags
        $this->Html->meta(['content' => $this->getTitleForLayout(), 'property' => 'og:title']);
        $this->Html->meta(['content' => Router::url(null, true), 'property' => 'og:url']);

        //Sets the app ID for Facebook
        if (getConfig('default.facebook_app_id')) {
            $this->Html->meta([
                'content' => getConfig('default.facebook_app_id'),
                'property' => 'fb:app_id',
            ]);
        }
    }

    /**
     * Initialization hook method
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();

        $this->loadHelper('MeTools.Breadcrumbs');
        $this->loadHelper('RecaptchaMailhide.Mailhide');
        $this->loadHelper('MeCms.Widget');
    }

    /**
     * Renders a layout. Returns output from _render(). Returns false on
     *  error. Several variables are created for use in layout
     * @param string $content Content to render in a view, wrapped by the
     *  surrounding layout
     * @param string|null $layout Layout name
     * @return string Rendered output
     * @uses setBlocks()
     * @uses userbar()
     */
    public function renderLayout($content, $layout = null): string
    {
        $this->plugin = 'MeCms';

        $this->setBlocks();

        //Assign the userbar
        $this->assign('userbar', implode(PHP_EOL, array_map(function ($element): string {
            return $this->Html->li($element);
        }, $this->userbar)));

        return parent::renderLayout($content, $layout);
    }

    /**
     * Adds content to the userbar
     * @param string|array $content Contents as string or an array of contents
     * @return void
     * @since 2.29.5
     */
    public function addToUserbar($content): void
    {
        $this->userbar = array_merge($this->userbar, (array)$content);
    }
}
