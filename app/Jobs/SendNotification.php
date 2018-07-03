<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SendNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $url = "";   //请求地址
    private $data = [];  //请求参数
    private $method;    //请求方式
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($url,$data = [],$method = "post")
    {
        $this->url = $url;
        $this->data = $data;
        $this->method = Str::lower($method);
        if($this->method == 'get'){
            if($data){
                $this->url .= "?".http_build_query($data);
                $this->data = [];
            }
        }
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $curl = app("curl");
        $method = $this->method;
        Log::info("转发通知:".$this->url);
        Log::info("通知参数：");
        Log::info($this->data);
        $response = $curl->$method($this->url,$this->data)->response;
        Log::info("通知返回:".$response);
    }
}
