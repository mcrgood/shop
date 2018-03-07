<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

/**
 * **************************************************************版本定义*******************************************************************************
 */
define('NS_VER_B2C', 'NS_VER_B2C'); // 单店版基础电商
define('NS_VER_B2C_FX', 'NS_VER_B2C_FX'); // 单店版分销
define('NS_VERSION', NS_VER_B2C);
/**
 * **************************************************************版本定义*******************************************************************************
 */

require APP_PATH . 'error_message.php';

$root = \think\Request::instance()->root();
$root = str_replace('/index.php', '', $root);
define("__ROOT__", $root);
/**
 * *************************************************************伪静态*******************************************************************************
 */
define("REWRITE_MODEL", false); // 设置伪静态
                                // 入口文件,系统未开启伪静态
$rewrite = REWRITE_MODEL;
if (! $rewrite) {
    define('__URL__', \think\Request::instance()->domain() . \think\Request::instance()->baseFile());
} else {
    // 系统开启伪静态
    if (empty($root)) {
        define('__URL__', \think\Request::instance()->domain());
    } else {
        define('__URL__', \think\Request::instance()->domain() . \think\Request::instance()->root());
    }
}
/**
 * *************************************************************伪静态*******************************************************************************
 */

define('UPLOAD', "upload"); // 上传文件路径
define('ADMIN_MODULE', "admin"); // 重新定义后台模块



