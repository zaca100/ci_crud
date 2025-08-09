<?php $title = 'Lista de Contatos'; $this->load->view('layouts/header'); ?>

<div class="d-flex align-items-center justify-content-between mb-3">
  <h1 class="h3 mb-0">Contatos</h1>
  <a href="<?= site_url('contacts/create'); ?>" class="btn btn-primary">
    + Novo Contato
  </a>
</div>

<div class="card">
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-striped table-hover mb-0">
        <thead class="thead-light">
          <tr>
            <th style="width:80px;">ID</th>
            <th>Nome</th>
            <th>Email</th>
            <th>Telefone</th>
            <th class="text-right" style="width:180px;">Ações</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($contacts)): ?>
            <?php foreach ($contacts as $c): ?>
              <tr>
                <td><?= (int) $c->id; ?></td>
                <td><?= html_escape($c->name); ?></td>
                <td>
                  <a href="mailto:<?= html_escape($c->email); ?>">
                    <?= html_escape($c->email); ?>
                  </a>
                </td>
                <td><?= html_escape($c->phone); ?></td>
                <td class="text-right table-actions">
                  <a class="btn btn-sm btn-secondary"
                     href="<?= site_url('contacts/edit/' . (int)$c->id); ?>">
                    Editar
                  </a>
                  <a class="btn btn-sm btn-outline-danger btn-delete"
                     href="<?= site_url('contacts/delete/' . (int)$c->id); ?>">
                    Excluir
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="5" class="text-center text-muted">
                Nenhum contato cadastrado ainda.
              </td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php $this->load->view('layouts/footer'); ?>