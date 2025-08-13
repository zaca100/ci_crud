<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Contact_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    //Todos os contatos...
    public function get_all_contacts() {
        $query=$this->db->get('contacts');
        return $query->result();
    }

    //Um contato único pelo ID
    public function get_contact($id) {
        $param=array('id'=>$id);
        $query=$this->db->get_where('contacts', $param);
        return $query->row();
    }

    //Inserindo um contato
    public function insert_contact($data) {
        return $this->db->insert('contacts', $data);
    }

    //Atualizando um contato
    public function update_contact($id, $data) {
        $this->db->where('id', $id);
        return $this->db->update('contacts', $data);
    }

    //Excluindo um contato 
    public function delete_contact($id) {
        $param = array('id' => $id);
        return $this->db->delete('contacts', $param);
    }

    /**
     * Lista contatos aplicando filtros e ordenação (sem paginação).
     * Opcional: um "limit" de segurança para não explodir a tela.
     */

    public function get_filtered_simple($filters = [], $sort = ['column' => 'id', 'dir' => 'desc'], $limit = 100)
    {
        // Filtros
        if (!empty($filters['q'])) {
            $q = trim($filters['q']);
            $this->db->group_start()
                    ->like('name',  $q)
                    ->or_like('email', $q)
                    ->or_like('phone', $q)
                    ->group_end();
        }

        // Ordenação (whitelist)
        $allowed = ['id', 'name', 'email', 'phone']; 
        $col = in_array($sort['column'], $allowed, true) ? $sort['column'] : 'id';
        $dir = strtolower($sort['dir']) === 'asc' ? 'asc' : 'desc';

        $this->db->from('contacts');
        $this->db->order_by($col, $dir);

        if ($limit > 0) {
            $this->db->limit((int)$limit);
        }

        return $this->db->get()->result();
    }

    /** (opcional) total só para exibir “N encontrados” */
    public function count_filtered_simple($filters = [])
    {
        if (!empty($filters['q'])) {
            $q = trim($filters['q']);
            $this->db->group_start()
                    ->like('name',  $q)
                    ->or_like('email', $q)
                    ->or_like('phone', $q)
                    ->group_end();
        }
        return $this->db->count_all_results('contacts');
    }
}

