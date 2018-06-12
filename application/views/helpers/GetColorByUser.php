<?php


class Zend_View_Helper_GetColorByUser extends Zend_View_Helper_Abstract{

    /**
     * @param $id_user
     * @return string
     * @throws Zend_Db_Table_Exception
     */
	public function GetColorByUser($id_user){
		$db_users = new Application_Model_Users();
		$user = $db_users->find($id_user)->current();
		
		if( isset( $user ) ) {
            $color = $user->color;

            $rouge = hexdec(substr($color, 1, 2));
            $vert = hexdec(substr($color, 3, 2));;
            $bleu = hexdec(substr($color, 5, 2));;

            if (0.3*($rouge) + 0.59*($vert) + 0.11*($bleu) <=  128) {
                $color .= ';color:#FFFFFF;';
            } else {
                $color .= ';color:#000000;';
            }
            return $color;
        }
		else
			return 'color:#000000;';
	}
}