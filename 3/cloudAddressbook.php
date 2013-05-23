<?php
/*
INSTALLATION:

This script uses the Zend Gdata library: you can download it at http://framework.zend.com/downloads/latest

1) download Zend Framework: wget http://packages.zendframework.com/releases/ZendGdata-1.12.0/ZendGdata-1.12.0.tar.gz
2) extract the archive: tar xzvf ZendGdata-1.12.0.tar.gz
2) rename it: mv ZendGdata-1.12.0 ZendGdata
3) configure your GMail credentials: 
*/
 
// Zend library include path
set_include_path(get_include_path() . PATH_SEPARATOR . "ZendGdata/library");
 
include_once("Google_Spreadsheet.php");
 
$user = "test@example.com";
$pass = "thisIsMyPassword";
$doc_name = "Cloud Addressbook";
$calls_sheet = "Calls"; 
$addressbook_sheet = "Customers";

$ss = new Google_Spreadsheet($user,$pass);
$ss->useSpreadsheet($doc_name);
$ss->useWorksheet($addressbook_sheet);

switch ($_GET['action']) {
	case 'get_entry':
		get_entry($ss, $_GET['id']);
		break;
	
	case 'del_entry':
		del_entry($ss, $_GET['id']);
		break;
	
	case 'add_entry':
		if (isset($_GET['step'])) {
			add_entry($ss, $_GET['step']);
		} else add_entry($ss, 0); 
		break;

	case 'add_demo_data':
		add_demo_data($ss);
		break;

	default:
		list_all($ss);
		break;
}

/*
*
* add_demo_data: add some demo entries
*
*/
function add_demo_data($ss){
	$ss->deleteRow('id >= 0');
	$new_rows = array
		(
			array(
				"id" => "0",
				"name" => "John Fante",
				"home" => "012345678",
				"work" => "0234345623",
				"mobile" => "34567893223",
			),
			array(
				"id" => 1,
				"name" => "Ray Bradbury",
				"home" => "012345678",
				"work" => "0234345623",
				"mobile" => "34567893223",
			),
			array(
				"id" => 2,
				"name" => "Franz Kafka",
				"home" => "012345678",
				"work" => "0234345623",
				"mobile" => "34567893223",
			),
			array(
				"id" => 3,
				"name" => "Charles Bukowsky",
				"home" => "012345678",
				"work" => "0234345623",
				"mobile" => "34567893223",
			),

	);
	foreach ($new_rows as $new_row) $ss->addRow($new_row)	
?>
<?xml version="1.0" encoding="UTF-8"?>
<SnomIPPhoneText track="no">
 <Title>Done</Title>
 <Prompt>Prompt Text</Prompt>
 <Text>Demo data loaded</Text>
 <fetch mil="500"><?php echo htmlentities("http://" . $_SERVER['SERVER_NAME'] . $_SERVER['PHP_SELF']);?></fetch>
</SnomIPPhoneText>
<?php
}

/*

*
* add_entry: add an addressbook entry
*
*/

