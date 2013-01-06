<?php
namespace CV\Veiculos;

class Carro extends \CV\Veiculo
{
	
	private static $keys = array(
		'cod_veiculos_carro', 'modelo', 'ano', 'portas', 'quilometros', 'combustivel',
		'direcao', 'transmissao', 'cor', 'chave_veiculo'
	);
	
	public function save()
	{
		$table = \ORM::for_table('cv2_veiculos_carros');
		if ($this->cod_veiculos_carro)
			$veiculo = $table->where('cod_veiculos_carro', $this->cod_veiculos_carro)
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
			$veiculo = \ORM::for_table('cv2_veiculos_carros')
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
						'Abertura interna do porta-malas',
						'Alarme de luzes acesas',
						'Ar condicionado',
						'Ar quente',
						'Banco traseiro retrátil',
						'Banco do motorista com regulagem de altura',
						'Bancos elétricos',
						'Bancos em couro',
						'Computador de bordo',
						'Faróis com regulagem interna',
						'GPS',
						'Piloto automático',
						'Porta copos',
						'Retrovisores elétricos',
						'Sensor de chuva',
						'Sensor de estacionamento',
						'Sensor de luz',
						'Teto solar',
						'Trava elétrica central',
						'Vidros elétricos'
				),
				'Exterior' => array(
						'Capota Marítima',
						'Limpador traseiro',
						'Pára-choques na cor do veículo',
						'Protetor de Caçamba',
						'Quebra-mato',
						'Santo Antônio',
						'Bancos elétricos',
						'Suporte para estepe',
						'Vidros verdes'
				),
				'Segurança' => array(
						'Airbag de cortina',
						'Airbag laterais',
						'Airbag motorista',
						'Airbag passageiro',
						'Alarme',
						'Blindado',
						'Break light',
						'Controle de estabilidade',
						'Controle de velocidade',
						'Desembaçador traseiro',
						'Distribuição eletrônica de frenagem',
						'Encosto de cabeça traseiro',
						'Encostro traseiro',
						'Faróis de neblina dianteiros',
						'Faróis de neblina traseiros',
						'Faróis de xenon',
						'Farol de neblina',
						'Freios ABS',
						'Imobilizador de motor',
						'Rodas de liga leve',
						'Tração',
						'Tração 4x4',
						'Tração elétricas'
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