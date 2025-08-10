<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Acl_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    // Busca feature compatível com controller/method
    public function find_feature($controller, $method)
    {
        // Primeiro tenta match exato controller+method ativo
        $this->db->where('LOWER(controller)', strtolower($controller));
        $this->db->where('LOWER(method)', strtolower($method));
        $this->db->where('is_active', 1);
        $this->db->limit(1);
        $q = $this->db->get('features');
        if ($row = $q->row()) return $row;

        // Depois tenta feature do controller inteiro (method NULL)
        $this->db->where('LOWER(controller)', strtolower($controller));
        $this->db->where('method IS NULL', null, false);
        $this->db->where('is_active', 1);
        $this->db->limit(1);
        $q = $this->db->get('features');
        return $q->row(); // pode ser null => sem feature definida
    }

    // Retorna TRUE se user tem permissão pela(s) role(s)
    public function user_has_feature_via_roles($user_id, $feature_id)
    {
        $sql = "SELECT 1
                  FROM user_roles ur
                  JOIN role_features rf ON rf.role_id = ur.role_id
                 WHERE ur.user_id = ? AND rf.feature_id = ?
                 LIMIT 1";
        $q = $this->db->query($sql, [(int)$user_id, (int)$feature_id]);
        return (bool) $q->row();
    }

    // Override por usuário (retorna NULL=sem override, 1=libera, 0=nega)
    public function user_override_for_feature($user_id, $feature_id)
    {
        $q = $this->db->get_where('user_features', [
            'user_id'    => (int)$user_id,
            'feature_id' => (int)$feature_id
        ], 1);
        $row = $q->row();
        if (!$row) return null;
        return (int)$row->allowed; // 1 ou 0
    }

    // Lista features de menu liberadas para um usuário (p/ navbar)
    public function list_menu_features_for_user($user_id)
    {
        // Features ativas e marcadas como menu
        // Libera se: override=1 OR (sem override E liberada por role)
        $sql = "
        SELECT DISTINCT f.*
          FROM features f
          LEFT JOIN user_features uf
                 ON uf.feature_id = f.id AND uf.user_id = ?
          LEFT JOIN role_features rf
                 ON rf.feature_id = f.id
          LEFT JOIN user_roles ur
                 ON ur.role_id = rf.role_id AND ur.user_id = ?
         WHERE f.is_active = 1
           AND f.is_menu = 1
           AND (
                 (uf.allowed = 1)
              OR (uf.allowed IS NULL AND EXISTS(
                   SELECT 1 FROM user_roles ur2
                   JOIN role_features rf2 ON rf2.role_id = ur2.role_id
                  WHERE ur2.user_id = ? AND rf2.feature_id = f.id
               ))
           )
         ORDER BY f.sort_order ASC, f.name ASC
        ";
        $q = $this->db->query($sql, [(int)$user_id, (int)$user_id, (int)$user_id]);
        return $q->result();
    }
}
