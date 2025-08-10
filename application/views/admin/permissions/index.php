<?php $title='Admin • Permissões'; $this->load->view('layouts/header'); ?>

<h1 class="h4 mb-3">Permissões</h1>

<div class="card">
  <div class="card-body">
    <h5>Papéis</h5>
    <ul>
      <?php foreach($roles as $r): ?>
        <li><a href="<?= site_url('admin/permissions/role/'.$r->id); ?>"><?= html_escape($r->name); ?></a></li>
      <?php endforeach; ?>
    </ul>

    <h5 class="mt-4">Overrides por usuário</h5>
    <form class="form-inline" action="<?= site_url('admin/permissions/user'); ?>" method="get" onsubmit="event.preventDefault(); var id=document.getElementById('uid').value; if(id){ window.location='<?= site_url('admin/permissions/user'); ?>/'+id; }">
      <div class="form-group">
        <label class="mr-2">ID do usuário</label>
        <input id="uid" type="number" class="form-control mr-2" min="1" placeholder="ex.: 1">
      </div>
      <button class="btn btn-secondary">Abrir</button>
    </form>
  </div>
</div>

<?php $this->load->view('layouts/footer'); ?>
