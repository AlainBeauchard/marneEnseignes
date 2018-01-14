<?php

class Application_Model_Taches extends Zend_Db_Table_Abstract
{
	protected $_name = "projetstaches";
	protected $_primary = "id";


    /**
     * @param $tabTaches
     * @return array
     */
    public function trieTableauTaches($tabTaches) {
        $retour = [];
        $monTab = [];

        foreach ($tabTaches as $tache) {
            array_push(
                $monTab,
                ['id'=> $tache->id, 'dte' => $this->getDateDone(strval($tache->id))]
            );
        }

        $monTab = $this->array_msort($monTab, array('dte'=>SORT_DESC));

        foreach($monTab as $tab) {
            $tacheFor = null;
            foreach ($tabTaches as $tache) {
                if ($tache->id == $tab['id']) {
                    $tacheFor = $tache;
                }
            }
            array_push($retour, $tacheFor);
        }


        return $retour;
    }

    public function GetDateDone($id_tache) {
        $db_calendrier = new Application_Model_Calendrier();
        $select = $db_calendrier->select()->where('id_tache = ?', $id_tache);

        $rows = $db_calendrier->fetchAll($select);

        if(count($rows->toArray()) == 0){
            return '';
        }else{
            foreach($rows as $cal){
                $date = $cal->date;
            }
            return date('d/m/Y', strtotime($date));
        }
    }

    function array_msort($array, $cols)
    {
        $colarr = array();
        foreach ($cols as $col => $order) {
            $colarr[$col] = array();
            foreach ($array as $k => $row) { $colarr[$col]['_'.$k] = strtolower($row[$col]); }
        }
        $eval = 'array_multisort(';
        foreach ($cols as $col => $order) {
            $eval .= '$colarr[\''.$col.'\'],'.$order.',';
        }
        $eval = substr($eval,0,-1).');';
        eval($eval);
        $ret = array();
        foreach ($colarr as $col => $arr) {
            foreach ($arr as $k => $v) {
                $k = substr($k,1);
                if (!isset($ret[$k])) $ret[$k] = $array[$k];
                $ret[$k][$col] = $array[$k][$col];
            }
        }
        return $ret;

    }
}

