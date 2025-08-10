<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once(APPPATH . 'core/Admin_Controller.php');

class Features extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Feature_model');
        $this->load->helper(['url','form','html']);
        $this->load->library('session');
    }

    public function index()
    {
        $features = $this->Feature_model->all();
        $this->load->view('admin/features/index', ['features'=>$features]);
    }

    public function create()
    {
        if ($this->input->method(TRUE) === 'POST') {
            $data = [
                'name'       => trim($this->input->post('name', TRUE)),
                'slug'       => trim($this->input->post('slug', TRUE)),
                'controller' => trim($this->input->post('controller', TRUE)),
                'method'     => trim($this->input->post('method', TRUE)) ?: null,
                'path'       => trim($this->input->post('path', TRUE)) ?: null,
                'is_menu'    => (int)$this->input->post('is_menu'),
                'is_active'  => (int)$this->input->post('is_active'),
                'sort_order' => (int)$this->input->post('sort_order'),
            ];
            $ok = $this->Feature_model->create($data);
            $this->session->set_flashdata($ok?'success':'error', $ok?'Feature criada.':'Falha ao criar feature.');
            redirect('admin/features');
        }
        $this->load->view('admin/features/form', ['feature'=>null]);
    }

    public function edit($id)
    {
        $feature = $this->Feature_model->get((int)$id);
        if (!$feature) show_404();

        if ($this->input->method(TRUE) === 'POST') {
            $data = [
                'name'       => trim($this->input->post('name', TRUE)),
                'slug'       => trim($this->input->post('slug', TRUE)),
                'controller' => trim($this->input->post('controller', TRUE)),
                'method'     => trim($this->input->post('method', TRUE)) ?: null,
                'path'       => trim($this->input->post('path', TRUE)) ?: null,
                'is_menu'    => (int)$this->input->post('is_menu'),
                'is_active'  => (int)$this->input->post('is_active'),
                'sort_order' => (int)$this->input->post('sort_order'),
            ];
            $ok = $this->Feature_model->update((int)$id, $data);
            $this->session->set_flashdata($ok?'success':'error', $ok?'Feature atualizada.':'Falha ao atualizar feature.');
            redirect('admin/features');
        }
        $this->load->view('admin/features/form', ['feature'=>$feature]);
    }

    public function delete($id)
    {
        $this->Feature_model->delete((int)$id);
        $this->session->set_flashdata('success','Feature excluída.');
        redirect('admin/features');
    }
}
