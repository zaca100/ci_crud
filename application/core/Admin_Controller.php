<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once(APPPATH . 'core/MY_Controller.php');

class Admin_Controller extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        // exige papel admin
        $this->load->database();
        $user_id = (int)$this->session->userdata('user_id');

        $sql = "SELECT 1
                  FROM user_roles ur
                  JOIN roles r ON r.id = ur.role_id
                 WHERE ur.user_id = ? AND r.slug = 'admin' LIMIT 1";
        $q = $this->db->query($sql, [$user_id]);
        if (!$q->row()) {
            show_error('Acesso negado (apenas administradores).', 403);
        }
    }
}
