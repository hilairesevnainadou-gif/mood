<div id="rejectModal" class="fixed inset-0 z-50 hidden">
    <div class="modal-overlay" onclick="closeModal('rejectModal')"></div>
    <div class="modal-content p-6">
        <div class="flex justify-between items-center mb-4">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-red-500">block</span>
                <h3 class="text-lg font-bold text-slate-900">Rejeter la demande</h3>
            </div>
            <button onclick="closeModal('rejectModal')" class="p-1 hover:bg-slate-100 rounded-full transition-colors">
                <span class="material-symbols-outlined text-slate-400">close</span>
            </button>
        </div>

        <form action="{{ route('admin.funding.reject-request', $request->id) }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-xs font-bold text-slate-500 uppercase mb-2">
                    Motif du rejet <span class="text-red-500">*</span>
                </label>
                <textarea name="rejection_reason" rows="4" required minlength="10"
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 text-sm input-focus resize-none"
                    placeholder="Expliquez la raison du rejet..."></textarea>
                <p class="text-[10px] text-slate-400 mt-2">Ce motif sera communiqué au client.</p>
            </div>

            <div class="flex gap-3">
                <button type="button" onclick="closeModal('rejectModal')" class="flex-1 py-3 rounded-xl border border-slate-200 text-slate-700 text-sm font-bold hover:bg-slate-50 transition-colors">
                    Annuler
                </button>
                <button type="submit" class="flex-1 py-3 rounded-xl bg-red-500 text-white text-sm font-bold shadow-lg shadow-red-500/20 hover:bg-red-600 active:scale-95 transition-all">
                    Confirmer le rejet
                </button>
            </div>
        </form>
    </div>
</div>
