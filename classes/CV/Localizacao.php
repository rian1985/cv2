<?php
namespace CV;

class Localizacao extends \Model
{

	public static $_table = 'cv2_localizacoes';
	public static $_id_column = 'cod_localizacao';
	
	public function uf()
	{
		return $this->belongs_to('CV\UF', 'chave_uf');
	}
	
	public function cidade()
	{
		return $this->belongs_to('CV\Cidade', 'chave_cidade');
	}
	
	public function vendedor()
	{
		return $this->belongs_to('CV\Vendedor', 'chave_vendedor');
	}
	
}