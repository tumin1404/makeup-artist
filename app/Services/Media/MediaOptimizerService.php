<?php

namespace App\Services\Media;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Format;
use Symfony\Component\Process\Process;
use Throwable;

class MediaOptimizerService
{
    /**
     * Default public storage disk.
     */
    protected string $disk = 'public';

    /**
     * Image manager instance.
     */
    protected ?ImageManager $imageManager = null;

    /**
     * Get or create ImageManager instance.
     */
    public function getImageManager(): ImageManager
    {
        if ($this->imageManager === null) {
            $this->imageManager = new ImageManager(new Driver());
        }

        return $this->imageManager;
    }

    /**
     * Locate available FFmpeg executable path.
     */
    public function getFFmpegBinaryPath(): ?string
    {
        // 1. Check custom configured path from env
        $envPath = env('FFMPEG_PATH');
        if (!empty($envPath) && file_exists($envPath)) {
            return $envPath;
        }

        // 2. Check bundled project bin directory
        $isWindows = PHP_OS_FAMILY === 'Windows';
        $bundledPath = $isWindows 
            ? base_path('bin/windows/ffmpeg.exe')
            : base_path('bin/linux/ffmpeg');

        if (file_exists($bundledPath)) {
            return $bundledPath;
        }

        // 3. Check storage/app/bin/
        $storageBinPath = $isWindows
            ? storage_path('app/bin/ffmpeg.exe')
            : storage_path('app/bin/ffmpeg');

        if (file_exists($storageBinPath)) {
            return $storageBinPath;
        }

        // 4. Check system PATH
        $lookupCmd = $isWindows ? ['where', 'ffmpeg'] : ['which', 'ffmpeg'];
        try {
            $process = new Process($lookupCmd);
            $process->run();
            if ($process->isSuccessful()) {
                $output = trim(explode("\n", trim($process->getOutput()))[0]);
                if (!empty($output) && file_exists($output)) {
                    return $output;
                }
            }
        } catch (Throwable) {
            // Silently ignore
        }

        return null;
    }

    /**
     * Check if FFmpeg is available.
     */
    public function isFFmpegAvailable(): bool
    {
        return $this->getFFmpegBinaryPath() !== null;
    }

