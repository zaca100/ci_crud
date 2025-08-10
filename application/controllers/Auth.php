<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        // Helpers / libs / models usados aqui
        $this->load->helper(['url', 'form', 'html']);
        $this->load->library(['session']);
        $this->load->model('User_model');

        // IMPORTANTE: como Auth é público (excluído do ACL no MY_Controller),
        // definimos defaults para o header não quebrar quando não há sessão.
        $this->load->vars([
            'menu_features' => [],
            'is_admin'      => false,
        ]);
    }


    /**
     * GET: exibe formulário de login
     * POST: processa login local (email/senha)
     */
    public function login()
    {
        if ($this->input->method(TRUE) === 'POST') {
            $email    = trim((string) $this->input->post('email', TRUE)); // ok filtrar
            $password = (string) $this->input->post('password');          // NÃO filtrar

            $user = $this->User_model->verify_password($email, $password);

            if ($user) {
                // segurança: renova ID de sessão
                $this->session->sess_regenerate(TRUE);

                // dados mínimos na sessão
                $this->session->set_userdata([
                    'user_id'       => (int) $user->id,
                    'user_name'     => $user->name,
                    'user_email'    => $user->email,
                    'logged_in'     => TRUE,
                    'auth_provider' => $user->provider, // local | google
                ]);

                // Redireciona para a PRIMEIRA feature liberada (ou no_access)
                $this->load->model('Acl_model');
                $menu = $this->Acl_model->list_menu_features_for_user((int)$user->id);
                if (!empty($menu)) {
                    $first = $menu[0];
                    redirect($first->path ?: $first->controller);
                    return;
                } else {
                    redirect('auth/no_access');
                    return;
                }
            }

            // Falha de credenciais
            $this->session->set_flashdata('error', 'Credenciais inválidas.');
            redirect('login');
            return;
        }

        // GET → só exibe a view
        $this->load->view('auth/login');
    }


    /**
     * Encerra sessão e redireciona para login
     */
    public function logout()
    {
        // Limpa tudo
        $this->session->sess_destroy();

        // Evita reuso do ID anterior
        $this->session->sess_regenerate(TRUE);

        redirect('login');
    }

    /**
     * Endpoint para receber o token do Google e autenticar.
     */
    public function google() {
        
    if ($this->input->method(TRUE) !== 'POST') {
        show_404();
        return;
    }

    $idToken = $this->input->post('credential', TRUE); // JWT vindo do Google
    if (!$idToken) {
        $this->session->set_flashdata('error', 'Token Google ausente.');
        redirect('login');
        return;
    }

    // Valida o ID token no endpoint oficial (modo simples, ótimo p/ dev)
    $payload = $this->verify_google_id_token_via_tokeninfo($idToken);

    if (!$payload) {
        $this->session->set_flashdata('error', 'Falha ao validar token do Google.');
        redirect('login');
        return;
    }

    // Checagens essenciais
    $clientId = (string) $this->config->item('google_client_id');
    if (!isset($payload['aud']) || $payload['aud'] !== $clientId) {
        $this->session->set_flashdata('error', 'Token inválido para este aplicativo (aud).');
        redirect('login'); return;
    }

    // Emissor aceito (pode vir com ou sem https://)
    $iss = isset($payload['iss']) ? $payload['iss'] : '';
    if ($iss !== 'accounts.google.com' && $iss !== 'https://accounts.google.com') {
        $this->session->set_flashdata('error', 'Emissor do token inválido (iss).');
        redirect('login'); return;
    }

    // Expiração
    if (!isset($payload['exp']) || (int)$payload['exp'] < time()) {
        $this->session->set_flashdata('error', 'Token expirado.');
        redirect('login'); return;
    }

    // E-mail
    $email = isset($payload['email']) ? $payload['email'] : null;
    $emailVerified = !empty($payload['email_verified']);
    if (!$email || !$emailVerified) {
        $this->session->set_flashdata('error', 'E-mail não verificado na conta Google.');
        redirect('login'); return;
    }

    // (Opcional) Restringir domínio (hosted domain)
    $hdAllowed = (string) $this->config->item('google_hosted_domain');
    if ($hdAllowed !== '') {
        $hd = isset($payload['hd']) ? $payload['hd'] : '';
        if ($hd !== $hdAllowed) {
            $this->session->set_flashdata('error', 'Domínio de e-mail não autorizado.');
            redirect('login'); return;
        }
    }

    // ID único do usuário no Google
    $googleSub = isset($payload['sub']) ? $payload['sub'] : null;
    if (!$googleSub) {
        $this->session->set_flashdata('error', 'Token inválido (sub ausente).');
        redirect('login'); return;
    }

    // Nome (pode vir em dado diferente a depender das permissões)
    $name = isset($payload['name']) ? $payload['name'] : (isset($payload['given_name']) ? $payload['given_name'] : 'Usuário Google');

    // Busca ou cria o usuário
    $user = $this->User_model->get_by_provider_id('google', $googleSub);
    if (!$user) {
        // Se quiser impedir criar automaticamente, troque por erro/convite
        $newId = $this->User_model->create_oauth_user($name, $email, 'google', $googleSub);
        if (!$newId) {
            $this->session->set_flashdata('error', 'Não foi possível criar usuário Google.');
            redirect('login'); return;
        }
        $user = $this->User_model->get_by_provider_id('google', $googleSub);
    }

    // Abre sessão
    $this->session->sess_regenerate(TRUE);
    $this->session->set_userdata([
        'user_id'       => (int) $user->id,
        'user_name'     => $user->name,
        'user_email'    => $user->email,
        'logged_in'     => TRUE,
        'auth_provider' => 'google',
    ]);

    $this->load->model('Acl_model');
    $uid  = (int) $this->session->userdata('user_id');
    $menu = $this->Acl_model->list_menu_features_for_user($uid);
    if (!empty($menu)) {
        $first = $menu[0];
        redirect($first->path ?: $first->controller);
    } else {
        redirect('auth/no_access');
    }

}

/**
 * Valida o ID token do Google consultando o endpoint tokeninfo.
 * Retorna array com o payload validado, ou null em caso de falha.
 */
private function verify_google_id_token_via_tokeninfo($idToken)
{
    $url = 'https://oauth2.googleapis.com/tokeninfo?id_token=' . urlencode($idToken);

    // cURL (compatível com PHP 5–7)
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 5,
    ]);
    $resp = curl_exec($ch);
    $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http !== 200 || !$resp) {
        return null;
    }

    $data = json_decode($resp, true);
    if (!is_array($data)) {
        return null;
    }

    // tokeninfo devolve campos como aud, iss, exp, email, email_verified, sub, hd (opcional), name (às vezes)
    return $data;
}

public function no_access()
{
    // esta rota deve ser acessível sem ACL (auth está em $acl_excluded no MY_Controller)
    $this->load->view('auth/no_access');
}

}
