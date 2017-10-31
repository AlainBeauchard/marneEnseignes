<?php

class Application_Form_NomModele extends Zend_Form
{

    public function init()
    {
        $intitule = new Zend_Form_Element_Text('nom_modele');
        $intitule->setAttribs(array('placeholder'=>'Nom du modèle','class'=>'form-control', 'aria-describedby'=>'sizing-addon2'));
        $intitule->setRequired(true);
        $this->addElement($intitule);
        
        $valid = new Zend_Form_Element_Button('valider');
        $valid->setLabel('Sauvegarder');
        $valid->setAttribs(array('class'=>'btn btn-primary', 'id'=>'saveModeleWithName'));
        $this->addElement($valid);
    }


}

