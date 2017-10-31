<?php

class Application_Form_Projet extends Zend_Form
{

    public function init()
    {
	    $db_clients = new Application_Model_Clients();
	    
	    $db_devis = new Application_Model_Devis();
	    $select = $db_devis->select()->where('valide = ?', 1);
	    $devis = $db_devis->fetchAll($select);
	    
	    foreach($devis as $row){
		    $listeDevis[$row->id] = $row->date . " - " . $row->titre;
	    }
	    
	    $dossier = new Zend_Form_Element_Text('dossier');
        $dossier->setAttribs(array('placeholder'=>'Numéro de dossier','class'=>'form-control', 'aria-describedby'=>'sizing-addon2'));
        $dossier->setValue($this->getNewNumProjet());
        $dossier->setRequired(true);
        $this->addElement($dossier);

	    
        $date = new ZendX_JQuery_Form_Element_DatePicker('date_commande');
        $date->setAttribs(array('placeholder'=>'Date de livraison','class'=>'form-control', 'aria-describedby'=>'sizing-addon2'));
        $date->setJQueryParam('dateFormat', 'dd/mm/yy');
        $this->addElement($date);
        
		$codeclient = new Zend_Form_Element_Text('code_client');
        $codeclient->setAttribs(array('placeholder'=>'Code Client','class'=>'form-control', 'aria-describedby'=>'sizing-addon2'));
        $codeclient->setRequired(true);
        $this->addElement($codeclient);
        
        $id_client = new Zend_Form_Element_Hidden('id_client');
        $id_client->removeDecorator('DtDdWrapper')->removeDecorator('label')->removeDecorator('HtmlTag');
        $this->addElement($id_client);
        
        $client = new ZendX_JQuery_Form_Element_AutoComplete('client');
        $client->setAttribs(array('placeholder'=>'Client','class'=>'form-control autocomplete', 'aria-describedby'=>'sizing-addon2'));
        $client->setJQueryParams(array('source' => '/ajax/clients', 'select' => new Zend_Json_Expr('function(event,ui){$("#id_client").val(ui.item.id); updateForm(ui.item.id);}')));
        $this->addElement($client);
        
        $nom = new Zend_Form_Element_Text('titre');
        $nom->setAttribs(array('placeholder'=>'Nom du projet','class'=>'form-control', 'aria-describedby'=>'sizing-addon2'));
        $this->addElement($nom);
        
        $contact_nom = new Zend_Form_Element_Text('contact_nom');
        $contact_nom->setAttribs(array('placeholder'=>'Nom du contact pour ce projet','class'=>'form-control', 'aria-describedby'=>'sizing-addon2'));
        $contact_nom->setDescription("Si plusieurs contacts, séparer par une virgule");
        $this->addElement($contact_nom);
        
        $contact_tel = new Zend_Form_Element_Text('contact_tel');
        $contact_tel->setAttribs(array('placeholder'=>'Téléphone du contact pour ce projet','class'=>'form-control', 'aria-describedby'=>'sizing-addon2'))
        	->setDescription("Si plusieurs téléphones, séparer par une virgule");;
        $this->addElement($contact_tel);
        
        $contact_mail = new Zend_Form_Element_Text('contact_mail');
        $contact_mail->setAttribs(array('placeholder'=>'Email du contact pour ce projet','class'=>'form-control', 'aria-describedby'=>'sizing-addon2'))
        	->setDescription("Si plusieurs emails, séparer par une virgule");
        $this->addElement($contact_mail);
        
        $nomlivraison = new Zend_Form_Element_Text('nomlivraison');
        $nomlivraison->setAttribs(array('placeholder'=>'Nom livraison','class'=>'form-control', 'aria-describedby'=>'sizing-addon2'));
        $this->addElement($nomlivraison);
        
        $adresselivraison = new Zend_Form_Element_Text('adresselivraison');
        $adresselivraison->setAttribs(array('placeholder'=>'Adresse livraison','class'=>'form-control', 'aria-describedby'=>'sizing-addon2'));
        $this->addElement($adresselivraison);
        
        $codepostal = new Zend_Form_Element_Text('codepostal');
        $codepostal->setAttribs(array('placeholder'=>'Code postal livraison','class'=>'form-control', 'aria-describedby'=>'sizing-addon2'));
        $this->addElement($codepostal);
        
        $villelivraison = new Zend_Form_Element_Text('ville');
        $villelivraison->setAttribs(array('placeholder'=>'Ville livraison','class'=>'form-control', 'aria-describedby'=>'sizing-addon2'));
        $this->addElement($villelivraison);
        
        $chemin = new Zend_Form_Element_Text('chemin');
        $chemin->setAttribs(array('placeholder'=>'Chemin du dossier client pour le projet','class'=>'form-control', 'aria-describedby'=>'sizing-addon2'));
        $chemin->setDescription("exemple : /c:/fichier/fichier/");
        $this->addElement($chemin);
        
        $note = new Zend_Form_Element_Textarea('note');
        $note->setAttribs(array('placeholder'=>'Notes', 'class'=>'form-control', 'aria-describedby'=>'sizing-addon2', 'id'=>'redactionDevis', 'rows'=>4));
        $this->addElement($note);
        
        $valid = new Zend_Form_Element_Button('creerdevis');
        $valid->setLabel('Créer Projet');
        $valid->setAttribs(array('class'=>'btn btn-primary', 'type'=>'submit'));
        $this->addElement($valid);
        
    }
    
    function getNewNumProjet(){
	    
	    $year = date('y');
	    
	   	$db_projet = new Application_Model_Projets();
	    $select = $db_projet->select()
	    		->from('projets', array('max(dossier) as max'))
	    		->where('dossier like ?', $year . "-%");
	    $row = $db_projet->fetchRow($select);

        //$newNumProject = '';
	    if(isset($row) && strpos($row->max, '-')>0) {
            list($y, $inc) = explode('-', $row->max);
            $inc++;

            $newNumProject = $y . '-' . (int)$inc;
        } else {
            $newNumProject = $year . '-1' ;
        }

	    return $newNumProject;
    }
}

