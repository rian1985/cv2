<?php
namespace CV;

class Marca extends \Model
{
	
	public static $_table = 'cv2_veiculos_marcas';
	public static $_id_column = 'cod_marca';
	
	public function tipoDeVeiculo()
	{
		return $this->belongs_to('CV\TipoDeVeiculo', 'chave_tipo_veiculo');
	}
	
}