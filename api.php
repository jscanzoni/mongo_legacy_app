<?php

include "./includes/connect.php";

$data_list = (array)($data_collection->findOne($app_query)->data);

$json = file_get_contents("php://input");
$body = json_decode($json, true);

$newId = (($data_list[(count($data_list) - 1)]->id) + 1);

$newArray = [];

$fieldlist = [];

foreach ($fields["visible"] as $section => $items) {
    if ($section == "Line Items") {
        $fieldlist[] = "lineitems";
    } else {
        foreach ($items as $field) {
            $fieldlist[] = $field;
        }
    }
}

if (isset($_GET['key'])) {
    if ($_GET['key'] == $apiKey) {
        switch (clean_data($_GET['action'])) {
            case "create": // api.php?app_id=test&key=1234&action=create
                if (count((array)$body) > 0) {
                    $newArray["id"] = $newId;
                    foreach ($body as $field => $value) {
                        if (in_array($field, $fieldlist))
                            $newArray[$field] = $value;
                    }
                    if (count($newArray) > 1) {
                        $data_list[] = $newArray;
                        $updateDocument = $data_collection->updateOne(
                            $app_query,
                            ['$set' => ['data' => $data_list]]
                        );
                        echo json_encode($newArray, JSON_PRETTY_PRINT);
                    }
                }
                break;
            case "update": // api.php?app_id=test&key=1234&action=update
                if (count((array)$body) > 0) {
                    if (isset($body["id"])) {

                        foreach ($data_list as $key => $data) {
                            if ($data->id == $body["id"]) {
                                $data_list[$key] = $body;

                                $updateDocument = $data_collection->updateOne(
                                    $app_query,
                                    ['$set' => ['data' => $data_list]]
                                );

                                echo json_encode($body, JSON_PRETTY_PRINT);

                                break;
                            }
                        }
                    }
                }
                break;
            case "read": // api.php?app_id=test&key=1234&action=read&id=4021
                if (isset($_GET['id'])) {
                    foreach (array_reverse($data_list) as $key => $data)
                        if ($data->id == $_GET['id'])
                            echo json_encode($data, JSON_PRETTY_PRINT);
                } else {
                    echo json_encode($data_list, JSON_PRETTY_PRINT);
                }
                break;
        }
    }

    session_destroy();
}
