<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Invoices extends MY_Controller
{
    public function __construct() {
        parent::__construct();
        $this->load->model(['Invoice_model', 'Contact_model']);
        $this->load->helper(['url','form','html']);
        $this->load->library('session');
    }

    /**
     * LISTA de invoices (com filtros simples)
     * Filtros por query string:
     *  - contact_id (int) -> lista só desse contato
     *  - sort (id|code|amount|created_at) e dir (asc|desc)
     */

    public function index() {
        
        // Query string
        $q          = trim((string)$this->input->get('q', TRUE));
        $contact_id = (int)$this->input->get('contact_id', TRUE);
        $sort       = (string)$this->input->get('sort', TRUE);
        $dir        = strtolower((string)$this->input->get('dir', TRUE)) === 'asc' ? 'asc' : 'desc';

        $allowed_cols = ['id','code','amount','created_at'];
        $sort_col     = in_array($sort, $allowed_cols, true) ? $sort : 'id';

        // Monta filtros e ordenação
        $filters = [];
        if ($q !== '')           $filters['q'] = $q;
        if ($contact_id > 0)     $filters['contact_id'] = $contact_id;
        $sort_cfg = ['column'=>$sort_col, 'dir'=>$dir];

        // Busca (sem paginação)
        $invoices = $this->Invoice_model->get_filtered_simple($filters, $sort_cfg, 500);

        // Nome dos contatos para exibir na tabela
        $contacts_map = $this->_contacts_map(); // [id => name]

        $data = [
            'invoices'     => $invoices,
            'contacts_map' => $contacts_map,
            'q'            => $q,
            'contact_id'   => $contact_id,
            'sort'         => $sort_col,
            'dir'          => $dir,
        ];

        $this->load->view('invoices/index', $data);
    }


    /**
     * CREATE: GET -> mostra form / POST -> salva e redireciona
     */
    public function create() {
        if ($this->input->method(TRUE) === 'POST') {
            $payload = [
                'code'        => trim((string)$this->input->post('code', TRUE)),
                'description' => trim((string)$this->input->post('description', TRUE)),
                'amount'      => (float)$this->input->post('amount'),
                'contact_id'  => (int)$this->input->post('contact_id'),
            ];

            $new_id = $this->Invoice_model->create($payload);

            if ($new_id) {
                $this->session->set_flashdata('success', 'Invoice criado com sucesso.');
                redirect('invoices');
                return;
            } else {
                $this->session->set_flashdata('error', 'Falha ao criar invoice. Verifique os dados.');
                // cai para exibir o form novamente com os dados preenchidos
            }
        }

        $data = [
            'invoice'  => null,
            'contacts' => $this->_contacts_list(), // para o <select>
        ];

        $this->load->view('invoices/form', $data); 
    }

    /**
     * EDIT: GET -> mostra form / POST -> atualiza e redireciona
     */
    public function edit($id) {
        $id = (int)$id;
        $invoice = $this->Invoice_model->get($id);
        if (!$invoice) {
            show_404();
            return;
        }

        if ($this->input->method(TRUE) === 'POST') {
            $payload = [
                'code'        => trim((string)$this->input->post('code', TRUE)),
                'description' => trim((string)$this->input->post('description', TRUE)),
                'amount'      => (float)$this->input->post('amount'),
                'contact_id'  => (int)$this->input->post('contact_id'),
            ];

            $ok = $this->Invoice_model->update($id, $payload);

            if ($ok) {
                $this->session->set_flashdata('success', 'Invoice atualizado.');
                redirect('invoices');
                return;
            } else {
                $this->session->set_flashdata('error', 'Não foi possível atualizar. Revise os dados.');
                // cai e reexibe o form
            }
        }

        $data = [
            'invoice'  => $invoice,
            'contacts' => $this->_contacts_list(), // para o <select>
        ];

        $this->load->view('invoices/form', $data); 
    }

    /**
     * DELETE: apaga e volta para a lista
     */
    public function delete($id)
    {
        $id = (int)$id;
        if ($id > 0) {
            $this->Invoice_model->delete($id);
            $this->session->set_flashdata('success', 'Invoice excluído.');
        }
        redirect('invoices');
    }

    // -----------------------
    // Helpers internos 
    // -----------------------

    /**
     * Retorna um array [id => name] de contatos (para mostrar nome na lista)
     */
    private function _contacts_map()
    {
        $rows = $this->db->select('id, name')->order_by('name','asc')->get('contacts')->result();
        $map = [];
        foreach ($rows as $r) $map[(int)$r->id] = $r->name;
        return $map;
    }

    /**
     * Retorna lista de contatos para popular <select>
     */
    private function _contacts_list()
    {
        return $this->db->select('id, name')->order_by('name','asc')->get('contacts')->result();
    }
}
