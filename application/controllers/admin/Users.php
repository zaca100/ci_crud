<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once(APPPATH . 'core/Admin_Controller.php');

class Users extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model(['User_model','Role_model','Feature_model']); 
        $this->load->helper(['url','form','html']);
        $this->load->library('session');
    }

    public function index()
    {
        $users = $this->db->order_by('id','desc')->get('users')->result();
        $this->load->view('admin/users/index', ['users'=>$users]);
    }

    public function create()
    {
        if ($this->input->method(TRUE) === 'POST') {
            $data = [
                'name'  => trim($this->input->post('name', TRUE)),
                'email' => trim($this->input->post('email', TRUE)),
                'provider' => 'local',
                'provider_id' => null,
                'password_hash' => password_hash((string)$this->input->post('password'), PASSWORD_DEFAULT),
            ];
            $ok = $this->db->insert('users', $data);

            if ($ok) {
                $user_id = (int)$this->db->insert_id();

                // papéis (roles)
                // papéis (roles)
                $roles = (array)$this->input->post('roles');

                // se nada marcado, atribui 'user' automaticamente
                if (empty($roles)) {
                    $rid = $this->db->select('id')
                                    ->get_where('roles', ['slug' => 'user'], 1)
                                    ->row();
                    if ($rid) { $roles = [(int)$rid->id]; }
                }

                $this->Role_model->set_user_roles($user_id, $roles);

                // overrides (allow/deny) — opcionalmente enviados já na criação
                $allow = (array)$this->input->post('allow');
                $deny  = (array)$this->input->post('deny');
                $map   = [];
                foreach ($allow as $fid) $map[(int)$fid] = 1;
                foreach ($deny  as $fid) $map[(int)$fid] = 0;
                if (!empty($map)) {
                    $this->Feature_model->set_user_overrides($user_id, $map);
                }

                $this->session->set_flashdata('success','Usuário criado.');
                redirect('admin/users');
                return;
            } else {
                $this->session->set_flashdata('error','Falha ao criar usuário.');
            }
        }

        $roles    = $this->Role_model->all();
        $features = $this->Feature_model->all();
        // na criação ainda não há overrides
        $this->load->view('admin/users/form', [
            'roles'       => $roles,
            'user'        => null,
            'user_roles'  => [],
            'features'    => $features,
            'overrides'   => [], // vazio
        ]);
    }


    public function edit($id)
    {
        $user = $this->db->get_where('users', ['id'=>(int)$id], 1)->row();
        if (!$user) show_404();

        if ($this->input->method(TRUE) === 'POST') {
            $data = [
                'name'  => trim($this->input->post('name', TRUE)),
                'email' => trim($this->input->post('email', TRUE)),
            ];
            $pwd = (string)$this->input->post('password');
            if ($pwd !== '') {
                $data['password_hash'] = password_hash($pwd, PASSWORD_DEFAULT);
                $data['provider'] = 'local';
                $data['provider_id'] = null;
            }
            $this->db->where('id', (int)$id)->update('users', $data);

            // papéis
            $roles = (array)$this->input->post('roles');

            // se nada marcado, atribui 'user' automaticamente
            if (empty($roles)) {
                $rid = $this->db->select('id')
                                ->get_where('roles', ['slug' => 'user'], 1)
                                ->row();
                if ($rid) { $roles = [(int)$rid->id]; }
            }

            $this->Role_model->set_user_roles((int)$id, $roles);


            // overrides (allow/deny)
            $allow = (array)$this->input->post('allow'); // ids a permitir
            $deny  = (array)$this->input->post('deny');  // ids a negar
            $map   = [];
            foreach ($allow as $fid) $map[(int)$fid] = 1;
            foreach ($deny  as $fid) $map[(int)$fid] = 0;
            $this->Feature_model->set_user_overrides((int)$id, $map);

            $this->session->set_flashdata('success','Usuário atualizado.');
            redirect('admin/users');
            return;
        }

        $roles         = $this->Role_model->all();
        $user_roles    = $this->Role_model->user_roles((int)$id);
        $user_role_ids = array_map(function($r){ return (int)$r->id; }, $user_roles);

        $features  = $this->Feature_model->all();
        $overrides = $this->Feature_model->user_overrides((int)$id); // [feature_id => allowed(0|1)]

        $this->load->view('admin/users/form', [
            'roles'       => $roles,
            'user'        => $user,
            'user_roles'  => $user_role_ids,
            'features'    => $features,
            'overrides'   => $overrides,
        ]);
    }


    public function delete($id)
    {
        $this->db->delete('users', ['id'=>(int)$id]);
        $this->session->set_flashdata('success','Usuário excluído.');
        redirect('admin/users');
    }
}
