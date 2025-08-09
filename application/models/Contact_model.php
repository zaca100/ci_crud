<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Contact_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    //All contacts...
    public function get_all_contacts() {
        $query=$this->db->get('contacts');
        return $query->result();
    }

    //getting a single contact by ID
    public function get_contact($id) {
        $param=array('id'=>$id);
        $query=$this->db->get_where('contacts', $param);
        return $query->row();
    }

    //Adding a contact
    public function insert_contact($data) {
        return $this->db->insert('contacts', $data);
    }

    //Updating a contact data
    public function update_contact($id, $data) {
        $this->db->where('id', $id);
        return $this->db->update('contacts', $data);
    }

    //Deleting a contact 
    public function delete_contact($id) {
        $param = array('id' => $id);
        return $this->db->delete('contacts', $param);
    }

}