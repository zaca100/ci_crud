<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <title><?= isset($title) ? html_escape($title) : 'CRUD - CodeIgniter 3'; ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php if ($this->config->item('csrf_protection')): ?>
    <meta name="csrf-token-name" content="<?= $this->security->get_csrf_token_name(); ?>">
    <meta name="csrf-token-hash" content="<?= $this->security->get_csrf_hash(); ?>">
  <?php endif; ?>

  <!-- Bootstrap CSS (versão estável compatível com jQuery) -->
  <link rel="stylesheet" href="<?= base_url('assets/css/bootstrap.min.css'); ?>">
  
  <style>
    body { padding-top: 24px; }
    .table-actions { white-space: nowrap; }
  </style>
</head>
<body>
<div class="container">

    <!-- Navbar simples -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light mb-4 rounded">
      <a class="navbar-brand" href="<?= site_url('contacts'); ?>">CI CRUD</a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarMain">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div id="navbarMain" class="collapse navbar-collapse">
        <ul class="navbar-nav mr-auto">
          <li class="nav-item">
            <a class="nav-link" href="<?= site_url('contacts'); ?>">Contatos</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?= site_url('contacts/create'); ?>">Novo Contato</a>
          </li>
        </ul>
      </div>
    </nav>

    <!-- Área global de mensagens -->
    <?php if ($this->session->flashdata('success')): ?>
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= html_escape($this->session->flashdata('success')); ?>
        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
      </div>
    <?php endif; ?>

    <?php if ($this->session->flashdata('error')): ?>
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= html_escape($this->session->flashdata('error')); ?>
        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
      </div>
    <?php endif; ?>