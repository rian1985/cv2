<?php
namespace CV;

class Veiculo extends \Model
{
	public static $_table = 'cv2_veiculos_veiculos';
	public static $_id_column = 'cod_veiculo';
	
	public function vendedor()
	{
		return $this->belongs_to('CV\Vendedor', 'chave_vendedor');
	}
	
	public function movimentacoes()
	{
		return $this->has_many('CV\Movimentacao', 'chave_veiculo');
	}
	
	public function tipo()
	{
		return $this->belongs_to('CV\TipoDeVeiculo', 'chave_tipo_veiculo');
	}
	
	public function fotos()
	{
		return array(
			'1' => $this->foto_1,
			'2' => $this->foto_2,
			'3' => $this->foto_3,
			'4' => $this->foto_4,
			'5' => $this->foto_5,
			'6' => $this->foto_6
		);
	}
	
	public function itensAdicionaisDisponiveis()
	{
		return array();
	}
	
	public function itensAdicionais()
	{
		return explode(';', $this->itens);
	}
	
	public function loadExtraData()
	{}
}

\ORM::configure('id_column_overrides', array(
		'cv2_veiculos_carros' => 'cod_veiculos_carro',
		'cv2_veiculos_motos' => 'cod_veiculos_moto',
		'cv2_veiculos_caminhoes' => 'cod_veiculos_caminhao',
		'cv2_veiculos_onibus' => 'cod_veiculos_onibus',
		'cv2_veiculos_nauticos' => 'cod_veiculos_nautico',
		'cv2_veiculos_outros' => 'cod_veiculos_outrs',
));