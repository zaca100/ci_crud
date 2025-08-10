<?php $title='Admin • Overrides por Usuário'; $this->load->view('layouts/header'); ?>

<h1 class="h4 mb-3">Overrides do usuário: <?= html_escape($user->name); ?> (ID <?= (int)$user->id; ?>)</h1>

<form method="post">
  <?php if ($this->config->item('csrf_protection')): ?>
    <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
  <?php endif; ?>

  <p class="text-muted">Marque “Permitir” para liberar explicitamente; “Negar” para bloquear explicitamente; deixe ambos desmarcados para herdar do(s) papel(is).</p>

  <div class="card">
    <div class="card-body">
      <?php foreach($features as $f):
        $ov = isset($overrides[$f->id]) ? (int)$overrides[$f->id] : null; ?>
        <div class="mb-2">
          <strong><?= html_escape($f->name); ?></strong>
          <small class="text-muted"> (<?= html_escape($f->controller); ?><?= $f->method ? '::'.html_escape($f->method) : '' ?>)</small><br>
          <label class="mr-2">
            <input type="checkbox" name="allow[]" value="<?= (int)$f->id; ?>" <?= $ov===1?'checked':''; ?>> Permitir
          </label>
          <label>
            <input type="checkbox" name="deny[]" value="<?= (int)$f->id; ?>" <?= $ov===0?'checked':''; ?>> Negar
          </label>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <button class="btn btn-primary mt-3">Salvar</button>
  <a class="btn btn-link" href="<?= site_url('admin/permissions'); ?>">Cancelar</a>
</form>

<?php $this->load->view('layouts/footer'); ?>
