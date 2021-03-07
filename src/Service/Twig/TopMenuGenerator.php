<?php

namespace App\Service\Twig;

//use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
//use Twig\Environment;

class TopMenuGenerator
{
	private $requestStack;
	//private $twig;

	public function __construct(RequestStack $requestStack/*, Environment $twig*/)
	{
		$this->requestStack = $requestStack;
		//$this->twig = $twig;
	}

	public function getVariable()
	{
		$request = $this->requestStack->getCurrentRequest();
		$path = $request->getPathInfo();
		$prefix = explode('/', $path)[1];

		return (($prefix !== '')? $prefix: 'default');
	}
}
