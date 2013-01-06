<?php
// Antes de cadastrar anúncios, o usuário deve ter definido localizações
$localizacaoCheck = function() use($app) {
	if ($_SESSION['usuario']->vendedor()->find_one()->localizacoes()->count() < 1)
		$app->redirect($app->urlFor('painel/localizacao/adicionar'));
};

$app->post('/painel/adicionarfoto', $authCheck, function() use($app) {

	$file = $_FILES['file'];
	$cod_vendedor = $_SESSION['usuario']->vendedor()->find_one()->cod_vendedor;

	$dstPath = __ROOT_DIR__ . str_replace('/', DIRECTORY_SEPARATOR, '/arquivos/' . $cod_vendedor);
	if (!is_dir($dstPath))
		mkdir($dstPath);

	$dstPath .= DIRECTORY_SEPARATOR . 'fotos';
	if (!is_dir($dstPath))
		mkdir($dstPath);

	do {
		$uniqid = md5(uniqid()) . '.' . strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
	} while(is_file(DIRECTORY_SEPARATOR . $uniqid));

	$dstPath .= DIRECTORY_SEPARATOR . $uniqid;

	move_uploaded_file($file['tmp_name'], $dstPath);

	$pathinfo = pathinfo($dstPath);

	$imagem = imagecreatefromstring(file_get_contents($dstPath));
	$width   = imagesx($imagem);
	$height   = imagesy($imagem);

	foreach (array('_P' => 128, '_G' => 450) as $sufix => $largura)
	{
		$scale  = @min($largura/$width, 1);
		$largura = @floor($scale*$width);
		$altura = @floor($scale*$height);

		$novaImagem = imagecreatetruecolor($largura, $altura);
		imagecopyresampled($novaImagem, $imagem, 0, 0, 0, 0, $largura, $altura, $width, $height);

		$path = $pathinfo['dirname'] . DIRECTORY_SEPARATOR . $pathinfo['filename'] . $sufix . '.' . $pathinfo['extension'];
		switch (strtolower($pathinfo['extension'])) {

			case 'jpg':
				imagejpeg($novaImagem, $path, 100);
				break;
					
			case 'png':
				imagepng($novaImagem, $path, 0);
				break;
					
			case 'gif':
				imagegif($novaImagem, $path);
				break;
		}
		imagedestroy($novaImagem);
	}

	imagedestroy($imagem);

	$app->contentType('application/json; charset=UTF-8');

	echo json_encode(array(
			'name' => $pathinfo['filename'],
			'extension' => $pathinfo['extension']
	));

})->name('painel/adicionarfoto');

$app->get('/painel/removerfoto/:filename/:extension', $authCheck, function($filename, $extension) use($app) {

	$filename = preg_replace('/[^A-Za-z0-9_]/', '', $filename);
	$extension = preg_replace('/[^A-Za-z0-9_]/', '', $extension);

	$cod_vendedor = $_SESSION['usuario']->vendedor()->find_one()->cod_vendedor;

	$dstPath = __ROOT_DIR__ . str_replace('/', DIRECTORY_SEPARATOR, '/arquivos/' . $cod_vendedor)
	. DIRECTORY_SEPARATOR . 'fotos' . DIRECTORY_SEPARATOR;

	@unlink($dstPath . $filename . '.' . $extension);

	foreach (array('_P' => 128, '_G' => 450) as $sufix => $largura)
		@unlink($dstPath . $filename . $sufix . '.' . $extension);

	$app->contentType('application/json; charset=UTF-8');

	echo json_encode(array('status' => 'ok'));

})->name('painel/removerfoto');

