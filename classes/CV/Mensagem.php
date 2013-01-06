<?php
namespace CV;

class Mensagem extends \Model
{
	
	public static $_table = 'cv2_mensagens';
	public static $_id_column = 'cod_mensagem';
	
	public function vendedor()
	{
		return $this->belongs_to('CV\Vendedor', 'chave_vendedor');
	}
	
}