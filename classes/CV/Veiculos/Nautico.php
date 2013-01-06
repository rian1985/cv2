<?php
namespace CV\Veiculos;

class Nautico extends \CV\Veiculo
{
	
	private static $keys = array(
		'cod_veiculos_nautico', 'tipo_veiculo', 'marca', 'modelo', 'comprimento',
		'boaca', 'calado', 'pontal', 'quantidade_motores', 'motor', 'marca_motor',
		'ano_motor', 'horas_uso', 'potencia', 'combustivel', 'capacidade_tanque',
		'altura_interior', 'material', 'quantidade_pessoas', 'camarotes',
		'banheiro', 'chave_veiculo'
	);
	
	public function save()
	{
		$table = \ORM::for_table('cv2_veiculos_nauticos');
		if ($this->cod_veiculos_nautico)
			$veiculo = $table->where('cod_veiculos_nautico', $this->cod_veiculos_nautico)
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
			$veiculo = \ORM::for_table('cv2_veiculos_nauticos')
					->where('chave_veiculo', $this->cod_veiculo)
					->find_one();
			
			foreach (self::$keys as $key)
				$this->$key = $veiculo->$key;
		}
	}
}