<?php
function CountUnreadMail($host, $login, $passwd) {
    $mbox = imap_open($host, $login, $passwd);
    $count = 0;
    if (!$mbox) {
    	return -1;
    } else {
        $headers = imap_headers($mbox);
        foreach ($headers as $mail) {
            $flags = substr($mail, 0, 4);
            $isunr = (strpos($flags, "U") !== false);
            if ($isunr)
            $count++;
        }
    }

    imap_close($mbox);
    return $count;
}

$hostname = '{imap.gmail.com:993/imap/ssl}INBOX';

header('Content-type: text/xml');

if (isset($_GET['password'])){
	$count = CountUnreadMail($hostname, $_GET['account'], $_GET['password']);
	if ($count>=0) {
//Print result
?>
<?xml version="1.0" encoding="UTF-8"?>
<SnomIPPhoneText>
 <Title>Inbox</Title>
 <Prompt>Text</Prompt>
 <Text><?php echo "You have " . $count . " new mail.";?></Text>
</SnomIPPhoneText>
<?php
	} else {
//Error
?>
<?xml version="1.0" encoding="UTF-8"?>
<SnomIPPhoneText>
 <Title>Error</Title>
 <Prompt>Text</Prompt>
 <Text>Error during mailbox access.</Text>
</SnomIPPhoneText>
<?php
	
	}
	die();
}

if(isset($_GET['account'])){
//Ask for password
?>
<?xml version="1.0" encoding="UTF-8"?>
<SnomIPPhoneInput>
<Title>Gmail Password</Title>
<Prompt>Prompt</Prompt>
<URL><?php echo htmlentities("http://" . $_SERVER['SERVER_NAME'] . $_SERVER['PHP_SELF'] . "?account=" . $_GET['account']); ?></URL>
<InputItem>
<DisplayName>Gmail Password</DisplayName>
<QueryStringParam>password</QueryStringParam>
<DefaultValue/>
<InputFlags>p</InputFlags>
</InputItem>
</SnomIPPhoneInput>
<?php
	die();
} else {
//Ask for Account
?>
<?xml version="1.0" encoding="UTF-8"?>
<SnomIPPhoneInput>
<Title>Gmail Account</Title>
<Prompt>Prompt</Prompt>
<URL><?php echo htmlentities("http://" . $_SERVER['SERVER_NAME'] . $_SERVER['PHP_SELF']); ?></URL>
<InputItem>
<DisplayName>Gmail Account</DisplayName>
<QueryStringParam>account</QueryStringParam>
<DefaultValue/>
<InputFlags>a</InputFlags>
</InputItem>
</SnomIPPhoneInput>
<?php
	die();
}
?>
