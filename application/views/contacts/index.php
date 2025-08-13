<?php $title = 'Contatos'; $this->load->view('layouts/header'); ?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h4 mb-0">Contatos</h1>
  <a class="btn btn-primary" href="<?= site_url('contacts/create'); ?>">+ Novo Contato</a>
</div>

<!-- Busca -->
<form class="card mb-3" method="get" action="<?= site_url('contacts'); ?>">
  <div class="card-body">
    <div class="form-row">
      <div class="form-group col-md-8 mb-2">
        <label class="sr-only" for="q">Buscar</label>
        <input type="text" class="form-control" id="q" name="q"
               placeholder="Buscar por nome, e-mail ou telefone"
               value="<?= html_escape($q); ?>">
      </div>
      <div class="form-group col-md-2 mb-2">
        <button class="btn btn-secondary btn-block">Buscar</button>
      </div>
      <div class="form-group col-md-2 mb-2">
        <a class="btn btn-light btn-block" href="<?= site_url('contacts'); ?>">Limpar</a>
      </div>
    </div>

    <!-- preserva ordenação atual ao fazer nova busca -->
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
              // helper inline para links de ordenação
              function sort_link_simple($label, $col, $currSort, $currDir, $q) {
                $dir = ($currSort === $col && $currDir === 'asc') ? 'desc' : 'asc';
                $arrow = '';
                if ($currSort === $col) {
                  $arrow = $currDir === 'asc' ? ' ▲' : ' ▼';
                }
                $params = http_build_query(['q' => $q, 'sort' => $col, 'dir' => $dir]);
                $url = site_url('contacts') . '?' . $params;
                return '<a href="'.$url.'" class="text-nowrap">'.$label.$arrow.'</a>';
              }
            ?>
            <th><?= sort_link_simple('ID', 'id', $sort, $dir, $q); ?></th>
            <th><?= sort_link_simple('Nome', 'name', $sort, $dir, $q); ?></th>
            <th><?= sort_link_simple('E-mail', 'email', $sort, $dir, $q); ?></th>
            <th><?= sort_link_simple('Telefone', 'phone', $sort, $dir, $q); ?></th>
            <th class="text-right">Ações</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($contacts)): ?>
            <tr><td colspan="5" class="text-center text-muted p-4">Nenhum contato encontrado.</td></tr>
          <?php else: foreach ($contacts as $c): ?>
            <tr>
              <td><?= (int)$c->id; ?></td>
              <td><?= html_escape($c->name); ?></td>
              <td><a href="mailto:<?= html_escape($c->email); ?>"><?= html_escape($c->email); ?></a></td>
              <td><?= html_escape($c->phone); ?></td>
              <td class="text-right">
                <a class="btn btn-sm btn-secondary" href="<?= site_url('contacts/edit/'.$c->id); ?>">Editar</a>
                <a class="btn btn-sm btn-outline-danger btn-delete" href="<?= site_url('contacts/delete/'.$c->id); ?>">Excluir</a>
              </td>
            </tr>
          <?php endforeach; endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <div class="card-footer d-flex justify-content-between align-items-center">
    <small class="text-muted mb-0">
      Total: <?= (int)$total; ?> registro(s)
    </small>
    <small class="text-muted mb-0">
      Ordenado por: <strong><?= html_escape($sort); ?></strong> (<?= $dir === 'asc' ? 'asc' : 'desc'; ?>)
    </small>
  </div>
</div>

<?php $this->load->view('layouts/footer'); ?>
