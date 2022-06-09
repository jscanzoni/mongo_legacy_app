<?php

$data_list = array_reverse((array)($data_collection->findOne($app_query)->data));
unset($_POST);
if (!isset($_GET["s"]))
    $start = 1;
else
    $start = intval(clean_data($_GET["s"]));

$end = $start + 9;
$total = count($data_list);
$lastpage = false;

if ($total <= $end) {
    $end = $total;
    $lastpage = true;
}


?>


<table style="width:90%; margin-left:auto; margin-right:auto;">
    <tr>
        <td style="text-align: left;">
            <h2><?=$recordName;?> List</h2>
        </td>
        <td style="text-align: right;">
            <form action="./view.php">
                <input id="new-<?=$shortNameLower?>-button" type="submit" value="New <?=$shortName;?>" />
            </form>
        </td>
    </tr>
</table>
<br />
<table style="width:90%; border: 1px solid #666666; margin-left:auto; margin-right:auto;">
    <tr style="background-color: <?=$bannerColor?>; color: <?=$bannerTextColor?>;">
        <?php
            foreach ($summaryFields as $field){
                
                    echo "<th>".displayName($field)."</th>";
                
            }
        ?>
    </tr>
    <?php
    for ($i = $start - 1; $i < $end; $i++) {
        if ($i % 2 == 0)
            $bg = "#FFFFFF";
        else
            $bg = "#F5F5F5";

        echo "<tr style=\"background-color:$bg>\">";

        foreach ($summaryFields as $key => $field){
            if ($key === array_key_first($summaryFields)) 
                echo "<td><a id=\"$shortNameLower-link-".$data_list[$i]->$field."\" href=\"./view.php?i=".$data_list[$i]->$field."\">".(intval($data_list[$i]->$field))."</a></td>";
            else
                echo "<td>".$data_list[$i]->$field."</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
    echo "<br><br>";
    echo "$start - $end of $total<br><br>";

    if (!$lastpage)
        echo "<a href=\"list.php?s=" . ($end + 1) . "\">Next Page</a><br>";
    if ($start != 1)
        echo "<a href=\"list.php?s=" . max($start - 10, 1) . "\">Previous Page</a><br>";
