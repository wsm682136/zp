<?php
error_reporting(0);
$base64_img = trim($_POST['img']);
$id         = trim($_GET['id']);
$url        = trim($_GET['url']);
$txt        = $_GET['txt'];
$ip         = $_GET['ip'];
$up_dir     = './img/'; //存放在当前目录的img文件夹下
echo "txt is " . $txt;
//echo "id is " . $id . " url is " . $url . " dir is " . $up_dir . " img is " . $base64_img;

//$methods = [
//    'getip',
//    'getip1',
//];

//foreach ($methods as $method) {
//    $response = json_decode($method($ip));
//    if ($response->code === 200) {

//        // 如果请求成功，输出请求结果并停止循环
//        echo $method($ip);
//        break;
//    }
//}


if (!empty($txt)) {
    $myfile = fopen("err.log", "a+") or die("Unable to open file!");
    $ips = json_decode(getip($ip), 1);
    $ip_add =  " ip address is ";
    if (!empty($ips['ipinfo']['country']))
        $ip_add .= $ips['ipinfo']['country'];
    if (!empty($ips['ipinfo']['province']))
        $ip_add .= '-' . $ips['ipinfo']['province'];
    if (!empty($ips['ipinfo']['city']))
        $ip_add .= '-' . $ips['ipinfo']['city'];
    if (!empty($ips['ipinfo']['district']))
        $ip_add .= '-' . $ips['ipinfo']['district'];

    $txt .= $ip_add . "\n";

    fwrite($myfile, $txt);
    fclose($myfile);
}

//if(empty($id) || empty($url) || empty($base64_img)){
if (empty($id) || empty($base64_img)) {
    exit;
}

if (!file_exists($up_dir)) {
    mkdir($up_dir, 0777);
}

if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_img, $result)) {
    $type = $result[2];
    if (in_array($type, ['bmp', 'png'])) {
        $new_file = $up_dir . $id . '_' . date('mdHis_') . '.' . $type;
        file_put_contents($new_file, base64_decode(str_replace($result[1], '', $base64_img)));
        header("Location: " . $url);
    }
}

// https://searchplugin.csdn.net/api/v1/ip/get?ip=120.7.241.0
function getip($ip)
{
    $response = cUrlGetIP('https://searchplugin.csdn.net/api/v1/ip/get?ip=' . $ip);
    $code = json_decode($response, true)['code'];

    if ($code == 200) {
        $str1 = json_decode($response, true)['data']['address'];
        // 国家
        $country = explode(' ', $str1)[0];
        // 省份
        $province = explode(' ', $str1)[1];
        // 城市
        $city = explode(' ', $str1)[2];
        // 县区
        $district = explode(' ', $str1)[3];

        // 判断是否获取成功
        if ($country || $province || $city || $district) {

            // 拼接数组
            $ipinfo = array(
                'code' => 200,
                'msg' => '获取成功',
                'ipinfo' => array(
                    'country' => $country,
                    'province' => $province,
                    'city' => $city,
                    'district' => $district,
                    'ip' => json_decode($response, true)['data']['ip']
                )
            );
        } else {

            $ipinfo = array(
                'code' => 201,
                'msg' => '获取失败'
            );
        }
    } else {

        $ipinfo = array(
            'code' => 201,
            'msg' => '获取失败'
        );
    }

    return json_encode($ipinfo, JSON_UNESCAPED_UNICODE);
}

function getip1($ip)
{
    $response = cUrlGetIP('https://c.runoob.com/wp-content/themes/toolrunoob2/option/ajax.php?type=checkIP&REMOTE_ADDR=' . $ip);
    $flag = json_decode($response, true)['flag'];

    if ($flag == true) {

        $str1 = json_decode($response, true)['data'];
        // 国家
        $country = $str1['country'];
        // 省份
        $province = $str1['regionName'];
        // 城市
        $city = $str1['city'];
        // 县区 
        $district = $str1['lsp'];

        // 判断是否获取成功
        if ($country || $province || $city || $district) {

            // 拼接数组
            $ipinfo = array(
                'code' => 200,
                'msg' => '获取成功',
                'ipinfo' => array(
                    'country' => $country,
                    'province' => $province,
                    'city' => $city,
                    'district' => $district,
                    'ip' => $ip
                )
            );
        } else {
            $ipinfo = array(
                'code' => 201,
                'msg' => '获取失败'
            );
        }
    } else {
        $ipinfo = array(
            'code' => 201,
            'msg' => '获取失败'
        );
    }

    return json_encode($ipinfo, JSON_UNESCAPED_UNICODE);
}


function cUrlGetIP($url)
{

    // cUrl
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $header[] = 'user-agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/97.0.4692.99 Safari/537.36';
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    return curl_exec($ch);
    curl_close($ch);
}
