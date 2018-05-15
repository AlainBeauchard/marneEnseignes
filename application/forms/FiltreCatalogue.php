<?php

class Application_Form_FiltreCatalogue extends Zend_Form
{

    public function init()
    {
        $codeMe = new ZendX_JQuery_Form_Element_AutoComplete('codeMe');
        $codeMe->setAttribs(array('placeholder'=>'Code Marne enseignes','class'=>'form-control autocomplete', 'aria-describedby'=>'sizing-addon2', 'data-filtre' => 1))
                    ->removeDecorator('label');
        $codeMe->setJQueryParams(array('source' => '/ajax/cataloguecodeme', 'select' => new Zend_Json_Expr('function(event,ui){$("#reference").val(ui.item.id)}')));
        $this->addElement($codeMe);

        $reference = new ZendX_JQuery_Form_Element_AutoComplete('reference');
        $reference->setAttribs(array('placeholder'=>'Référence','class'=>'form-control autocomplete', 'aria-describedby'=>'sizing-addon2', 'data-filtre' => 1))
        			->removeDecorator('label');
        $reference->setJQueryParams(array('source' => '/ajax/cataloguereference', 'select' => new Zend_Json_Expr('function(event,ui){$("#reference").val(ui.item.id)}')));
        $this->addElement($reference);

        $fournisseur = new ZendX_JQuery_Form_Element_AutoComplete('fournisseur');
        $fournisseur->setAttribs(array('placeholder'=>'Fournisseur','class'=>'form-control autocomplete', 'aria-describedby'=>'sizing-addon2', 'data-filtre' => 1))
        			->removeDecorator('label');
        $fournisseur->setJQueryParams(array('source' => '/ajax/cataloguefournisseur', 'select' => new Zend_Json_Expr('function(event,ui){$("#fournisseur").val(ui.item.id)}')));
        $this->addElement($fournisseur);
        
        $designation = new ZendX_JQuery_Form_Element_AutoComplete('produit');
        $designation->setAttribs(array('placeholder'=>'Désignation','class'=>'form-control autocomplete', 'aria-describedby'=>'sizing-addon2', 'data-filtre' => 1))
        			->removeDecorator('label');
        $designation->setJQueryParams(array('source' => '/ajax/cataloguedesignation', 'select' => new Zend_Json_Expr('function(event,ui){$("#produit").val(ui.item.id)}')));
        $this->addElement($designation);
        
        $type = new ZendX_JQuery_Form_Element_AutoComplete('type');
        $type->setAttribs(array('placeholder'=>'Type','class'=>'form-control autocomplete', 'aria-describedby'=>'sizing-addon2', 'data-filtre' => 1))
        		->removeDecorator('label');
        $type->setJQueryParams(array('source' => '/ajax/cataloguetype', 'select' => new Zend_Json_Expr('function(event,ui){$("#type").val(ui.item.id)}')));
        $this->addElement($type);

        $surfaceTotal = new ZendX_JQuery_Form_Element_AutoComplete('surface_totale');
        $surfaceTotal->setAttribs(array('placeholder'=>'Surface Totale','class'=>'form-control autocomplete', 'aria-describedby'=>'sizing-addon2', 'data-filtre' => 1))
        		->removeDecorator('label');
        $surfaceTotal->setJQueryParams(array('source' => '/ajax/cataloguest', 'select' => new Zend_Json_Expr('function(event,ui){$("#surface_totale").val(ui.item.id)}')));
        $this->addElement($surfaceTotal);

        $format = new ZendX_JQuery_Form_Element_AutoComplete('format');
        $format->setAttribs(array('placeholder'=>'Format','class'=>'form-control autocomplete', 'aria-describedby'=>'sizing-addon2', 'data-filtre' => 1))
        		->removeDecorator('label');
        $format->setJQueryParams(array('source' => '/ajax/catalogueformat', 'select' => new Zend_Json_Expr('function(event,ui){$("#format").val(ui.item.id)}')));
        $this->addElement($format);
        
        $epaisseur = new ZendX_JQuery_Form_Element_AutoComplete('epaisseur');
        $epaisseur->setAttribs(array('placeholder'=>'Epaisseur','class'=>'form-control autocomplete', 'aria-describedby'=>'sizing-addon2', 'data-filtre' => 1))
        			->removeDecorator('label');
        $epaisseur->setJQueryParams(array('source' => '/ajax/catalogueepaisseur', 'select' => new Zend_Json_Expr('function(event,ui){$("#epaisseur").val(ui.item.id)}')));
        $this->addElement($epaisseur);
        
        $rechercher = new Zend_Form_Element_Submit('rechercher');
        $rechercher->setLabel('Rechercher');
        $rechercher->setAttribs(array('class'=>'btn btn-primary btn-sm', 'type'=>'button', 'id'=>'filtreRechercheCatalogue'))
        			->removeDecorator('DtDdWrapper');
        $this->addElement($rechercher);
        
        $resetClient = new Zend_Form_Element_Button('resetCatalogue');
        $resetClient->setLabel('Reset');
        $resetClient->setAttribs(array('class'=>'btn btn-primary btn-sm', 'type'=>'button', 'id'=>'resetFiltreCatalogue'))
        			->removeDecorator('DtDdWrapper');
        $this->addElement($resetClient);

	}
}

