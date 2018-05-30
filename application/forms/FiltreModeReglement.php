<?php

class Application_Form_FiltreModeReglement extends Zend_Form
{

    public function init()
    {
        $codeMe = new Zend_Form_Element_Text('id');
        $codeMe->setAttribs(array('placeholder'=>'id','class'=>'form-control autocomplete', 'aria-describedby'=>'sizing-addon2', 'data-filtre' => 1))
                    ->removeDecorator('label');
        $this->addElement($codeMe);

        $reference = new Zend_Form_Element_Text('libelle_reglement');
        $reference->setAttribs(array('placeholder'=>'Libelle','class'=>'form-control autocomplete', 'aria-describedby'=>'sizing-addon2', 'data-filtre' => 1))
        			->removeDecorator('label');
        $this->addElement($reference);

        $rechercher = new Zend_Form_Element_Submit('rechercher');
        $rechercher->setLabel('Rechercher');
        $rechercher->setAttribs(array('class'=>'btn btn-primary btn-sm', 'type'=>'button', 'id'=>'filtreRechercheCatalogue'))
        			->removeDecorator('DtDdWrapper');
        $this->addElement($rechercher);
        
        $resetClient = new Zend_Form_Element_Button('resetMr');
        $resetClient->setLabel('Reset');
        $resetClient->setAttribs(array('class'=>'btn btn-primary btn-sm', 'type'=>'button', 'id'=>'resetFiltreCatalogue'))
        			->removeDecorator('DtDdWrapper');
        $this->addElement($resetClient);

	}
}

