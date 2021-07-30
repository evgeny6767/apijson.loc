<?php
require 'dbconfig.php';

function route($method, $urlData, $formData, $db)
{
    if ($method === 'GET' && empty($urlData))               //Получение всех записей
    {
        $stmt = $db->query("SELECT * FROM items");
        $out = [];
        $rowjson = [];

        while ($row = $stmt->fetch())
        {
            $rowjson['id'] = $row['id'];
            $rowjson['name'] = $row['name'];
            $rowjson['price'] = $row['price'];
            $out[] = $rowjson;
            $rowjson = [];
        }
        echo json_encode($out);
        return;
    }

    if ($method === 'POST' && empty($urlData))              //Добавление записей
    {
        if ($formData != null)
        {
            $query = "INSERT INTO `items` (`name`, `price`) VALUES (:name, :price)";
            $params = [
                ':name' => $formData['name'],
                ':price'=> $formData['price']
            ];
            $stmt = $db->prepare($query);
            $stmt->execute($params);
            $err = $stmt->errorInfo();

            $out = [];
            $out['status'] = 'OK';
            echo json_encode($out);
            http_response_code(200);
        }
        else
        {
            $out = [];
            $out['status'] = 'BAD_REQUEST';
            echo json_encode($out);
            http_response_code(400);
        }
        return;
    }

    if ($method === 'GET' && count($urlData) === 1)                 //Получение одной записи
    {
        $id = $urlData[0];
        $stmt = $db->query("SELECT * FROM items WHERE id=$id");
        $row = $stmt->fetch();

        if ($row != false)
        {
            $rowjson = [];
            $rowjson['id'] = $row['id'];
            $rowjson['name'] = $row['name'];
            $rowjson['price'] = $row['price'];
            echo json_encode($rowjson);
            http_response_code(200);
        }
        else
        {
            $out = [];
            $out['status'] = 'BAD_REQUEST';
            echo json_encode($out);
            http_response_code(400);
        }
        return;
    }

    if ($method === 'PUT' && count($urlData) === 1)                     //Обновление записи
    {
        $id = $urlData[0];
        if ($formData != null)
        {
            $query = "UPDATE items SET name = :name, price = :price WHERE id=$id";
            $params = [
                ':name' => $formData['name'],
                ':price'=> $formData['price']
            ];
            $stmt = $db->prepare($query);
            $stmt->execute($params);
            $err = $stmt->errorInfo();

            $out = [];
            $out['status'] = 'OK';
            echo json_encode($out);
            http_response_code(200);
        }
        else
        {
            $out = [];
            $out['status'] = 'BAD_REQUEST';
            echo json_encode($out);
            http_response_code(400);
        }
        return;
    }

    if ($method === 'DELETE' && count($urlData) === 1)                  //Удаление записи
    {
        $id = $urlData[0];
        $query = "DELETE FROM `items` WHERE `id` = $id";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $out = [];
        $out['status'] = 'OK';
        echo json_encode($out);
        http_response_code(200);
        return;
    }
}