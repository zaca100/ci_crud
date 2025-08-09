 <!-- Scripts JS no final p/ carregar mais rápido -->

<script src="<?= base_url('assets/js/jquery.min.js'); ?>"></script>
<script src="<?= base_url('assets/js/bootstrap.bundle.min.js'); ?>"></script>

    <!-- Modal de confirmação -->
    <div class="modal fade" id="confirmModal" tabindex="-1" role="dialog" aria-labelledby="confirmTitle" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="confirmTitle">Confirmar exclusão</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            Tem certeza que deseja excluir este contato? Esta ação não pode ser desfeita.
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancelar</button>
            <button id="btnConfirmDelete" type="button" class="btn btn-danger">Excluir</button>
          </div>
        </div>
      </div>
    </div>

    <script>
      (function() {
        var pendingDeleteHref = null;
        var pendingRow = null;

        // Lê CSRF (se estiver ativo)
        var csrfName = $('meta[name="csrf-token-name"]').attr('content') || null;
        var csrfHash = $('meta[name="csrf-token-hash"]').attr('content') || null;

        // Abre modal e guarda referências
        $(document).on('click', '.btn-delete', function(e) {
          e.preventDefault();
          pendingDeleteHref = $(this).attr('href');
          pendingRow = $(this).closest('tr');
          $('#confirmModal').modal('show');
        });

        // Confirma (AJAX)
        $('#btnConfirmDelete').on('click', function() {
          if (!pendingDeleteHref) return;

          // Se quiser manter fallback (sem AJAX), descomente a linha abaixo:
          // window.location.href = pendingDeleteHref; return;

          // Fazemos POST para a rota de delete (mais seguro que GET)
          // Vamos converter a rota /contacts/delete/{id} em uma chamada POST
          // Ex.: /contacts/delete/5  -> POST com CSRF (se ativo)
          $.ajax({
            url: pendingDeleteHref,
            type: 'POST',
            data: (csrfName && csrfHash) ? { [csrfName]: csrfHash } : {},
            success: function(resp) {
              // Remove a linha da tabela (UX)
              if (pendingRow) {
                pendingRow.fadeOut(200, function() { $(this).remove(); });
              }
              $('#confirmModal').modal('hide');

              // Atualiza token CSRF se o servidor devolveu um novo (opcional)
              try {
                var data = (typeof resp === 'string') ? JSON.parse(resp) : resp;
                if (data && data.csrf && data.csrf.name && data.csrf.hash) {
                  $('meta[name="csrf-token-name"]').attr('content', data.csrf.name);
                  $('meta[name="csrf-token-hash"]').attr('content', data.csrf.hash);
                }
              } catch (e) {
                // se não for JSON, ignore
              }
            },
            error: function() {
              alert('Falha ao excluir. Tente novamente.');
            }
          });
        });
      })();
    </script>

  </div> <!-- /.container -->
</body>
</html>
