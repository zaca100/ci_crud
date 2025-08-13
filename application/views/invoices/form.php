<?php
  $is_edit = !empty($invoice);
  $title = $is_edit ? 'Editar Invoice' : 'Novo Invoice';
  $this->load->view('layouts/header');
?>

<?php if ($this->session->flashdata('error')): ?>
  <div class="alert alert-danger"><?= $this->session->flashdata('error'); ?></div>
<?php endif; ?>

<h1 class="h4 mb-3"><?= $title; ?></h1>

<form method="post" novalidate>
  <?php if ($this->config->item('csrf_protection')): ?>
    <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
  <?php endif; ?>

  <div class="card">
    <div class="card-body">
      <div class="form-row">
        <div class="form-group col-md-4">
          <label for="code">Código</label>
          <input type="text" id="code" name="code" class="form-control" required maxlength="30"
                 value="<?= set_value('code', $is_edit ? $invoice->code : ''); ?>">
          <small class="text-muted">Ex.: INV-0001</small>
        </div>

        <div class="form-group col-md-4">
          <label for="amount">Valor</label>
          <input type="number" id="amount" name="amount" class="form-control" required step="0.01" min="0"
                 value="<?= set_value('amount', $is_edit ? (float)$invoice->amount : ''); ?>">
        </div>

        <div class="form-group col-md-4">
          <label for="contact_id">Contato</label>
          <select id="contact_id" name="contact_id" class="form-control" required>
            <option value="">— selecione —</option>
            <?php foreach ($contacts as $c): ?>
              <option value="<?= (int)$c->id; ?>"
                <?= set_select('contact_id', $c->id, $is_edit && (int)$invoice->contact_id === (int)$c->id); ?>>
                <?= html_escape($c->name); ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>

      <div class="form-group">
        <label for="description">Descrição</label>
        <textarea id="description" name="description" class="form-control" rows="3"
                  placeholder="Opcional"><?= set_value('description', $is_edit ? $invoice->description : ''); ?></textarea>
      </div>

      <div class="mt-3">
        <button class="btn btn-primary"><?= $is_edit ? 'Atualizar' : 'Salvar'; ?></button>
        <a class="btn btn-link" href="<?= site_url('invoices'); ?>">Cancelar</a>
      </div>
    </div>
  </div>
</form>

<?php $this->load->view('layouts/footer'); ?>
