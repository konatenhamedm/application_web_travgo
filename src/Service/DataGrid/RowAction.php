<?php

namespace App\Service\DataGrid;

use APY\DataGridBundle\Grid\Action\RowAction as DataGridRowAction;

class RowAction extends DataGridRowAction
{

    /**
     * @var array
     */
    private $iconsMap = [

        'commentaire'      => ['icon' => 'fa fa-comments', 'class' => 'bg-grey-salsa bg-font-grey-salsa'],
        'historique'       => ['icon' => 'fa fa-calendar', 'class' => 'dark'],
        'aperçu'           => ['icon' => 'fa fa-eye', 'class' => 'bg-green-haze bg-font-green-haze'],
        'ajouter'          => ['icon' => 'fa fa-plus', 'class' => 'bg-green-sharp bg-font-green-sharp'],
        'nouveau'          => ['icon' => 'fa fa-plus', 'class' => 'bg-blue-steel bg-font-blue-steel'],
        'nouvelle'         => ['icon' => 'fa fa-plus', 'class' => 'bg-blue-steel bg-font-blue-steel'],
        'valider'          => ['icon' => 'fa fa-check', 'class' => 'bg-grey-salsa bg-font-grey-salsa'],
        'imprimer'         => ['icon' => 'fa fa-print', 'class' => 'bg-blue bg-font-blue'],
        'traiter'          => ['icon' => 'fa fa-check', 'class' => 'bg-green-sharp bg-font-green-sharp'],
        'fichier'          => ['icon' => 'fa fa-file-pdf-o', 'class' => 'bg-blue bg-font-blue'],
        'email'            => ['icon' => 'fa fa-at', 'class' => 'bg-grey-salsa bg-font-grey-salsa'],
        //
        'voir'             => ['icon' => 'flaticon2-checking', 'class' => 'btn-icon-success btn-hover-success'],
        'aperçu'           => ['icon' => 'flaticon2-checking', 'class' => 'btn-icon-success btn-hover-success'],
        'détails'          => ['icon' => 'flaticon2-checking', 'class' => 'btn-light-warning'],

        'modifier'         => ['icon' => 'flaticon2-pen', 'class' => 'btn-light-primary'],

        'supprimer'        => ['icon' => 'flaticon2-rubbish-bin', 'class' => 'btn-icon-danger btn-hover-danger'],

        'suivi'            => ['icon' => 'flaticon2-folder', 'class' => 'btn-icon-warning btn-hover-warning'],
    ];


    /**
     * @param $action
     */
    public function getIcon($action)
    {
        $action = mb_strtolower($action);

        if (!isset($this->iconsMap[$action])) {
            $action = explode(' ', $action)[0];
        }

        return isset($this->iconsMap[$action]) ? $this->iconsMap[$action] : false ;

    }

}

