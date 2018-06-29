<?php
/**
 * 链接地址配置
 * 除HOST_ALL外每个数组必须要包含MASTER 和 TEST 两个元素，用于区分正式站和测试站
 * MASTER 对应主站相应配置
 * TEST 对应测试站相应配置
 */
return [
    //本网站所有的域名地址，用于判断本站现处于正式或测试域名下，若不在以下列表中，则默认为测试站
    'HOST_ALL'  =>  [
        'bzr.dapengjiaoyu.com'  =>  \App\Utils\Util::MASTER,
        'zc.dapengjiaoyu.com'  =>  \App\Utils\Util::MASTER,
        'ms.dapengjiaoyu.com'   =>  \App\Utils\Util::MASTER,
        'test.bzr.dapengjiaoyu.com'  =>  \App\Utils\Util::DEV,
        'dev.bzr.dapengjiaoyu.com'  =>  \App\Utils\Util::DEV,
        'test.ms.dapengjiaoyu.com'  =>  \App\Utils\Util::DEV,
        'it.dapengjiaoyu.com'   =>  \App\Utils\Util::MASTER,
        'test.it.dapengjiaoyu.com'  =>  \App\Utils\Util::DEV,
    ],
    //学院名字
    'SCHOOL_NAME'   =>  [
        'bzr.dapengjiaoyu.com'   =>  \App\Utils\Util::SCHOOL_NAME_SJ,
        'zc.dapengjiaoyu.com'   =>  \App\Utils\Util::SCHOOL_NAME_SJ,
        'test.bzr.dapengjiaoyu.com'  =>  \App\Utils\Util::SCHOOL_NAME_SJ,
        'dev.bzr.dapengjiaoyu.com'  =>  \App\Utils\Util::SCHOOL_NAME_SJ,
        'ms.dapengjiaoyu.com'   =>  \App\Utils\Util::SCHOOL_NAME_MS,
        'test.ms.dapengjiaoyu.com'  =>  \App\Utils\Util::SCHOOL_NAME_MS,
        'it.dapengjiaoyu.com'       =>  \App\Utils\Util::SCHOOL_NAME_IT,
        'test.it.dapengjiaoyu.com'  =>  \App\Utils\Util::SCHOOL_NAME_IT,
    ],
    //学院标题
    'SCHOOL_NAME_TEXT'  =>  [
        \App\Utils\Util::SCHOOL_NAME_SJ =>  '设计学院',
        \App\Utils\Util::SCHOOL_NAME_MS =>  '美术学院',
        \App\Utils\Util::SCHOOL_NAME_IT =>  'IT学院',
    ],
    //学院ID号，接口使用,
    'SCHOOL_ID'  =>  [
        \App\Utils\Util::SCHOOL_NAME_SJ =>  [
            \App\Utils\Util::MASTER    =>  'j5m484vz',
            \App\Utils\Util::DEV      =>  'j5m484vz',
        ],
        \App\Utils\Util::SCHOOL_NAME_MS =>  [
            \App\Utils\Util::MASTER    =>  'j5m48deg',
            \App\Utils\Util::DEV      =>  'j5m48deg',
        ],
        \App\Utils\Util::SCHOOL_NAME_IT =>  [
            \App\Utils\Util::MASTER    =>  '',
            \App\Utils\Util::DEV      =>  '',
        ],
    ],
    //大鹏主站URL
    'PC_URL'   =>  [
        \App\Utils\Util::MASTER    =>  'http://www.dapengjiaoyu.com',
        \App\Utils\Util::DEV      =>  'http://123.56.200.151',
    ],
    //展翅系统URL
    'ZC_URL'   =>  [
        //设计学院
        \App\Utils\Util::SCHOOL_NAME_SJ    =>  [
            \App\Utils\Util::DEV      =>  'http://test.bzr.dapengjiaoyu.com',
            \App\Utils\Util::MASTER    =>  'http://bzr.dapengjiaoyu.com',
        ],
        //美术学院
        \App\Utils\Util::SCHOOL_NAME_MS    =>  [
            \App\Utils\Util::MASTER    =>  'http://ms.dapengjiaoyu.com',
            \App\Utils\Util::DEV      =>  'http://test.ms.dapengjiaoyu.com',
        ]
    ],
    //M站地址
    'WAP_URL'   =>  [
        \App\Utils\Util::DEV      =>  'http://test.m.dapengjiaoyu.com',
        \App\Utils\Util::MASTER    =>  'http://m.dapengjiaoyu.com',
    ],
    /*第三方接口,生成短连接*/
    'SHORT_URL_API' =>  "http://api.t.sina.com.cn/short_url/shorten.json?source=3645189310&url_long=",
];