<?php

/*
    ���׵�SMTP�����ʼ��࣬����Ƚ��٣�������ѧϰSMTPЭ�飬
    ���Դ�������֧����Ҫ��֤��SMTP��������Ŀǰ��SMTP��������Ҫ��֤��
    ��д: chenall
    ʱ��: 2012-12-04
    ��ַ: http://chenall.net/post/cs_smtp/
    �޶���¼:
        2012-12-08
            ���AddURL����������ֱ�Ӵ�ĳ����ַ�������ļ�����Ϊ�������͡�
            �������ڷ����˺ͽ������ʼ���ַû��ʹ��"<>"126����SMTP�޷�ʹ�õ����⡣
        2012-12-06
            ���reset�������������ӣ��������Է��Ͷ���ʼ���
        2012-12-05
           ���͸����Ĵ������ϵ�send�����У����ٱ�����ʹ�ã������������ʡ�ڴ�ռ��;
        2012-12-04
           ��һ���汾

    ʹ�÷���:

        1. ��ʼ�������ӵ���������Ĭ����QQ���䣩
           $mail = new cs_smtp('smtp.qq.com',25)
           if ($mail->errstr) //������ӳ���
               die($mail->errstr;
        2. ��¼����������֤,���ʧ�ܷ���FALSE;
           if (!$mail->login('USERNAME','PASSWORD'))
                die($mail->errstr;
        3. ��Ӹ��������ָ��name�Զ���ָ�����ļ���ȡ�ļ���
           $mail->AddFile($file,$name) //�������ϵ��ļ�������ָ���ļ���;
        4. �����ʼ�
            $mail->send($to,$subject,$body)
            $to �ռ��ˣ����ʹ��','�ָ�
            $subject �ʼ����⣬��ѡ��
            $body  �ʼ��������ݣ���ѡ
*/
class cs_smtp
{
    private $CRLF = "\r\n";
    private $from;
    private $smtp = null;
    private $attach = array();
    public $debug = true;//���Կ���
    public $errstr = '';

    function __construct($host='smtp.qq.com',$port = 25) {
        if (empty($host))
            die('SMTP������δָ��!');
        $this->smtp = fsockopen($host,$port,$errno,$errstr,5);
        if (empty($this->smtp))
        {
            $this->errstr = '����'.$errno.':'.$errstr;
            return;
        }
        $this->smtp_log(fread($this->smtp, 515));
        if (intval($this->smtp_cmd('EHLO '.$host)) != 250 && intval($this->smtp_cmd('HELO '.$host)))
            return $this->errstr = '��������֧�֣�';
        $this->errstr = '';
    }

    private function AttachURL($url,$name)
    {
        $info = parse_url($url);
        isset($info['port']) || $info['port'] = 80;
        isset($info['path']) || $info['path'] = '/';
        isset($info['query']) || $info['query'] = '';
        $down = fsockopen($info['host'],$info['port'],$errno,$errstr,5);
        if (!$down)
            return false;
        $out = "GET ".$info['path'].'?'.$info['query']." HTTP/1.1\r\n";
        $out .="Host: ".$info['host']."\r\n";
        $out .= "Connection: Close\r\n\r\n";
        fwrite($down, $out);
        $filesize = 0;
        while (!feof($down)) {
            $a = fgets($down,515);
            if ($a == "\r\n")
                break;
            $a = explode(':',$a);
            if (strcasecmp($a[0],'Content-Length') == 0)
                $filesize = intval($a[1]);
        }
        $sendsize = 0;
        echo "TotalSize: ".$filesize."\r\n";
        $i = 0;
        while (!feof($down)) {
            $data = fread($down,0x2000);
            $sendsize += strlen($data);
            if ($filesize)
            {
                echo "$i Send:".$sendsize."\r";
                ob_flush();
                flush();
            }
            ++$i;
            fwrite($this->smtp,chunk_split(base64_encode($data)));
        }
        echo "\r\n";
        fclose($down);
        return ($filesize>0)?$filesize==$sendsize:true;
    }

    function __destruct()
    {
        if ($this->smtp)
            $this->smtp_cmd('QUIT');//�����˳�����
    }

    private function smtp_log($msg)//��ʱ�������ʹ��
    {
        if ($this->debug == false)
            return;
        echo $msg."\r\n";
        ob_flush();
        flush();
    }

