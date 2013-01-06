<?php
namespace CV;

class Movimentacao extends \Model
{
	
	public static $_table = 'cv2_anuncios_movimentacoes';
	public static $_id_column = 'cod_movimentacao';
	
	public function veiculo()
	{
		return $this->belongs_to('CV\Veiculo', 'chave_veiculo');
	}
	
}