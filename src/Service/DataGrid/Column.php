<?php

/*
 * This file is part of the DataGridBundle.
 *
 * (c) Abhoryo <abhoryo@free.fr>
 * (c) Stanislav Turza
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Service\DataGrid;

use APY\DataGridBundle\Grid\Column as BaseGridColumn;

use APY\DataGridBundle\Grid\Filter;
use APY\DataGridBundle\Grid\Row;
use Doctrine\Common\Version as DoctrineVersion;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

abstract class Column extends BaseGridColumn
{
  
    

    public function __initialize(array $params)
    {
        parent::__initialize($params);
       
        $this->setOperatorsVisible($this->getParam('operatorsVisible', false));
    }

  
}
