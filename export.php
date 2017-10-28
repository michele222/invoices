<?php
include_once 'class/html.class.php';
include_once 'class/db.class.php';
include_once 'include/parameters.php';

session_start();

$db = new db();
$html = new html();

if(isset($_GET['action']))
	if($_GET['action'] == 'export' || $_GET['action'] == 'export_cust')
	{
		//set headers for csv file download
		header('Content-Type: text/csv; charset=utf-8');
		header('Content-Disposition: attachment; filename='._CSVFILENAME);
		
		//we are downloading the output as csv file, not displaying
		if ($csv = fopen('php://output', 'w'))
		{
			if ($_GET['action'] == 'export')
				$db->exportInvoices($csv); //export all invoices
			else 
				$db->exportInvoicesByCustomer($csv); //export customer data
			
			fclose($csv);
		}
		else 
			$html->error("Error creating csv file");
	}
				
$db->close();