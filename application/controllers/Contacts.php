<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Contacts extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Contact_model');
        $this->load->helper('url'); // Necessário para redirect()
    }

    // Página inicial: lista todos os contatos
    public function index() {
        $data['contacts'] = $this->Contact_model->get_all_contacts();
        $this->load->view('contacts/index', $data);
    }

    // Exibe o formulário de criação
    public function create() {
        $this->load->view('contacts/create');
    }

    // Salva novo contato
    public function store() {
        $data = array(
            'name'  => $this->input->post('name'),
            'email' => $this->input->post('email'),
            'phone' => $this->input->post('phone')
        );

        if ($this->Contact_model->insert_contact($data)) {
            $this->session->set_flashdata('success', 'Contato criado com sucesso!');
            redirect('contacts');
        } else {
            $this->session->set_flashdata('error', 'Erro ao salvar o contato.');
            redirect('contacts/create');
        }

    }

    // Exibe formulário de edição
    public function edit($id) {
        $data['contact'] = $this->Contact_model->get_contact($id);
        if (!$data['contact']) {
            show_404();
        }
        $this->load->view('contacts/edit', $data);
    }

    // Atualiza um contato
    public function update($id) {
        $data = array(
            'name'  => $this->input->post('name'),
            'email' => $this->input->post('email'),
            'phone' => $this->input->post('phone')
        );

        if ($this->Contact_model->update_contact($id, $data)) {
            $this->session->set_flashdata('success', 'Contato atualizado com sucesso!');
            redirect('contacts');
        } else {
            $this->session->set_flashdata('error', 'Erro ao atualizar o contato.');
            redirect('contacts/edit/' . (int)$id);
        }

    }

    // Deleta um contato
    public function delete($id) {
    // Aceita GET (fallback) e POST (AJAX)
    $deleted = $this->Contact_model->delete_contact($id);

    // Se for requisição AJAX (POST), devolve JSON
    if ($this->input->is_ajax_request() || $this->input->method(TRUE) === 'POST') {
        $resp = array('success' => (bool) $deleted);

        // Se CSRF estiver ativo, devolvemos token novo (boa prática)
        if ($this->config->item('csrf_protection')) {
            $resp['csrf'] = array(
                'name' => $this->security->get_csrf_token_name(),
                'hash' => $this->security->get_csrf_hash()
            );
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($resp));
        return;
    }

    // Fluxo tradicional (GET + redirect com flash)
    if ($deleted) {
        $this->session->set_flashdata('success', 'Contato excluído com sucesso!');
    } else {
        $this->session->set_flashdata('error', 'Erro ao deletar o contato.');
    }
    redirect('contacts');
}
}
