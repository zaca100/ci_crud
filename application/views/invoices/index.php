<?php $title = 'Invoices'; $this->load->view('layouts/header'); ?>

<?php if ($this->session->flashdata('success')): ?>
  <div class="alert alert-success"><?= $this->session->flashdata('success'); ?></div>
<?php endif; ?>
<?php if ($this->session->flashdata('error')): ?>
  <div class="alert alert-danger"><?= $this->session->flashdata('error'); ?></div>
<?php endif; ?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h4 mb-0">Invoices</h1>
  <a class="btn btn-primary" href="<?= site_url('invoices/create'); ?>">+ Novo Invoice</a>
</div>

<!-- Busca + Filtro -->
<form class="card mb-3" method="get" action="<?= site_url('invoices'); ?>">
  <div class="card-body">
    <div class="form-row">
      <div class="form-group col-md-6 mb-2">
        <label for="q" class="mb-1">Buscar</label>
        <input type="text" id="q" name="q" class="form-control"
               placeholder="Buscar por código ou descrição"
               value="<?= html_escape($q); ?>">
      </div>

      <div class="form-group col-md-4 mb-2">
        <label for="contact_id" class="mb-1">Filtrar por contato</label>
        <select class="form-control" id="contact_id" name="contact_id">
          <option value="">— Todos —</option>
          <?php foreach ($contacts_map as $id => $name): ?>
            <option value="<?= (int)$id; ?>" <?= ($contact_id == $id ? 'selected' : ''); ?>>
              <?= html_escape($name); ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="form-group col-md-2 mb-2 d-flex align-items-end">
        <button class="btn btn-secondary btn-block" type="submit">Aplicar</button>
      </div>
    </div>

    <!-- preserva ordenação atual ao filtrar/buscar -->
    <input type="hidden" name="sort" value="<?= html_escape($sort); ?>">
    <input type="hidden" name="dir"  value="<?= html_escape($dir); ?>">
  </div>
</form>

<div class="card">
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-striped table-hover mb-0">
        <thead>
          <tr>
            <?php
              // helper inline para link de ordenação preservando filtros atuais
              function inv_sort_link($label, $col, $currSort, $currDir, $q, $contact_id) {
                $dir = ($currSort === $col && $currDir === 'asc') ? 'desc' : 'asc';
                $arrow = '';
                if ($currSort === $col) {
                  $arrow = $currDir === 'asc' ? ' ▲' : ' ▼';
                }
                $params = ['sort'=>$col, 'dir'=>$dir];
                if ($q !== '')                 $params['q'] = $q;
                if (!empty($contact_id))       $params['contact_id'] = $contact_id;
                $url = site_url('invoices') . '?' . http_build_query($params);
                return '<a href="'.$url.'" class="text-nowrap">'.$label.$arrow.'</a>';
              }
            ?>
            <th><?= inv_sort_link('ID', 'id', $sort, $dir, $q, $contact_id); ?></th>
            <th><?= inv_sort_link('Código', 'code', $sort, $dir, $q, $contact_id); ?></th>
            <th>Descrição</th>
            <th class="text-right"><?= inv_sort_link('Valor', 'amount', $sort, $dir, $q, $contact_id); ?></th>
            <th><?= inv_sort_link('Criado em', 'created_at', $sort, $dir, $q, $contact_id); ?></th>
            <th>Contato</th>
            <th class="text-right">Ações</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($invoices)): ?>
            <tr><td colspan="7" class="text-center text-muted p-4">Nenhum invoice encontrado.</td></tr>
          <?php else: foreach ($invoices as $inv): ?>
            <tr>
              <td><?= (int)$inv->id; ?></td>
              <td><?= html_escape($inv->code); ?></td>
              <td><?= html_escape($inv->description); ?></td>
              <td class="text-right">R$ <?= number_format((float)$inv->amount, 2, ',', '.'); ?></td>
              <td><?= html_escape($inv->created_at); ?></td>
              <td><?= isset($contacts_map[$inv->contact_id]) ? html_escape($contacts_map[$inv->contact_id]) : '—'; ?></td>
              <td class="text-right">
                <a class="btn btn-sm btn-secondary" href="<?= site_url('invoices/edit/'.$inv->id); ?>">Editar</a>
                <a class="btn btn-sm btn-outline-danger btn-delete" href="<?= site_url('invoices/delete/'.$inv->id); ?>">Excluir</a>
              </td>
            </tr>
          <?php endforeach; endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<script>
document.addEventListener('click', function(e){
  if (e.target.matches('.btn-delete')) {
    if (!confirm('Tem certeza que deseja excluir este invoice?')) {
      e.preventDefault();
    }
  }
});
</script>

<?php $this->load->view('layouts/footer'); ?>
