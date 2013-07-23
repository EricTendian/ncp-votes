<?php
class Auth extends CI_Model {

    var $server = "il-idc02.instr.cps.k12.il.us";
    var $domain = "instr.cps.k12.il.us";
    var $dn = "OU=Students,OU=Users,OU=nscollege-1740,OU=school-high,DC=instr,DC=cps,DC=k12,DC=il,DC=us";

    function __construct()
    {
        parent::__construct();
    }

    function login($username, $password)
    {
        //uncomment for testing
        if (defined('ENVIRONMENT') && ENVIRONMENT=='development') {
            $this->session->set_userdata('year', 2);
            $this->session->set_userdata('username', $username);
            $this->session->set_userdata('realname', 'Last, First M');
            $this->load->database();
            $this->db->from('voters');
            $this->db->where('username', $username);
            if ($this->db->count_all_results()) {
                $this->session->sess_destroy();
                return "You are only allowed one vote.";
            }
            return 200;
        }
        $conn = ldap_connect("ldap://".$this->server."/"); //connect to our ldap server
        if (is_resource($conn)) {
            if ($bind = ldap_bind($conn, $username."@".$this->domain, $password)) { //try to login with credentials
                $filter = "(samAccountName=" . $username . ")"; //we will filer our results by username
                $attrs = array("department", "displayname"); //we want to get the class year, which is located in the department attribute
                $result = ldap_search($conn, $this->dn, $filter, $attrs); //search with our specified params
                $entries = ldap_get_entries($conn, $result); //get all the entries
                if ($entries['count'] > 0) //if we have an entry, that must be it, as all the usernames have to be unique
                {
                    $currentMonth = date("m", time()); //current month
                    $currentYear = date("Y", time()); //current year_
                    if (7 < $currentMonth) $year = 4 - ($entries[0]["department"][0] - ($currentYear+1)); //find out the difference between the graduation year and the current year+1 (because we are in second half of current year)
                    else $year = 4 - ($entries[0]["department"][0] - ($currentYear)); //otherwise no +1, just find the difference
                    $this->session->set_userdata('year', $year); //set the year attribute in session
                    $this->session->set_userdata('username', $username); //set the username attribute in session, so we can use this later
                    $this->session->set_userdata('realname', $entries[0]["displayname"][0]); //set the realname from LDAP
                    ldap_close($conn); //remember to close your connections!
                    $this->load->database(); //load our database class
                    $this->db->from('voters'); //voters is the table that contains the list of people that have voted
                    $this->db->where('username', $username); //we want to search for the current username
                    if ($this->db->count_all_results()) { //results>0
                        $this->session->sess_destroy(); //delete the session!
                        return "You are only allowed one vote."; //students cannot vote twice
                    } else if ($year==4) {
                        $this->session->sess_destroy(); //delete the session!
                        return "Current seniors are not allowed to vote."; //seniors cannot vote, only freshmen, sophomores and juniors
                    } else if ($year==1 || $year==3) {
                        $this->session->sess_destroy(); //delete the session!
                        return "Freshmen and Juniors may not vote in the run-off election.";
                    }
                    return 200; //if we got this far, it must have worked
                } else return "Not a student of Northside College Prep."; //staff are forbidden from logging in
            } else return "Username and/or password incorrect."; //we were unable to bind so something must be wrong
        } else return "Unable to connect to authentication server."; //we cannot create a connection so the server must be down
    }
}
?>