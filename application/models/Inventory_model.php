<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Inventory_model extends CI_Model {

    private $table = 'inventory';

    public function __construct() {
        parent::__construct();
    }

    // Lista todos os itens de inventário
    public function get_all() {
        $get_all=$this->db->get($this->table);
        return $get_all->result();
    }

    // Lista itens de inventário por invoice
    public function get_by_invoice($invoice_id) {
        $data=['invoice_id'=>$invoice_id];
        $get_by_invoice=$this->db->where($this->table, $data);
        return $get_by_invoice->result();
    }

    // Busca um item de inventário específico pelo ID
    public function get($id) {
        $data=['id'=>$id];
        $get=$this->db->get_where($this->table, $data);
        return $get->row();
    }

    // Insere um novo invoice
    public function insert($data) {
        $insert= $this->db->insert($this->table, $data);
        return $insert;
    }

    // Atualiza um invoice existente
    public function update($id, $data) {
        $this->db->where('id', $id);
        $update=$this->db->update($this->table, $data);
        return $update;
    }

    // Remove um invoice
    public function delete($id) {
        $data=['id'=>$id];
        $delete=$this->db->delete($this->table, $data);
        return $delete;
    }
}
