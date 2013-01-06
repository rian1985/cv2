<?php

use CV\Usuario;

$app->view()->appendData(array(
	'app' => $app
));

// Verifica se um usuário está logado
$authCheck = function() use($app) {
	if (empty($_SESSION['usuario']) || !($_SESSION['usuario'] instanceof Usuario))
		$app->redirect($app->urlFor('painel/login'));
	
	$app->view()->appendData(array(
		'this_path' => $app->request()->getPathInfo(),
		'referer_path' => parse_url($app->request()->getReferrer(), PHP_URL_PATH),
		'usuario' => $_SESSION['usuario'],
		'vendedor' => $_SESSION['usuario']->vendedor()->find_one()
	));
};

// Verifica se um usuário não está logado
$nonAuthCheck = function() use($app) {
	if (isset($_SESSION['usuario']) && $_SESSION['usuario'] instanceof Usuario)
		$app->redirect($app->urlFor('painel'));
};

// GET painel
$getPainelIndex = function() use($app) {
	$app->render('painel/base.twig');
};

// GET painel/login
$getPainelLogin = function() use($app) {
	
	$app->render('painel/login.twig');
	
};

// GET painel/logout
$getPainelLogout = function() use($app) {
	
	unset($_SESSION['usuario']);
	$app->redirect($app->urlFor('painel/login'));
	
};

// POST painel/login
$postPainelLogin = function() use($app) {
	
	$login = $app->request()->post('usuario');
	$senha = $app->request()->post('senha');
	
	if (empty($login) || empty($senha)) {
		$app->flash('erro', 'Você deve fornecer login e senha.');
		$app->redirect($app->urlFor('painel/login'));
	}
	
	$usuario = Model::factory('CV\Usuario')
				->where_equal('usuario', $login)
				->where_equal('senha', $senha)
				->find_one();
	
	if (!$usuario) {
		$app->flash('erro', 'O login e/ou a senha devem estar incorretos.');
		$app->redirect($app->urlFor('painel/login'));
	}
	
	$_SESSION['usuario'] = $usuario;
	$app->redirect($app->urlFor('painel'));
};

$app->get('/painel(/)', $authCheck, $getPainelIndex)->name('painel');
$app->get('/painel/login', $nonAuthCheck, $getPainelLogin)->name('painel/login');
$app->get('/painel/logout', $authCheck, $getPainelLogout)->name('painel/logout');
$app->post('/painel/login', $nonAuthCheck, $postPainelLogin);

require __DIR__ . '/painel/usuarios.php';
require __DIR__ . '/painel/anuncios.php';