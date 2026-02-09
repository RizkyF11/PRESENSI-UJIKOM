<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\QrCode;

class CleanupQrCode extends Command
{
    protected $signature = 'qr:cleanup';
    protected $description = 'Hapus QR code yang sudah expired lama';

    public function handle()
    {
        $deleted = QrCode::where('expired_at', '<', now()->subDays(1))->delete();

        $this->info("QR code lama terhapus: {$deleted}");
    }
}

