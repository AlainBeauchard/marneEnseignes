<?php

class Application_Form_FiltreArticles extends Zend_Form
{

    public function init()
    {
        $code = new Zend_Form_Element_Text('code');
		$code->setAttribs(array('placeholder'=>'Code','class'=>'form-control autocomplete input-sm', 'aria-describedby'=>'sizing-addon2'))->removeDecorator('label');
        $this->addElement($code);

        $libelle = new Zend_Form_Element_Text('libelle');
		$libelle->setAttribs(array('placeholder'=>'Libellé','class'=>'form-control autocomplete input-sm', 'aria-describedby'=>'sizing-addon2'))->removeDecorator('label');
        $this->addElement($libelle);

        $rechercher = new Zend_Form_Element_Button('rechercher');
        $rechercher->setLabel('Rechercher');
        $rechercher->setAttribs(array('class'=>'btn btn-primary btn-sm', 'type'=>'submit'));
        $rechercher->removeDecorator('DtDdWrapper');
        $this->addElement($rechercher);

        $resetDevis = new Zend_Form_Element_Reset('resetFiltreArticle');
        $resetDevis->setLabel('Reset');
        $resetDevis->setAttribs(array('class'=>'btn btn-primary btn-sm', 'type'=>'button', 'id'=>'resetFiltreDevis'));
        $resetDevis->removeDecorator('DtDdWrapper');
        $this->addElement($resetDevis);
    }
}

