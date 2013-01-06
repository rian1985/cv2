<?php
namespace CV\Veiculos;

class Moto extends \CV\Veiculo
{
	
	private static $keys = array(
		'cod_veiculos_moto', 'versao', 'quilometros', 'freios', 'tipo_motor',
		'partida', 'cor', 'alarme', 'chave_veiculo'
	);
	
	public function save()
	{
		$table = \ORM::for_table('cv2_veiculos_motos');
		if ($this->cod_veiculos_moto)
			$veiculo = $table->where('cod_veiculos_moto', $this->cod_veiculos_moto)
							->find_one();
		else
			$veiculo = $table->create($this->data);
		
		foreach (self::$keys as $key) {
			$veiculo->$key = $this->$key;
			unset($this->orm->$key);
		}
		
		parent::save();
		
		$veiculo->chave_veiculo = $this->cod_veiculo;
		
		var_dump($veiculo);
		$veiculo->save();
	}
	
	public function loadExtraData()
	{
		if ($this->cod_veiculo) {
			$veiculo = \ORM::for_table('cv2_veiculos_motos')
					->where('chave_veiculo', $this->cod_veiculo)
					->find_one();
			
			foreach (self::$keys as $key)
				$this->$key = $veiculo->$key;
		}
	}
}