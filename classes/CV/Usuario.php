<?php
namespace CV;

class Usuario extends \Model
{
	
	public static $_table = 'cv2_usuarios';
	public static $_id_column = 'cod_usuario';
	
	public function vendedor()
	{
		return $this->belongs_to('CV\Vendedor', 'chave_vendedor');
	}
	
}