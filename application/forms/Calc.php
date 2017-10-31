<?php

class Application_Form_Calc extends Zend_Form
{

    public function init()
    {
        $longueur = new Zend_Form_Element_Text('longueur');
        $longueur->setAttribs(array('placeholder'=>'Longueur','class'=>'form-control calc', 'aria-describedby'=>'sizing-addon2'));
        $this->addElement($longueur);
        
        $largeur = new Zend_Form_Element_Text('largeur');
        $largeur->setAttribs(array('placeholder'=>'Largeur','class'=>'form-control calc', 'aria-describedby'=>'sizing-addon2'));
        $this->addElement($largeur);
        
        $qte = new Zend_Form_Element_Text('qte_calc');
        $qte->setAttribs(array('placeholder'=>'Quantité','class'=>'form-control', 'aria-describedby'=>'sizing-addon2'));
        $qte->setValue(1);
        $this->addElement($qte);
        
        $total = new Zend_Form_Element_Text('total_calc');
        $total->setAttribs(array('placeholder'=>'Total','class'=>'form-control', 'aria-describedby'=>'sizing-addon2', 'readonly'=>'readonly'));
        $this->addElement($total);
        
        $ajouter = new Zend_Form_Element_Button('ajouter_calc');
        $ajouter->setLabel('Ajouter');
        $ajouter->setAttribs(array('class'=>'btn btn-primary', 'type'=>'button', 'id'=>'ajouter_calc'));
        $this->addElement($ajouter);
    }


}