    /**
     * Optimize image file and convert to lightweight WebP format.
     *
     * @param string $relativePath Relative path in public storage disk (e.g. 'portfolios/photo.jpg')
     * @param array $options Options: maxWidth, maxHeight, quality, keepOriginal
     * @return array Result metadata
     */
    public function optimizeImage(string $relativePath, array $options = []): array
    {
        $maxWidth = $options['maxWidth'] ?? 1920;
        $maxHeight = $options['maxHeight'] ?? null;
        $quality = $options['quality'] ?? 80;
        $keepOriginal = $options['keepOriginal'] ?? false;

        $storage = Storage::disk($this->disk);

        if (!$storage->exists($relativePath)) {
            return [
                'success' => false,
                'message' => 'Image file not found: ' . $relativePath,
                'path' => $relativePath,
            ];
        }

        $origSize = $storage->size($relativePath);
        $binary = $storage->get($relativePath);

        // Check if file is supported image
        $mime = '';
        try {
            $mime = $storage->mimeType($relativePath) ?: '';
        } catch (Throwable) {
            $mime = '';
        }

        $extension = strtolower(pathinfo($relativePath, PATHINFO_EXTENSION));

        if (str_contains($mime, 'svg') || $extension === 'svg' || (!str_starts_with($mime, 'image/') && !in_array($extension, ['jpg', 'jpeg', 'png', 'webp', 'gif', 'bmp']))) {
            return [
                'success' => true,
                'path' => $relativePath,
                'mime' => $mime,
                'original_size' => $origSize,
                'optimized_size' => $origSize,
                'saved_percent' => 0,
                'message' => 'SVG or non-raster image preserved without conversion.',
            ];
        }

        try {
            $pathInfo = pathinfo($relativePath);
            $targetDir = ($pathInfo['dirname'] !== '.' && $pathInfo['dirname'] !== '') ? $pathInfo['dirname'] : '';
            $filename = $pathInfo['filename'];
            $newRelativePath = ($targetDir ? $targetDir . '/' : '') . $filename . '.webp';

            $encodedBinary = null;

            // Attempt optimization with Intervention Image
            try {
                $manager = $this->getImageManager();
                $img = $manager->decode($binary);
                
                // Auto orient based on EXIF
                try {
                    $img->orient();
                } catch (Throwable) {
                    // Ignore orientation if EXIF missing
                }

                // Scale down if dimensions exceed bounds
                if ($maxWidth || $maxHeight) {
                    $img->scaleDown(width: $maxWidth, height: $maxHeight);
                }

                // Encode and save as WebP
                $encoded = $img->encodeUsingFormat(Format::WEBP, quality: $quality);
                $encodedBinary = (string) $encoded;
            } catch (Throwable $e) {
                // Fallback to Native PHP GD
                $encodedBinary = $this->nativeGdWebpConvertFromBinary($binary, $maxWidth, $maxHeight, $quality);
            }

            if ($encodedBinary !== null) {
                $storage->put($newRelativePath, $encodedBinary);

                $newSize = strlen($encodedBinary);

                // If path changed (e.g. .jpg -> .webp), remove old file if not keeping original
                if (!$keepOriginal && $relativePath !== $newRelativePath) {
                    $storage->delete($relativePath);
                }

                $savedBytes = max(0, $origSize - $newSize);
                $savedPct = $origSize > 0 ? round(($savedBytes / $origSize) * 100, 1) : 0;

                Log::info("MediaOptimizer: Image optimized to WebP", [
                    'old_path' => $relativePath,
                    'new_path' => $newRelativePath,
                    'old_size' => $origSize,
                    'new_size' => $newSize,
                    'saved_pct' => $savedPct . '%',
                ]);

                return [
                    'success' => true,
                    'path' => $newRelativePath,
                    'original_size' => $origSize,
                    'optimized_size' => $newSize,
                    'saved_percent' => $savedPct,
                    'mime' => 'image/webp',
                ];
            }

            return [
                'success' => false,
                'path' => $relativePath,
                'message' => 'Failed to encode image to WebP.',
            ];
        } catch (Throwable $e) {
            Log::error("MediaOptimizer: Image optimization failed: " . $e->getMessage(), [
                'path' => $relativePath,
                'exception' => $e,
            ]);

            return [
                'success' => false,
                'path' => $relativePath,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Native PHP GD fallback image converter from binary to WebP binary.
     */
    protected function nativeGdWebpConvertFromBinary(string $binary, ?int $maxWidth, ?int $maxHeight, int $quality): ?string
    {
        $srcImg = @imagecreatefromstring($binary);
        if (!$srcImg) {
            return null;
        }

        $origW = imagesx($srcImg);
        $origH = imagesy($srcImg);

        $newW = $origW;
        $newH = $origH;

        if ($maxWidth && $newW > $maxWidth) {
            $newH = (int) round(($maxWidth / $newW) * $newH);
            $newW = $maxWidth;
        }

        if ($maxHeight && $newH > $maxHeight) {
            $newW = (int) round(($maxHeight / $newH) * $newW);
            $newH = $maxHeight;
        }

        $destImg = imagecreatetruecolor($newW, $newH);
        imagealphablending($destImg, false);
        imagesavealpha($destImg, true);
        imagecopyresampled($destImg, $srcImg, 0, 0, 0, 0, $newW, $newH, $origW, $origH);

        ob_start();
        imagewebp($destImg, null, $quality);
        $output = ob_get_clean();

        imagedestroy($srcImg);
        imagedestroy($destImg);

        return $output ?: null;
    }

    /**
     * Optimize video into ultra-lightweight silent micro-video MP4 (GIF replacement)
     * and extract a crisp WebP poster frame.
     *
     * @param string $relativePath Relative path in public storage disk (e.g. 'portfolios/video.mp4')
     * @param array $options Options: maxDimension (default 720), fps (default 24), crf (default 30)
     * @return array Result metadata
     */
    public function optimizeVideo(string $relativePath, array $options = []): array
    {
        $maxDimension = $options['maxDimension'] ?? ($options['maxHeight'] ?? 720);
        $fps = $options['fps'] ?? 24;
        $crf = $options['crf'] ?? 30;
        $generatePoster = $options['generatePoster'] ?? true;

        $storage = Storage::disk($this->disk);

        if (!$storage->exists($relativePath)) {
            return [
                'success' => false,
                'message' => 'Video file not found: ' . $relativePath,
                'video_path' => $relativePath,
                'poster_path' => null,
            ];
        }

        $fullPath = $storage->path($relativePath);
        $origSize = filesize($fullPath);

        $ffmpeg = $this->getFFmpegBinaryPath();
        if (!$ffmpeg) {
            Log::warning("MediaOptimizer: FFmpeg binary not found. Video stored without transcoding.", [
                'path' => $relativePath,
            ]);

            return [
                'success' => false,
                'message' => 'FFmpeg binary not available. Stored original video.',
                'video_path' => $relativePath,
                'poster_path' => null,
                'original_size' => $origSize,
                'optimized_size' => $origSize,
            ];
        }

        try {
            $pathInfo = pathinfo($relativePath);
            $targetDir = ($pathInfo['dirname'] !== '.' && $pathInfo['dirname'] !== '') ? $pathInfo['dirname'] : '';
            $filename = $pathInfo['filename'];

            $tmpOptimizedRelative = ($targetDir ? $targetDir . '/' : '') . $filename . '_silent.mp4';
            $tmpOptimizedFull = $storage->path($tmpOptimizedRelative);

            $posterRelative = ($targetDir ? $targetDir . '/' : '') . $filename . '_poster.webp';
            $posterFull = $storage->path($posterRelative);

            // 1. Adaptive Scaling Filter: Scale max dimension (width for landscape, height for portrait/square)
            $dim = intval($maxDimension);
            $scaleFilter = "scale='if(gt(iw,ih),min({$dim},iw),-2)':'if(gt(iw,ih),-2,min({$dim},ih))',fps=" . intval($fps);

            $videoCmd = [
                $ffmpeg,
                '-y',
                '-i', $fullPath,
                '-an', // Strip audio track completely for silent micro-video
                '-vf', $scaleFilter,
                '-c:v', 'libx264',
                '-crf', (string) $crf,
                '-preset', 'medium',
                '-b:v', '800k',
                '-maxrate', '1200k',
                '-bufsize', '2400k',
                '-pix_fmt', 'yuv420p',
                '-movflags', '+faststart',
                $tmpOptimizedFull,
            ];

            $process = new Process($videoCmd);
            $process->setTimeout(300);
            $process->run();

            if (!$process->isSuccessful() || !file_exists($tmpOptimizedFull) || filesize($tmpOptimizedFull) === 0) {
                throw new \RuntimeException('FFmpeg video transcoding failed: ' . $process->getErrorOutput());
            }

            // 2. Extract WebP poster frame from the video at 0.1s
            if ($generatePoster) {
                $posterScaleFilter = "scale='if(gt(iw,ih),min({$dim},iw),-2)':'if(gt(iw,ih),-2,min({$dim},ih))'";
                $posterCmd = [
                    $ffmpeg,
                    '-y',
                    '-ss', '00:00:00.100',
                    '-i', $fullPath,
                    '-vframes', '1',
                    '-vf', $posterScaleFilter,
                    '-q:v', '2',
                    $posterFull,
                ];

                $posterProcess = new Process($posterCmd);
                $posterProcess->setTimeout(60);
                $posterProcess->run();
            }

            // Replace original video file with the optimized micro-video
            @unlink($fullPath);
            rename($tmpOptimizedFull, $fullPath);

            clearstatcache(true, $fullPath);
            $newSize = filesize($fullPath);
            $savedBytes = max(0, $origSize - $newSize);
            $savedPct = $origSize > 0 ? round(($savedBytes / $origSize) * 100, 1) : 0;

            $hasPoster = file_exists($posterFull) && filesize($posterFull) > 0;

            Log::info("MediaOptimizer: Silent micro-video created successfully", [
                'path' => $relativePath,
                'poster_path' => $hasPoster ? $posterRelative : null,
                'old_size' => $origSize,
                'new_size' => $newSize,
                'saved_pct' => $savedPct . '%',
            ]);

            return [
                'success' => true,
                'video_path' => $relativePath,
                'poster_path' => $hasPoster ? $posterRelative : null,
                'original_size' => $origSize,
                'optimized_size' => $newSize,
                'saved_percent' => $savedPct,
            ];
        } catch (Throwable $e) {
            Log::error("MediaOptimizer: Video optimization failed: " . $e->getMessage(), [
                'path' => $relativePath,
                'exception' => $e,
            ]);

            return [
                'success' => false,
                'video_path' => $relativePath,
                'poster_path' => null,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Extract a WebP poster frame from an existing video file on demand.
     */
    public function extractPoster(string $relativePath, int $maxDimension = 720): ?string
    {
        $storage = Storage::disk($this->disk);
        if (!$storage->exists($relativePath)) {
            return null;
        }

        $ffmpeg = $this->getFFmpegBinaryPath();
        if (!$ffmpeg) {
            return null;
        }

        try {
            $pathInfo = pathinfo($relativePath);
            $targetDir = ($pathInfo['dirname'] !== '.' && $pathInfo['dirname'] !== '') ? $pathInfo['dirname'] : '';
            $filename = $pathInfo['filename'];
            $posterRelative = ($targetDir ? $targetDir . '/' : '') . $filename . '_poster.webp';
            $posterFull = $storage->path($posterRelative);

            if (file_exists($posterFull) && filesize($posterFull) > 0) {
                return $posterRelative;
            }

            $fullPath = $storage->path($relativePath);
            $posterScaleFilter = "scale='if(gt(iw,ih),min({$maxDimension},iw),-2)':'if(gt(iw,ih),-2,min({$maxDimension},ih))'";
            $posterCmd = [
                $ffmpeg,
                '-y',
                '-ss', '00:00:00.100',
                '-i', $fullPath,
                '-vframes', '1',
                '-vf', $posterScaleFilter,
                '-q:v', '2',
                $posterFull,
            ];

            $process = new Process($posterCmd);
            $process->setTimeout(30);
            $process->run();

            if (file_exists($posterFull) && filesize($posterFull) > 0) {
                return $posterRelative;
            }
        } catch (Throwable $e) {
            Log::warning("MediaOptimizer: extractPoster failed for {$relativePath}: " . $e->getMessage());
        }

        return null;
    }

    /**
     * Auto-detect file type and process accordingly.
     */
    public function optimize(string $relativePath, array $options = []): array
    {
        $storage = Storage::disk($this->disk);
        if (!$storage->exists($relativePath)) {
            return ['success' => false, 'message' => 'File not found'];
        }

        $fullPath = $storage->path($relativePath);
        $mime = mime_content_type($fullPath) ?: '';

        if (str_starts_with($mime, 'image/')) {
            return $this->optimizeImage($relativePath, $options);
        }

        if (str_starts_with($mime, 'video/')) {
            return $this->optimizeVideo($relativePath, $options);
        }

        return ['success' => true, 'path' => $relativePath, 'message' => 'Uncompressed file type preserved.'];
    }
}
