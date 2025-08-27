<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Auto Fill Sample Data</title>
</head>

<body>


<?php
echo "

<!-- Mr. TTA -->
<!-- Start Auto Fill Sample Data -->
";
$db_name=$_GET["db_name"];
$conn = mysql_connect("localhost","root","");
mysql_select_db($db_name,$conn);
mysql_set_charset("utf8"); 
	$blockconfig_sql = "SELECT ps_spcustomhtml.id_spcustomhtml,ps_spcustomhtml.params, ps_hook.name, ps_spcustomhtml_lang.content, ps_spcustomhtml_lang.title_module 
						FROM ps_spcustomhtml INNER JOIN ps_spcustomhtml_lang ON ps_spcustomhtml.id_spcustomhtml = ps_spcustomhtml_lang.id_spcustomhtml INNER JOIN ps_hook ON id_hook = hook
						WHERE ps_spcustomhtml_lang.id_lang = '1'";
	$blockconfig_result = mysql_query($blockconfig_sql);
while($blockconfig_row=mysql_fetch_array($blockconfig_result))
{
	$params = unserialize($blockconfig_row["params"]);
echo '
array(
\'active\' => '.$params["active"].',
\'id_spcustomhtml\' => '.$blockconfig_row["id_spcustomhtml"].',
\'hook\' => Hook::getIdByName(\''.$blockconfig_row["name"].'\'),
\'title_module\' => \''.$blockconfig_row["title_module"].'\',
\'content\' => 	\''.$blockconfig_row["content"].'\',
\'moduleclass_sfx\' => \''.$params["moduleclass_sfx"].'\',
\'display_title_module\' =>  '.$params["display_title_module"].'
),
';
}
echo '<!-- Finish Auto Fill Sample Data -->
';

	
?> 
</body>

</html>