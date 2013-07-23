<?php
class Login extends CI_Controller {

    public function index()
    {
        if (!$this->input->post('username') || !$this->input->post('password')) die("Missing username or password."); //end if no username or password
        $this->load->library('session'); //load session library
        $this->load->model('auth'); //load our authentication module
        $result = $this->auth->login($this->input->post('username', true), $this->input->post('password', true)); //authenticate with the given username and password and set the result
        if ($result==200) //result is good
        {
            $this->load->model('data'); //load our data model
        	$ballot = $this->data->load(); //create the ballot
        	$this->load->view('ballot', array('positions' => $ballot)); //send the ballot to the view
        } else {
            $this->session->sess_destroy(); //clear all session data
            echo $result; //echo our error message
        }
    }

}
?>