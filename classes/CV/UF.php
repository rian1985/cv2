<?php
namespace CV;

class UF extends \Model
{
	
	public static $_table = 'cv2_localizacoes_ufs';
	public static $_id_column = 'cod_uf';
	
	public function cidades()
	{
		return $this->has_many('CV\Cidade', 'chave_uf');
	}
	
}