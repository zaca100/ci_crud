<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Feature_model extends CI_Model
{
    protected $table = 'features';

    public function all()
    {
        return $this->db->order_by('sort_order ASC, name ASC')->get($this->table)->result();
    }

    public function get($id)
    {
        return $this->db->get_where($this->table, ['id'=>(int)$id], 1)->row();
    }

    public function create($data)
    {
        return $this->db->insert($this->table, $data);
    }

    public function update($id, $data)
    {
        $this->db->where('id',(int)$id);
        return $this->db->update($this->table, $data);
    }

    public function delete($id)
    {
        return $this->db->delete($this->table, ['id'=>(int)$id]);
    }

    public function role_feature_ids($role_id)
    {
        $sql = "SELECT feature_id FROM role_features WHERE role_id = ?";
        $rows = $this->db->query($sql, [(int)$role_id])->result();
        return array_map(function($r){ return (int)$r->feature_id; }, $rows);
    }

    public function set_role_features($role_id, $feature_ids)
    {
        $this->db->where('role_id', (int)$role_id)->delete('role_features');
        foreach ((array)$feature_ids as $fid) {
            $this->db->insert('role_features', ['role_id'=>(int)$role_id, 'feature_id'=>(int)$fid]);
        }
        return true;
    }

    public function user_overrides($user_id)
    {
        $sql = "SELECT uf.feature_id, uf.allowed FROM user_features uf WHERE uf.user_id = ?";
        $rows = $this->db->query($sql, [(int)$user_id])->result();
        $map = [];
        foreach ($rows as $r) $map[(int)$r->feature_id] = (int)$r->allowed;
        return $map; // ex.: [12=>1, 14=>0]
    }

    public function set_user_overrides($user_id, $allowed_map)
    {
        $this->db->where('user_id', (int)$user_id)->delete('user_features');
        foreach ($allowed_map as $fid=>$allowed) {
            $this->db->insert('user_features', [
                'user_id'=>(int)$user_id,
                'feature_id'=>(int)$fid,
                'allowed'=>(int)$allowed
            ]);
        }
        return true;
    }
}
