<?php

class Application_Form_Itemsdevis extends Zend_Form
{

    public function init()
    {
	    $id_devis = new Zend_Form_Element_Hidden('id_devis');
// 	    $id_devis->removeDecorator('DtDdWrapper')->removeDecorator('label')->removeDecorator('HtmlTag');
        $this->addElement($id_devis);
        
        $id_item = new Zend_Form_Element_Hidden('id_item');
        $id_item->removeDecorator('DtDdWrapper')->removeDecorator('label')->removeDecorator('HtmlTag');
        $this->addElement($id_item);
        
        $designation = new ZendX_JQuery_Form_Element_AutoComplete('designation');
        $designation->setAttribs(array('placeholder'=>'Désignation','class'=>'form-control autocomplete', 'aria-describedby'=>'sizing-addon2'));
        $designation->setJQueryParams(array('source' => '/ajax/refproduit', 'select' => new Zend_Json_Expr('function(event,ui){$("#id_item").val(ui.item.id); $("#pu").val(ui.item.pu); $("#qte").val(1); $("#coeff_marge").val(ui.item.coef); updatePrix();}')));
        $this->addElement($designation);
        
        $designation_tl = new Zend_Form_Element_Textarea('item');
        $designation_tl->setAttribs(array('placeholder'=>'Désignation','class'=>'form-control', 'aria-describedby'=>'sizing-addon2', 'rows'=>5));
        $this->addElement($designation_tl);
        
        $pu = new Zend_Form_Element_Text('pu');
        $pu->setAttribs(array('placeholder'=>'P.U.','class'=>'form-control', 'aria-describedby'=>'sizing-addon2'));
        $this->addElement($pu);
        
        $coef = new Zend_Form_Element_Text('coeff_marge');
        $coef->setAttribs(array('placeholder'=>'Coefficient','class'=>'form-control', 'aria-describedby'=>'sizing-addon2'));
        $this->addElement($coef);
        
        $qte = new Zend_Form_Element_Text('qte');
        $qte->setAttribs(array('placeholder'=>'Qté','class'=>'form-control', 'aria-describedby'=>'sizing-addon2'));
        $this->addElement($qte);
        
        $remise = new Zend_Form_Element_Text('remise');
        $remise->setAttribs(array('placeholder'=>'Remise','class'=>'form-control', 'aria-describedby'=>'sizing-addon2'));
        $this->addElement($remise);
        
        $pht = new Zend_Form_Element_Text('pht');
        $pht->setAttribs(array('placeholder'=>'P.H.T.','class'=>'form-control', 'aria-describedby'=>'sizing-addon2'));
        $this->addElement($pht);
        
        $ajouter = new Zend_Form_Element_Button('ajouter');
        $ajouter->setLabel('Ajouter');
        $ajouter->setAttribs(array('class'=>'btn btn-primary', 'type'=>'button', 'id'=>'ajouter_item'));
        $this->addElement($ajouter);
        
        
        $this->setDecorators(array('FormElements'));
    }


}

