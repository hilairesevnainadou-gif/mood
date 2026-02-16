<?php

namespace App\Jobs;

use App\Models\Document;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class BulkValidateDocuments implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $userId;
    protected $validatorId;

    public function __construct($userId, $validatorId)
    {
        $this->userId = $userId;
        $this->validatorId = $validatorId;
    }

    public function handle()
    {
        Document::where('user_id', $this->userId)
            ->where('status', '!=', 'validated')
            ->select(['id', 'status'])
            ->chunkById(50, function ($documents) {
                foreach ($documents as $doc) {
                    try {
                        $doc->validateDocument($this->validatorId);
                    } catch (\Exception $e) {
                        Log::error('Job validation error doc ' . $doc->id . ': ' . $e->getMessage());
                    }
                }
            });

        \Illuminate\Support\Facades\Cache::forget('document_stats');
    }
}
