<?php
require '/config/config.inc.php'; // assuming your script is in the root folder of your site

$db = MySQLCore::getInstance();
if (($handle = fopen("test.csv", "r")) !== FALSE) 
{
$row = 1;
  while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) 
  {
     $num = count($data);
    echo "<p> $num fields in line $row: <br /></p>\n";
    $row++;
    for ($c=0; $c < $num; $c++) {
        echo $data[$c] . "<br />\n";
    }

	$db->insert("shopping_list_product", array(
		'id_shopping_list'=>(int)$data[0],
		'id_product'=>(int)$data[1],
		'id_product_attribute'=>(int)$data[2], 
		'title'=>$data[3]));
  }
	fclose($handle);
}


class vncImportShoppingList
{
	 $m_CSVfileName ; // Nom du fichier CSV
	  $dbUser ; // DBUser
	 $dbPwd;
	 $dbName;
	 $dbSrv;
	
	public function connect($pdbUser, $pdbPwd, $pdbName, $pdbSrv)
	{
		$return = false;
		try
		{
			$return = true;
		}
		catch (Exception $e)
		{
			$Return = false;
		}
		return $Return;
	}//connect
	
	public function import()
	{
		$return = false;
		try
		{
			$return = true;
		}
		catch (Exception $e)
		{
			$Return = false;
		}
		return $Return;
	}
}
?>