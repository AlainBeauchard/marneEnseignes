<?php

class CalendarController extends Zend_Controller_Action
{

    public function init()
    {
        $this->_helper->layout->setLayout("calendrier");
    }

    public function indexAction()
    {
	    $user = (int) $this->_getParam('user');
	    
        $w = $this->_getParam('week')?$this->_getParam('week'):date('W');
        
        $w1 = $w + 1;
        
        $this->view->prevWeek = (int) $w - 1;
        $this->view->nextWeek = (int) $w + 1;
        $this->view->week = $w;
        $this->view->numJour = $this->_getParam('numJour')?$this->_getParam('numJour'):0;
        
		
		
		for($i = 1; $i<=12; $i++){
			$gendate = new DateTime();
			$gendate->setISODate(date('Y'), $w, $i);
			$dates[$gendate->format('Y-m-d')] = $gendate->format('d/m/Y');
			if($i == 1){
				$min = $gendate->format('Y-m-d');
			}
			if($i == 5){
				$max = $gendate->format('Y-m-d');
			}
			
			$db_event = new Application_Model_Calendrier();
			
			$db_event->delete('id_user1 IS NULL AND id_user2 IS NULL AND id_user3 IS NULL AND id_tache = 0');
			
			$select = $db_event->select()
					->where("date like ?", $gendate->format('Y-m-d'))
					->where("realise = 0");
					
			if($user){
				$select->where('id_user1 = ?', $user);
				$select->orWhere('id_user2 = ?', $user);
				$select->orWhere('id_user3 = ?', $user);
			}
			
			$select->order(array('priority DESC','rang'));

			$events = $db_event->fetchAll($select);
			$eventsDate[$gendate->format('Y-m-d')] = $events;
			
		}
		
		$dateNextLundi = end($dates);
		
		list($j, $m, $y) = explode('/', $dateNextLundi);
		$j = $j+3;
		
		$this->view->semaineProchaine = $y.'-'.$m.'-'.sprintf("%02d",$j);
		
		$this->view->eventsDate = $eventsDate;
		
		$this->view->dates = $dates;
		
		$form = new Application_Form_Addevent($dates);
		$this->view->form = $form;
		
		$selectUser = new Application_Form_SelectUser();
		$this->view->selectUser = $selectUser;
       
    }

    public function deleteAction()
    {
	    $this->_helper->viewRenderer->setNoRender();
	    
        $db_event = new Application_Model_Calendrier();
        $db_event->delete('id_event = ' . $this->_getParam('id'));
        
        $this->_redirect('/calendar/index/week/' . $this->_getParam('week'));
        
    }


}



