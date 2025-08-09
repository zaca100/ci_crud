<?php $title = 'Editar Contato'; $this->load->view('layouts/header'); ?>

<div class="d-flex align-items-center justify-content-between mb-3">
  <h1 class="h3 mb-0">Editar Contato</h1>
  <a href="<?= site_url('contacts'); ?>" class="btn btn-outline-secondary">Voltar</a>
</div>

<div class="card">
  <div class="card-body">
    <form action="<?= site_url('contacts/update/' . (int)$contact->id); ?>" method="post" novalidate>
      <?php if ($this->config->item('csrf_protection')): ?>
        <input type="hidden"
               name="<?= $this->security->get_csrf_token_name(); ?>"
               value="<?= $this->security->get_csrf_hash(); ?>">
      <?php endif; ?>

      <div class="form-group">
        <label for="name">Nome</label>
        <input
          type="text"
          class="form-control"
          id="name"
          name="name"
          required
          maxlength="100"
          value="<?= set_value('name', $contact ? $contact->name : ''); ?>">
        <div class="invalid-feedback">Informe o nome.</div>
      </div>

      <div class="form-group">
        <label for="email">E-mail</label>
        <input
          type="email"
          class="form-control"
          id="email"
          name="email"
          required
          maxlength="100"
          value="<?= set_value('email', $contact ? $contact->email : ''); ?>">
        <div class="invalid-feedback">Informe um e-mail válido.</div>
      </div>

      <div class="form-group">
        <label for="phone">Telefone</label>
        <input
          type="text"
          class="form-control"
          id="phone"
          name="phone"
          required
          maxlength="20"
          value="<?= set_value('phone', $contact ? $contact->phone : ''); ?>">
        <div class="invalid-feedback">Informe o telefone.</div>
      </div>

      <button type="submit" class="btn btn-primary">Atualizar</button>
      <a href="<?= site_url('contacts'); ?>" class="btn btn-link">Cancelar</a>
    </form>
  </div>
</div>

<script>
  (function() {
    'use strict';
    var form = document.querySelector('form');

    function isValidEmail(email) {
      // Regex simples (não perfeito, mas suficiente p/ client-side)
      return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }

    function isValidPhone(phone) {
      // Aceita dígitos, espaços, parênteses, traços e +, com mínimo de 8 chars
      return /^[0-9()\-\s+]{8,}$/.test(phone);
    }

    form.addEventListener('submit', function(e) {
      // Validação nativa do HTML5
      var nativeValid = form.checkValidity();
      var email = document.getElementById('email').value.trim();
      var phone = document.getElementById('phone').value.trim();

      var customValid = true;
      if (!isValidEmail(email)) {
        customValid = false;
        document.getElementById('email').setCustomValidity('Informe um e-mail válido.');
      } else {
        document.getElementById('email').setCustomValidity('');
      }

      if (!isValidPhone(phone)) {
        customValid = false;
        document.getElementById('phone').setCustomValidity('Informe um telefone válido.');
      } else {
        document.getElementById('phone').setCustomValidity('');
      }

      if (!nativeValid || !customValid) {
        e.preventDefault();
        e.stopPropagation();
      }

      form.classList.add('was-validated');
    }, false);
  })();
</script>

<?php $this->load->view('layouts/footer'); ?>
