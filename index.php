<?php
/**
 * 获取当前目录下所有文件
 */
function getFiles(){
    return scandir(dirname(__FILE__));
}

/**
 * 获取文件后缀名,并判断是否合法
 *
 * @param string    $file_name  文件名
 * @param array     $allow_type 白名单
 * @return Booleans 合法true 不合法false
*/
function get_file_suffix($file_name, $allow_type = array())
{
    $fnarray=explode('.', $file_name);
    $file_suffix = strtolower(array_pop($fnarray));

    if (in_array($file_suffix, $allow_type)){
        return true;
    }
    else{
        return false;
    }
}

/**
 * 获取符合的文件列表
 * @param  Array   $files      文件列表
 * @param  Array   $allow_type 白名单列表
 * @return Array   符合的列表数组
 */
function getShowList($files, $allow_type){
    $showList = [];

    foreach($files as $key=> $item){
        if(get_file_suffix($item, $allow_type)){
            $name = explode('.', $item)[0];
            $href = $item;

            array_push($showList, [
                'name'=> $name,
                'href'=> $href
            ]);
        }
    }
    return $showList;
}
// 当前目录下的文件列表
$files = getFiles();

// 白名单
$allow_type = ['htm', 'html'];

// 显示的列表数组
$showList = getShowList($files, $allow_type);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <?php foreach($showList as $val){ ?>
        <div><a href="<?=$val['href']?>" target="_blank"><?=$val['name']?></a></div>
    <?php } ?>
</body>
</html>