<?php
// GET painel/usuarios
$app->get('/painel/usuarios(/:pagina)', $authCheck, function($pagina = 1) use($app) {
	
	$usuarios = Model::factory('CV\Usuario')
		->where_equal('chave_vendedor', $_SESSION['usuario']->vendedor()->find_one()->cod_vendedor)
		->where_equal('status', 'cadastrado');
	
	$usuariosCount = clone $usuarios;
	
	$usuarios = $usuarios
					->order_by_asc('cod_usuario')
					->offset(($pagina - 1) * 30)->limit(30)
					->find_many();
	
	foreach ($usuarios as &$usuario)
		$usuario->deletavel = $_SESSION['usuario']->cod_usuario != $usuario->cod_usuario;
	
	$usuariosCount = $usuariosCount->count();
	$paginas = ceil($usuariosCount / 30.0);
	
	if ($paginas > 1) {
	
		$criarUrl = function($pagina) use($app) {
			return $app->urlFor('painel/usuarios', array('pagina' => $pagina));
		};
		
		$paginacao = array(
			'intervalo' => array(),
			'esquerda' => false,
			'direita' => false,
			'pagina_ativa' => $pagina
		);
		
		foreach (range(max(1, $pagina - 5), min($paginas, $pagina + 5)) as $n)
			$paginacao['intervalo'][] = array('numero' => $n, 'url' => $criarUrl($n));
		
		if ($pagina - 5 > 1)
			$paginacao['esquerda'] = array('numero' => max(1, $pagina - 10), 'url' => $criarUrl(max(1, $pagina - 10)));
		
		if ($pagina + 5 < $paginas)
			$paginacao['direita'] = array('numero' => min($paginas, $pagina + 10), 'url' => $criarUrl(min($paginas, $pagina + 10)));
	}
	else
		$paginacao = false;
	
	$app->render('painel/usuarios/listar.twig', array(
		'usuarios' => $usuarios,
		'paginacao' => $paginacao,
	));
	
})->name('painel/usuarios');

// GET painel/usuario/adicionar
$app->get('/painel/usuario/adicionar', $authCheck, function() use($app) {
	
	$usuario = Model::factory('CV\Usuario')->create();
	
	if ($app->request()->post('nome'))
		$usuario->nome = $app->request()->post('nome');
	if ($app->request()->post('login'))
		$usuario->login = $app->request()->post('login');
	
	$app->render('painel/usuarios/adicionar.twig', array('usuario' => $usuario));
	
})->name('painel/usuario/adicionar');

// GET painel/usuario/editar
$app->get('/painel/usuario/editar/:id', $authCheck, function($id) use($app) {

	$usuario = Model::factory('CV\Usuario')->find_one($id);
	
	$app->render('painel/usuarios/editar.twig', array('usuario' => $usuario));
	
})->name('painel/usuario/editar');

// POST painel/usuario/salvar
$app->post('/painel/usuario/salvar', $authCheck, function() use($app) {
	
	$insert = !$app->request()->post('id');
	
	$erros = array();
	
	if ($insert) {
		
		$usuario = Model::factory('CV\Usuario')->create();
		 
		if (!$app->request()->post('nome'))
			$erros[] = 'Você deve definir um nome para o usuário.';
		
		if (!$app->request()->post('login'))
			$erros[] = 'Você deve definir um login para o usuário.';
		else {
			if (!preg_match('/^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i', $app->request()->post('login')))
				$erros[] = 'O login deve ser um endereço de e-mail válido.';
				
			if (Model::factory('CV\Usuario')->where_equal('usuario', $app->request()->post('login'))->count() != 0)
				$erros[] = 'O login já está em uso. Escolha outro.';
		}
		
		if (!$app->request()->post('senha'))
			$erros[] = 'Você deve definir uma senha para o usuário.';
		elseif ($app->request()->post('senha') != $app->request()->post('confirmar-senha'))
			$erros[] = 'A senha não coincide com a senha de confirmação.';
	}
	else {
		
		$usuario = Model::factory('CV\Usuario')->find_one($app->request()->post('id'));
		
		if (!$app->request()->post('nome'))
			$erros[] = 'Você deve definir um nome para o usuário.';
		
		if ($app->request()->post('senha-atual') && $app->request()->post('senha')) {
			if ($app->request()->post('senha-atual') != $usuario->senha)
				$erros[] = 'Você deve fornecer a senha atual do usuário.';
			if ($app->request()->post('senha') != $app->request()->post('confirmar-senha'))
				$erros[] = 'A senha não coincide com a senha de confirmação.';
		}
	}
	
	if (count($erros) > 0) {
		$app->flash('erros', $erros);
		$app->flash('nome', $app->request()->post('nome'));
		$app->flash('login', $app->request()->post('login'));
		
		$app->redirect($insert ?
			$app->urlFor('painel/usuario/adicionar') :
			$app->urlFor('painel/usuario/editar', array('id' => $usuario->cod_usuario))
		);
	}
	
	$usuario->nome = $app->request()->post('nome');
	
	if ($insert) {
		$usuario->usuario = $app->request()->post('login');
		$usuario->senha = $app->request()->post('senha');
		$usuario->status = 'cadastrado';
		$usuario->chave_vendedor = $_SESSION['usuario']->chave_vendedor;
	}
	else {
		if ($app->request()->post('senha-atual') && $app->request()->post('senha'))
			$usuario->senha = $app->request()->post('senha');
	}
	
	$usuario->save();
	
	if ($usuario->id == $_SESSION['usuario']->id)
		$_SESSION['usuario'] = $usuario;
	
	$app->redirect($app->urlFor('painel/usuarios'));
	
})->name('painel/usuario/salvar');

$app->get('/painel/usuario/deletar/:id', $authCheck, function($id) use($app) {
	
	$usuario = Model::factory('CV\Usuario')->find_one($id);
	
	$app->render('painel/usuarios/deletar.twig', array('usuario' => $usuario));
	
})->name('painel/usuario/deletar');

$app->get('/painel/usuario/deletar/confirmar/:id', $authCheck, function($id) use($app) {

	Model::factory('CV\Usuario')->find_one($id)->delete();

	$app->redirect($app->urlFor('painel/usuarios'));

})->name('painel/usuario/deletar/confirmar');