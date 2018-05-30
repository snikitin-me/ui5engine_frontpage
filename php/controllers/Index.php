<?php

use Symfony\Component\HttpFoundation\Request;

$c = $app['controllers_factory'];

$c->get('/', function() use ($app) {

	return $app['twig']->render('layout.twig', array(
	  	'mainblock' => 'index.html'
  	));
});

return $c;