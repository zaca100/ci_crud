<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Role_model extends CI_Model
{
    public function all()
    {
        return $this->db->order_by('name','asc')->get('roles')->result();
    }

    public function get($id)
    {
        return $this->db->get_where('roles', ['id'=>(int)$id], 1)->row();
    }

    public function user_roles($user_id)
    {
        $sql = "SELECT r.*
                  FROM roles r
                  JOIN user_roles ur ON ur.role_id = r.id
                 WHERE ur.user_id = ?";
        return $this->db->query($sql, [(int)$user_id])->result();
    }

    public function set_user_roles($user_id, $role_ids)
    {
        $this->db->where('user_id',(int)$user_id)->delete('user_roles');
        foreach ((array)$role_ids as $rid) {
            $this->db->insert('user_roles', ['user_id'=>(int)$user_id, 'role_id'=>(int)$rid]);
        }
        return true;
    }
}
