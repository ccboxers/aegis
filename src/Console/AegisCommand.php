<?php

namespace Layman\Aegis\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Exception\ProcessFailedException;

class AegisCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'aegis:generate {bits=2048}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'openssl generate secret key';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $config = config('aegis');

        if (File::exists($config['private_path'])) {
            File::delete($config['private_path']);
        }

        if (File::exists($config['public_path'])) {
            File::delete($config['public_path']);
        }

        try {
            $bits = (int)$this->argument('bits');

            $openssl = [
                "private_key_type" => OPENSSL_KEYTYPE_RSA,
                "private_key_bits" => $bits,
            ];

            $secret = openssl_pkey_new($openssl);

            if ($secret === false) {
                $this->error('生成密钥失败：' . openssl_error_string());
                die();
            }

            openssl_pkey_export($secret, $privateKey);

            $keyDetails = openssl_pkey_get_details($secret);
            $publicKey  = $keyDetails['key'];

            file_put_contents($config['private_path'], $privateKey);
            file_put_contents($config['public_path'], $publicKey);

            $this->info('success');
            die();
        } catch (ProcessFailedException $exception) {
            $this->error("Command execution failed: {$exception->getMessage()}");
        }
    }
}
