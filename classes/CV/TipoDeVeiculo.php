<?php
namespace CV;

class TipoDeVeiculo extends \Model
{
	
	public static $_table = 'cv2_veiculos_tipos';
	public static $_id_column = 'cod_tipo_veiculo';
	
	public function veiculos()
	{
		return $this->has_many('CV\Veiculo', 'chave_tipo_veiculo');
	}
	
	public function marcas()
	{
		return $this->has_many('CV\Marca', 'chave_tipo_veiculo');
	}
	
	public function criarVeiculo()
	{
		switch ($this->codigo)
		{
			case 'carro':
				$veiculo = \Model::factory('CV\Veiculos\Carro')->create(array(
					'chave_tipo_veiculo' => $this->cod_tipo_veiculo
				));
				break;
				
			case 'moto':
				$veiculo = \Model::factory('CV\Veiculos\Moto')->create(array(
					'chave_tipo_veiculo' => $this->cod_tipo_veiculo
				));
				break;
				
			case 'caminhao':
				$veiculo = \Model::factory('CV\Veiculos\Caminhao')->create(array(
					'chave_tipo_veiculo' => $this->cod_tipo_veiculo
				));
				break;
				
			case 'onibus':
				$veiculo = \Model::factory('CV\Veiculos\Onibus')->create(array(
					'chave_tipo_veiculo' => $this->cod_tipo_veiculo
				));
				break;
				
			case 'nautico':
				$veiculo = \Model::factory('CV\Veiculos\Nautico')->create(array(
					'chave_tipo_veiculo' => $this->cod_tipo_veiculo
				));
				break;
				
			case 'outro':
				$veiculo = \Model::factory('CV\Veiculos\Outro')->create(array(
					'chave_tipo_veiculo' => $this->cod_tipo_veiculo
				));
				break;
		}
			
		return $veiculo;
	}
	
	public function veiculo($id)
	{
		switch ($this->codigo)
		{
			case 'carro':
				$veiculo = \Model::factory('CV\Veiculos\Carro')->find_one($id);
				break;
	
			case 'moto':
				$veiculo = \Model::factory('CV\Veiculos\Moto')->find_one($id);
				break;
	
			case 'caminhao':
				$veiculo = \Model::factory('CV\Veiculos\Caminhao')->find_one($id);
				break;
	
			case 'onibus':
				$veiculo = \Model::factory('CV\Veiculos\Onibus')->find_one($id);
				break;
	
			case 'nautico':
				$veiculo = \Model::factory('CV\Veiculos\Nautico')->find_one($id);
				break;
	
			case 'outro':
				$veiculo = \Model::factory('CV\Veiculos\Outro')->find_one($id);
				break;
		}
		
		$veiculo->loadExtraData();
		return $veiculo;
	}
	
}