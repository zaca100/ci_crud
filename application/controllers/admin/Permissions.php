<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once(APPPATH . 'core/Admin_Controller.php');

class Permissions extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model(['Role_model','Feature_model']);
        $this->load->helper(['url','form','html']);
        $this->load->library('session');
    }

    public function index()
    {
        $roles = $this->Role_model->all();
        $this->load->view('admin/permissions/index', ['roles'=>$roles]);
    }

    // Conceder features a um role
    public function role($role_id)
    {
        $role = $this->Role_model->get((int)$role_id);
        if (!$role) show_404();

        if ($this->input->method(TRUE) === 'POST') {
            $fids = (array)$this->input->post('features');
            $this->Feature_model->set_role_features((int)$role_id, $fids);
            $this->session->set_flashdata('success','Permissões do papel atualizadas.');
            redirect('admin/permissions');
        }

        $features = $this->Feature_model->all();
        $checked  = $this->Feature_model->role_feature_ids((int)$role_id);

        $this->load->view('admin/permissions/role', [
            'role'=>$role, 'features'=>$features, 'checked'=>$checked
        ]);
    }

    // Overrides por usuário (liberar/negar específicas)
    public function user($user_id)
    {
        $user = $this->db->get_where('users', ['id'=>(int)$user_id], 1)->row();
        if (!$user) show_404();

        if ($this->input->method(TRUE) === 'POST') {
            // Receber arrays: allow[] e deny[]
            $allow = (array)$this->input->post('allow'); // fids a liberar
            $deny  = (array)$this->input->post('deny');  // fids a negar
            $map = [];

            foreach ($allow as $fid) $map[(int)$fid] = 1;
            foreach ($deny as $fid)  $map[(int)$fid] = 0;

            $this->Feature_model->set_user_overrides((int)$user_id, $map);
            $this->session->set_flashdata('success','Overrides do usuário atualizados.');
            redirect('admin/permissions');
        }

        $features = $this->Feature_model->all();
        $overrides = $this->Feature_model->user_overrides((int)$user_id); // [fid=>allowed]

        $this->load->view('admin/permissions/user', [
            'user'=>$user, 'features'=>$features, 'overrides'=>$overrides
        ]);
    }
}
