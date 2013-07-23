<?php
class Data extends CI_Model {

    function __construct()
    {
        parent::__construct();
    }

    private function _arraySearch($key, $needle, $haystack)
    {
        foreach ($haystack as $index=>$row)
        {
            if ($row[$key]==$needle) return $index;
        }
        return false;
    }

    private function _filter_by_value($array, $index, $value) { 
        if (is_array($array) && count($array)>0)
        {
            foreach (array_keys($array) as $key)
            {
                $temp[$key] = $array[$key][$index];
                if ($temp[$key] == $value) $newarray[$key] = $array[$key];
            } 
        }
        if (!isset($newarray)) $newarray = array();
        return $newarray; 
    }

    function getPositions($year = 0)
    {
        $this->db->from('positions'); //select from the positions table
        switch ($year)
        {
            case 1:
                if (date("m", time()) > 7) $this->db->where('year', 1); //if at the beginning of the year, only vote for year 1 positions (everything else has been set)
                else {
                    $this->db->where_not_in('year', array('2', '3')); //don't allow voting for other years
                    $this->db->where('id !=', 1); //since id 1 is freshmen senators, and they will only be voting for sophomore senators
                }
                break;
            case 2:
                $this->db->where_not_in('year', array('1', '3'));
                //$this->db->where_in('year', array('2'));
                break;
            case 3:
                $this->db->where_not_in('year', array('1', '2'));
                break;
            default:
                break; //no need for where
        }
        $query = $this->db->get(); //get our results
        $data = array(); //where we will put our results
        foreach ($query->result_array() as $row)
        {
            $data[$row['id']] = array('id' => $row['id'], 'name' => $row['name'], 'title' => $row['title'], 'reqvotes' => ($row['year']==2 ? 1 : $row['reqvotes']), 'year' => $row['year']);
        }
        return $data;
    }

    function getCandidates($position = false)
    {
        $result = array();
        if ($position)
        {
            /*$this->db->from('candidates');
            $this->db->where_in('id', array(10, 11));
            $query = $this->db->get();*/
            $query = $this->db->get_where('candidates', array('position_id' => $position));
        }
        else $query = $this->db->get('candidates');
        foreach ($query->result_array() as $row)
        {
            $result[] = array('id' => $row['id'], 'name' => $row['name'], 'position_id' => $row['position_id']);
        }
        return $result;
    }

    private function _makeBallot($year)
    {
        $data = $this->getPositions($year);
        foreach ($data as $id=>$row)
        {
            $data[$id]['candidates'] = $this->getCandidates($id);
        }
        return $data;
    }

    function load()
    {
        $this->load->database();
        $ballot = $this->_makeBallot($this->session->userdata('year'));
        $this->db->close();
        return $ballot;
    }

    private function _dataDump($data, $message='')
    {
        ob_start();
        var_dump($data);
        $data_dump = ob_get_clean();
        $dump = "ELECTION LOG FILE\nFor user: ".$this->session->userdata('username')."\nMessage: ".$message."\nBallot var_dump() result:\n".$data_dump;
        $this->session->set_userdata('dump', $dump);
        return 409;
    }

    function vote($ballot)
    {
        //if you have got this far into the process, good job. now we will record the user's votes.
        //there are three checks we want to do:
        //1. is the candidate a real candidate?
        //2. does the candidate's id match up with the position set in the key?
        //3. is the required number of votes per position equal to the actual number of votes per position?
        $this->load->database(); //load our database class
        $positions = $this->getPositions($this->session->userdata('year')); //get all positions available to vote for using the user's current year (1-4)
        $data = array(); //we will use this to insert row data
        $votes = array(); //we will use this to check the number of votes per position
        foreach ($ballot as $pname => $candidate) //position is e.g. name1, candidate is the candidate id
        {
            $this->db->select('position_id'); //we want to find the candidate's position
            $this->db->from('candidates'); //from our list of candidates
            $this->db->where('id', $candidate); //using the candidate id we got from the form
            $query = $this->db->get(); //GET THE DATA!
            if ($query->num_rows()==0) return $this->_dataDump($ballot, ''); //invalid candidate? must be a conflict
            $row = $query->first_row('array');
            preg_match('/p([0-9]*)/', $pname, $matches); //we just want the position id from our input array so we can check it
            $pid = intval(str_replace('p', '', $matches[0]));
            if (intval($row['position_id'])!=$pid) return $this->_dataDump($ballot, "Submitted position ID does not match candidate's position ID in database. ".$row['position_id'].'!='.$pid); //candidate position does not match what the form says, so we have a problem
            if (!isset($positions[$row['position_id']])) return $this->_dataDump($ballot, "Cannot find specified position ID in positions table."); //user does not have permissions to vote for this candidate
            $votes[$pid] = isset($votes[$pid]) ? $votes[$pid]+1 : 1; //next we add a vote to that position and add the id to the data array
            array_push($data, array('candidate_id' => $candidate, 'session_id' => $this->session->userdata('session_id'))); //add the vote
        }
        foreach ($votes as $id => $vote) //now we check to see if there is the correct amount of votes
        {
            $position = $positions[$id]; //get the position data
            if ($position['year']!=$this->session->userdata('year') && $position['year']!=0) return $position['year'].' '.$this->session->userdata('year'); //return 409; //the user is not allowed to vote because they are not in the correct year
            if ($position['reqvotes']!=$vote) return $this->_dataDump($ballot, "There is an ".($position['reqvotes']<$vote ? 'excessive' : 'inadequate')." number of votes."); //there is a conflict, why was this not caught in the JS form validation? suspicious...
        }
        $this->db->insert_batch('votes', $data); //SUCCESS! INSERT ALL THE VOTES!
        $this->db->insert('voters', array('username' => $this->session->userdata('username'))); //the user has voted successfully so we mark their name
        $this->db->close(); //close the database connection, success
        return 200;
    }

    private function _getVotes()
    {
        $query = $this->db->get('votes');
        return $query->result_array();
    }

    private function _getVoters()
    {
        $query = $this->db->get('voters');
        return $query->result_array();
    }

    function getResults()
    {
        $this->load->database();
        $votes = $this->_getVotes();
        $candidates = $this->getCandidates();
        $results = array();
        foreach ($votes as $vote)
        {
            $index = $this->_arraySearch('id', $vote['candidate_id'], $candidates);
            if (isset($candidates[$index]['votes'])) $candidates[$index]['votes']++;
            else $candidates[$index]['votes'] = 1;
        }
        $positions = $this->getPositions();
        foreach ($positions as $id=>$position)
        {
            $pos_candidates = $this->_filter_by_value($candidates, 'position_id', $id);
            $positions[$id]['candidates'] = $pos_candidates;
        }
        $results = $positions;
        return $results;
    }

    function getTotals()
    {
        $this->load->database();
        $totals = array('overall' => $this->db->count_all('voters'));
        for ($year=1; $year<5; $year++)
        {
            $query = $this->db->query("
            SELECT COUNT(DISTINCT `session_id`) FROM (
                SELECT * FROM `votes` WHERE `candidate_id` IN (
                    SELECT id FROM `candidates` WHERE `position_id` IN (
                        SELECT id FROM `positions` WHERE `year`=$year
                    )
                )
            ) AS count");
            $count = $query->result_array();
            $totals[$year] = $count[0]['COUNT(DISTINCT `session_id`)'];
        }
        return $totals;
    }

}
?>