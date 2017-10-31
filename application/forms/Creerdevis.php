<?php

class Application_Form_Creerdevis extends Zend_Form
{

    public function init()
    {
        $date = new ZendX_JQuery_Form_Element_DatePicker('date');
        $date->setAttribs(array('placeholder'=>'Date','class'=>'form-control', 'aria-describedby'=>'sizing-addon2'));
        $date->setJQueryParam('dateFormat', 'dd/mm/yy');
        $date->setValue(date('d/m/Y'));
        $date->setRequired(true);
        $this->addElement($date);
        
	    $refdossier = new Zend_Form_Element_Text('ref');
        $refdossier->setAttribs(array('placeholder'=>'Référence dossier','class'=>'form-control', 'aria-describedby'=>'sizing-addon2'));
        $this->addElement($refdossier);
        
        $id_client = new Zend_Form_Element_Hidden('id_client');
        $this->addElement($id_client);
        
        $client = new ZendX_JQuery_Form_Element_AutoComplete('client');
        $client->setAttribs(array('placeholder'=>'Client','class'=>'form-control autocomplete', 'aria-describedby'=>'sizing-addon2'));
        $client->setJQueryParams(array('source' => '/ajax/clients', 'select' => new Zend_Json_Expr('function(event,ui){$("#id_client").val(ui.item.id)}')));
        $client->setRequired(true);
        $this->addElement($client);
        
        $intitule = new Zend_Form_Element_Text('titre');
        $intitule->setAttribs(array('placeholder'=>'Intitulé','class'=>'form-control', 'aria-describedby'=>'sizing-addon2'));
        $intitule->setRequired(true);
        $this->addElement($intitule);
        
        $reglement = new ZendX_JQuery_Form_Element_AutoComplete('reglement');
        $reglement->setAttribs(array('placeholder'=>'Mode de reglement','class'=>'form-control autocomplete', 'aria-describedby'=>'sizing-addon2'));
        $reglement->setJQueryParams(array('source' => '/ajax/reglements', 'select' => new Zend_Json_Expr('function(event,ui){$("#id_client").val(ui.item.value)}')));
        $reglement->setRequired(true);
        $this->addElement($reglement);
        
        $delai = new Zend_Form_Element_Text('delai');
        $delai->setAttribs(array('placeholder'=>'Délai de réalisation','class'=>'form-control', 'aria-describedby'=>'sizing-addon2'));
        $delai->setDescription("Délai en semaines");
        $delai->setRequired(true);
        $this->addElement($delai);
        
        $valid = new Zend_Form_Element_Button('creerdevis');
        $valid->setLabel('Créer Devis');
        $valid->setAttribs(array('class'=>'btn btn-primary', 'type'=>'submit'));
        $this->addElement($valid);
    }


}

