<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\User;
use App\Util\MailchimpUtil;
use Exception;
use Illuminate\Console\Command;
use Log;

class Test extends Command
{
    protected $signature = 'test:command';

    protected $description = 'Command to test log'

    
    public function handle()
    {
        Log::info('Testing worker');
    }
}
