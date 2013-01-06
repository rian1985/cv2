<?php
namespace CV\Veiculos;

class Outro extends \CV\Veiculo
{
	
	private static $keys = array(
		'cod_veiculos_outro', 'tipo_veiculo', 'marca', 'modelo', 'chave_veiculo'
	);
	
	public function save()
	{
		$table = \ORM::for_table('cv2_veiculos_outros');
		if ($this->cod_veiculos_outro)
			$veiculo = $table->where('cod_veiculos_outro', $this->cod_veiculos_outro)
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
			$veiculo = \ORM::for_table('cv2_veiculos_outros')
					->where('chave_veiculo', $this->cod_veiculo)
					->find_one();
			
			foreach (self::$keys as $key)
				$this->$key = $veiculo->$key;
		}
	}
}