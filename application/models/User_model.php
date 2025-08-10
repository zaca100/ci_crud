<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model
{
    protected $table = 'users';

    public function __construct()
    {
        parent::__construct();
    }

    public function get_by_email($email)
    {
        $query = $this->db->get_where($this->table, ['email' => $email], 1);
        return $query->row();
    }

    public function verify_password($email, $password_plain)
    {
        $user = $this->get_by_email($email);
        if (!$user) return null;

        if (empty($user->password_hash)) return null;

        if(password_verify($password_plain, $user->password_hash)) {
            //var_dump($user); die();
            return $user;
        } else {
            //var_dump(password_verify($password_plain, $user->password_hash)); die();
            return null;
        }
    }

    public function get_by_provider_id($provider, $provider_id)
    {
        $query = $this->db->get_where(
            $this->table,
            ['provider' => $provider, 'provider_id' => $provider_id],
            1
        );
        return $query->row();
    }

    public function create_oauth_user($name, $email, $provider, $provider_id)
    {
        $data = [
            'name'          => $name,
            'email'         => $email,
            'provider'      => $provider,
            'provider_id'   => $provider_id,
            'password_hash' => null,
        ];
        $ok = $this->db->insert($this->table, $data);
        return $ok ? $this->db->insert_id() : null;
    }
}
