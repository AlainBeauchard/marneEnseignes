<?php

class Application_Model_Catalogue extends Zend_Db_Table_Abstract
{
	protected $_name = "catalogue";
	protected $_primary = "id_produit";


    public function searchFournisseur($str){
        $str = ltrim($str,'0');
        $select = $this->select()->distinct()->from('catalogue', 'fournisseur')
            ->where('fournisseur like ?', $str . '%');

        $clients = $this->fetchAll($select);

        $tmp = [];
        foreach($clients as $row){
            $tmp[] = '{"value":"' . $row->fournisseur . '"}';
        }

        return new Zend_Json_Expr(implode(',', $tmp));
    }

    public function searchSurfaceTotale($str){
        $str = ltrim($str,'0');
        $select = $this->select()->distinct()->from('catalogue', 'surface_totale')
            ->where('surface_totale like ?', $str . '%');

        $clients = $this->fetchAll($select);

        $tmp = [];
        foreach($clients as $row){
            $tmp[] = '{"value":"' . $row->surface_totale . '"}';
        }

        return new Zend_Json_Expr(implode(',', $tmp));
    }

    public function searchReference($str){
        $str = ltrim($str,'0');
        $select = $this->select()->distinct()->from('catalogue', 'code_me')
            ->where('code_me like ?', $str . '%');

        $clients = $this->fetchAll($select);

        $tmp = [];
        foreach($clients as $row){
            $tmp[] = '{"value":"' . $row->code_me . '"}';
        }

        return new Zend_Json_Expr(implode(',', $tmp));
    }

    public function searchType($str){
        $str = ltrim($str,'0');
        $select = $this->select()->distinct()->from('catalogue', 'type')
            ->where('type like ?', $str . '%');

        $clients = $this->fetchAll($select);

        $tmp = [];
        foreach($clients as $row){
            $tmp[] = '{"value":"' . $row->type . '"}';
        }

        return new Zend_Json_Expr(implode(',', $tmp));
    }

    public function searchEpaisseur($str){
        $str = ltrim($str,'0');
        $select = $this->select()->distinct()->from('catalogue', 'epaisseur')
            ->where('epaisseur like ?', '%'.$str . '%');

        $clients = $this->fetchAll($select);

        $tmp = [];
        foreach($clients as $row){
            $tmp[] = '{"value":"' . $row->epaisseur . '"}';
        }

        return new Zend_Json_Expr(implode(',', $tmp));
    }

    public function searchFormat($str){
        $str = ltrim($str,'0');
        $select = $this->select()->distinct()->from('catalogue', 'format')
            ->where('format like ?', '%'.$str . '%');

        $clients = $this->fetchAll($select);

        $tmp = [];
        foreach($clients as $row){
            $tmp[] = '{"value":"' . $row->format . '"}';
        }

        return new Zend_Json_Expr(implode(',', $tmp));
    }

    public function searchDesignation($str){
        $str = ltrim($str,'0');
        $select = $this->select()->distinct()->from('catalogue', 'designation')
            ->where('designation like ?', '%'.$str . '%');

        $clients = $this->fetchAll($select);

        $tmp = [];
        foreach($clients as $row){
            $tmp[] = '{"value":"' . $row->designation . '"}';
        }

        return new Zend_Json_Expr(implode(',', $tmp));
    }
}

