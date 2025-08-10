<?php $title='Admin • Features'; $this->load->view('layouts/header'); ?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h4 mb-0">Funcionalidades</h1>
  <a class="btn btn-primary" href="<?= site_url('admin/features/create'); ?>">+ Nova</a>
</div>

<div class="card">
  <div class="card-body p-0">
    <table class="table table-striped mb-0">
      <thead><tr>
        <th>ID</th><th>Nome</th><th>Slug</th><th>Controller</th><th>Método</th><th>Menu</th><th>Ativo</th><th>Ordem</th><th>Ações</th>
      </tr></thead>
      <tbody>
      <?php foreach($features as $f): ?>
        <tr>
          <td><?= (int)$f->id; ?></td>
          <td><?= html_escape($f->name); ?></td>
          <td><?= html_escape($f->slug); ?></td>
          <td><?= html_escape($f->controller); ?></td>
          <td><?= html_escape($f->method); ?></td>
          <td><?= $f->is_menu ? 'Sim' : 'Não'; ?></td>
          <td><?= $f->is_active ? 'Sim' : 'Não'; ?></td>
          <td><?= (int)$f->sort_order; ?></td>
          <td class="table-actions text-nowrap">
            <a class="btn btn-sm btn-secondary" href="<?= site_url('admin/features/edit/'.$f->id); ?>">Editar</a>
            <a class="btn btn-sm btn-outline-danger btn-delete" href="<?= site_url('admin/features/delete/'.$f->id); ?>">Excluir</a>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php $this->load->view('layouts/footer'); ?>
