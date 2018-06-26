<?php
require_once __DIR__.DIRECTORY_SEPARATOR .'autoload.php';

function keys($length)
{
    $key = '';
    $pattern = '1234567890ABCDEFGHIJKLOMNOPQRSTUVWXYZ';
    for ($i = 0; $i < $length; $i++) {
        $key .= $pattern{mt_rand(0, 35)}; //生成php随机数
    }
    return $key;
}

function getList($type,$host, $url)
{
    $Grab            = new Grab();
    $all_text_string = $Grab->https('Computer', $url,$host);

    $all_text_reg = '#<div class="post"> <a href="(.*?)"  title="(.*?)"><img  class="lazy" src="(.*?)" data-original="(.*?)" alt="(.*?)" /> </a>#s';
    preg_match_all($all_text_reg, $all_text_string, $list_data, PREG_PATTERN_ORDER);

    foreach ($list_data[1] as $key => $value) {
        $content_url = $host . $value;
        $content_data  = getContent($content_url,$url);
        $imgNum = count($content_data['content']) - 1;
        $cover_img[0] = $content_data['content'][mt_rand(0,$imgNum)]['img_url'];
        $data[$key]['aid'] = keys(2) . (strtotime(date("Y-m-d H:i:s")) + 1) . keys(2);
        $data[$key]['title'] = trim($list_data[2][$key]);
        $data[$key]['create_time'] = !empty($content_data['time']) ? $content_data['time'] . " 00:00:00": date("Y-m-d H:i:s");
        $data[$key]['tags'] = str_replace($data[$key]['title'].'，','',$content_data['summary']);
        $data[$key]['type'] = $type;
        $data[$key]['summary'] = $content_data['summary'];
        $data[$key]['source'] = '05532美女吧';
        $data[$key]['cover_img'] = serialize($cover_img);
        $data[$key]['img_num'] = $imgNum+1;
        $data[$key]['content'] = serialize($content_data['content']);
        $data[$key]['original_link'] = $content_url;
        echo '<pre/>';
        var_dump($data);
        $Grab->insert('se7c_post_data', $data);
        exit;
    }
}

function getContent($url,$referer)
{
    $Grab            = new Grab();
    $all_text_string = $Grab->http('Computer', $url, $referer);

    $all_page_reg = '#<span id="allnum">(.*?)</span>#s';
    preg_match($all_page_reg, $all_text_string, $all_page_num);

    $page_time_reg = '#<span><i class="icon-time"></i>(.*?)</span>#s';
    preg_match($page_time_reg, $all_text_string, $page_time);

    $summary_reg = '#<div class="pictext">(.*?)</div>#s';
    preg_match($summary_reg,$all_text_string,$summary_text);

    for ($i=1; $i<=$all_page_num[1]; $i++) {
        $nextPage = str_replace('_1.html','.html',str_replace('.html', '_' . $i . '.html', $url));
        if($i == 1){
            $currentPage = $referer;
        }elseif($i == 2){
            $currentPage = $url;
        }else{
            $currentPage = str_replace('.html', '_' . $i-1 . '.html', $url);
        }
        $nextImg[$i-1]['img_url'] = nextPage($nextPage,$currentPage);
    }

    $data['time'] = $page_time[1];
    $data['content'] = $nextImg;
    $data['summary'] = $summary_text['1'];

    return $data;
}

function nextPage($url,$referer)
{
    $Grab            = new Grab();
    $all_text_string = $Grab->http('Computer', $url,$referer);
    $page_img_reg = '#<div class="images"><a href="(.*?)"><img src=(.*?)  alt="(.*?)" /></a>#s';
    preg_match($page_img_reg, $all_text_string, $page_img);

    $fileName		= md5(keys(32));
    $imgPath = $Grab->curlImg('Computer',trim($page_img[2]),$fileName,$url);

    $Upload = new Upload();
    $OssLink = $Upload->upImage($imgPath);
    return $OssLink;
}


$array = array(
    0 => array(
        'type' => '10000',
        'link' => 'http://www.05532.com/xinggan/',
        'referer' => 'http://www.05532.com',
    ),
);

foreach ($array as $key => $value) {
    GetList($value['type'],$value['referer'], $value['link']);
}
