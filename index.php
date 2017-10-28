<?php
include_once 'class/html.class.php';
include_once 'class/db.class.php';
include_once 'include/parameters.php';

session_start();

$db = new db();
$html = new html(true);

//for the paginator: page 1 is displayed in case of invalid input
$page = 1;
if(is_numeric($_GET['page']))
	if($_GET['page'] > 0)
		$page = $_GET['page'];

//set specific invoice paid/unpaid after validating request
if(isset($_GET['id']) && isset($_GET['status']))
{
	if(is_numeric($_GET['id']) && ($_GET['status'] == 'paid' || $_GET['status'] == 'unpaid'))
		if(!$db->invoiceSetStatus($_GET['id'], $_GET['status']))
			$html->error('Error setting invoice status');
}
		
$invoices = $db->countInvoices();
if ($invoices < 1) //no invoices to display
	$html->error("No invoices to display");
else //we have invoices to display
{
	$last = (int)ceil($invoices / _PAGELIMIT); //last page for the paginator
	if ($page > $last) //validate page input, we don't want the paginator to show page 3 of 2 
		$page = $last;
	$first_invoice = _PAGELIMIT * ($page - 1); //first invoice of this page
	
	//build and display menu
	$menu = array(
		array ('title' => 'Export invoices', 'url' => 'export.php?action=export'),
		array ('title' => 'Export invoices by customer', 'url' => 'export.php?action=export_cust')
	);
	$html->menu($menu);
	
	//display paginator
	$html->printPaginator($page, $last);
	
	//create table with invoices, add the 'set paid/unpaid' switch and display the table
	$table = $db->listInvoices($first_invoice);
	$html->addPaidSwitch($table, $page);
	$html->printTable($table);
}
				
$db->close();
$html->close();