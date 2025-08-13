<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Invoice_model extends CI_Model
{
    protected $table = 'invoices';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Cria um invoice.
     * Espera: code (string), description (string|nullable), amount (float|decimal), contact_id (int)
     * Retorna: ID do novo registro (int) em caso de sucesso, ou null em caso de falha.
     */
    public function create($data) {

        // refetching o array pra só pegar campos que existem na tabela
        $row = [
            'code'        => isset($data['code']) ? trim($data['code']) : '',
            'description' => isset($data['description']) ? trim($data['description']) : null,
            'amount'      => isset($data['amount']) ? (float)$data['amount'] : 0.0,
            'contact_id'  => isset($data['contact_id']) ? (int)$data['contact_id'] : 0,
        ];

        // validações 
        if ($row['code'] === '' || $row['contact_id'] <= 0) {
            return null;
        }

        $ok = $this->db->insert($this->table, $row);
        return $ok ? (int)$this->db->insert_id() : null;
    }

    /**
     * Busca um invoice pelo ID.
     * Retorna: objeto (linha) ou null.
     */
    public function get($id) {

        $id = (int)$id;
        if ($id <= 0) return null;

        $q = $this->db->get_where($this->table, ['id' => $id], 1);
        return $q->row(); // pode ser null se não achar nenhum registro!
    }

    /**
     * Lista todos os invoices de um contato.
     * Retorna: array de objetos (pode ser vazio).
     */
    public function list_by_contact($contact_id) {

        $contact_id = (int)$contact_id;
        if ($contact_id <= 0) return [];

        $this->db->where('contact_id', $contact_id);
        $this->db->order_by('created_at', 'desc'); // mais novos primeiro
        $q = $this->db->get($this->table);
        return $q->result();
    }

    /**
     * Busca um invoice pelo "code".
     * Retorna: objeto (linha) ou null.
     */
    public function get_by_code($code)
    {
        $code = trim((string)$code);
        if ($code === '') return null;

        $q = $this->db->get_where($this->table, ['code' => $code], 1);
        return $q->row();
    }

    /**
     * Atualiza um invoice.
     * Aceita: code, description, amount, contact_id (todos opcionais).
     * Retorna: true/false.
     */
    public function update($id, $data) {

        $id = (int)$id;
        if ($id <= 0 || !is_array($data)) return false;

        // monta os campos permitidos
        $row = [];
        if (isset($data['code']))        $row['code']        = trim((string)$data['code']);
        if (array_key_exists('description', $data)) $row['description'] = $data['description'] === null ? null : trim((string)$data['description']);
        if (isset($data['amount']))      $row['amount']      = (float)$data['amount'];
        if (isset($data['contact_id']))  $row['contact_id']  = (int)$data['contact_id'];

        if (empty($row)) return false; // nada para atualizar

        // (opcional) se veio code, não pode ser vazio
        if (isset($row['code']) && $row['code'] === '') {
            return false;
        }

        // (opcional) garantir que contact_id é válido (>0) se veio no payload
        if (isset($row['contact_id']) && $row['contact_id'] <= 0) {
            return false;
        }

        $this->db->where('id', $id);
        return $this->db->update($this->table, $row);
    }

    /**
     * Apaga um invoice pelo ID.
     * Retorna: true/false.
     */
    public function delete($id) {

        $id = (int)$id;
        if ($id <= 0) return false;

        return $this->db->delete($this->table, ['id' => $id]);

    }

    /**
     * Lista invoices com filtros simples (q, contact_id) e ordenação.
     * Sem paginação. Use "limit" como guarda.
     * Filtros:
     *   - q: busca em code e description
     *   - contact_id: inteiro (opcional)
     * Sort:
     *   - column: id|code|amount|created_at
     *   - dir: asc|desc
     */
    public function get_filtered_simple($filters = [], $sort = ['column'=>'id','dir'=>'desc'], $limit = 500) {

        // filtros
        if (!empty($filters['q'])) {
            $q = trim((string)$filters['q']);
            $this->db->group_start()
                    ->like('code', $q)
                    ->or_like('description', $q)
                    ->group_end();
        }
        if (!empty($filters['contact_id'])) {
            $this->db->where('contact_id', (int)$filters['contact_id']);
        }

        // ordenação (whitelist)
        $allowed = ['id','code','amount','created_at'];
        $col = in_array($sort['column'], $allowed, true) ? $sort['column'] : 'id';
        $dir = strtolower($sort['dir']) === 'asc' ? 'asc' : 'desc';

        $this->db->from($this->table);
        $this->db->order_by($col, $dir);

        if ($limit > 0) {
            $this->db->limit((int)$limit);
        }

        return $this->db->get()->result();
    }






}
