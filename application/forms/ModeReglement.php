<?php

class Application_Form_ModeReglement extends Zend_Form
{

    public function init()
    {
	    $this->setAttrib('role', 'form');

	    /*
	    $codeMe = new Zend_Form_Element_Text('id');
	    $codeMe->setAttribs(array('placeholder'=>'Identifiant','class'=>'form-control', 'aria-describedby'=>'sizing-addon2'));
	    $codeMe->setLabel('Identifiant');
	    $codeMe->setRequired(true);
	    $this->addElement($codeMe);
*/
        $ref = new Zend_Form_Element_Text('libelle_reglement');
        $ref->setAttribs(array('placeholder'=>'Libellé','class'=>'form-control', 'aria-describedby'=>'sizing-addon2'));
        $ref->setLabel('Libellé');
        $ref->setRequired(true);
        $this->addElement($ref);

        $valid = new Zend_Form_Element_Button('Ajouter');
        $valid->setAttribs(array('class'=>'btn btn-primary', 'type'=>'submit'));
        $this->addElement($valid);
    }

}

