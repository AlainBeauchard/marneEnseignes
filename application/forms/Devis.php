<?php

class Application_Form_Devis extends Zend_Form
{

    public function init()
    {
	    $this->setAttrib('role', 'form');
        //$this->addDecorators([['Description'=>['tag'=> 'p', 'class' => 'formDevis']]]);

        $codeClient = new Zend_Form_Element_Text('codeClient');
        $codeClient->setAttribs(array('placeholder'=>'code client','class'=>'form-control', 'aria-describedby'=>'sizing-addon2'));
        $codeClient->setLabel('Code Client');
        $codeClient->setRequired(true);
        $this->addElement($codeClient);

        $refDossier = new Zend_Form_Element_Text('refDossier');
        $refDossier->setAttribs(array('placeholder'=>'ref dossier','class'=>'form-control', 'aria-describedby'=>'sizing-addon2'));
        $refDossier->setLabel('Ref dossier');
        $refDossier->setRequired(true);
        $this->addElement($refDossier);

        $intitule = new Zend_Form_Element_Text('intitule');
        $intitule->setAttribs(array('placeholder'=>'Intitule','class'=>'form-control', 'aria-describedby'=>'sizing-addon2'));
        $intitule->setLabel('Intitulé');
        $intitule->setRequired(true);
        $this->addElement($intitule);

        $dteCreation = new ZendX_JQuery_Form_Element_DatePicker('dateCreation', ['attribs' => ['readonly' => 'true']]);
        $dteCreation->setAttribs(array('placeholder'=>'Date création','class'=>'form-control', 'aria-describedby'=>'sizing-addon2'));
        $dteCreation->setLabel('Date Création');
        $dteCreation->setRequired(true);
        $this->addElement($dteCreation);


        $valid = new Zend_Form_Element_Button('Ajouter');
        $valid->setAttribs(array('class'=>'btn btn-primary', 'type'=>'submit'));
        //$valid->addDecorators([['Description'=>['tag'=>'p', 'class' => 'mesBoutons']]]);
        $this->addElement($valid);
    }
    
    function getDevisNewNum(){
	    //TODO attention ceci est le devis initial : pas le mien !
	    $db_devis = new Application_Model_Devis();
	    $select = $db_devis->select()->from('devis', array('max(ref) as max'));
	    $newNumdevis = $db_devis->fetchRow($select);
	    
	    return $newNumdevis->max + 1;
	    
    }
}

