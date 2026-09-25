<?php

namespace App\Jobs;

use App\Services\Media\MediaOptimizerService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class CompressImageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public int $tries = 3;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public int $timeout = 120;

    /**
     * Create a new job instance.
     *
     * @param string $relativePath Relative path within the storage public disk (e.g. 'posts/image.jpg')
     * @param string|null $modelClass Optional Model class to update with new path
     * @param int|null $modelId Optional Model ID
     * @param string|null $columnName Optional column name to update
     */
    public function __construct(
        public string $relativePath,
        public ?string $modelClass = null,
        public ?int $modelId = null,
        public ?string $columnName = null,
    ) {
    }

    /**
     * Execute the job using self-contained MediaOptimizerService.
     */
    public function handle(MediaOptimizerService $optimizer): void
    {
        try {
            $result = $optimizer->optimize($this->relativePath);
            
            if (!empty($result['success'])) {
                if ($this->modelClass && $this->modelId && $this->columnName && !empty($result['path'])) {
                    $model = $this->modelClass::find($this->modelId);
                    if ($model && $model->{$this->columnName} !== $result['path']) {
                        $model->{$this->columnName} = $result['path'];
                        $model->saveQuietly();
                    }
                }

                Log::info('MediaOptimizer Job: Successfully optimized media file', [
                    'path' => $this->relativePath,
                    'result' => $result,
                ]);
            } else {
                Log::warning('MediaOptimizer Job: Could not optimize file: ' . ($result['message'] ?? 'Unknown reason'), [
                    'path' => $this->relativePath,
                ]);
            }
        } catch (Throwable $e) {
            Log::error('MediaOptimizer Job: Failed to process media: ' . $e->getMessage(), [
                'path' => $this->relativePath,
                'exception' => $e,
            ]);
        }
    }
}
