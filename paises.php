<?php

class Countries {

	private $url_resource = "https://gist.githubusercontent.com/ivanrosolen/f8e9e588adf0286e341407aca63b5230/raw/99e205ea104190c5e09935f06b19c30c4c0cf17e/country";
	private $text_resource;
	private $array_resource;
	
	function __construct() {
		$this->text_resource = $this->getResource();
		$this->array_resource = $this->convertResource();
	}
	
	/**
	* Carrega o conteudo do recurso e armazena em uma variavel
	*/
	private function getResource() {
		return file_get_contents($this->url_resource);
	}

	/**
	* Transforma a lista de países em um array
	*/
	private function convertResource() {
		$array_resource = preg_split('/$\R?^/m', $this->text_resource);
		foreach($array_resource as $line) {
			$explode_line = preg_split('/\s+/', $line);
			if(ctype_upper($explode_line[0])) {
				$index = $explode_line[0];
				unset($explode_line[0]);
				$saida[$index] = trim(implode(' ',$explode_line));
			}
		}
		return $saida;
	}
	
	private function ordenaInverte(){
		asort($this->array_resource);
		$this->array_resource = array_flip($this->array_resource);		
	}
	
	public function getHtml() {
		$this->ordenaInverte();
		return $this->array_resource;
	}
	
	public function getCsv() {
		
		$data = date('YmdHis');
		header('Content-Type: application/csv');
		header('Content-Disposition: attachment; filename="countries_'.$data.'.csv";');
		$f = fopen('php://output', 'w');

		$this->ordenaInverte();
		
		foreach($this->array_resource as $indice => $valor) {
			fputcsv($f, array($indice,$valor));
		}
	}
	
}

$countries = new Countries();

$tipo_saida = 'csv'; // Saída padrão
if(!empty($_REQUEST['tipo'])) {
	$tipo_saida = $_REQUEST['tipo'];
}

if($tipo_saida == 'csv') {
		$countries->getCsv();
} elseif ($tipo_saida == 'html') {
		echo json_encode(array(
			'status' => true,
			'retorno' => $countries->getHtml()
		));
} else {
		echo json_encode(array(
			'status' => false,
			'retorno' => null
		));
}

?>