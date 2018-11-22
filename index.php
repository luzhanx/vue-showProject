<?php
header("Content-Type: text/html;charset=utf-8");

$title = '点击更改项目名称';
$file = null;

init();

function init(){
    if (file_exists('title')) {
        $title =  file_get_contents('title');
        if($title == ''){
            $GLOBALS['file'] = fopen("title", "w") or die("Unable to open file!");
        }
        
        $GLOBALS['title'] = $title != '' ? $title : $GLOBALS['title'];
    } else {
        $GLOBALS['file'] = fopen("title", "w") or die("Unable to open file!");
    }
}

/**
 * 获取当前目录下所有文件
 */
function getFiles(){
    return scandir(dirname(__FILE__));
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
		if(PATH_SEPARATOR==':'){
         	 $file = $item;
        } else {
           $file =  iconv('gbk' , 'utf-8' , $item);
        }

        if(get_file_suffix($file, $allow_type)){
            $sort = 999;
            $name = explode('.', $file)[0];
            $res = explode('-', $name);
            if(!empty($res[1])){
                $sort = (int)$res[0];
                $name = $res[1];
            }
            $href = $file;

            array_push($showList, [
                'sort'=> $sort,
                'name'=> $name,
                'href'=> $href
            ]);
        }
    }
    
    return arraySort($showList, 'sort');
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
* 根据某个字段对数组进行排序
*
* @param Array  $array 需要排序的数组
* @param String $keys  需要根据某个key排序
* @param String $sort  倒叙还是顺序
*/
function arraySort($array, $keys, $sort='asc') {
    $newArr = $valArr = array();
    foreach ($array as $key=>$value) {
        $valArr[$key] = $value[$keys]; 
    }
    ($sort == 'asc') ?  asort($valArr) : arsort($valArr);//先利用keys对数组排序，目的是把目标数组的key排好序
    reset($valArr); //指针指向数组第一个值 
    foreach($valArr as $key=>$value) {
        $newArr[$key] = $array[$key];
    }
    return $newArr;
}

// 更改项目名称POST处理
if($_SERVER['REQUEST_METHOD'] == 'POST' )
{
    $title = json_decode(file_get_contents('php://input'))->title;

    if($title){
        fwrite($file, $title);
        fclose($file);
    }
    echo 'true';
    return true;
} 



// 当前目录下的文件列表
$files = getFiles();

// 白名单
$allow_type = ['htm', 'html'];

// 显示的列表数组
$showList = getShowList($files, $allow_type);

?>

<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="./style.css">
    <title></title>
</head>
<body>
<div id="indexPage">
        <div class="top">
            <a href="http://www.xtuo.net/" target="_blank" class="logo"></a>
            <div class="dname" onclick="handleNameChange(this)"></div>
            <div class="xname">WWW.XTUO.NET</div>
        </div>
        <div id="pc">
            <div class="pccom">
                <ul class="list">
                    <?php foreach($showList as $val){ ?>
                        <li class="item"><a href="./<?=$val['href']?>" target="_blank"><?=$val['name']?></a></li>
                    <?php } ?>
                </ul>
            </div>
        </div>
        <div id="mobile">
            <div class="mcom">
                <div class="lists">
                    <ul class="list">
                        <?php foreach($showList as $val){ ?>
                            <li class="item"><a href="./<?=$val['href']?>" target="_blank"><?=$val['name']?></a></li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
    <script>
        var state = {
            title: '<?= $title ?>'
        };
        
        document.title = state.title;
        document.getElementsByClassName('dname')[0].innerText = state.title;


        
        function handleNameChange(el){
            // 如果已经设置了项目名称
            if(state.title !== '点击更改项目名称')return;

            var title = prompt('请输入项目名');
            if(title === '' || title === null){
                return;
                // return alert()
            }
            axios.post('/index.php', {
                title: title
            }).then((res)=> {
                if(res){
                    state.title = title;
                    document.title = state.title;
                    document.getElementsByClassName('dname')[0].innerText = state.title;
                    // console.log(res);
                } else {
                    alert('修改失败');
                }
            })
        }
    </script>

</body>
</html>