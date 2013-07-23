<?php
class Votes extends CI_Controller {

    public function index()
    {
    	$this->load->database();
		echo $this->db->count_all('voters')." people have voted. That's a ".((intval($this->db->count_all('voters'))/794)*100)."% voter turnout.";
	}

}
?>