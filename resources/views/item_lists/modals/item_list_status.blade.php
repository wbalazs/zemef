<!-- Confirmation Modal -->
<div class="modal fade" id="itemlistStatusModal" tabindex="-1" role="dialog" aria-labelledby="confirmationModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="confirmationModalLabel">Státusz módosítás</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p id="itemlistStatusModalText">Biztosan le akarja zárni?</p>
      </div>
      <div id="modal-footer">
        <input type="hidden" id="modal_itemlist_id">
        <input type="hidden" id="modal_item_sor">
        <input type="hidden" id="modal_new_status" value="1">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Nem</button>
        <button type="button" class="btn btn-primary" id="itemlistStatusModalYes">Igen</button>
      </div>
    </div>
  </div>
</div>