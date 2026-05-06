<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Model_table_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        date_default_timezone_set('asia/dhaka');
    }

    public $Id;
    public $Err;

    public function SelectNews($table, $limit,$oby, $order) {
        return $this->db->order_by($oby, $order)->get($table, $limit)->result();
    }

    public function getData($table, $oby, $order, $limit) {
        return $this->db->order_by($oby, $order)->get($table, $limit)->result();
    }

    function get_teachers() {
        $query = $this->db->get('teacher');
        return $query->result_array();
    }

    function get_members() {
        $query = $this->db->get('member');
        return $query->result_array();
    }
function get_designation() {
        $query = $this->db->get('designation');
        return $query->result_array();
    }
    function get_message() {
        $query = $this->db->get('message', 1);
        return $query->result_array();
    }

    function get_exams(){
        $query = $this->db->get('exam');
        return $query->result_array();
    }
    
    function get_staffs(){
        $query = $this->db->get('staff');
        return $query->result_array();
    }

}

?>