$app->get('/painel/anuncios(/:tipo_veiculo(/:pagina))', $authCheck, function($tipo_veiculo = null, $pagina = 1) use($app) {
	
	if (isset($tipo_veiculo)) {
		$tipo_veiculo = Model::factory('CV\TipoDeVeiculo')
			->where_equal('codigo_plural', $tipo_veiculo)
			->find_one();
		$veiculos = $tipo_veiculo->veiculos()
					->where_equal('chave_vendedor', $_SESSION['usuario']->vendedor()->find_one()->cod_vendedor);
	}
	else
		$veiculos = $_SESSION['usuario']->vendedor()->find_one()->veiculos();
	
	$veiculosCount = clone $veiculos;
	$veiculos = $veiculos->order_by_desc('data_cadastro')->offset(($pagina - 1) * 30)->limit(30)->find_many();
	
	$veiculosCount = $veiculosCount->count();
	$paginas = ceil($veiculosCount / 30.0);
	
	if ($paginas > 1) {
	
		$criarUrl = function($pagina) use($app) {
			return $app->urlFor('painel/anuncios', array('tipo_veiculo' => $tipo_veiculo, 'pagina' => $pagina));
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
	
	$tiposDeVeiculos = Model::factory('CV\TipoDeVeiculo')->where('ativo', 1)->find_many(); 
	
	$app->render('painel/anuncios/listar.twig', array(
		'veiculos' => $veiculos,
		'tipos_de_veiculos' => $tiposDeVeiculos,
		'paginacao' => $paginacao
	));
	
})->name('painel/anuncios');

$app->get('/painel/anuncio/adicionar/:tipo_veiculo', $authCheck, $localizacaoCheck, function($tipo_veiculo) use($app) {
	$tipo_veiculo = Model::factory('CV\TipoDeVeiculo')->where_equal('codigo', $tipo_veiculo)->find_one();
	$veiculo = $tipo_veiculo->criarVeiculo();
	$veiculo->chave_vendedor = $_SESSION['usuario']->chave_vendedor;
	
	$app->render('painel/anuncios/form-' . $tipo_veiculo->codigo . '.twig', array(
		'veiculo' => $veiculo,
		'tipo_veiculo' => $tipo_veiculo
	));
	
})->name('painel/anuncio/adicionar');

$app->get('/painel/anuncio/editar/:id', $authCheck, $localizacaoCheck, function($id) use($app) {
	$veiculo = Model::factory('CV\Veiculo')->find_one($id);
	$tipo_veiculo = $veiculo->tipo()->find_one();
	$veiculo = $tipo_veiculo->veiculo($id);
	
	if ($veiculo->chave_vendedor != $_SESSION['usuario']->chave_vendedor)
		$app->halt(403);

	$app->render('painel/anuncios/form-' . $tipo_veiculo->codigo . '.twig', array(
		'veiculo' => $veiculo,
		'tipo_veiculo' => $tipo_veiculo
	));

})->name('painel/anuncio/editar');

$save = function($veiculo, $tipo_veiculo) use($app) {
	#var_dump($_POST);
	
	$triplo_estado = array(
		'null' => null,
		'true' => true,
		'false' => false
	);
	
	$request = $app->request();
	
	$descricao = trim($request->post('descricao'));
	
	$foto_1 = trim($request->post('foto_1'));
	if (!$foto_1)
		$foto_1 = null;
	$foto_2 = trim($request->post('foto_2'));
	if (!$foto_2)
		$foto_2 = null;
	$foto_3 = trim($request->post('foto_3'));
	if (!$foto_3)
		$foto_3 = null;
	$foto_4 = trim($request->post('foto_4'));
	if (!$foto_4)
		$foto_4 = null;
	$foto_5 = trim($request->post('foto_5'));
	if (!$foto_5)
		$foto_5 = null;
	$foto_6 = trim($request->post('foto_6'));
	if (!$foto_6)
		$foto_6 = null;
	
	$valor = explode(',', trim($request->post('valor')));
	if (!$valor[0])
		$valor = null;
	else {
		if (strlen($valor[0]) > 20)
			$valor[0] = str_repeat('9', 20);
		if (isset($valor[1]))
			$valor[1] = str_pad($valor[1], 2, '0', STR_PAD_RIGHT);
		else
			$valor[1] = '00';
		$valor = $valor[0] . '.' . $valor[1];
	}
	$valor_promocional = explode(',', trim($request->post('valor_promocional')));
	if (!$valor_promocional[0])
		$valor_promocional = null;
	else {
		if (strlen($valor_promocional[0]) > 20)
			$valor_promocional[0] = str_repeat('9', 20);
		if (isset($valor_promocional[1]))
			$valor_promocional[1] = str_pad($valor_promocional[1], 2, '0', STR_PAD_RIGHT);
		else
			$valor_promocional[1] = '00';
		$valor = $valor_promocional[0] . '.' . $valor_promocional[1];
	}
	
	$itens = $request->post('itens');
	
	$observacoes = trim($request->post('observacoes'));
	if (!$observacoes)
		$observacoes = null;
	
	$ano = $request->post('ano') == 'null' ? null : $request->post('ano');
	
	$unico_dono = isset($triplo_estado[$request->post('unico_dono')]) ? $triplo_estado[$request->post('unico_dono')] : null;
	$novo = isset($triplo_estado[$request->post('novo')]) ? $triplo_estado[$request->post('novo')] : null;
	
	$chave_localizacao = $request->post('chave_localizacao') == 'null' ? null : intval($request->post('chave_localizacao'));
	$chave_marca = $request->post('chave_marca') == 'null' ? null : intval($request->post('chave_marca'));
	
	$erros = array();
	
	if (!$descricao)
		$erros[] = 'Você deve fornecer uma descrição do veículo.';
	if (!$valor)
		$erros[] = 'Você deve fornecer um valor para o veículo.';
	if (!$chave_localizacao)
		$erros[] = 'Você deve fornecer uma localização para o veículo.';
	elseif (!Model::factory('CV\Localizacao')->find_one($chave_localizacao))
		$erros[] = 'Você deve fornecer uma localização válida para o veículo.';
	if ($chave_marca && !Model::factory('CV\Marca')->find_one($chave_marca))
		$erros[] = 'Você deve fornecer uma marca válida para o veículo.';
	
	$veiculo->descricao = $descricao;
	$veiculo->foto_1 = $foto_1;
	$veiculo->foto_2 = $foto_2;
	$veiculo->foto_3 = $foto_3;
	$veiculo->foto_4 = $foto_4;
	$veiculo->foto_5 = $foto_5;
	$veiculo->foto_6 = $foto_6;
	$veiculo->valor = $valor;
	$veiculo->valor_promocional = $valor_promocional;
	$veiculo->itens = $itens;
	$veiculo->observacoes = $observacoes;
	$veiculo->ano = $ano;
	$veiculo->unico_dono = $unico_dono;
	$veiculo->novo = $novo;
	$veiculo->chave_localizacao = $chave_localizacao;
	$veiculo->chave_marca = $chave_marca;
	
	switch ($tipo_veiculo->codigo) {
		case 'carro':
			$modelo = trim($request->post('modelo'));
			$ano = $request->post('ano') == 'null' ? null : intval($request->post('ano'));
			$portas = $request->post('portas') == 'null' ? null : intval($request->post('portas'));
			$quilometros = trim($request->post('quilometros'));
			$combustivel = $request->post('combustivel') == 'null' ? null : trim($request->post('combustivel'));
			$direcao = $request->post('direcao') == 'null' ? null : trim($request->post('direcao'));
			$transmissao = $request->post('transmissao') == 'null' ? null : trim($request->post('transmissao'));
			$cor = $request->post('cor') == 'null' ? null : trim($request->post('cor'));
			
			$veiculo->modelo = $modelo;
			$veiculo->ano = $ano;
			$veiculo->portas = $portas;
			$veiculo->quilometros = $quilometros;
			$veiculo->combustivel = $combustivel;
			$veiculo->direcao = $direcao;
			$veiculo->transmissao = $transmissao;
			$veiculo->cor = $cor;
			break;
			
		case 'moto':
			$modelo = trim($request->post('modelo'));
			$ano = $request->post('ano') == 'null' ? null : intval($request->post('ano'));
			$quilometros = trim($request->post('quilometros'));
			$freios = $request->post('freios') == 'null' ? null : trim($request->post('freios'));
			$tipo_motor = $request->post('tipo_motor') == 'null' ? null : trim($request->post('tipo_motor'));
			$partida = $request->post('partida') == 'null' ? null : trim($request->post('partida'));
			$cor = $request->post('cor') == 'null' ? null : trim($request->post('cor'));
			$alarme = $request->post('alarme') == 'null' ? null : trim($request->post('alarme'));
			
			$veiculo->modelo = $modelo;
			$veiculo->ano = $ano;
			$veiculo->quilometros = $quilometros;
			$veiculo->freios = $freios;
			$veiculo->tipo_motor = $tipo_motor;
			$veiculo->partida = $partida;
			$veiculo->cor = $cor;
			$veiculo->alarme = $alarme;
			break;
		
		case 'caminhao':
			$transmissao = trim($request->post('transmissao'));
			$ano = $request->post('ano') == 'null' ? null : intval($request->post('ano'));
			$direcao = $request->post('direcao') == 'null' ? null : trim($request->post('direcao'));
			$quilometros = trim($request->post('quilometros'));
			$tracao = $request->post('tracao') == 'null' ? null : trim($request->post('tracao'));
			$capacidade_tracao = trim($request->post('capacidade_tracao'));
			$capacidade_carga = trim($request->post('capacidade_carga'));
			$potencia = trim($request->post('potencia'));
			$medidas = trim($request->post('medidas'));
			$marca_motor = trim($request->post('marca_motor'));
			$freios = $request->post('freios') == 'null' ? null : trim($request->post('freios'));
			$cor = $request->post('cor') == 'null' ? null : trim($request->post('cor'));
			
			$veiculo->transmissao = $transmissao;
			$veiculo->ano = $ano;
			$veiculo->direcao = $direcao;
			$veiculo->quilometros = $quilometros;
			$veiculo->tracao = $tracao;
			$veiculo->capacidade_tracao = $capacidade_tracao;
			$veiculo->capacidade_carga = $capacidade_carga;
			$veiculo->potencia = $potencia;
			$veiculo->medidas = $medidas;
			$veiculo->marca_motor = $marca_motor;
			$veiculo->freios = $freios;
			$veiculo->cor = $cor;
			break;
		
		case 'nautico':
			$ano = $request->post('ano') == 'null' ? null : intval($request->post('ano'));
			$calado = trim($request->post('calado'));
			$comprimento = trim($request->post('comprimento'));
			$marca = trim($request->post('marca'));
			$modelo = trim($request->post('modelo'));
			$pontal = trim($request->post('pontal'));
			$banheiro = $request->post('banheiro') == 'null' ? null : trim($request->post('banheiro'));
			$camarotes = $request->post('camarotes') == 'null' ? null : trim($request->post('camarotes'));
			$quantidade_motores = $request->post('quantidade_motores') == 'null' ? null : trim($request->post('quantidade_motores'));
			$quantidade_pessoas = $request->post('quantidade_pessoas') == 'null' ? null : trim($request->post('quantidade_pessoas'));
			$capacidade_tanque = trim($request->post('capacidade_tanque'));
			$combustivel = $request->post('combustivel') == 'null' ? null : trim($request->post('combustivel'));
			$horas_uso = trim($request->post('horas_uso'));
			$marca_motor = $request->post('marca_motor') == 'null' ? null : trim($request->post('marca_motor'));
			$material = $request->post('material') == 'null' ? null : trim($request->post('material'));
			$potencia = trim($request->post('potencia'));
			$motor = $request->post('motor') == 'null' ? null : trim($request->post('motor'));
			$ano_motor = $request->post('ano_motor') == 'null' ? null : trim($request->post('ano_motor'));
			
			$veiculo->ano = $ano;
			$veiculo->calado = $calado;
			$veiculo->comprimento = $comprimento;
			$veiculo->marca = $marca;
			$veiculo->modelo = $modelo;
			$veiculo->pontal = $pontal;
			$veiculo->banheiro = $banheiro;
			$veiculo->camarotes = $camarotes;
			$veiculo->quantidade_motores = $quantidade_motores;
			$veiculo->quantidade_pessoas = $quantidade_pessoas;
			$veiculo->capacidade_tanque = $capacidade_tanque;
			$veiculo->combustivel = $combustivel;
			$veiculo->horas_uso = $horas_uso;
			$veiculo->marca_motor = $marca_motor;
			$veiculo->material = $material;
			$veiculo->potencia = $potencia;
			$veiculo->motor = $motor;
			$veiculo->ano_motor = $ano_motor;
			break;
	}
	
	if (count($erros) == 0)
		$veiculo->save();
	
	return $erros;
};

$app->post('/painel/anuncio/adicionar/:tipo_veiculo', $authCheck, $localizacaoCheck, function($tipo_veiculo) use($app, $save) {
	$tipo_veiculo = Model::factory('CV\TipoDeVeiculo')->where_equal('codigo', $tipo_veiculo)->find_one();
	$veiculo = $tipo_veiculo->criarVeiculo();
	
	$veiculo->chave_vendedor = $_SESSION['usuario']->chave_vendedor;
	
	$erros = $save($veiculo, $tipo_veiculo);
	if (count($erros) > 0) {
		$app->flashNow('erros', $erros);
		$app->render('painel/anuncios/form-' . $tipo_veiculo->codigo . '.twig', array(
				'veiculo' => $veiculo,
				'tipo_veiculo' => $tipo_veiculo
		));
	}
	else {
		$app->flash('sucesso', true);
		$app->redirect($app->urlFor('painel/anuncio/editar', array('id' => $veiculo->cod_veiculo)));
	}
});

$app->post('/painel/anuncio/editar/:id', $authCheck, $localizacaoCheck, function($id) use($app, $save) {
	$veiculo = Model::factory('CV\Veiculo')->find_one($id);
	$tipo_veiculo = $veiculo->tipo()->find_one();
	$veiculo = $tipo_veiculo->veiculo($id);
	
	if ($veiculo->chave_vendedor != $_SESSION['usuario']->chave_vendedor)
		$app->halt(403);
	
	$erros = $save($veiculo, $tipo_veiculo);
	
	if (count($erros) > 0) {
		$app->flashNow('erros', $erros);
		$app->render('painel/anuncios/form-' . $tipo_veiculo->codigo . '.twig', array(
				'veiculo' => $veiculo,
				'tipo_veiculo' => $tipo_veiculo
		));
	}
	else {
		$app->flash('sucesso', true);
		$app->redirect($app->urlFor('painel/anuncio/editar', array('id' => $veiculo->cod_veiculo)));
	}
	
});