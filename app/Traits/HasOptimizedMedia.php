<?php

namespace App\Traits;

use App\Services\Media\MediaOptimizerService;
use Illuminate\Support\Facades\Schema;
use Throwable;

trait HasOptimizedMedia
{
    /**
     * Boot the trait and register Eloquent saved hook.
     */
    public static function bootHasOptimizedMedia(): void
    {
        static::saved(function ($model) {
            $model->optimizeMediaAttributes();
        });
    }

    /**
     * Optimize all declared media attributes on the model.
     */
    public function optimizeMediaAttributes(): void
    {
        $mediaAttributes = method_exists($this, 'getMediaOptimizationAttributes')
            ? $this->getMediaOptimizationAttributes()
            : [];

        if (empty($mediaAttributes)) {
            return;
        }

        $optimizer = app(MediaOptimizerService::class);
        $hasChanges = false;

        foreach ($mediaAttributes as $column => $options) {
            if (is_int($column)) {
                $column = $options;
                $options = [];
            }

            $currentPath = $this->getAttribute($column);
            if (empty($currentPath) || !is_string($currentPath)) {
                continue;
            }

            $isVideo = ($this->getAttribute('type') === 'video') || 
                in_array(strtolower(pathinfo($currentPath, PATHINFO_EXTENSION)), ['mp4', 'mov', 'avi', 'webm', 'mkv']);

            $needsOptimization = $this->wasRecentlyCreated || $this->wasChanged($column);
            $needsPoster = $isVideo && Schema::hasColumn($this->getTable(), 'poster_path') && empty($this->getAttribute('poster_path'));

            if (!$needsOptimization && !$needsPoster) {
                continue;
            }

            try {
                if ($isVideo) {
                    $result = $optimizer->optimizeVideo($currentPath, $options);
                    if (!empty($result['success']) && !empty($result['poster_path'])) {
                        // Check if model has poster_path column in table
                        if (Schema::hasColumn($this->getTable(), 'poster_path')) {
                            $this->setAttribute('poster_path', $result['poster_path']);
                            $hasChanges = true;
                        }
                    }
                } else {
                    $result = $optimizer->optimizeImage($currentPath, $options);
                    if (!empty($result['success']) && !empty($result['path']) && $result['path'] !== $currentPath) {
                        $this->setAttribute($column, $result['path']);
                        $hasChanges = true;
                    }
                }
            } catch (Throwable $e) {
                // Ignore failure and preserve original file
            }
        }

        if ($hasChanges) {
            $this->saveQuietly();
        }
    }
}