return [
    // +----------------------------------------------------------------------
    // | 应用设置
    // +----------------------------------------------------------------------
    // 应用命名空间
    'app_namespace' => 'app',
    // 应用调试模式，正式发布版本时改为false
    'app_debug' => false,
    // 应用Trace
    'app_trace' => false,
    // 应用模式状态
    'app_status' => '',
    // 是否支持多模块
    'app_multi_module' => true,
    // 入口自动绑定模块
    'auto_bind_module' => false,
    // 注册的根命名空间
    'root_namespace' => [],
    // 扩展配置文件
    'extra_config_list' => [
        'database',
        'validate'
    ],
    // 扩展函数文件
    'extra_file_list' => [
        THINK_PATH . 'helper' . EXT
    ],
    // 默认输出类型
    'default_return_type' => 'html',
    // 默认AJAX 数据返回格式,可选json xml ...
    'default_ajax_return' => 'json',
    // 默认JSONP格式返回的处理方法
    'default_jsonp_handler' => 'jsonpReturn',
    // 默认JSONP处理方法
    'var_jsonp_handler' => 'callback',
    // 默认时区
    'default_timezone' => 'PRC',
    // 是否开启多语言
    'lang_switch_on' => false,
    // 默认全局过滤方法 用逗号分隔多个
    'default_filter' => '',
    // 默认语言
    'default_lang' => 'zh-cn',
    // 应用类库后缀
    'class_suffix' => false,
    // 控制器类后缀
    'controller_suffix' => false,
    
    // +----------------------------------------------------------------------
    // | 模块设置
    // +----------------------------------------------------------------------
    
    // 默认模块名
    'default_module' => 'shop',
    // 禁止访问模块
    'deny_module_list' => [
        'common'
    ],
    // 默认控制器名
    'default_controller' => 'Index',
    // 默认操作名
    'default_action' => 'index',
    // 默认验证器
    'default_validate' => '',
    // 默认的空控制器名
    'empty_controller' => 'Error',
    // 操作方法后缀
    'action_suffix' => '',
    // 自动搜索控制器
    'controller_auto_search' => false,
    
    // +----------------------------------------------------------------------
    // | URL设置
    // +----------------------------------------------------------------------
    
    // PATHINFO变量名 用于兼容模式
    'var_pathinfo' => 's',
    // 兼容PATH_INFO获取
    'pathinfo_fetch' => [
        'ORIG_PATH_INFO',
        'REDIRECT_PATH_INFO',
        'REDIRECT_URL'
    ],
    // pathinfo分隔符
    'pathinfo_depr' => '/',
    // URL伪静态后缀
    'url_html_suffix' => 'html',
    // URL普通方式参数 用于自动生成
    'url_common_param' => false,
    // URL参数方式 0 按名称成对解析 1 按顺序解析
    'url_param_type' => 0,
    // 是否开启路由
    'url_route_on' => true,
    // 路由配置文件（支持配置多个）
    'route_config_file' => [
        'route'
    ],
    // 是否强制使用路由
    'url_route_must' => false,
    // 域名部署
    'url_domain_deploy' => false,
    // 域名根，如thinkphp.cn
    'url_domain_root' => '',
    // 是否自动转换URL中的控制器和操作名
    'url_convert' => true,
    // 默认的访问控制器层
    'url_controller_layer' => 'controller',
    // 表单请求类型伪装变量
    'var_method' => '_method',
    
    // +----------------------------------------------------------------------
    // | 模板设置
    // +----------------------------------------------------------------------
    
    'template' => [
        // 模板引擎类型 支持 php think 支持扩展
        'type' => 'Think',
        // 模板路径
        'view_path' => 'template/',
        
        // 模板后缀
        'view_suffix' => 'html',
        // 模板文件名分隔符
        'view_depr' => DS,
        // 模板引擎普通标签开始标记
        'tpl_begin' => '{',
        // 模板引擎普通标签结束标记
        'tpl_end' => '}',
        // 标签库标签开始标记
        'taglib_begin' => '{',
        // 标签库标签结束标记
        'taglib_end' => '}',
        'taglib_load' => true, // 是否使用内置标签库之外的其它标签库，默认自动检测
        'taglib_build_in' => 'cx'
    ], // 内置标签库名称(标签使用不必指定标签库名称),以逗号分隔 注意解析顺序
       // 'taglib_pre_load' => 'data\extend\Niu',
       
    // 视图输出字符串内容替换
    'view_replace_str' => [
        '__ROOT__' =>'/',
        '__INDEX__' =>'/public/static/index',
        '__PUBLIC__' =>'/public'
    ],
    // 默认跳转页面对应的模板文件
    'dispatch_success_tmpl' => ROOT_PATH . 'template' . DS . 'success_tmpl.html',
    'dispatch_error_tmpl' => ROOT_PATH . 'template' . DS . 'error_tmpl.html',
    
    // +----------------------------------------------------------------------
    // | 异常及错误设置
    // +----------------------------------------------------------------------
    
    // 异常页面的模板文件
    'exception_tmpl' => ROOT_PATH . 'template' . DS . 'think_exception.html',
    
    // 错误显示信息,非调试模式有效
    'error_message' => '页面不存在或者系统正忙，请稍后再试！',
    // 显示错误信息
    'show_error_msg' => true,
    // 异常处理handle类 留空使用 \think\exception\Handle
    'exception_handle' => '',
    
    // +----------------------------------------------------------------------
    // | 日志设置
    // +----------------------------------------------------------------------
    
    'log' => [
        // 日志记录方式，内置 file socket 支持扩展
        'type' => 'test',
        // 日志保存目录
        'path' => LOG_PATH,
        // 日志记录级别
        'level' => []
    ],
    
    // +----------------------------------------------------------------------
    // | Trace设置 开启 app_trace 后 有效
    // +----------------------------------------------------------------------
    'trace' => [
        // 内置Html Console 支持扩展
        'type' => 'Html'
    ],
    
    // +----------------------------------------------------------------------
    // | 缓存设置
    // +----------------------------------------------------------------------
    
    'cache' => [
        // 驱动方式
        'type' => 'File',
        // 缓存保存目录
        'path' => CACHE_PATH,
        // 缓存前缀
        'prefix' => '',
        // 缓存有效期 0表示永久缓存
        'expire' => 0
    ],
    
    // +----------------------------------------------------------------------
    // | 会话设置
    // +----------------------------------------------------------------------
    
    'session' => [
        'id' => '',
        // SESSION_ID的提交变量,解决flash上传跨域
        'var_session_id' => '',
        // SESSION 前缀
        'prefix' => 'niu',
        // 驱动方式 支持redis memcache memcached
        'type' => '',
        // 是否自动开启 SESSION
        'auto_start' => true
    ],
    
    // +----------------------------------------------------------------------
    // | Cookie设置
    // +----------------------------------------------------------------------
    'cookie' => [
        // cookie 名称前缀
        'prefix' => '',
        // cookie 保存时间
        'expire' => 0,
        // cookie 保存路径
        'path' => '/',
        // cookie 有效域名
        'domain' => '',
        // cookie 启用安全传输
        'secure' => false,
        // httponly设置
        'httponly' => '',
        // 是否使用 setcookie
        'setcookie' => true
    ],
    'view_replace_str' => array(
        '__PUBLIC__' => __ROOT__ . '/public/',
        '__STATIC__' => __ROOT__ . '/public/static',
        '__STATIC1__' => __ROOT__ . '/public/static/admin',
        '__INDEX__' => __ROOT__ . '/public/static/index',
        'ADMIN_IMG' => __ROOT__ . '/public/admin/images',
        'ADMIN_CSS' => __ROOT__ . '/public/admin/css',
        'ADMIN_JS' => __ROOT__ . '/public/admin/js',
        'PLATFORM_IMG' => __ROOT__ . '/public/platform/images',
        'PLATFORM_CSS' => __ROOT__ . '/public/platform/css',
        'PLATFORM_JS' => __ROOT__ . '/public/platform/js',
        '__TEMP__' => __ROOT__ . '/template',
        '__ROOT__' => __ROOT__,
        'UPLOAD_URL' => __URL__ . '/' . ADMIN_MODULE,
        'PLATFORM_MAIN' => __URL__ . '/platform',
        'ADMIN_MAIN' => __URL__ . '/' . ADMIN_MODULE,
        'APP_MAIN' => __URL__ . '/wap',
        'SHOP_MAIN' => __URL__ . '',
        '__UPLOAD__' => __ROOT__,
        '__MODULE__' => '/' . ADMIN_MODULE,
        '__ADDONS__' => __ROOT__ . '/addons', // 插件目录
                                          
        // 上传文件路径
        'UPLOAD_GOODS' => UPLOAD . '/goods/', // 存放商品图片主图
        'UPLOAD_GOODS_SKU' => UPLOAD . '/goods_sku/', // 存放商品sku图片
        'UPLOAD_GOODS_BRAND' => UPLOAD . '/goods_brand/', // 存放商品品牌图
        'UPLOAD_GOODS_GROUP' => UPLOAD . '/goods_group/', // 存放商品分组图片
        'UPLOAD_GOODS_CATEGORY' => UPLOAD . '/goods_category/', // 存放商品分组图片
        'UPLOAD_COMMON' => UPLOAD . '/common/', // 存放公共图片、网站logo、独立图片、没有任何关联的图片
        'UPLOAD_AVATOR' => UPLOAD . '/avator/', // 存放用户头像
        'UPLOAD_PAY' => UPLOAD . '/pay/', // 存放支付生成的二维码图片
        'UPLOAD_ADV' => UPLOAD . '/image_collection/', // //存放广告位图片，由于原“advertising”文件夹名称会被过滤掉。2017年9月14日 14:58:07 修改为“image_collection”
        'UPLOAD_EXPRESS' => UPLOAD . '/express/', // 存放物流
        'UPLOAD_CMS' => UPLOAD . '/cms/', // 存放文章图片
        'UPLOAD_VIDEO' => UPLOAD . "/video/"
    ), // 存放视频文件
       
    // 验证码排至文件
    'captcha' => [
        // 2345678abcdefhijkmnpqrstuvwxyzABCDEFGHJKLMNPQRTUVWXY
        // niushop2017459ABCDEFGHJKLMNQRTVWXYZ
        // 验证码字符集合
        'codeSet' => '0123456789',
        // 验证码字体大小(px)
        'fontSize' => 15,
        
        // 是否画混淆曲线
        'useCurve' => false,
        
        // 是否添加杂点
        'useNoise' => false,
        
        // 验证码图片高度
        'imageH' => 30,
        // 验证码图片宽度
        'imageW' => 100,
        // 验证码位数
        'length' => 4,
        // 验证成功后是否重置
        'reset' => true
    ],
    // 分页配置
    'paginate' => [
        'type' => 'bootstrap',
        'var_page' => 'page',
        'list_rows' => 14,
        'list_showpages' => 5,
        'picture_page_size' => 15
    ],
    //商家比例数组
    "business_arr" =>[10,15,20,25,30,35,40],
    'bankinfo'=>[
        'jiansheyh'=>[
            436728 ,436742 ,453242, 491031, 53242, 53243 ,5453242 ,5491031 ,553242, 5544033 ,544033, 622707 ,622725 ,622728 ,622700 ,621284 ,625955, 625956, 621700, 622966, 622988 ,421349 ,434061 ,434062, 524094 ,526410 ,552245, 589970 ,620060 ,621080 ,621081 ,621082 ,621466, 621467, 621488, 621499, 621598 ,622280 ,621621 ,544887 ,557080, 436718, 436745,489592 ,532450, 532458, 436738, 436748, 552801 ,558895 ,559051 ,622168 ,628266 ,628366 ,622708 ,622166 ,531693 ,356895, 356896, 356899, 625964, 625965 ,625966 ,622381 ,622675 ,622676, 622677, 621083, 622382 ,621487, 621084
        ],
        'zhongguoyh'=>[
            621660,621661,621662,621663,621665,621667,621668,621669,621666,625908,625910,625909,356833,356835,409665,
            409666,409668,409669,409670,409671,409672,456351,512315,512316,512411,512412,514957,409667,518378,518379,518474,
            518475,518476,438088,524865,525745,525746,547766,552742,553131,558868,514958,622752,622753,622755,524864,622757,
            622758,622759,622760,622761,622762,622763,601382,622756,628388,621256,621212,620514,622754,622764,518377,622765,
            622788,621283,620061,621725,620040,558869,621330,621331,621332,621333,621297,377677,621568,621569,625905,625906,
            625907,621756,621757,621758,621759,621785,621786,621787,621788,621789,621790,621620,620025,620026,621293,621294,
            621342,621343,621364,621394,621648,621248,621215,621249,622273,622274,622750,622751,622771,622772,622770,625145,
            620531,620210,620211,620019,620035,621231,622789,621638,621334,625140,621395,620513,621741,623040,620202,620203,
            622346,622347,622348,621041
        ],
        'nongyeyh'=>[
            103,403361,404117,404118,404119,404120,404121,463758,519412,519413,520082,520083,53591,552599,558730,
            49102,514027,622836,622837,622840,622841,622843,622844,622824,622847,622826,628268,6349102,6353591,
            95595,95596,95597,95598,95599,622846,622848,620059,621282,622828,625996,625998,622823,621336,621619,
            622821,622822,622825,622827,622845,622849,623018,625997,622820,622830,622838,620501
        ],
     
    ]
];

return array(
    //'配置项'=>'配置值'
    "MD5_PRE" =>'ns_',
    
);

