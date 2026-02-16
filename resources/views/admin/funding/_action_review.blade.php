<section class="info-card border-l-4 border-blue-500">
    <div class="flex items-center gap-3 mb-3">
        <span class="material-symbols-outlined text-blue-600">search</span>
        <h3 class="text-sm font-bold text-slate-900">Mettre en étude</h3>
    </div>
    <p class="text-xs text-slate-600 mb-4">Cette demande nécessite une analyse approfondie.</p>

    <form action="{{ route('admin.funding.under-review', $request->id) }}" method="POST">
        @csrf
        <button type="submit" class="w-full py-3 rounded-xl bg-blue-600 text-white text-sm font-bold shadow-lg shadow-blue-900/20 hover:bg-blue-700 active:scale-95 transition-all flex items-center justify-center gap-2">
            <span class="material-symbols-outlined text-sm">play_arrow</span>
            Commencer l'étude
        </button>
    </form>
</section>
