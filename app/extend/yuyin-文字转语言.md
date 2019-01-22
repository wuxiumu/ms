## 接口说明

>语音合成

### 接口描述
基于该接口，开发者可以轻松的获取语音合成能力

### 请求说明
合成文本长度必须小于1024字节，如果本文长度较长，可以采用多次请求的方式。文本长度不可超过限制
举例，要把一段文字合成为语音文件：
```
require_once 'AipSpeech.php';
// 你的 APPID AK SK
const APP_ID = '你的 App ID';
const API_KEY = '你的 Api Key';
const SECRET_KEY = '你的 Secret Key';
$client = new AipSpeech(APP_ID, API_KEY, SECRET_KEY);
$result = $client->synthesis('你好百度', 'zh', 1, array(
    'vol' => 5,
));
// 识别正确返回语音二进制 错误则返回json 参照下面错误码
if(!is_array($result)){
    file_put_contents('audio.mp3', $result);
}
```
参数	| 类型	| 描述| 	是否必须
---- | -------| -------| -------
tex	| String| 	合成的文本，使用UTF-8编码，请注意文本长度必须小于1024字节	| 是
lang| String| 	语言选择,填写zh	| 是
ctp	| String| 	客户端类型选择，web端填写1	| 是
cuid| String| 	用户唯一标识，用来区分用户，填写机器 MAC 地址或 IMEI 码，长度为60以内	| 否
spd	| String| 	语速，取值0-9，默认为5中语速	| 否
pit	| String| 	音调，取值0-9，默认为5中语调	| 否
vol	| String| 	音量，取值0-15，默认为5中音量	| 否
per	| String| 	发音人选择, 0为女声，1为男声，3为情感合成-度逍遥，4为情感合成-度丫丫，默认为普通女	| 否

返回样例：
```
// 成功返回二进制文件
// 失败返回
{
    "err_no":500,
    "err_msg":"notsupport.",
    "sn":"abcdefgh",
    "idx":1
}
```
错误信息

### 错误返回格式

若请求错误，服务器将返回的JSON文本包含以下参数：

- error_code：错误码。
- error_msg：错误描述信息，帮助理解和解决发生的错误。

### 错误码
	
错误码| 含义
---- | -------
500  |不支持的输入
501	 |输入参数不正确
502	 |token验证失败
503	 |合成后端错误