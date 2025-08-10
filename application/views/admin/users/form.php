<?php $title = ($user?'Editar':'Novo').' Usuário'; $this->load->view('layouts/header'); ?>

<h1 class="h4 mb-3"><?= $title; ?></h1>

<form method="post" novalidate>
  <?php if ($this->config->item('csrf_protection')): ?>
    <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
  <?php endif; ?>

  <div class="card mb-3">
    <div class="card-body">
      <div class="form-group">
        <label>Nome</label>
        <input name="name" class="form-control" required maxlength="100"
               value="<?= set_value('name', $user ? $user->name : ''); ?>">
      </div>

      <div class="form-group">
        <label>E-mail</label>
        <input type="email" name="email" class="form-control" required maxlength="150"
               value="<?= set_value('email', $user ? $user->email : ''); ?>">
      </div>

      <div class="form-group">
        <label>Senha <?= $user ? '(deixe em branco para não alterar)' : '' ?></label>
        <input type="password" name="password" class="form-control" <?= $user?'':'required'; ?> minlength="6">
      </div>

      <div class="form-group">
        <label>Papéis (roles)</label><br>
        <?php foreach ($roles as $r):
          $checked = in_array((int)$r->id, (array)$user_roles, true) ? 'checked' : ''; ?>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" name="roles[]" value="<?= (int)$r->id; ?>" <?= $checked; ?>>
            <label class="form-check-label"><?= html_escape($r->name); ?></label>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>

  <!-- NOVA SEÇÃO: Overrides por funcionalidade -->
  <div class="card">
    <div class="card-body">
      <h5 class="card-title">Permissões específicas (Overrides)</h5>
      <p class="text-muted">
        Selecione <strong>Permitir</strong> para liberar explicitamente, ou <strong>Negar</strong> para bloquear explicitamente.
        Deixe ambos desmarcados para <em>herdar dos papéis</em>.
      </p>

      <?php if (!empty($features)): ?>
        <div class="table-responsive">
          <table class="table table-sm">
            <thead>
              <tr>
                <th>Funcionalidade</th>
                <th>Controller::Método</th>
                <th class="text-center">Permitir</th>
                <th class="text-center">Negar</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($features as $f):
                $fid = (int)$f->id;
                $ov  = isset($overrides[$fid]) ? (int)$overrides[$fid] : null;
              ?>
                <tr>
                  <td><strong><?= html_escape($f->name); ?></strong></td>
                  <td class="text-muted">
                    <?= html_escape($f->controller); ?><?= $f->method ? '::'.html_escape($f->method) : '' ?>
                  </td>
                  <td class="text-center">
                    <input type="checkbox" class="ov-allow" name="allow[]" value="<?= $fid; ?>" <?= $ov===1?'checked':''; ?>>
                  </td>
                  <td class="text-center">
                    <input type="checkbox" class="ov-deny"  name="deny[]"  value="<?= $fid; ?>" <?= $ov===0?'checked':''; ?>>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php else: ?>
        <p class="text-muted">Nenhuma funcionalidade cadastrada ainda.</p>
      <?php endif; ?>

      <div class="mt-3">
        <button class="btn btn-primary">Salvar</button>
        <a class="btn btn-link" href="<?= site_url('admin/users'); ?>">Cancelar</a>
      </div>
    </div>
  </div>
</form>

<!-- Exclusividade Allow/Deny -->
<script>
  (function() {
    // quando marcar "permitir", desmarca "negar" do mesmo feature, e vice-versa
    document.addEventListener('change', function(e) {
      if (e.target.matches('.ov-allow')) {
        var tr = e.target.closest('tr');
        if (e.target.checked) {
          var deny = tr.querySelector('.ov-deny');
          if (deny) deny.checked = false;
        }
      }
      if (e.target.matches('.ov-deny')) {
        var tr = e.target.closest('tr');
        if (e.target.checked) {
          var allow = tr.querySelector('.ov-allow');
          if (allow) allow.checked = false;
        }
      }
    });
  })();
</script>

<?php $this->load->view('layouts/footer'); ?>
