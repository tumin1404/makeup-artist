<?php

namespace App\Console\Commands;

use App\Services\Media\MediaOptimizerService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Throwable;

class OptimizeMediaCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'media:optimize {--type=all : Filter by type: all, images, videos} {--dir= : Specific subfolder in public storage}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scan and optimize existing media files in public storage (WebP images & Silent MP4 micro-videos)';

    /**
     * Execute the console command.
     */
    public function handle(MediaOptimizerService $optimizer): int
    {
        $type = $this->option('type');
        $subDir = $this->option('dir') ?: '';

        $storage = Storage::disk('public');
        $allFiles = $storage->allFiles($subDir);

        $this->info("Scanning public storage... Found " . count($allFiles) . " files.");

        $totalOldBytes = 0;
        $totalNewBytes = 0;
        $processedCount = 0;

        foreach ($allFiles as $file) {
            $mime = $storage->mimeType($file) ?: '';
            $isImage = str_starts_with($mime, 'image/');
            $isVideo = str_starts_with($mime, 'video/');

            if ($type === 'images' && !$isImage) continue;
            if ($type === 'videos' && !$isVideo) continue;
            if (!$isImage && !$isVideo) continue;

            $this->line("Processing: <comment>{$file}</comment> ({$mime})...");

            try {
                $result = $optimizer->optimize($file);
                if (!empty($result['success'])) {
                    $origSize = $result['original_size'] ?? 0;
                    $optSize = $result['optimized_size'] ?? $origSize;
                    $savedPct = $result['saved_percent'] ?? 0;

                    $totalOldBytes += $origSize;
                    $totalNewBytes += $optSize;
                    $processedCount++;

                    // Sync portfolio records if video generated poster
                    if (!empty($result['poster_path'])) {
                        \App\Models\Portfolio::where('file_path', $file)
                            ->whereNull('poster_path')
                            ->update(['poster_path' => $result['poster_path']]);
                    }

                    // Sync database records if image path changed to .webp
                    if (!empty($result['path']) && $result['path'] !== $file) {
                        \App\Models\Banner::where('image_path', $file)->update(['image_path' => $result['path']]);
                        \App\Models\Banner::where('image_mobile_path', $file)->update(['image_mobile_path' => $result['path']]);
                        \App\Models\Expense::where('product_image', $file)->update(['product_image' => $result['path']]);
                        \App\Models\Expense::where('receipt_image', $file)->update(['receipt_image' => $result['path']]);
                        \App\Models\Payment::where('proof_image', $file)->update(['proof_image' => $result['path']]);
                        \App\Models\Portfolio::where('file_path', $file)->update(['file_path' => $result['path']]);
                        \App\Models\Post::where('thumbnail', $file)->update(['thumbnail' => $result['path']]);
                        \App\Models\Setting::where('value', $file)->update(['value' => $result['path']]);
                        \App\Models\User::where('avatar', $file)->update(['avatar' => $result['path']]);
                    }

                    $this->info("  ✓ Optimized: " . number_format($origSize / 1024, 1) . " KB -> " . number_format($optSize / 1024, 1) . " KB (Saved {$savedPct}%)");
                }
            } catch (Throwable $e) {
                $this->error("  ✗ Failed: " . $e->getMessage());
            }
        }

        $totalSavedBytes = max(0, $totalOldBytes - $totalNewBytes);
        $totalSavedMB = round($totalSavedBytes / (1024 * 1024), 2);
        $totalSavedPct = $totalOldBytes > 0 ? round(($totalSavedBytes / $totalOldBytes) * 100, 1) : 0;

        $this->newLine();
        $this->info("==========================================");
        $this->info("🎉 Optimization Complete!");
        $this->info("Total Files Processed : {$processedCount}");
        $this->info("Total Storage Saved   : {$totalSavedMB} MB ({$totalSavedPct}%)");
        $this->info("==========================================");

        return Command::SUCCESS;
    }
}
