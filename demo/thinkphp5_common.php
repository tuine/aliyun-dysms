<?php

/**
 * @param $mobile 手机号
 * @param $signName 签名
 * @param $tplCode 模板CODE
 * @param $tplParam 模板变量
 * @param $outId 流水号
 * @return array
 */
function send_sms($mobile, $signName, $tplCode, $tplParam='', $outId='')
{
    $errNo = 0;
    $msg   = '';
    try {
        // 加载区域结点配置
        \Aliyun\Core\Config::load();

        //产品名称:云通信流量服务API产品,开发者无需替换
        $product = "Dysmsapi";
        //产品域名,开发者无需替换
        $domain = "dysmsapi.aliyuncs.com";

        // TODO 此处需要替换成开发者自己的AK (https://ak-console.aliyun.com/)
        $accessKeyId = config('aliyun_dysms_key'); // AccessKeyId
        $accessKeySecret = config('aliyun_dysms_secret'); // AccessKeySecret

        // 暂时不支持多Region
        $region = "cn-hangzhou";
        // 服务结点
        $endPointName = "cn-hangzhou";
        //初始化acsClient,暂不支持region化
        $profile = \Aliyun\Core\Profile\DefaultProfile::getProfile($region, $accessKeyId, $accessKeySecret);
        // 增加服务结点
        \Aliyun\Core\Profile\DefaultProfile::addEndpoint($endPointName, $region, $product, $domain);
        // 初始化AcsClient用于发起请求
        $acsClient = new \Aliyun\Core\DefaultAcsClient($profile);

        // 初始化SendSmsRequest实例用于设置发送短信的参数
        $request = new \Aliyun\Api\Sms\Request\V20170525\SendSmsRequest();

        //可选-启用https协议
        //$request->setProtocol("https");

        // 必填，设置短信接收号码
        $request->setPhoneNumbers($mobile);

        // 必填，设置签名名称，应严格按"签名名称"填写，请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/sign
        $request->setSignName($signName);

        // 必填，设置模板CODE，应严格按"模板CODE"填写, 请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/template
        $request->setTemplateCode($tplCode);

        if ($tplParam) {
            // 可选，设置模板参数, 假如模板中存在变量需要替换则为必填项
            $request->setTemplateParam(json_encode(array(  // 短信模板中字段的值
                $tplParam
            ), JSON_UNESCAPED_UNICODE));
        }
        if ($outId) {
            // 可选，设置流水号
            $request->setOutId($outId);
        }
        

        // 选填，上行短信扩展码（扩展码字段控制在7位或以下，无特殊需求用户请忽略此字段）
        //$request->setSmsUpExtendCode("1234567");

        // 发起访问请求
        $acsResponse = $acsClient->getAcsResponse($request);
        if ('OK' != $acsResponse->Code) {
            $errNo = 1001;
            $msg   = $acsResponse->Code.':'.$acsResponse->Message;
        }
    } catch (\Exception $e) {
        $errNo = 1002;
        $msg   = '请求失败：'.$e->getMessage();
    }
    return ['errno'=>$errno, 'msg'=>$msg];
}