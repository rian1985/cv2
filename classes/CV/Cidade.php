<?php
namespace CV;

class Cidade extends \Model
{
	
	public static $_table = 'cv2_localizacoes_cidades';
	public static $_id_column = 'cod_cidade';
	
	public function localizacoes()
	{
		return $this->has_many('CV\Localizacao', 'chave_cidade');
	}
	
	public function uf()
	{
		return $this->belongs_to('CV\UF', 'chave_uf');
	}
	
}