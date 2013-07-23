<?php
class Ballot extends CI_Controller {

    public function index()
    {
        $result = array('code' => 500, 'data' => 'An unknown error has occurred. Please try again.');
        $this->load->library('session'); //load session library
        $this->load->model('data'); //load data model
        $confirm = $this->data->vote($this->input->post()); //send in our ballot and get confirmation/result
        if ($confirm==200) $result = array('code' => '200', 'data' => 'You ballot has been counted. Thank you for voting!');
        else if ($confirm==409) //we have a conflict, going to assume somebody was trying to hack
        {
            require_once('phpmailer/class.phpmailer.php');
            $mail = new PHPMailer(); //Create a new PHPMailer instance
            $mail->IsSMTP(); // Set PHPMailer to use the sendmail transport
            $mail->SMTPAuth   = true;                  // enable SMTP authentication
            $mail->SMTPSecure = "tls";                 // sets the prefix to the servier
            $mail->Host       = "smtp.gmail.com";      // sets GMAIL as the SMTP server
            $mail->Port       = 587;                   // set the SMTP port for the GMAIL server
            $mail->Username   = "sample.email@gmail.com";  // GMAIL username
            $mail->Password   = "";            // GMAIL password
            $mail->SetFrom('sample.email@gmail.com', 'Elections Bot'); //Set who the message is to be sent from
            $mail->AddReplyTo($this->session->userdata('username').'@cps.edu', $this->session->userdata('realname')); //Set an alternative reply-to address
            $mail->AddAddress('sample.email@gmail.com', 'Student Council'); //Set who the message is to be sent to
            $mail->Subject = 'Suspicious Election Activity Report - '.$this->session->userdata('username'); //Set the subject line
            $mail->MsgHTML('Suspicious activity, including form value modification, have been detected on account <em>'.$this->session->userdata('username').'</em>. Please investigate the data, which has been included in the data_dump.txt attachment. If hacking is confirmed, it may result in disciplinary action.<br/>- Election Bot'); //HTML message body*/
            $mail->AddStringAttachment($this->session->userdata('dump'), 'data_dump_'.date("Ymd_H-i-s").'.txt');
            if (!$mail->Send()) {
                log_message('error', "Mailer Error: " . $mail->ErrorInfo);
            }

            $result = array('code' => 409, 'data' => 'Suspicious activity has been observed in this ballot. You have been reported to the admin.');
        } else $result = array('code' => 203, 'data' => $confirm); //something bad happened or we are using this for debugging
        $this->session->sess_destroy();
        echo json_encode($result);
    }

}
?>