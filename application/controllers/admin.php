<?php
class Admin extends CI_Controller {

	public function index()
	{
		$this->load->helper('security');
		if (do_hash($this->input->get('passcode'))!='4563db8ac6145c00a9777f076dfde0b0942631d7') header("Location: /ncphs/activities/clubs/scouncil/vote/");
		$this->load->model('data');
		$this->load->view('admin', array('results' => $this->data->getResults(), 'totals' => $this->data->getTotals()));
	}

}
?>