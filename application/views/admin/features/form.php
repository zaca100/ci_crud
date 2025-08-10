<?php $title = ($feature?'Editar':'Nova').' Feature'; $this->load->view('layouts/header'); ?>

<h1 class="h4 mb-3"><?= $title; ?></h1>

<form method="post" novalidate>
  <?php if ($this->config->item('csrf_protection')): ?>
    <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
  <?php endif; ?>

  <div class="form-group"><label>Nome</label>
    <input name="name" class="form-control" required
           value="<?= set_value('name', $feature ? $feature->name : ''); ?>">
  </div>
  <div class="form-group"><label>Slug</label>
    <input name="slug" class="form-control" required
           value="<?= set_value('slug', $feature ? $feature->slug : ''); ?>">
  </div>
  <div class="form-group"><label>Controller</label>
    <input name="controller" class="form-control" required
           value="<?= set_value('controller', $feature ? $feature->controller : ''); ?>">
  </div>
  <div class="form-group"><label>Método (opcional)</label>
    <input name="method" class="form-control"
           value="<?= set_value('method', $feature ? $feature->method : ''); ?>">
  </div>
  <div class="form-group"><label>Path (para menu)</label>
    <input name="path" class="form-control"
           value="<?= set_value('path', $feature ? $feature->path : ''); ?>">
    <small class="text-muted">Ex.: "contacts" ou "contacts/create". Se vazio, usa o controller.</small>
  </div>
  <div class="form-row">
    <div class="form-group col-md-4"><label>Mostrar no menu?</label>
      <select class="form-control" name="is_menu">
        <option value="1" <?= isset($feature) && !$feature->is_menu ? '' : 'selected'; ?>>Sim</option>
        <option value="0" <?= isset($feature) && $feature->is_menu ? '' : 'selected'; ?>>Não</option>
      </select>
    </div>
    <div class="form-group col-md-4"><label>Ativa?</label>
      <select class="form-control" name="is_active">
        <option value="1" <?= isset($feature) && !$feature->is_active ? '' : 'selected'; ?>>Sim</option>
        <option value="0" <?= isset($feature) && $feature->is_active ? '' : 'selected'; ?>>Não</option>
      </select>
    </div>
    <div class="form-group col-md-4"><label>Ordem (menu)</label>
      <input type="number" class="form-control" name="sort_order"
             value="<?= set_value('sort_order', $feature ? (int)$feature->sort_order : 100); ?>">
    </div>
  </div>

  <button class="btn btn-primary">Salvar</button>
  <a class="btn btn-link" href="<?= site_url('admin/features'); ?>">Cancelar</a>
</form>

<?php $this->load->view('layouts/footer'); ?>