function add_entry($ss, $step){
	header('Content-type: text/xml');
	switch ($step) {

		case 1:
// STEP 1 Ask for Home:
?>
<?xml version="1.0" encoding="UTF-8"?>
<SnomIPPhoneInput track="no">
 <Title>Home phone:</Title>
 <Prompt>Prompt</Prompt>
 <URL><?php echo htmlentities("http://" . $_SERVER['SERVER_NAME'] . $_SERVER['PHP_SELF'] . "?action=add_entry&name=" . $_GET['name'] . "&step=2");?></URL>
  <InputItem>
   <DisplayName>Home phone:</DisplayName>
   <QueryStringParam>home</QueryStringParam>
   <DefaultValue/>
   <InputFlags>a</InputFlags>
 </InputItem>
</SnomIPPhoneInput>	
<?php
			break;
		
		case 2:
// STEP 2 Ask for Work:
?>
<?xml version="1.0" encoding="UTF-8"?>
<SnomIPPhoneInput track="no">
 <Title>Work phone:</Title>
 <Prompt>Prompt</Prompt>
 <URL><?php echo htmlentities("http://" . $_SERVER['SERVER_NAME'] . $_SERVER['PHP_SELF'] . "?action=add_entry&home=" . $_GET['home'] . "&name=" . $_GET['name'] . "&step=3");?></URL>
  <InputItem>
   <DisplayName>Work phone:</DisplayName>
   <QueryStringParam>work</QueryStringParam>
   <DefaultValue/>
   <InputFlags>a</InputFlags>
 </InputItem>
</SnomIPPhoneInput>	
<?php
			break;
	
		case 3:
// STEP 3 Ask for Mobile:
?>
<?xml version="1.0" encoding="UTF-8"?>
<SnomIPPhoneInput track="no">
 <Title>Mobile phone:</Title>
 <Prompt>Prompt</Prompt>
 <URL><?php echo htmlentities("http://" . $_SERVER['SERVER_NAME'] . $_SERVER['PHP_SELF'] . "?action=add_entry&work=" . $_GET['work'] . "&home=" . $_GET['home'] . "&name=" . $_GET['name'] . "&step=4");?></URL>
  <InputItem>
   <DisplayName>Mobile phone:</DisplayName>
   <QueryStringParam>mobile</QueryStringParam>
   <DefaultValue/>
   <InputFlags>a</InputFlags>
 </InputItem>
</SnomIPPhoneInput>	
<?php

			break;

		case 4:
// STEP 4 Done:
			$r = $ss->getRows();
			$new_id=0;
			foreach($r as $tmp_row){
				if ($tmp_row['id'] > $new_id) $new_id = $tmp_row['id'];	
			}
			$row = $r[0];
			if ($row){
				$new_row = array
				(
					"id" => $new_id,
					"name" => $_GET['name'],
					"home" => $_GET['home'],
					"work" => $_GET['work'],
					"mobile" => $_GET['mobile']
				);
				if ($ss->addRow($new_row)){	
?>
<?xml version="1.0" encoding="UTF-8"?>
<SnomIPPhoneText track="no">
 <Title>Done</Title>
 <Prompt>Prompt Text</Prompt>
 <Text><?php echo "New entry added:<br/>
	Name: " . $_GET['name'] . "<br/>
	Home phone: " . $_GET['home'] . "<br/>
	Work phone: " . $_GET['work'] . "<br/>
	Moile phone: " . $_GET['mobile'] . "<br/>" ?>
  </Text>
 <fetch mil="500"><?php echo htmlentities("http://" . $_SERVER['SERVER_NAME'] . $_SERVER['PHP_SELF']);?></fetch>
</SnomIPPhoneText>
<?php
				} else print_error("Error during add");
			} else print_error("Error during search for MAX ID");

			break;

// STEP 0 Ask for name:
		case 0:
		default:
?>
<?xml version="1.0" encoding="UTF-8"?>
<SnomIPPhoneInput track="no">
 <Title>Name:</Title>
 <Prompt>Prompt</Prompt>
 <URL><?php echo htmlentities("http://" . $_SERVER['SERVER_NAME'] . $_SERVER['PHP_SELF'] . "?action=add_entry&step=1");?></URL>
  <InputItem>
   <DisplayName>Name:</DisplayName>
   <QueryStringParam>name</QueryStringParam>
   <DefaultValue/>
   <InputFlags>a</InputFlags>
 </InputItem>
</SnomIPPhoneInput>	
<?php
	}
}

/*
*
* del_entry: delete an addressbook entry
*
*/

