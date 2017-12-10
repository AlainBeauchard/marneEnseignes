<?php

class Application_Form_Catalogue extends Zend_Form
{

    public function init()
    {
	    $this->setAttrib('role', 'form');

	    $codeMe = new Zend_Form_Element_Text('code_me');
	    $codeMe->setAttribs(array('placeholder'=>'Code Marne Enseignes','class'=>'form-control', 'aria-describedby'=>'sizing-addon2'));
	    $codeMe->setLabel('Code Marne Enseignes');
	    $codeMe->setRequired(true);
	    $this->addElement($codeMe);

        $ref = new Zend_Form_Element_Text('reference');
        $ref->setAttribs(array('placeholder'=>'Référence','class'=>'form-control', 'aria-describedby'=>'sizing-addon2'));
        $ref->setLabel('Références');
        $ref->setRequired(true);
        $this->addElement($ref);

        $designation = new Zend_Form_Element_Text('designation');
        $designation->setAttribs(array('placeholder'=>'Désignation','class'=>'form-control', 'aria-describedby'=>'sizing-addon2'));
        $designation->setLabel('Désignation');
        $designation->setRequired(false);
        $this->addElement($designation);

        $format = new Zend_Form_Element_Text('format');
        $format->setAttribs(array('placeholder'=>'Format','class'=>'form-control', 'aria-describedby'=>'sizing-addon2'));
        $format->setLabel("Format");
        $format->setRequired(false);
        $this->addElement($format);

        $pu = new Zend_Form_Element_Text('pu');
        $pu->setAttribs(array('placeholder'=>'Prix unitaire','class'=>'form-control', 'aria-describedby'=>'sizing-addon2'));
        $pu->setLabel("Prix unitaire");
        $pu->setRequired(false);
        $this->addElement($pu);

        $epaisseur = new Zend_Form_Element_Text('epaisseur');
        $epaisseur->setAttribs(array('placeholder'=>'Epaisseur','class'=>'form-control', 'aria-describedby'=>'sizing-addon2'));
        $epaisseur->setLabel("Epaisseur");
        $epaisseur->setRequired(false);
        $this->addElement($epaisseur);

        $surfaceTotale = new Zend_Form_Element_Text('surface_total');
        $surfaceTotale->setAttribs(array('placeholder'=>'Surface totale','class'=>'form-control', 'aria-describedby'=>'sizing-addon2'));
        $surfaceTotale->setLabel("Surface Totale");
        $surfaceTotale->setRequired(false);
        $this->addElement($surfaceTotale);

        $prixM2= new Zend_Form_Element_Text('prixM2');
        $prixM2->setAttribs(array('placeholder'=>'Prix au metre carré','class'=>'form-control', 'aria-describedby'=>'sizing-addon2'));
        $prixM2->setLabel("Prix au m2");
        $prixM2->setRequired(false);
        $this->addElement($prixM2);

        $prixMl = new Zend_Form_Element_Text('prixML');
        $prixMl->setAttribs(array('placeholder'=>'Prix de metre','class'=>'form-control', 'aria-describedby'=>'sizing-addon2'));
        $prixMl->setLabel("Prix ML");
        $prixMl->setRequired(false);
        $this->addElement($prixMl);

        $unitaire = new Zend_Form_Element_Text('unitaire');
        $unitaire->setAttribs(array('placeholder'=>'Unitaire','class'=>'form-control', 'aria-describedby'=>'sizing-addon2'));
        $unitaire->setLabel("Unitaire");
        //$contact_nom->setDescription("Si plusieurs contacts, séparer par une virgule");
        $this->addElement($unitaire);

        $type = new Zend_Form_Element_Text('type');
        $type->setAttribs(array('placeholder'=>'Type','class'=>'form-control', 'aria-describedby'=>'sizing-addon2'));
        //	->setDescription("Si plusieurs téléphones, séparer par une virgule, dans le même ordre que dans le champs précédent.");;
        $type->setLabel("Type");
        $this->addElement($type);

        $fournisseur = new Zend_Form_Element_Text('fournisseur');
        $fournisseur->setAttribs(array('placeholder'=>'Fournisseur','class'=>'form-control', 'aria-describedby'=>'sizing-addon2'));
        //	->setDescription("Si plusieurs emails, séparer par une virgule, dans le même ordre que dans le champs précédent.");
        $fournisseur->setLabel("Fournisseur");
        $this->addElement($fournisseur);

        $codeMarge = new Zend_Form_Element_Text('code_marge');
        $codeMarge->setAttribs(array('placeholder'=>'Code marge','class'=>'form-control', 'aria-describedby'=>'sizing-addon2'));
        $codeMarge->setLabel("Code Marge");
        $codeMarge->setRequired(false);
        $this->addElement($codeMarge);

        $notes= new Zend_Form_Element_Text('note');
        $notes->setAttribs(array('placeholder'=>'notes','class'=>'form-control', 'aria-describedby'=>'sizing-addon2'));
        $notes->setLabel("Notes");
        $notes->setRequired(false);
        $this->addElement($notes);

        $valid = new Zend_Form_Element_Button('Ajouter');
        $valid->setAttribs(array('class'=>'btn btn-primary', 'type'=>'submit'));
        $this->addElement($valid);
    }
    
    function getClientNewNum(){
	    
	    $db_client = new Application_Model_Clients();
	    $select = $db_client->select()->from('clients', array('max(ref) as max'));
	    $newNumClient = $db_client->fetchRow($select);
	    
	    return $newNumClient->max + 1;
	    
    }


}

