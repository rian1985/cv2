<?php
namespace CV;

class Vendedor extends \Model
{

	public static $_table = 'cv2_vendedores';
	public static $_id_column = 'cod_vendedor';
	
	public function usuarios()
	{
		return $this->has_many('CV\Usuario');
	}
	
	public function veiculos()
	{
		return $this->has_many('CV\Veiculo', 'chave_vendedor');
	}
	
	public function mensagens()
	{
		return $this->has_many('CV\Mensagem', 'chave_vendedor');
	}
	
	public function localizacoes()
	{
		return $this->has_many('CV\Localizacao', 'chave_vendedor')->order_by_asc('cod_localizacao');
	}
	
	public function nomeExibido()
	{
		return in_array($this->tipo_vendedor, array('fisico', 'fisica')) ?
				$this->nome :
				$this->nome_fantasia;
	}

}