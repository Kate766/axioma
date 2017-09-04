<?php
//mb_internal_encoding("utf-8");

header("Content-Type: text/html; charset=utf-8");

//для myphotoua.ru
$config['smtp_username'] = 'a.mustafina@globalexpert.kz';//Смените на имя своего почтового ящика.
$config['smtp_password'] = '43431q';//Измените пароль
$config['smtp_host']     = 'ssl://smtp.gmail.com';//ssl или tls - для яндекса !!! 'tls://smtp.yandex.ru';//'smtp.yandex.ru';//сервер для отправки почты(для наших клиентов менять не требуется)
$config['smtp_port'] = '465';// Порт работы. Не меняйте, если не уверены.
$config['smtp_debug'] = true;//Если Вы хотите видеть сообщения ошибок, укажите true
$config['smtp_charset'] = 'UTF-8';//кодировка сообщений. (или UTF-8, итд)
$config['smtp_from'] = 'Vitaliy';//Ваше имя - или имя Вашего сайта. Будет показывать при прочтении в поле "От кого"


//отправка почты через smtp
function smtpmail($mail_to, $theme, $message, $headers='', $message_type='text/plain') //'text/html'
{
   global $config;
   $SEND =   "Date: ".date("D, d M Y H:i:s") . "\r\n";
   $SEND .=   'Subject: =?'.$config['smtp_charset'].'?B?'.base64_encode($theme)."=?=\r\n";

   if ($headers) $SEND .= $headers."\r\n\r\n";
   else
   {
       $SEND .= "Reply-To: ".$config['smtp_username']."\r\n";
       $SEND .= "MIME-Version: 1.0\r\n";
       $SEND .= "Content-Type: text/html; boundary=\"----------A4D921C2D10D7DB\"\r\n";
       $SEND .= "From: \"".$config['smtp_from']."\" <".$config['smtp_username'].">\r\n";
       $SEND .= "To: $mail_to <$mail_to>\r\n";
       $SEND .= "X-Priority: 3\r\n\r\n";
   }

   //var_dump($_FILES);

   if (count($_FILES))
   {
       // Важно чтобы не было отступов!
       $SEND .="------------A4D921C2D10D7DB\r\n";
       $SEND .="Content-Type: " . $message_type . "; charset=\"" . $config['smtp_charset'] . "\"\r\n";
       $SEND .="Content-Transfer-Encoding: 8bit\r\n\r\n";
       $SEND .=$message."\r\n";

       foreach($_FILES as $file)
       {
           if(is_array($file['error']))
           {//если <input name="file[]" type="file" multiple="true"> или <input name="file[]" type="file"> и таких может быть много (<input name="file[]" type="file"> && <input name="file[]" type="file">)
               for($i = 0; $i < count($file['name']); $i++)
               {
                   if($file['error'][$i] == 0)
                   {
                       if(is_uploaded_file($file['tmp_name'][$i]))
                       {
                           $path = $_SERVER['DOCUMENT_ROOT']."/files/";

                           if(file_exists($path))
                           {
                               $file_tmp_name=$file['tmp_name'][$i];
                               $new_path = $path . $file['name'][$i];
                               $file_name = $file['name'][$i];
                               move_uploaded_file($file_tmp_name, $new_path);
                               $fp = fopen($new_path, "rb");
                               $code_file1 = chunk_split(base64_encode(fread($fp, filesize($new_path))));
                               fclose($fp);

                               $SEND .="------------A4D921C2D10D7DB\r\n";
                               $SEND .="Content-Type: application/octet-stream; name=\"" . $file_name . "\"\r\n";
                               $SEND .="Content-transfer-encoding: base64\r\n";
                               $SEND .="Content-Disposition: attachment; filename=\"" . $file_name . "\"\r\n\r\n";
                               $SEND .=$code_file1."\r\n";

                               chmod($new_path, 0777);
                               unlink($new_path);
                           }
                           else
                           {
                               mkdir($path, 0777, true);
                               chmod($path, 0777);

                               $file_tmp_name=$file['tmp_name'][$i];
                               $new_path = $path . $file['name'][$i];
                               $file_name = $file['name'][$i];
                               move_uploaded_file($file_tmp_name, $new_path);

                               //chmod($new_path, 0777);

                               $fp = fopen($new_path, "rb");
                               $code_file1 = chunk_split(base64_encode(fread($fp, filesize($new_path))));
                               fclose($fp);

                               $SEND .="------------A4D921C2D10D7DB\r\n";
                               $SEND .="Content-Type: application/octet-stream; name=\"" . $file_name . "\"\r\n";
                               $SEND .="Content-transfer-encoding: base64\r\n";
                               $SEND .="Content-Disposition: attachment; filename=\"" . $file_name . "\"\r\n\r\n";
                               $SEND .=$code_file1."\r\n";

                               chmod($new_path, 0777);
                               unlink($new_path);
                           }
                       }
                   }
               }
           }
           else
           {//если <input name="file" type="file"> и таких может быть много (<input name="file1" type="file"> && <input name="file2" type="file">)
               if($file['error'] == 0)
               {
                   if(is_uploaded_file($file['tmp_name']))
                   {
                       $path = $_SERVER['DOCUMENT_ROOT']."/files/";

                       if(file_exists($path))
                       {
                           $file_tmp_name=$file['tmp_name'];
                           $new_path = $path . $file['name'];
                           $file_name = $file['name'];
                           move_uploaded_file($file_tmp_name, $new_path);
                           $fp = fopen($new_path, "rb");
                           $code_file1 = chunk_split(base64_encode(fread($fp, filesize($new_path))));
                           fclose($fp);

                           $SEND .="------------A4D921C2D10D7DB\r\n";
                           $SEND .="Content-Type: application/octet-stream; name=\"" . $file_name . "\"\r\n";
                           $SEND .="Content-transfer-encoding: base64\r\n";
                           $SEND .="Content-Disposition: attachment; filename=\"" . $file_name . "\"\r\n\r\n";
                           $SEND .=$code_file1."\r\n";

                           chmod($new_path, 0777);
                           unlink($new_path);
                       }
                       else
                       {
                           mkdir($path, 0777, true);
                           chmod($path, 0777);

                           $file_tmp_name=$file['tmp_name'];
                           $new_path = $path . $file['name'];
                           $file_name = $file['name'];
                           move_uploaded_file($file_tmp_name, $new_path);

                           //chmod($new_path, 0777);

                           $fp = fopen($new_path, "rb");
                           $code_file1 = chunk_split(base64_encode(fread($fp, filesize($new_path))));
                           fclose($fp);

                           $SEND .="------------A4D921C2D10D7DB\r\n";
                           $SEND .="Content-Type: application/octet-stream; name=\"" . $file_name . "\"\r\n";
                           $SEND .="Content-transfer-encoding: base64\r\n";
                           $SEND .="Content-Disposition: attachment; filename=\"" . $file_name . "\"\r\n\r\n";
                           $SEND .=$code_file1."\r\n";

                           chmod($new_path, 0777);
                           unlink($new_path);
                       }
                   }
               }
           }

       }

       $SEND .="------------A4D921C2D10D7DB--\r\n";
   }
   else
   {
       // Важно чтобы не было отступов!
       $SEND .="------------A4D921C2D10D7DB\r\n";
       $SEND .="Content-Type: " . $message_type . "; charset=\"" . $config['smtp_charset'] . "\"\r\n";
       $SEND .="Content-Transfer-Encoding: 8bit\r\n\r\n";

       $SEND .=$message."\r\n";
       $SEND .="------------A4D921C2D10D7DB--\r\n";
   }

   if( !$socket = fsockopen($config['smtp_host'], $config['smtp_port'], $errno, $errstr, 30) ) {
       if ($config['smtp_debug']) echo "$errno\n\r$errstr";
       return false;
   }

   if (!server_parse($socket, "220", __LINE__)) return false;

   fputs($socket, "HELO " . $config['smtp_host'] . "\r\n");
   if (!server_parse($socket, "250", __LINE__)) {
       if ($config['smtp_debug']) echo 'Не могу отправить HELO!';
       fclose($socket);
       return false;
   }
   fputs($socket, "AUTH LOGIN\r\n");
   if (!server_parse($socket, "334", __LINE__)) {
       if ($config['smtp_debug']) echo 'Не могу найти ответ на запрос авторизаци.';
       fclose($socket);
       return false;
   }
   fputs($socket, base64_encode($config['smtp_username']) . "\r\n");
   if (!server_parse($socket, "334", __LINE__)) {
       if ($config['smtp_debug']) echo 'Логин авторизации не был принят сервером!';
       fclose($socket);
       return false;
   }
   fputs($socket, base64_encode($config['smtp_password']) . "\r\n");
   if (!server_parse($socket, "235", __LINE__)) {
       if ($config['smtp_debug']) echo 'Пароль не был принят сервером как верный! Ошибка авторизации!';
       fclose($socket);
       return false;
   }
   fputs($socket, "MAIL FROM: <".$config['smtp_username'].">\r\n");
   if (!server_parse($socket, "250", __LINE__)) {
       if ($config['smtp_debug']) echo 'Не могу отправить комманду MAIL FROM: ';
       fclose($socket);
       return false;
   }
   fputs($socket, "RCPT TO: <" . $mail_to . ">\r\n");

   if (!server_parse($socket, "250", __LINE__)) {
       if ($config['smtp_debug']) echo 'Не могу отправить комманду RCPT TO: ';
       fclose($socket);
       return false;
   }
   fputs($socket, "DATA\r\n");

   if (!server_parse($socket, "354", __LINE__)) {
       if ($config['smtp_debug']) echo 'Не могу отправить комманду DATA';
       fclose($socket);
       return false;
   }
   fputs($socket, $SEND."\r\n.\r\n");

   if (!server_parse($socket, "250", __LINE__)) {
       if ($config['smtp_debug']) echo 'Не смог отправить тело письма. Письмо не было отправленно!';
       fclose($socket);
       return false;
   }
   fputs($socket, "QUIT\r\n");
   fclose($socket);
   return TRUE;
}

//работа с socket 'ами
function server_parse($socket, $response, $line = __LINE__) {
   global $config;
   $server_response='';
   while (substr($server_response, 3, 1) != ' ') {
       if (!($server_response = fgets($socket, 256))) {
           if ($config['smtp_debug']) echo "Проблемы с отправкой почты!\n\r$response\n\r$line";
           return false;
       }
   }
   if (!(substr($server_response, 0, 3) == $response)) {
       if ($config['smtp_debug']) echo "Проблемы с отправкой почты!\n\r$response\n\r$line";
       return false;
   }
   return true;
}

var_dump($_POST);

$name = $_POST['name'];
$tel = $_POST['tel'];
$email = $_POST['email'];
$content = $_POST['message'];

$message = '<h1>Сообщение с сайта AXIOMA</h1>';
$message .= '<p>Имя: ' . $name . '</p>';
$message .= '<p>E-mail: ' . $email . '</p>';
$message .= '<p>Телефон: ' . $tel . '</p>';
$message .= '<p>Сообщение: ' . $content . '</p>';

smtpmail('iliy4@ua.fm', 'AXIOMA', $message);

?>
