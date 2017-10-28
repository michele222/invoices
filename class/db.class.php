<?php
require_once 'include/parameters.php';

//handles db related functions
class db
{
	private $sql;
	
	//connection to mysql db
	function __construct()
	{
		$this->sql = new mysqli(_DBHOST,_DBUSER,_DBPWD,_DBNAME);
		if (mysqli_connect_error())
			return false;
		return true;
	}
	
	//close db connection
	public function close()
	{
		return $this->sql->close();
	}
	
	//run mysql query
	public function query($text)
	{
		return $this->sql->query($text);
	}
	
	//returns number of rows affected by query
	public function affectedRows()
	{
		return $this->sql->affected_rows;
	}
	
	//escaping function to prevent code injection
	public function escape($text)
	{
		return $this->sql->real_escape_string($text);
	}
	
	//returns total number of invoices, 0 in case of error
	public function countInvoices()
	{
		$ret = 0;
		$query = "SELECT count(id) AS number FROM invoices";
		$result = $this->query($query);
		if ($row = $result->fetch_assoc())
			$ret = $row['number'];
		$result->free();
		return $ret;
	}
	
	//returns records from table invoices, limited to _PAGELIMIT records starting from record $from
	public function listInvoices($from)
	{
		$ret = array();
		$query = "SELECT * FROM invoices";
		if(is_int($from))
			$query .= " LIMIT ".$from.", "._PAGELIMIT;
		$result = $this->query($query);
		while ($row = $result->fetch_assoc())
			$ret[] = $row;
		$result->free();
		return $ret;
	}
	
	//set status $status to invoice with id $id
	public function invoiceSetStatus($id, $status)
	{
		$query = "UPDATE invoices SET invoice_status='".$this->escape($status)."' WHERE id='".$this->escape($id)."'";
		return ($this->query($query));
	}
	
	//export all invoices in csv file $csv
	public function exportInvoices($csv)
	{
		if (!fputcsv($csv, array('id','client','invoice_amount')))
			return false;
		$query = "SELECT id, client, invoice_amount FROM invoices";
		$result = $this->query($query);
		while ($row = $result->fetch_assoc())
			if (!fputcsv($csv, $row))
				return false;
		$result->free();
		
		return true;
	}
	
	//export customer report in csv file $csv
	public function exportInvoicesByCustomer($csv)
	{
		if (!fputcsv($csv, array('client','tot_invoiced','tot_paid','tot_unpaid')))
			return false;
		$query = "
		SELECT DISTINCT(invoices.client) AS client,
		COALESCE(paid.tot_invoice_amount,0) + COALESCE(unpaid.tot_invoice_amount,0) AS tot_invoiced,
		COALESCE(paid.tot_invoice_amount,0) AS tot_paid, 
		COALESCE(unpaid.tot_invoice_amount,0) AS tot_unpaid 
		FROM invoices
		LEFT JOIN
		    (SELECT client, 
		    SUM(invoice_amount) AS tot_invoice_amount
		    FROM invoices
		    WHERE invoice_status = 'paid'
		    GROUP BY client) paid ON invoices.client = paid.client
		LEFT JOIN
		    (SELECT client, 
		    SUM(invoice_amount) AS tot_invoice_amount
		    FROM invoices
		    WHERE invoice_status = 'unpaid'
		    GROUP BY client) unpaid ON invoices.client = unpaid.client";
		$result = $this->query($query);
		while ($row = $result->fetch_assoc())
			if (!fputcsv($csv, $row))
				return false;
		$result->free();
		
		return true;
	}

}