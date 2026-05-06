<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenerateItemQRCodes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'items:generate-qr-codes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate QR codes for all items that don\'t have them';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $items = \App\Models\Item::whereNull('qr_code_svg')->get();

        if ($items->isEmpty()) {
            $this->info('All items already have QR codes!');
            return;
        }

        $this->info("Generating QR codes for {$items->count()} items...");

        $progressBar = $this->output->createProgressBar($items->count());
        $progressBar->start();

        foreach ($items as $item) {
            $item->update(['qr_code_svg' => $item->generateQrCodeSvg()]);
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine();

        $this->info('QR codes generated successfully!');
    }
}
