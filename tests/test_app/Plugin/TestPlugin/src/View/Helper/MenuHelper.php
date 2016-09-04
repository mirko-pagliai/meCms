<?php
/**
 * This file is part of MeCms.
 *
 * MeCms is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * MeCms is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with MeCms.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author      Mirko Pagliai <mirko.pagliai@gmail.com>
 * @copyright   Copyright (c) 2016, Mirko Pagliai for Nova Atlantis Ltd
 * @license     http://www.gnu.org/licenses/agpl.txt AGPL License
 * @link        http://git.novatlantis.it Nova Atlantis Ltd
 */
namespace TestPlugin\View\Helper;

use Cake\View\Helper;

class MenuHelper extends Helper
{
    public $helpers = ['Html' => ['className' => 'MeTools.Html']];
    
    public function _invalidMethod()
    {
        
    }
    
    public function __otherInvalidMethod()
    {
        
    }
    
    public function articles()
    {
        $menu = [
            $this->Html->link('First link', '/'),
            $this->Html->link('Second link', '/'),
        ];
        
        return [$menu, 'First menu', ['icon' => 'home']];
    }
    
    public function other_items()
    {
        $menu = [
            $this->Html->link('Third link', '/'),
            $this->Html->link('Fourth link', '/'),
        ];
        
        return [$menu, 'Second menu', ['icon' => 'flag']];
    }
}