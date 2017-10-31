<?php

class Application_Model_Produits extends Zend_Db_Table_Abstract
{
	protected $_name = "catalogue";
	protected $_primary = "id_produit";
	
	
	public function searchProduit($str){
		$select = $this->select()->where('designation like ?', $str . '%')
									->orWhere('code_me like ?', $str . '%');

		$produits = $this->fetchAll($select);
	
		foreach($produits as $row){
			if($row->unitaire == null)
			{
				if($row->prixM2 == null){
					$pu = $row->prixML;
				}else{
					$pu = $row->prixM2;
				}
			}else{
				$pu = $row->unitaire;
			}

			
			$tmp[] = '{ "id":"' . $row->id_produit . '", "value":"' . $row->designation . '", "pu":"' . $pu . '", "coef":"' . $row->coeff_marge . '"}';
		}
		
		return new Zend_Json_Expr(implode(',', $tmp));
	}

}

