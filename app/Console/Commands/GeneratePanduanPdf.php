<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GeneratePanduanPdf extends Command
{
    protected $signature = 'panduan:pdf';
    protected $description = 'Generate official Buku Panduan Presensi SMKN 1 Beringin PDF';

    public function handle()
    {
        $this->info('Rendering panduan.pdf view...');
        $html = view('panduan.pdf')->render();

        $tmpHtml = sys_get_temp_dir() . '/panduan_rendered.html';
        file_put_contents($tmpHtml, $html);

        $outDir = public_path('docs');
        if (!is_dir($outDir)) {
            mkdir($outDir, 0755, true);
        }

        $outFile = $outDir . '/Panduan_Penggunaan_Presensi_SMKN1_Beringin.pdf';

        $this->info('Compiling PDF via Chromium headless...');
        $cmd = "chromium --headless --disable-gpu --no-sandbox --no-pdf-header-footer --print-to-pdf=" . escapeshellarg($outFile) . " " . escapeshellarg($tmpHtml) . " 2>&1";
        exec($cmd, $output, $status);

        if ($status === 0 && file_exists($outFile)) {
            $size = round(filesize($outFile) / 1024, 2);
            $this->info("PDF generated successfully: {$outFile} ({$size} KB)");
            return Command::SUCCESS;
        }

        $this->error('Failed to generate PDF. Output: ' . implode("\n", $output));
        return Command::FAILURE;
    }
}

