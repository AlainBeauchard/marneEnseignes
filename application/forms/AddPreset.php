<?php

class Application_Form_AddPreset extends Zend_Form
{

    public function init()
    {
        $tache = new Zend_Form_Element_Text('libelle_tache');
		$tache->setAttribs(array('placeholder'=>'Libelle de la tache','class'=>'form-control', 'aria-describedby'=>'sizing-addon2'));
		$this->addElement($tache);
		
		$ajouter = new Zend_Form_Element_Submit('ajouter');
        $ajouter->setLabel('Ajouter');
        $ajouter->setAttribs(array('class'=>'btn btn-primary'));
        $this->addElement($ajouter);
    }


}

