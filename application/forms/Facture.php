<?php

class Application_Form_Facture extends Zend_Form
{

    public function init()
    {
	    
	    $date = new ZendX_JQuery_Form_Element_DatePicker('date_facture');
        $date->setAttribs(array('placeholder'=>'Date','class'=>'form-control', 'aria-describedby'=>'sizing-addon2'));
        $date->setJQueryParam('dateFormat', 'dd/mm/yy');
        $date->setValue(date('d/m/Y'));
        $date->setRequired(true);
        $this->addElement($date);
	    
	    $reglement = new ZendX_JQuery_Form_Element_AutoComplete('reglement');
        $reglement->setAttribs(array('placeholder'=>'Mode de reglement','class'=>'form-control autocomplete', 'aria-describedby'=>'sizing-addon2'));
        $reglement->setJQueryParams(array('source' => '/ajax/reglements', 'select' => new Zend_Json_Expr('function(event,ui){$("#id_client").val(ui.item.value)}')));
        $reglement->setRequired(true);
        $this->addElement($reglement);
    
        $acompte = new Zend_Form_Element_Text('acompte');
        $acompte->setAttribs(array('placeholder'=>'Acompte versé','class'=>'form-control', 'aria-describedby'=>'sizing-addon2'));
        $this->addElement($acompte);
        
        $remise = new Zend_Form_Element_Text('remise');
        $remise->setAttribs(array('placeholder'=>'Remise (%)','class'=>'form-control', 'aria-describedby'=>'sizing-addon2'));
        $this->addElement($remise);
        
        $date = new ZendX_JQuery_Form_Element_DatePicker('date_paiement');
        $date->setAttribs(array('placeholder'=>'Date d\'échéance','class'=>'form-control', 'aria-describedby'=>'sizing-addon2'));
        $date->setJQueryParam('dateFormat', 'dd/mm/yy');
        $date->setValue(date('d/m/Y'));
        $this->addElement($date);
        
        $valid = new Zend_Form_Element_Button('valider');
        $valid->setLabel('Valider');
        $valid->setAttribs(array('class'=>'btn btn-primary', 'type'=>'submit'));
        $this->addElement($valid);
    }


}

