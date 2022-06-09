<?php
$newId = clean_data($_GET['i']);


$data_list = (array)($data_collection->findOne($app_query)->data);



//var_dump($data_list);
echo "<h2 style=\"text-align: center;\">$recordName #" . ($newId) . "</h2>";
foreach (array_reverse($data_list) as $key => $data) {
    if ($data->id == $newId) {
        //var_dump($data);
        foreach ($fields as $field) {
            if ($field->type == "hidden")
                echo "<input type=\"hidden\" id=\"$field->field\" name=\"$field->field\" value=\"" . $data[$field->field] . "\">";
        }
        foreach ($groups as $group) {
            if ($group == "Line Items") {
                echo "<h3>" . $lineItemRecordNamePlural . " (" . count($data->lineitems) . ")</h3>";
                echo "<input type=\"hidden\" id=\"numLines\" name=\"numLines\" value=\"" . count($data->lineitems) . "\">";
                echo "<table width=\"100%\" style=\"background-color:#f5f5f5; text-align:center;\"><tr>";
                foreach ($fields as $field) {
                    if (isset($field->group) and $field->group == $group) {
                        echo "<th>";
                        echo $field->friendlyName;
                        echo "</th>";
                    }
                }
                echo "</tr>";
                foreach ($data->lineitems as $key => $line) {
                    echo "<tr style=\"border: 1px solid #666666;\">";
                    foreach ($fields as $field) {
                        if (isset($field->group) and $field->group == $group) {

                            echo "<td id=\"lineitem-$key-field-" . $field->field . "\">";
                            echo "<input type=\"text\" id=\"lineitem-" . $field->field . "" . $key . "\" name=\"lineitem-" . $field->field . $key . "\" value=\"" . $line[$field->field] . "\" disabled>";
                            echo "</td>";
                        }
                    }
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "<h3>$group</h3>";
                echo "<table>";
                foreach ($fields as $field) {
                    if (isset($field->group) and $field->group == $group and $field->type != "hidden") {
                        echo "<tr><td style=\"padding:4px\"><strong><label for=\"" . $field->field . "\">";
                        echo $field->friendlyName;
                        echo "</label></strong></td>";
                        echo "<td id=\"field-" . $field->field . "\" style=\"padding:4px\"><input type=\"text\" id=\"" . $field->field . "\" name=\"" . $field->field . "\" value=\"" . $data[$field->field] . "\" disabled></td></tr>";
                    }
                }
                echo "</table><br />";
            }
        }

        echo "<div style=\"text-align:center;\">";
        echo "<table width=\"100%\"><tr><td width=\"30%\"></td>";
        echo "<td><form action=\"./edit.php?i=$newId\" method=\"POST\">
        <input type=\"hidden\" id=\"id\" name=\"id\" value=\"$newId\">
        <input id=\"edit\" name=\"edit\" type=\"submit\" value=\"Edit\" />
        </form></td>";

        if ($webhook_enabled) {
            if ($webhook_tetherField == "" | $data->$webhook_tetherField == "") {
                echo "<td><form action=\"./update.php\" method=\"POST\">
                    <input type=\"hidden\" id=\"id\" name=\"id\" value=\"$newId\">
                    <input id=\"action-$shortNameLower-$newId-button\" type=\"submit\" value=\"" . $webhook_actionDescription . "\" name=\"action\"/>
                </form></td>";
            }
        }
        echo "</div><td width=\"30%\"></td></tr></table>";
        break;
    }
}
