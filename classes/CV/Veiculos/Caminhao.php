<?php
namespace CV\Veiculos;

class Caminhao extends \CV\Veiculo
{
	
	private static $keys = array(
		'cod_veiculos_caminhao', 'quilometros', 'tracao', 'direcao',
		'transmissao', 'cor', 'capacidade_tracao', 'capacidade_carga',
		'potencia_maxima', 'medidas', 'motor', 'freios', 'chave_veiculo'
	);
	
	public function save()
	{
		$table = \ORM::for_table('cv2_veiculos_caminhoes');
		if ($this->cod_veiculos_caminhao)
			$veiculo = $table->where('cod_veiculos_caminhao', $this->cod_veiculos_caminhao)
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
			$veiculo = \ORM::for_table('cv2_veiculos_caminhoes')
					->where('chave_veiculo', $this->cod_veiculo)
					->find_one();
			
			foreach (self::$keys as $key)
				$this->$key = $veiculo->$key;
		}
	}
	
	public function itensAdicionaisDisponiveis()
	{
		return array(
				'Conforto' => array(
						'Ar condicionado',
						'Banco acompanhante com regulagem de altura',
						'Banco motorista com regulagem de altura',
						'Banco do motorista elétrico',
						'Bancos dianteiros térmicos',
						'Bancos reguláveis com memória',
						'Bancos dianteiros térmicos',
						'Computador de bordo',
						'Controle automático de velocidade',
						'Faróis com regulagem interna',
						'Fechamento centralizado',
						'Limpador de faróis',
						'Retrovisores elétricos',
						'Retrovisores com regulagem interna',
						'Tração dupla',
						'Vidros elétricos'
				),
				'Segurança' => array(
						'Alarme de luzes acesas',
						'Blindado',
						'Chave com código',
						'Cintos de segurança',
						'Faróis de neblina dianteiros',
						'Faróis de xenon',
						'Freios ABS',
						'Freios de disco dianteiros',
						'Freios a motor',
						'Distribuição eletrônica de frenagem',
						'Imobilizador de motor'
				),
				'Som' => array(
						'AM/FM',
						'Bluetooth',
						'Carregador de CD',
						'Cartão SD',
						'CD player',
						'CD player com MP3',
						'Controle de som no volante',
						'DVD player',
						'Entrada auxiliar',
						'Entrada USB',
						'Rádio toca fitas'
				)
		);
	}
}