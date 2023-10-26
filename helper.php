<?php 


/**
 * 设置配置
 */
function set_config($title,$body,$shop_id = '')
{
    if($shop_id){
        $shop_id = "_".$shop_id;
        $title = $title.$shop_id;
    }
    if(in_array($title,[
        '_timestamp',
        '_signature',
    ])){
        return;
    }
    $one = db_get_one("config", "*", ['title' => $title]); 
    if (!$one) {
        db_insert("config", ['title' => $title, 'body' => $body]);
    } else {
        db_update("config", ['body' => $body], ['id' => $one['id']]);
    }
}
/**
 * 优先取数据库，未找到后取配置文件
 */
function get_config($title,$shop_id = '')
{
    if($shop_id){
        $shop_id = "_".$shop_id;
    }
    global $config;
    if (is_array($title)) {
        if($shop_id){
            $new_title = [];
            $in_array = [];
            foreach($title as $k){
                $new_k = $k.$shop_id;
                $new_title[] = $new_k;
                $in_array[$new_k] = $k;
            }
            $title = $new_title;
        }
        $list = [];
        $all  = db_get("config", "*", ['title' => $title]);
        foreach ($all as $one) {
            $body = $one['body']; 
            $key  = $one['title'];
            $list[$key] = $body ?: $config[$key];
            $list[$in_array[$key]] = $list[$key];
        }
        return $list;
    } else {
        if($shop_id){
            $title = $title.$shop_id;
        }
        $one  = db_get_one("config", "*", ['title' => $title]);
        $body = $one['body'];
        if (!$body) {
            return $config[$title];
        } 
        return $body; 
    }
}

/**
 * json输出 
 */
if(!function_exists('json')){
    function json($data)
    {
        global $config;
        $config['is_json'] = true;
        //JSON输出前
        do_action('end.data', $data);
        echo json_encode($data,JSON_UNESCAPED_UNICODE);
        //JSON输出后或页面渲染后
        do_action("end");
        exit;
    }
} 
