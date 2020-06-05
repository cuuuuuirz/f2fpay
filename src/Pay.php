<?php

namespace Cuuuuuirz\F2FPay;

use Cuuuuuirz\F2FPay\Support\Client;
use Cuuuuuirz\F2FPay\Support\Support;
use Cuuuuuirz\F2FPay\Exceptions\HttpException;
use Cuuuuuirz\F2FPay\Exceptions\InvalidArgumentException;

class Pay
{
    const URL = 'https://openapi.alipay.com/gateway.do?charset=utf-8';

    protected $config;
    protected $payload;

    public function __construct(array $config)
    {
        $this->config   	= $config;
        $this->payload  	= [
            'app_id'        => $this->config['app_id'],
            'return_url'    => $this->config['return_url'],
            'notify_url'    => $this->config['notify_url'],
            'method'        => '',
            'sign'          => '',
            'biz_content'   => '',
            'format'        => 'JSON',
            'charset'       => 'utf-8',
            'sign_type'     => 'RSA2',
            'version'       => '1.0',
            'timestamp'     => date('Y-m-d H:i:s'),
        ];
    }

    public function qrcode(array $params)
    {
        $this->payload['method']        = 'alipay.trade.precreate';
        $this->payload['biz_content']   = json_encode($params);
        $this->payload['sign']          = Support::generateSign($this->config['rsa_private_key'], $this->payload);

        $response = Support::getHttpResponse(self::URL, $this->payload);
        return json_decode($response, true);
    }

    public function barPay(array $params)
    {
        $this->payload['method']            = 'alipay.trade.pay';
        $this->payload['scene']             = 'bar_code';
        $this->payload['store_id']          = $this->config['store_id'];
        $this->payload['timeout_express']   = $this->config['timeout_express'];
        $this->payload['biz_content']       = json_encode($params);
        $this->payload['sign']              = Support::generateSign($this->config['rsa_private_key'], $this->payload);

        $response = Support::getHttpResponse(self::URL, $this->payload);
        return json_decode($response, true);
    }

    public function find(array $params)
    {
        $this->payload['method']        = 'alipay.trade.query';
        $this->payload['biz_content']   = json_encode($params);
        $this->payload['sign']          = Support::generateSign($this->config['rsa_private_key'], $this->payload);

        $response = Support::getHttpResponse(self::URL, $this->payload);
        return json_decode($response, true);
    }

    public function cancel(array $params)
    {
        $this->payload['method']        = 'alipay.trade.cancel';
        $this->payload['biz_content']   = json_encode($params);
        $this->payload['sign']          = Support::generateSign($this->config['rsa_private_key'], $this->payload);

        $response = Support::getHttpResponse(self::URL, $this->payload);
        return json_decode($response, true);
    }

    public function refund(array $params)
    {
        $this->payload['method']        = 'alipay.trade.refund';
        $this->payload['biz_content']   = json_encode($params);
        $this->payload['sign']          = Support::generateSign($this->config['rsa_private_key'], $this->payload);

        $response = Support::getHttpResponse(self::URL, $this->payload);
        return json_decode($response, true);
    }

    public function billDownload(array $params)
    {
        $this->payload['method']        = 'alipay.data.dataservice.bill.downloadurl.query';
        $this->payload['biz_content']   = json_encode($params);
        unset($this->payload['notify_url']);
        $this->payload['sign']          = Support::generateSign($this->config['rsa_private_key'], $this->payload);

        $response = Support::getHttpResponse(self::URL, $this->payload);
        return json_decode($response, true);
    }
}