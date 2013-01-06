<?php
$app->get('/', function() use($app) {
	
	$app->render('portal/base.twig');
	
})->name('portal/index');