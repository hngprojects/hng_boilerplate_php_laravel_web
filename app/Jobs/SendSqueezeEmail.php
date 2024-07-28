<?php

namespace App\Jobs;

use App\Models\Squeeze;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Mail\SqueezeTemplateMail;

class SendSqueezeEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $squeeze;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Squeeze $squeeze)
    {
        $this->squeeze = $squeeze;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Mail::to($this->squeeze->email)->send(new SqueezeTemplateMail($this->squeeze));
    }
}
