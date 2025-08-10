<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller
{
    protected $acl_excluded = ['auth', 'welcome']; // controllers sempre liberados

    public function __construct()
    {
        parent::__construct();

        // 1) exige login
        if (!$this->session->userdata('logged_in')) {
            $this->session->set_flashdata('error', 'Faça login para continuar.');
            redirect('login');
            exit;
        }

        // 2) ignora controllers públicos/infra
        $controller = strtolower($this->router->class);
        $method     = strtolower($this->router->method);

        if (in_array($controller, $this->acl_excluded, true)) {
            return; // sem checagem de permissão
        }

        // 3) ACL: verifica se existe feature mapeada
        $this->load->model('Acl_model');

        $feature = $this->Acl_model->find_feature($controller, $method);
        if (!$feature) {
            // Modo FECHADO por padrão: sem feature cadastrada => 403
            show_error('Acesso negado (feature não cadastrada).', 403);
            return;
        }

        $user_id = (int) $this->session->userdata('user_id');

        // 4) Override por usuário?
        $ov = $this->Acl_model->user_override_for_feature($user_id, $feature->id);
        if ($ov === 0) {
            show_error('Acesso negado (override do usuário).', 403);
            return;
        }
        if ($ov === 1) {
            return; // liberado explicitamente
        }

        // 5) Senão, verifica via role(s)
        if (!$this->Acl_model->user_has_feature_via_roles($user_id, $feature->id)) {
            show_error('Acesso negado (sem permissão via role).', 403);
            return;
        }
    }
}
