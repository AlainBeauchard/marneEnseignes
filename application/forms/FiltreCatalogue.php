<?php

class Application_Form_FiltreCatalogue extends Zend_Form
{

    public function init()
    {
        $codeMe = new Zend_Form_Element_Text('codeMe');
        $codeMe->setAttribs(array('placeholder'=>'Code Marne enseignes','class'=>'form-control autocomplete', 'aria-describedby'=>'sizing-addon2', 'data-filtre' => 1))
                    ->removeDecorator('label');
        $this->addElement($codeMe);

        $reference = new Zend_Form_Element_Text('reference');
        $reference->setAttribs(array('placeholder'=>'Référence','class'=>'form-control autocomplete', 'aria-describedby'=>'sizing-addon2', 'data-filtre' => 1))
        			->removeDecorator('label');
        $this->addElement($reference);
        
        $fournisseur = new Zend_Form_Element_Text('fournisseur');
        $fournisseur->setAttribs(array('placeholder'=>'Fournisseur','class'=>'form-control autocomplete', 'aria-describedby'=>'sizing-addon2', 'data-filtre' => 1))
        			->removeDecorator('label');
        $this->addElement($fournisseur);
        
        $designation = new Zend_Form_Element_Text('produit');
        $designation->setAttribs(array('placeholder'=>'Désignation','class'=>'form-control autocomplete', 'aria-describedby'=>'sizing-addon2', 'data-filtre' => 1))
        			->removeDecorator('label');
        $this->addElement($designation);
        
        $type = new Zend_Form_Element_Text('type');
        $type->setAttribs(array('placeholder'=>'Type','class'=>'form-control autocomplete', 'aria-describedby'=>'sizing-addon2', 'data-filtre' => 1))
        		->removeDecorator('label');
        $this->addElement($type);

        $format = new Zend_Form_Element_Text('format');
        $format->setAttribs(array('placeholder'=>'Format','class'=>'form-control autocomplete', 'aria-describedby'=>'sizing-addon2', 'data-filtre' => 1))
        		->removeDecorator('label');
        $this->addElement($format);
        
        $epaisseur = new Zend_Form_Element_Text('epaisseur');
        $epaisseur->setAttribs(array('placeholder'=>'Epaisseur','class'=>'form-control autocomplete', 'aria-describedby'=>'sizing-addon2', 'data-filtre' => 1))
        			->removeDecorator('label');
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

