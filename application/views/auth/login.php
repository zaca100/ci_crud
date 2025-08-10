<?php $title = 'Entrar'; $this->load->view('layouts/header'); ?>

<div class="row justify-content-center">
  <div class="col-md-6 col-lg-5">
    <div class="card shadow-sm">
      <div class="card-body p-4">
        <h1 class="h4 mb-3">Acessar</h1>

        <?php if ($this->session->flashdata('error')): ?>
          <div class="alert alert-danger">
            <?= html_escape($this->session->flashdata('error')); ?>
          </div>
        <?php endif; ?>

        <!-- Login Local (email/senha) -->
        <form action="<?= site_url('login'); ?>" method="post" novalidate id="form-login-local" class="mb-3">
          <?php if ($this->config->item('csrf_protection')): ?>
            <input type="hidden"
                   name="<?= $this->security->get_csrf_token_name(); ?>"
                   value="<?= $this->security->get_csrf_hash(); ?>">
          <?php endif; ?>

          <div class="form-group">
            <label for="email">E-mail</label>
            <input type="email"
                   class="form-control"
                   id="email"
                   name="email"
                   required
                   maxlength="150"
                   placeholder="voce@exemplo.com"
                   value="<?= set_value('email'); ?>">
            <div class="invalid-feedback">Informe um e-mail válido.</div>
          </div>

          <div class="form-group">
            <label for="password">Senha</label>
            <input type="password"
                   class="form-control"
                   id="password"
                   name="password"
                   required
                   minlength="6"
                   placeholder="Sua senha">
            <div class="invalid-feedback">Informe sua senha.</div>
          </div>

          <button type="submit" class="btn btn-primary btn-block">Entrar</button>
        </form>

        <div class="text-center text-muted my-2">ou</div>

        <!-- Login com Google -->
        <div class="mb-2">
          <!-- O container do botão será renderizado pelo script do Google -->
            <div id="g_id_onload"
                data-client_id="<?= html_escape($this->config->item('google_client_id')); ?>"
                data-context="signin"
                data-ux_mode="popup"
                data-callback="onGoogleCredential"
                data-auto_select="false"
                data-itp_support="true">
            </div>

          <div class="d-flex justify-content-center">
            <div class="g_id_signin"
                 data-type="standard"
                 data-shape="rectangular"
                 data-theme="outline"
                 data-text="signin_with"
                 data-size="large"
                 data-logo_alignment="left">
            </div>
          </div>
        </div>

        <p class="text-muted small mt-3 mb-0">
          Ao continuar, você concorda com os termos de uso.
        </p>
      </div>
    </div>
  </div>
</div>

<!-- Validação leve client-side para o form local -->
<script>
  (function() {
    'use strict';
    var form = document.getElementById('form-login-local');
    form.addEventListener('submit', function(e) {
      if (!form.checkValidity()) {
        e.preventDefault();
        e.stopPropagation();
      }
      form.classList.add('was-validated');
    }, false);
  })();
</script>

<!-- Script oficial do Google Identity Services -->
<script src="https://accounts.google.com/gsi/client" async defer></script>

<script>
  /**
   * Callback disparada pelo Google ao obter o ID token (JWT) no front.
   * Vamos enviar o token para nosso endpoint /auth/google via POST.
   */
  function onGoogleCredential(response) {
    // response.credential é o ID token (JWT)
    var idToken = response.credential;

    // Monta um POST simples com form invisível (funciona bem com CSRF do CI)
    var form = document.createElement('form');
    form.method = 'POST';
    form.action = "<?= site_url('auth/google'); ?>";

    // Inclui o token do Google
    var inputCred = document.createElement('input');
    inputCred.type = 'hidden';
    inputCred.name = 'credential';
    inputCred.value = idToken;
    form.appendChild(inputCred);

    // Inclui CSRF se estiver ativo
    <?php if ($this->config->item('csrf_protection')): ?>
      var csrfName = "<?= $this->security->get_csrf_token_name(); ?>";
      var csrfHash = "<?= $this->security->get_csrf_hash(); ?>";
      var inputCsrf = document.createElement('input');
      inputCsrf.type = 'hidden';
      inputCsrf.name = csrfName;
      inputCsrf.value = csrfHash;
      form.appendChild(inputCsrf);
    <?php endif; ?>

    document.body.appendChild(form);
    form.submit();
  }
</script>

<?php $this->load->view('layouts/footer'); ?>
