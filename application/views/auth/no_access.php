<?php $title='Sem acesso'; $this->load->view('layouts/header'); ?>

<div class="alert alert-warning">
  <h4 class="alert-heading mb-2">Você ainda não tem acesso a nenhuma funcionalidade.</h4>
  <p class="mb-3">Peça a um administrador para conceder um papel (role) ou liberar funcionalidades específicas para sua conta.</p>
  <a class="btn btn-secondary" href="<?= site_url('logout'); ?>">Sair</a>
  <a class="btn btn-link" href="<?= site_url('login'); ?>">Voltar ao login</a>
</div>

<?php $this->load->view('layouts/footer'); ?>
