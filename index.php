<?php

header("Content-type: application/json; charset=utf-8");                        //Передает содержимое в формате джейсон в кодировке utf-8
$user_agent = $_SERVER["HTTP_USER_AGENT"];                                            //Получение имени броузера

if (strpos($user_agent, "Firefox") !== false) $browser = "Firefox";
elseif (strpos($user_agent, "Opera") !== false) $browser = "Opera";
elseif (strpos($user_agent, "Chrome") !== false) $browser = "Chrome";
elseif (strpos($user_agent, "MSIE") !== false) $browser = "Internet Explorer";
elseif (strpos($user_agent, "Safari") !== false) $browser = "Safari";
else $browser = "NoName";

// Определяем метод запроса
$method = $_SERVER['REQUEST_METHOD'];                                               //Получение метода  из суперглобальной переменной

// Получаем данные из тела запроса
$formData = getFormData($method);

// Получение данных из тела запроса
function getFormData($method)
{
    // GET или POST: данные возвращаем как есть
    if ($method === 'GET')
        return $_GET;

    // POST, PUT, PATCH или DELETE
    $param =  file_get_contents('php://input');         //Получает содержимое файла (В качестве имени файла указывается поток - (php://input)
    $param = json_decode($param, true);

    return $param;
}

// Разбираем url
$url = (isset($_GET['q'])) ? $_GET['q'] : '';
$url = rtrim($url, '/');
$urls = explode('/', $url);

// Определяем роутер и url data
$router = $urls[0];                                     //В переменной $router будет значение которое в дальнейшем будет являться именем файла
$urlData = array_slice($urls, 1);                 //Отсекает у массива все лишнее по смещению определяемому вторым параметром

// Подключаем файл-роутер и запускаем главную функцию
if ($router != '')
{
    include_once 'routers/' . $router . '.php';
    route($method, $urlData, $formData, $db);
}

else
{

    if ($browser != "NoName")
    {
        header('Content-Type: text/html; charset=utf-8');
        ?>
        <h1 style='font-family: Arial,serif;font-weight: bold;font-size: 50px;margin-top: 70px;' align='center'>Браузеры не поддерживаются</h1>;
        <?php
    }
    else
    {
        // Возвращаем ошибку

        header('HTTP/1.0 400 Bad Request');

        echo json_encode(array(
            'error' => 'Bad Request'
        ));
    }
}


//$.ajax({url: 'http://apitest.local/goods/98', method: 'GET', dataType: 'json', success: function(response){console.log('response:', response)}})
//Проверка в браузере

//curl -X PATCH http://apitest.local/goods/15  --data-urlencode "asd=78&qwerty=135"
//Проверка через команду curl в терминале