    function reset()
    {
        $this->attach = null;
        $this->smtp_cmd('RSET');
    }

    function smtp_cmd($msg)//SMTP����ͺ�����
    {
        fputs($this->smtp,$msg.$this->CRLF);
        $this->smtp_log('SEND:'. substr($msg,0,80));
        $res = fread($this->smtp, 515);
        $this->smtp_log($res);
        $this->errstr = $res;
        return $res;
    }

    function AddURL($url,$name)
    {
        $this->attach[$name] = $url;
    }

    function AddFile($file,$name = '')//����ļ�����
    {
        if (file_exists($file))
        {
            if (!empty($name))
                return $this->attach[$name] = $file;
            $fn = pathinfo($file);
            return $this->attach[$fn['basename']] = $file;
        }
        return false;
    }

    function send($to,$subject='',$body = '')
    {
        $this->smtp_cmd("MAIL FROM: <".$this->from.'>');
        $mailto = explode(',',$to);
        foreach($mailto as $email_to)
            $this->smtp_cmd("RCPT TO: <".$email_to.">");
        if (intval($this->smtp_cmd("DATA")) != 354)//��ȷ�ķ��ر�����354
            return false;
        fwrite($this->smtp,"To:$to\nFrom: ".$this->from."\nSubject: $subject\n");

        $boundary = uniqid("--BY_CHENALL_",true);
        $headers = "MIME-Version: 1.0".$this->CRLF;
        $headers .= "From: <".$this->from.">".$this->CRLF;
        $headers .= "Content-type: multipart/mixed; boundary= $boundary\n\n".$this->CRLF;//headers����Ҫ������������
        fwrite($this->smtp,$headers);

        $msg = "--$boundary\nContent-Type: text/html;charset=\"ISO-8859-1\"\nContent-Transfer-Encoding: base64\n\n";
        $msg .= chunk_split(base64_encode($body));
        fwrite($this->smtp,$msg);
        $files = '';
        $errinfo = '';
        foreach($this->attach as $name=>$file)
        {
            $files .= $name;
            $msg = "--$boundary\n--$boundary\n";
            $msg .= "Content-Type: application/octet-stream; name=\"".$name."\"\n";
            $msg .= "Content-Disposition: attachment; filename=\"".$name."\"\n";
            $msg .= "Content-transfer-encoding: base64\n\n";
            fwrite($this->smtp,$msg);
            if (substr($file,4,1) == ':')//URL like http:///file://
            {
                if (!$this->AttachURL($file,$name))
                    $errinfo .= '�ļ����ش���:'.$name.",�ļ������Ǵ����\r\n$file";
            }
            else
                fwrite($this->smtp,chunk_split(base64_encode(file_get_contents($file))));//ʹ��BASE64���룬����chunk_split��ж�˿飨ÿ��76���ַ���
        }
        if (!empty($errinfo))
        {
            $msg = "--$boundary\n--$boundary\n";
            $msg .= "Content-Type: application/octet-stream; name=Error.log\n";
            $msg .= "Content-Disposition: attachment; filename=Error.log\n";
            $msg .= "Content-transfer-encoding: base64\n\n";
            fwrite($this->smtp,$msg);
            fwrite($this->smtp,chunk_split(base64_encode($errinfo)));
        }
        return intval($this->smtp_cmd("--$boundary--\n\r\n.")) == 250;//����DATA���ͣ��������᷵��ִ�н����������벻��250�����
    }

    function login($su,$sp)
    {
        if (empty($this->smtp))
            return false;
        $res = $this->smtp_cmd("AUTH LOGIN");
        if (intval($res)>400)
            return !$this->errstr = $res;
        $res = $this->smtp_cmd(base64_encode($su));
        if (intval($res)>400)
            return !$this->errstr = $res;
        $res = $this->smtp_cmd(base64_encode($sp));
        if (intval($res)>400)
            return !$this->errstr = $res;
        $this->from = $su;
        return true;
    }
	
	
           

}
     $mail = new cs_smtp('smtp.163.com',25);
     if ($mail->errstr)  
	 {die($mail->errstr);}
     if (!$mail->login('mosige_life@163.com','cfoiuxtjehcpcttw'))
	 {die($mail->errstr);}
	   $to='30540281@qq.com';
        $subject='test';
       $body='�ʼ��������ݣ���ѡ';
       $mail->send($to,$subject,$body);
	   echo "done";
	 ?>