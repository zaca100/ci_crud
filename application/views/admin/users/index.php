<?php $title='Admin • Usuários'; $this->load->view('layouts/header'); ?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h4 mb-0">Usuários</h1>
  <a class="btn btn-primary" href="<?= site_url('admin/users/create'); ?>">+ Novo</a>
</div>

<div class="card">
  <div class="card-body p-0">
    <table class="table table-striped mb-0">
      <thead><tr>
        <th>ID</th><th>Nome</th><th>E-mail</th><th>Provider</th><th>Ações</th>
      </tr></thead>
      <tbody>
      <?php foreach($users as $u): ?>
        <tr>
          <td><?= (int)$u->id; ?></td>
          <td><?= html_escape($u->name); ?></td>
          <td><?= html_escape($u->email); ?></td>
          <td><?= html_escape($u->provider); ?></td>
          <td class="table-actions text-nowrap">
            <a class="btn btn-sm btn-secondary" href="<?= site_url('admin/users/edit/'.$u->id); ?>">Editar</a>
            <a class="btn btn-sm btn-outline-danger btn-delete" href="<?= site_url('admin/users/delete/'.$u->id); ?>">Excluir</a>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php $this->load->view('layouts/footer'); ?>
