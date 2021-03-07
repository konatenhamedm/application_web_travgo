<?php

/**
 * Génération de fil d'ariane
 */
namespace App\Service;


class Breadcrumb
{

    /**
     * @var array
     */
    private $items = [];

    /**
     * @var mixed
     */
    private $templating;


    /**
     * @param \Twig_Environment $templating
     */
    public function __construct(\Twig_Environment $templating)
    {
        $this->templating = $templating;
    }


    /**
     * @param $item
     * @return mixed
     */
    public function addItem($item)
    {
        $this->items[] = $item;
        return $this;
    }


    public function addItems($items)
    {
        $this->items = $items;
    }

    /**
     * @return mixed
     */
    public function render(array $items = [])
    {
        return $this->templating->render('_services/breadcrumb.html.twig', ['items' => $this->items]);
    }

    /**
     * @return mixed
     */
    public function __toString()
    {
        return $this->render();
    }

}

