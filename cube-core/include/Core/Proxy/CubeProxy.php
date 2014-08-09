<?php
/**
 * only tcp, single server now.
 */
class MCore_Proxy_CubeProxy
{
    private static $proxy_list = array();

    const MESSAGE_MAGIC = 'CB';
    const MESSAGE_VER = 1;
    const MESSAGE_TYPE_WELCOME = 1;
    const MESSAGE_TYPE_CLOSE = 2;
    const MESSAGE_TYPE_QUERY = 3;
    const MESSAGE_TYPE_ANSWER = 4;

    private $last_id = 0;
    private $service;

    public static function getInstance($end_point)
    {
        list($service, $ext) = explode('@', $end_point);
        list($protocal, $host, $port) = explode(':', $ext);
        !$protocal && $protocal = 'tcp';
        !$host && $host = '127.0.0.1';

        if (!isset(self::$proxy_list[$service]))
        {
            $proxy = new MCore_Proxy_CubeProxy($service, $host, $port);
            self::$proxy_list[$service] = $proxy;
        }
        return self::$proxy_list[$service];
    }

    public function __construct($service, $host, $port)
    {
        $this->service = $service;

        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        if ($socket === false)
        {
            throw new MCore_Proxy_Exception('socket_create() failed: reason: ' . socket_strerror(socket_last_error()));
        }

        $result = socket_connect($socket, $host, $port);
        if ($result === false)
        {
            throw new MCore_Proxy_Exception('socket_connect() failed: reason: ' . socket_strerror(socket_last_error()));
        }

        $this->socket = $socket;

        $type = $this->readMsgType();
        if ($type != self::MESSAGE_TYPE_WELCOME)
        {
            throw new MCore_Proxy_Exception('unexpected message recieved');
        }
    }

    private function readData($len)
    {
        $data = '';
        while (strlen($data) < $len)
        {
            $buf = socket_read($this->socket, $len - strlen($data));
            if (!$buf)
            {
                return false;
            }
            $data .= $buf;
        }
        return $data;
    }

    private function readMsgType()
    {
        $str = $this->readData(4);
        if (!$str)
        {
            return false;
        }
        $msg = unpack('A2magic/Cver/Cmsg_type', $str);
        if (!is_array($msg) || $msg['magic'] != self::MESSAGE_MAGIC || $msg['ver'] != self::MESSAGE_VER)
        {
            return false;
        }
        return $msg['msg_type'];
    }

    private function readAnswer()
    {
        $type = $this->readMsgType();
        if ($type != self::MESSAGE_TYPE_ANSWER)
        {
            return false;
        }
        $buf = $this->readData(4);
        if (!$buf)
        {
            return false;
        }
        $msg = unpack('V1len', $buf);

        if (!is_array($msg) || !isset($msg['len']))
        {
            return false;
        }
        $len = $msg['len'];
        $buf = $this->readData($len);
        $data = bin_decode($buf);
        return $data;
    }

    public function request($method, $params, $wait_return = true)
    {
        $qid = 0;
        if ($wait_return)
        {
            $qid = ++$this->last_id;
        }
        $data = array($qid, $this->service, $method, $params);
        $str = bin_encode($data);
        $data_len = strlen($str);
        $header = pack('A2C2V', self::MESSAGE_MAGIC, self::MESSAGE_VER, self::MESSAGE_TYPE_QUERY, $data_len);
        $msg = unpack('A2magic/Cver/Cmsg_type', $header);

        $buf = $header . $str;
        $ret = socket_write($this->socket, $buf);

        $answer = $this->readAnswer();

        $status = $answer[1];
        $data = $answer[2];
        if ($status)
        {
            $msg = $data['message'] . '; raiser: ' . $data['raiser'];
            throw new MCore_Proxy_Exception($msg, $data['code'], $data['detail']);
        }
        return $data;
    }
}

