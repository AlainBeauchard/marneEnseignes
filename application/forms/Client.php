<?php

class Application_Form_Client extends Zend_Form
{

    public function init()
    {
	    $this->setAttrib('role', 'form');
	    
	    $ref = new Zend_Form_Element_Text('ref');
	    $ref->setAttribs(array('placeholder'=>'Code client','class'=>'form-control', 'aria-describedby'=>'sizing-addon2'));
	    $ref->setRequired(true);
	    $ref->setValue($this->getClientNewNum());
	    $this->addElement($ref);
	    
        $societe = new Zend_Form_Element_Text('societe');
        $societe->setAttribs(array('placeholder'=>'Nom de la société','class'=>'form-control', 'aria-describedby'=>'sizing-addon2'));
        $societe->setRequired(true);
        $this->addElement($societe);
        
        $metier = new Zend_Form_Element_Text('metier');
        $metier->setAttribs(array('placeholder'=>'Activité','class'=>'form-control', 'aria-describedby'=>'sizing-addon2'));
        $metier->setRequired(false);
        $this->addElement($metier);
        
        $tel = new Zend_Form_Element_Text('tel');
        $tel->setAttribs(array('placeholder'=>'Téléphone','class'=>'form-control', 'aria-describedby'=>'sizing-addon2'));
        $tel->setRequired(false);
        $this->addElement($tel);
        
        $fax = new Zend_Form_Element_Text('fax');
        $fax->setAttribs(array('placeholder'=>'Fax','class'=>'form-control', 'aria-describedby'=>'sizing-addon2'));
        $fax->setRequired(false);
        $this->addElement($fax);
        
        $mel = new Zend_Form_Element_Text('mel');
        $mel->setAttribs(array('placeholder'=>'Email','class'=>'form-control', 'aria-describedby'=>'sizing-addon2'));
        $mel->setRequired(true);
        $this->addElement($mel);
        
        $adresse = new Zend_Form_Element_Text('adresse');
        $adresse->setAttribs(array('placeholder'=>'Adresse','class'=>'form-control', 'aria-describedby'=>'sizing-addon2'));
        $adresse->setRequired(true);
        $this->addElement($adresse);
        
        $codepostal= new Zend_Form_Element_Text('codepostal');
        $codepostal->setAttribs(array('placeholder'=>'Code postal','class'=>'form-control', 'aria-describedby'=>'sizing-addon2'));
        $codepostal->setRequired(true);
        $this->addElement($codepostal);
        
        $ville = new Zend_Form_Element_Text('ville');
        $ville->setAttribs(array('placeholder'=>'Ville','class'=>'form-control', 'aria-describedby'=>'sizing-addon2'));
        $ville->setRequired(true);
        $this->addElement($ville);
        
        $contact_nom = new Zend_Form_Element_Text('contact_nom');
        $contact_nom->setAttribs(array('placeholder'=>'Nom des contact ','class'=>'form-control', 'aria-describedby'=>'sizing-addon2'));
        $contact_nom->setDescription("Si plusieurs contacts, séparer par une virgule");
        $this->addElement($contact_nom);
        
        $contact_tel = new Zend_Form_Element_Text('contact_tel');
        $contact_tel->setAttribs(array('placeholder'=>'Téléphone des contacts','class'=>'form-control', 'aria-describedby'=>'sizing-addon2'))
        	->setDescription("Si plusieurs téléphones, séparer par une virgule, dans le même ordre que dans le champs précédent.");;
        $this->addElement($contact_tel);
        
        $contact_mail = new Zend_Form_Element_Text('contact_mail');
        $contact_mail->setAttribs(array('placeholder'=>'Email des contacts','class'=>'form-control', 'aria-describedby'=>'sizing-addon2'))
        	->setDescription("Si plusieurs emails, séparer par une virgule, dans le même ordre que dans le champs précédent.");
        $this->addElement($contact_mail);
        
        
        $seconde_adresse = new Zend_Form_Element_Text('seconde_adresse');
        $seconde_adresse->setAttribs(array('placeholder'=>'2ème Adresse','class'=>'form-control', 'aria-describedby'=>'sizing-addon2'));
        $seconde_adresse->setRequired(false);
        $this->addElement($seconde_adresse);
        
        $seconde_codepostal= new Zend_Form_Element_Text('seconde_codepostal');
        $seconde_codepostal->setAttribs(array('placeholder'=>'2ème Code postal','class'=>'form-control', 'aria-describedby'=>'sizing-addon2'));
        $seconde_codepostal->setRequired(false);
        $this->addElement($seconde_codepostal);
        
        $seconde_ville = new Zend_Form_Element_Text('seconde_ville');
        $seconde_ville->setAttribs(array('placeholder'=>'2ème Ville','class'=>'form-control', 'aria-describedby'=>'sizing-addon2'));
        $seconde_ville->setRequired(false);
        $this->addElement($seconde_ville);
        
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

