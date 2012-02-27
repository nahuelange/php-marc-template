<?php

require_once 'File/MARC.php';
require_once 'File/MARC/Record.php';
require_once 'File/MARC/Data_Field.php';
require_once 'File/MARC/Control_Field.php';
require_once 'File/MARC/Subfield.php';

class MarcTemplate {
	
	private $template;
	
	/*
	 * Constructeur
	 * 
	 * @param array template of datas
	 */
	public function __construct( $template ){
		$this->template	 = $template;
	}
	
	/**
	 * Traitement des données
	 *
	 * @param string $datas les données suivant le template
	 * @return array of File_MARC_Record
	 */
	public function spell( $datas ){
		$records = array();		
		foreach($datas as $data){
			$record = new File_MARC_Record();
			foreach($this->template as $fieldname => $fieldinfos){
				if(isset($data[$fieldname])){
					$record->appendField( $this->_field( $fieldinfos, $data[$fieldname] ) );
				}
			}
			$records[] = $record;
		}
		
		return $records;
	}

	/**
	 * Construction d'un champ
	 * 
	 * @param array les informations d'un champ provenant du templates
	 * @param array les données correspondant au champ
	 * @return File_MARC_Data_Field Contenant les données du champ
	 */
	private function _field( $fieldinfos, $data ){
		if(is_array($fieldinfos)){
			foreach( $fieldinfos as $fieldnumber => $subfieldsinfos ){
				$subfields = array();			
				foreach($subfieldsinfos as $subfield => $subfieldname){
					if(isset($data[$subfieldname])){
						if(is_array($data[$subfieldname])){
							foreach($data[$subfieldname] as $subfielddata){
								$subfields[] = $this->_subfield( $subfield, $subfielddata );
							}
						}else{
							$subfields[] = $this->_subfield( $subfield, $data[$subfieldname] );
						}
					}
				}
				return new File_MARC_Data_Field( $fieldnumber, $subfields );
			}
		}else{
			return new File_MARC_Control_Field($fieldinfos, $data);
		}
	}
	
	/**
	 * Construction d'un sous-champ
	 * 
	 * @param string tag du sous-champ
	 * @param string les données à intégrer au sous-champ
	 * @return File_MARC_Subfield
	 */
	private function _subfield( $subfield, $data ){
		return new File_MARC_Subfield($subfield, trim(html_entity_decode($data)));
	}
	
}

?>
