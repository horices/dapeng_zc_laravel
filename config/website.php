<?php
/**
 * 链接地址配置
 * 除HOST_ALL外每个数组必须要包含MASTER 和 TEST 两个元素，用于区分正式站和测试站
 * MASTER 对应主站相应配置
 * TEST 对应测试站相应配置
 */
return [
    'HOST_ALL'  =>  [
        'bzr.dapengjiaoyu.com'  =>  \App\Utils\Util::MASTER,
        'zc.dapengjiaoyu.com'  =>  \App\Utils\Util::MASTER,
        'test.bzr.dapengjiaoyu.com'  =>  \App\Utils\Util::TEST,
        'dev.bzr.dapengjiaoyu.com'  =>  \App\Utils\Util::TEST,
    ],
    //大鹏主站URL
    'PC_URL'   =>  [
        \App\Utils\Util::MASTER    =>  'http://www.dapengjiaoyu.com',
        \App\Utils\Util::TEST      =>  'http://123.56.200.151',
    ],
    //展翅系统URL
    'ZC_URL'   =>  [
        //设计学院
        'sj'    =>  [
            \App\Utils\Util::TEST      =>  'http://test.bzr.dapengjiaoyu.com',
            \App\Utils\Util::MASTER    =>  'http://bzr.dapengjiaoyu.com',
        ],
        //美术学院
        'ms'    =>  [
            \App\Utils\Util::MASTER    =>  'http://ms.dapengjiaoyu.com',
            \App\Utils\Util::TEST      =>  'http://test.ms.dapengjiaoyu.com',
        ]
    ],
];