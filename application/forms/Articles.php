<?php

class Application_Form_Articles extends Zend_Form
{

    /**
     * @throws Zend_Form_Exception
     */
    public function init()
    {
	    $this->setAttrib('role', 'form');
        //$this->addDecorators([['Description'=>['tag'=> 'p', 'class' => 'formDevis']]]);

        $id = new Zend_Form_Element_Hidden('id');
        $id->setAttribs(array('placeholder'=>'Libellé','class'=>'form-control', 'aria-describedby'=>'sizing-addon2', 'list' => 'listeClientsDevis'));
        $id->setRequired(false);
        $this->addElement($id);

        $code = new Zend_Form_Element_Text('code');
        $code->setAttribs(array('placeholder'=>'Code','class'=>'form-control', 'aria-describedby'=>'sizing-addon2', 'list' => 'listeClientsDevis'));
        $code->setLabel('Code');
        $code->setRequired(true);
        $this->addElement($code);

        $libelle = new Zend_Form_Element_Textarea('libelle');
        // $libelle->setAttribs(array('placeholder'=>'Libellé','class'=>'form-control rte', 'aria-describedby'=>'sizing-addon2', 'list' => 'listeClientsDevis'));
        $libelle->setAttribs(array('placeholder'=>'Libellé','class'=>'form-control', 'aria-describedby'=>'sizing-addon2', 'list' => 'listeClientsDevis'));
        $libelle->setLabel('Libellé');
        $libelle->setRequired(true);
        $this->addElement($libelle);

        $qte = new Zend_Form_Element_Text('qte');
        $qte->setAttribs(array('placeholder'=>'Code','class'=>'form-control', 'aria-describedby'=>'sizing-addon2', 'list' => 'listeClientsDevis'));
        $qte->setLabel('Quantité');
        $qte->setRequired(false);
        $this->addElement($qte);

        $pu = new Zend_Form_Element_Text('pu');
        $pu->setAttribs(array('placeholder'=>'Code','class'=>'form-control', 'aria-describedby'=>'sizing-addon2', 'list' => 'listeClientsDevis'));
        $pu->setLabel('Prix unitaire');
        $pu->setRequired(false);
        $this->addElement($pu);

        $valid = new Zend_Form_Element_Button('Enregistrer');
        $valid->setAttribs(array('class'=>'btn btn-primary', 'type'=>'submit'));
        //$valid->addDecorators([['Description'=>['tag'=>'p', 'class' => 'mesBoutons']]]);
        $this->addElement($valid);
    }
}