function del_entry($ss, $id){
	$r = $ss->getRows("id=" . $id);
	$row = $r[0];
	if ($row){
		header('Content-type: text/xml');
		if ($ss->deleteRow("id=" . $id)) {
		?>
<?xml version="1.0" encoding="UTF-8"?>
<SnomIPPhoneText track="no">
 <Title><?php echo $row['name'] . " Deleted";?></Title>
 <Prompt>Prompmt text</Prompt>
 <Text><?php echo $row['name'] . " Deleted."?> </Text>
 <fetch mil="500"><?php echo htmlentities("http://" . $_SERVER['SERVER_NAME'] . $_SERVER['PHP_SELF']);?></fetch>
</SnomIPPhoneText>
		<?php
		} else print_error("Error during delete (ID:" . $id . ")");
	} else print_error("Cannot find entry (ID:" . $id . ")");
}
/*
*
* get_entry: get an addressbook entry
*
*/

function get_entry($ss, $id){
	$r = $ss->getRows("id=" . $id);
	$row = $r[0];
	if ($row){
		header('Content-type: text/xml');
		?>
<?xml version="1.0" encoding="UTF-8"?>
<SnomIPPhoneDirectory track="no">
 <Title><?php echo $row['name']; ?></Title>
 <Prompt>Prompt</Prompt>
 <DirectoryEntry>
  <Name>Home</Name>
  <Telephone><?php echo $row['home']; ?></Telephone>
 </DirectoryEntry>
 <DirectoryEntry>
  <Name>Work</Name>
  <Telephone><?php echo $row['work']; ?></Telephone>
 </DirectoryEntry>
 <DirectoryEntry>
  <Name>Mobile</Name>
  <Telephone><?php echo $row['mobile']; ?></Telephone>
 </DirectoryEntry>

 <SoftKeyItem>
  <Name>F1</Name>
  <Label>DELETE</Label>
  <URL><?php echo htmlentities("http://" . $_SERVER['SERVER_NAME'] . $_SERVER['PHP_SELF'] . "?action=del_entry&id=" . $row["id"]);?></URL>
 </SoftKeyItem>
 <SoftKeyItem>
  <Name>F2</Name>
  <Label>BACK</Label>
  <URL><?php echo htmlentities("http://" . $_SERVER['SERVER_NAME'] . $_SERVER['PHP_SELF']);?></URL>
 </SoftKeyItem>

</SnomIPPhoneDirectory>
	<?php
	} else print_error("Error, unable to get row");
}

/*
*
* list_all: list all addressbook entries
*
*/

function list_all($ss){
	$rows = $ss->getRows();
	if ($rows){
		header('Content-type: text/xml');
	?>
<?xml version="1.0" encoding="UTF-8"?>
<SnomIPPhoneMenu track="no">
<Title>Select Customer</Title>
	<?php
	foreach ($rows as $row) {
	?>

<MenuItem>
<Name><?php echo $row["name"];?></Name>
<URL><?php echo htmlentities("http://" . $_SERVER['SERVER_NAME'] . $_SERVER['PHP_SELF'] . "?action=get_entry&id=" . $row["id"]);?></URL>
</MenuItem>
	<?php
	}
	?>

 <SoftKeyItem>
  <Name>F1</Name>
  <Label>NEW</Label>
  <URL><?php echo htmlentities("http://" . $_SERVER['SERVER_NAME'] . $_SERVER['PHP_SELF'] . "?action=add_entry");?></URL>
 </SoftKeyItem>

 <SoftKeyItem>
  <Name>F2</Name>
  <Label>CLEAR DEMO</Label>
  <URL><?php echo htmlentities("http://" . $_SERVER['SERVER_NAME'] . $_SERVER['PHP_SELF'] . "?action=add_demo_data");?></URL>
 </SoftKeyItem>

</SnomIPPhoneMenu>


<?php
} else print_error("Error, unable to get spreadsheet data");
}

function print_error($text){
	header('Content-type: text/xml');
?>
<?xml version="1.0" encoding="UTF-8"?>
<SnomIPPhoneText track="no">
 <Title>ERROR</Title>
 <Prompt>Prompt Text</Prompt>
 <Text><?php echo $text ?></Text>
</SnomIPPhoneText>
<?php
}
?>
