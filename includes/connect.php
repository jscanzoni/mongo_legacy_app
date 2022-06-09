<?php

session_start();

require 'vendor/autoload.php';

//app id set?
if(isset($_GET['app_id'])){
    $_SESSION['app_id']= clean_data($_GET['app_id']);
}else{
    if(!isset($_SESSION['app_id']))
        die("No app id was set");
}
$app_id= $_SESSION['app_id'];

//establish mongo connection

$connectionString = getenv('CONNECTION_STRING');

$database = (new MongoDB\Client($connectionString))->legacy_apps;

$config_collection = $database->app_config;
$data_collection = $database->data;
$default_collection = $database->default_data;

$app_query = ['app_id' => $app_id];

//app id exists?
$error = error_reporting();
error_reporting(E_ERROR | E_PARSE);
$config = $config_collection->findOne($app_query)->config or die("$app_id does not exist");;
error_reporting($error);

//app_settings
$appname = $config["app_settings"]["appname"];
$bannerColor = (isset($config["app_settings"]["bannerColor"]) ? $config["app_settings"]["bannerColor"]: "#999999");
$bannerTextColor = (isset($config["app_settings"]["bannerTextColor"]) ? $config["app_settings"]["bannerTextColor"]: "#FFFFFF");
$domain = $config["app_settings"]["domain"];
$copyright = $config["app_settings"]["copyright"];
$recordName = $config["app_settings"]["recordName"];
$shortName = $config["app_settings"]["shortName"];
$shortNameLower = strtolower($shortName);
$shortNamePlural = $config["app_settings"]["shortNamePlural"];
$lineItemRecordName = $config["app_settings"]["lineItemRecordName"];
$lineItemRecordNamePlural = $config["app_settings"]["lineItemRecordNamePlural"];
$apiKey = $config["app_settings"]["apiKey"];

//fields object
$fields = $config["fields"];

$groups = [];

foreach($fields as $field){
    if (isset($field->group)){
        if (!in_array($field->group,$groups)){
            $groups[]=$field->group;
        }
    }
}

//summary fields array
$summaryFields = (array)$config["summaryFields"];

//webhook actions
$webhook_enabled = strlen($config["app_settings"]["webhook"]["key"]) > 1 ? true : false;
$webhook_url = $config["app_settings"]["webhook"]["url"];
$webhook_key = $config["app_settings"]["webhook"]["key"];
$webhook_tetherField = $config["app_settings"]["webhook"]["enabledWhenBlankTetherField"];
$webhook_actionDescription = $config["app_settings"]["webhook"]["actionDescription"];
$webhook_processPageDescription = $config["app_settings"]["webhook"]["processPageDescription"];



function clean_data($data) {
    $data = strip_tags($data);
    $data = htmlspecialchars($data);
    $data = stripslashes($data);
    $data = trim($data);
    return $data;
}

function displayName($field) {
    global $fields;

    foreach($fields as $f){
        if ($field==$f->field){
            
            return $f->friendlyName;
            break;
        }
    }
}