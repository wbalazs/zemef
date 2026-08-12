<!-- Confirmation Modal -->
<div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="confirmationModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="confirmationModalLabel">Ékszer vásárlás</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="importModalData">
        @include('item_lists.edit', ['item' => $itemList->import['rows']])
      </div>
      <div id="modal-footer">
        <inpu type="hidden" id="modal_itemlist_id">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Elvet</button>
        <button type="button" class="btn btn-primary" id="importModalYes">Mentés</button>
      </div>
    </div>
  </div>
</div>