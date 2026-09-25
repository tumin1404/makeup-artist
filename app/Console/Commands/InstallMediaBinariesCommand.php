<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;
use Throwable;

class InstallMediaBinariesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'media:install-binaries {--force : Overwrite existing binaries}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Download and setup standalone static FFmpeg binary in project bin/ directory for 100% portable deployment';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $isWindows = PHP_OS_FAMILY === 'Windows';
        $binDir = base_path('bin/' . ($isWindows ? 'windows' : 'linux'));
        $targetFile = $binDir . '/' . ($isWindows ? 'ffmpeg.exe' : 'ffmpeg');

        if (!File::exists($binDir)) {
            File::makeDirectory($binDir, 0755, true);
        }

        if (File::exists($targetFile) && !$this->option('force')) {
            $this->info("✅ Static FFmpeg binary already present at: {$targetFile}");
            return Command::SUCCESS;
        }

        $this->info("Downloading standalone static FFmpeg for " . ($isWindows ? 'Windows' : 'Linux') . "...");

        try {
            if ($isWindows) {
                // Windows static binary download (standalone ~25MB build)
                $url = 'https://github.com/ffbinaries/ffbinaries-prebuilt/releases/download/v6.1/ffmpeg-6.1-win-64.zip';
                $zipPath = $binDir . '/ffmpeg.zip';

                $this->info("Downloading from {$url}...");
                file_put_contents($zipPath, fopen($url, 'r'));

                $this->info("Extracting ffmpeg.exe...");
                $zip = new \ZipArchive();
                if ($zip->open($zipPath) === true) {
                    for ($i = 0; $i < $zip->numFiles; $i++) {
                        $filename = $zip->getNameIndex($i);
                        if (basename($filename) === 'ffmpeg.exe') {
                            copy("zip://" . $zipPath . "#" . $filename, $targetFile);
                            break;
                        }
                    }
                    $zip->close();
                }
                @unlink($zipPath);
            } else {
                // Linux x86_64 static binary download (~25MB)
                $url = 'https://github.com/ffbinaries/ffbinaries-prebuilt/releases/download/v6.1/ffmpeg-6.1-linux-64.zip';
                $zipPath = $binDir . '/ffmpeg.zip';

                $this->info("Downloading Linux static binary from {$url}...");
                file_put_contents($zipPath, fopen($url, 'r'));

                $zip = new \ZipArchive();
                if ($zip->open($zipPath) === true) {
                    for ($i = 0; $i < $zip->numFiles; $i++) {
                        $filename = $zip->getNameIndex($i);
                        if (basename($filename) === 'ffmpeg') {
                            copy("zip://" . $zipPath . "#" . $filename, $targetFile);
                            break;
                        }
                    }
                    $zip->close();
                }
                @unlink($zipPath);
                @chmod($targetFile, 0755);
            }

            if (File::exists($targetFile)) {
                $this->info("🎉 FFmpeg portable binary successfully installed at: {$targetFile}");
                return Command::SUCCESS;
            } else {
                $this->warn("⚠️ Could not extract binary automatically. You can manually copy ffmpeg executable to: {$targetFile}");
                return Command::FAILURE;
            }
        } catch (Throwable $e) {
            $this->error("Failed to download binary: " . $e->getMessage());
            $this->line("Manual installation: Place ffmpeg binary in: {$targetFile}");
            return Command::FAILURE;
        }
    }
}
