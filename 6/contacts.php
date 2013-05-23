<?php
/**
INSTALLATION:

This script uses the Zend Gdata library: you can download it at http://framework.zend.com/downloads/latest

1) download Zend Framework: wget http://packages.zendframework.com/releases/ZendGdata-1.12.0/ZendGdata-1.12.0.tar.gz
2) extract the archive: tar xzvf ZendGdata-1.12.0.tar.gz
2) rename it: mv ZendGdata-1.12.0 ZendGdata
3) configure your GMail credentials:
3) configure $pattern and $replacement: this vars are used to manipulate the phone number with preg_replace($pattern, $replacement, $phone_number)
**/

$user = "ciccio@pasticcio.com"; // GMail username
$pass = "XXXXXXXXXXXXXXXXXXXX"; // GMail password

// This example adds a 0 in front of the number
$pattern = '/(.*)/';
$replacement = '0$1';

// Zend Framework path:
set_include_path(get_include_path() . PATH_SEPARATOR . "ZendGdata/library");

header('Content-Type:text/xml');
?>
<?xml version="1.0" encoding="utf-8"?>
<?php

require_once 'Zend/Loader.php';

Zend_Loader::loadClass('Zend_Gdata');
Zend_Loader::loadClass('Zend_Gdata_ClientLogin');
Zend_Loader::loadClass('Zend_Http_Client');
Zend_Loader::loadClass('Zend_Gdata_Query');
Zend_Loader::loadClass('Zend_Gdata_Feed');
 
if (isset($_GET["q"])){ 
  try {
    $client = Zend_Gdata_ClientLogin::getHttpClient($user, $pass, 'cp');
 
    $gdata = new Zend_Gdata($client);
    $gdata->setMajorProtocolVersion(3);
 
    $query = new Zend_Gdata_Query('http://www.google.com/m8/feeds/contacts/default/full?q='.$_GET["q"].'&max-results=10000');
    $feed = $gdata->getFeed($query);
    $results=array();
    $print = false;
    $out_buffer = "<SnomIPPhoneDirectory>
<Title>Menu</Title>
<Prompt>Prompt</Prompt>
";

    foreach($feed as $entry){
    
      $xml = simplexml_load_string($entry->getXML());
      $name = array();
      $org = (string)$xml->organization->orgName;
      if(!empty($org)) {
        $name[] = $org;
      }
 
      $title = (string)$entry->title;
      if(!empty($title)) {
        $name[] = $title;
      }

      if(count($name) > 0 && count($xml->phoneNumber)) {	
        foreach ($xml->phoneNumber as $p) {
          // apply number subst.
	  $tel = preg_replace($pattern,$replacement,(string)$p);
	  $label = isset($p['label']) ? $p['label'] : preg_replace('/^((?:.*)#)/', '', $p['rel']);
	  $key = implode(" - ", $name) . " - " . $label;
          $out_buffer = $out_buffer . "<DirectoryEntry>
<Name>$key</Name>
<Telephone>$tel</Telephone>
</DirectoryEntry>
";
        }
     $print = true;
     } else {
       continue;
     }
   } 
   $out_buffer = $out_buffer . "
</SnomIPPhoneDirectory>";
    if ($print == false) {
?>
<SnomIPPhoneText>
 <Title>Not found</Title>
 <Prompt>Prompt Text</Prompt>
 <Text>
  No entry found
 </Text>
</SnomIPPhoneText>
<?php
  } else {
  echo $out_buffer;
}  
  } catch (Exception $e) {
?>
<SnomIPPhoneText>
 <Title>ERROR</Title>
 <Prompt>Prompt Text</Prompt>
 <Text>
 <?php echo "Error occoured: $e->getMessage()"; ?>
 </Text>
</SnomIPPhoneText>
<?php
  die();  
  } 
} else {
?>
<SnomIPPhoneInput>
<Title>Search</Title>
<Prompt>Prompt</Prompt>
<URL>http://<?php echo $_SERVER['SERVER_NAME'] . $_SERVER["PHP_SELF"] ?></URL>
<InputItem>
<DisplayName>Search:</DisplayName>
<QueryStringParam>q</QueryStringParam>
<DefaultValue/>
<InputFlags>a</InputFlags>
</InputItem>
</SnomIPPhoneInput>
<?php
}
