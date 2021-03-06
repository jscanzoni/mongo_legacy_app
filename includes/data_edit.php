<?php

$dataNo = (isset($_GET['i']) ? $_GET['i'] : null);

$data_list = (array)($data_collection->findOne($app_query)->data);

$newId = (isset($_GET['i']) ? $_GET['i'] : $_POST['id']);

$data = [];
$key = count($data_list);

foreach ($data_list as $k => $d) {
    if ($d->id == $newId) {
        $data=$d;
        $key=$k;
    }
}
//var_dump($data);

$numLines = isset($_POST["numLines"]) ? intval($_POST["numLines"]) : 0;

//add a new line
if (isset($_POST['new-lineitem'])) {
    $numLines++;
}


//populate $data array
foreach ($fields as $field) {
    if (isset($field->group) and $field->group == "Line Items") {

        for ($i = 0; $i < $numLines; $i++) {
            if(!isset($data["lineitems"][$i]))
                $data["lineitems"][$i]=[];
            if(!isset($data["lineitems"][$i][$field->field]))
                $data["lineitems"][$i][$field->field]="";

            $data["lineitems"][$i][$field->field] = isset($_POST["lineitem-" . $field->field . $i]) ? $_POST["lineitem-" . $field->field . $i] : $data["lineitems"][$i][$field->field];
        }
    } else {

        $data[$field->field] = isset($_POST[$field->field]) ? $_POST[$field->field] : $data[$field->field];
    }
}

//save new record
if (isset($_POST["submit"])) {

    //populate $data array 

    $data["lineitems"] = [];

    foreach ($fields as $field) {
        if ($field->field == "id")
            $_POST[$field->field] = intval($_POST[$field->field]);

        if (isset($field->group) and $field->group == "Line Items") {
            for ($i = 0; $i < $numLines; $i++) {
                $data["lineitems"][$i][$field->field] = isset($_POST["lineitem-" . $field->field . $i]) ? $_POST["lineitem-" . $field->field . $i] : "";
            }
        } else {

            $data[$field->field] = isset($_POST[$field->field]) ? $_POST[$field->field] : "";
        }
    }

    $data_list[$key] = $data;

  //  echo "<div id=\"success\" style=\"background-color:green;color:white;text-align:center;width:100%;\">SUCCESS</div>";

    $updateDocument = $data_collection->updateOne(
        $app_query,
        ['$set' => ['data' => $data_list]]
    );

    //redirect to read-only
    unset($_POST);
    echo "<script>
        setTimeout(function() {
        window.location.href = \"./view.php?i=$newId\";}, 0);
        </script>";
}

echo "<h2 style=\"text-align: center;\">Edit $recordName #" . ($newId) . "</h2>";
echo "<form action=\"" . $_SERVER['PHP_SELF'] . "\" method=\"POST\">";
foreach ($fields as $field) {
    if ($field->type == "hidden")
        echo "<input type=\"hidden\" id=\"$field->field\" name=\"$field->field\" value=\"" . $data[$field->field] . "\">";
}
foreach ($groups as $group) {
    if ($group == "Line Items") {
        echo "<h3>" . $lineItemRecordNamePlural . " (" . count($data["lineitems"]) . ")</h3>";
        echo "<input type=\"hidden\" id=\"numLines\" name=\"numLines\" value=\"" . count($data["lineitems"]) . "\">";
        echo "<!--set number of lines as a GET variable using \"?n=" . count($data["lineitems"]) . "\"-->";
        echo "<table width=\"100%\" style=\"background-color:#f5f5f5; text-align:center;\"><tr>";
        foreach ($fields as $field) {
            if (isset($field->group) and $field->group == $group) {
                echo "<th>";
                echo $field->friendlyName;
                echo "</th>";
            }
        }
        echo "</tr>";
        foreach ($data['lineitems'] as $key => $line) {
            echo "<tr style=\"border: 1px solid #666666;\">";
            foreach ($fields as $field) {
                if (isset($field->group) and $field->group == $group) {

                    echo "<td id=\"lineitem-$key-field-" . $field->field . "\">";

                    switch($field->type){
                        case "text":
                            echo "<input type=\"text\" id=\"lineitem-" . $field->field . "". $key . "\" name=\"lineitem-" . $field->field. $key . "\" value=\"" . $line[$field->field] . "\" >";
                            break;
                        case "select":
                            //array_unshift((array)$field->options,"");
                            echo "<select type=\"text\" id=\"lineitem-" . $field->field . "". $key . "\" name=\"lineitem-" . $field->field . "". $key . "\" >";
                            echo "<option value=\"\"> -- Select a value -- </option>";
                            foreach($field->options as $option){
                                echo "<option value=\"" . $option . "\"".($line[$field->field]==$option?" selected":""). ">" . $option . "</option>";
                            }
                            echo "</select>";
                            break;
                        case "date":
                            echo "<input type=\"text\" id=\"lineitem-" . $field->field . "". $key . "\" name=\"lineitem-" . $field->field. $key . "\" value=\"" . $line[$field->field] . "\" >";
                            break;
                        default:
                        echo "<input type=\"text\" id=\"lineitem-" . $field->field . "". $key . "\" name=\"lineitem-" . $field->field. $key . "\" value=\"" . $line[$field->field] . "\" >";
                        break;
                        }
                        echo "</td>";

                    }

            }
            echo "</tr>";
        }
        echo "<tr style=\"border: 1px solid #666666;\"><td colspan=\"40\"><input id=\"new-lineitem\" name=\"new-lineitem\" type=\"submit\" value=\"Add $lineItemRecordName\" /></td></tr>";
        echo "</table>";
    } else {
        echo "<h3>$group</h3>";
        echo "<table>";
        foreach ($fields as $field) {
            if (isset($field->group) and $field->group == $group and $field->type != "hidden") {
                echo "<tr><td style=\"padding:4px\"><strong><label for=\"" . $field->field . "\">";
                echo $field->friendlyName;
                echo "</label></strong></td>";
                switch($field->type){
                    case "text":
                        echo "<td id=\"field-" . $field->field . "\" style=\"padding:4px\"><input type=\"text\" id=\"" . $field->field . "\" name=\"" . $field->field . "\" value=\"" . $data[$field->field] . "\" ></td></tr>";
                        break;
                    case "select":
                        //array_unshift((array)$field->options,"");
                        echo "<td id=\"field-" . $field->field . "\" style=\"padding:4px\"><select type=\"text\" id=\"" . $field->field . "\" name=\"" . $field->field . "\" >";
                        echo "<option value=\"\"> -- Select a value -- </option>";
                        foreach($field->options as $option){
                            echo "<option value=\"" . $option . "\"".($data[$field->field]==$option?" selected":""). ">" . $option . "</option>";
                        }

                        echo "</select></td></tr>";
                        break;
                    case "date":
                        echo "<td id=\"field-" . $field->field . "\" style=\"padding:4px\"><input type=\"text\" id=\"" . $field->field . "\" name=\"" . $field->field . "\" value=\"" . $data[$field->field] . "\" ></td></tr>";
                        break;
                    default:
                        echo "<td id=\"field-" . $field->field . "\" style=\"padding:4px\"><input type=\"text\" id=\"" . $field->field . "\" name=\"" . $field->field . "\" value=\"" . $data[$field->field] . "\" ></td></tr>";
                        break;
                    }
                }
            }
            echo "</table><br />";

        }
        
    }


echo "<div style=\"text-align:center;\"><input id=\"submit\" name=\"submit\" type=\"submit\" value=\"Submit\" /></div>";
echo "</form>";
