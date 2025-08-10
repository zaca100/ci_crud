<?php $title='Admin • Permissões por Papel'; $this->load->view('layouts/header'); ?>

<h1 class="h4 mb-3">Permissões do papel: <?= html_escape($role->name); ?></h1>

<form method="post">
  <?php if ($this->config->item('csrf_protection')): ?>
    <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
  <?php endif; ?>

  <div class="card">
    <div class="card-body">
      <?php foreach($features as $f):
        $isChecked = in_array((int)$f->id, (array)$checked, true);
      ?>
        <div class="form-check">
          <input class="form-check-input" type="checkbox" name="features[]" value="<?= (int)$f->id; ?>" <?= $isChecked?'checked':''; ?>>
          <label class="form-check-label">
            <strong><?= html_escape($f->name); ?></strong>
            <small class="text-muted"> (<?= html_escape($f->controller); ?><?= $f->method ? '::'.html_escape($f->method) : '' ?>)</small>
          </label>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <button class="btn btn-primary mt-3">Salvar</button>
  <a class="btn btn-link" href="<?= site_url('admin/permissions'); ?>">Cancelar</a>
</form>

<?php $this->load->view('layouts/footer'); ?>
