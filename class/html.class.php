<?php

//handles html and front-end related functions
class html
{
	//html header and body initialized if $init is true
	function __construct($init = false)
	{
		if($init)
		{
			$this->header();
			$this->bodyon();
			$this->div();
		}
	}
	
	//display html body and footer
	function close()
	{
		$this->divoff();
		$this->bodyoff();
		$this->footer();
	}
	
	//display header
	public function header()
	{
		readfile("html/header.html");
	}
	
	//display footer
	public function footer()
	{
		readfile("html/footer.html");
	}
	
	//display tag <div>
	public function div(){echo '<div>';}
	
	//display tag <body>
	public function bodyon(){echo '<body>';}
	
	//display tag </body>
	public function bodyoff(){echo '</body>';}
	
	//display tag </div>
	public function divoff() {echo '</div>';}
	
	//display error message
	public function error($text) {echo '<div class="errmsg">'.$text.'</div>';}
	
	//display menu as bullet point list starting from array $list
	public function menu($list = array())
	{
		if(is_array($list))
		{
			$items = count($list);
			echo '<ul>';
			for ($i = 0; $i < $items; $i++)
				echo '<li><a href="'.$list[$i]['url'].'">'.$list[$i]['title'].'</a></li>';
			echo '</ul>';
		}
	}
	
	//display a table, parameter $table is a matrix with headers in the first row
	public function printTable($table = array())
	{
		$ret = "";
		$rows = count($table); //number of invoices
		if ($rows > 0)
		{
			$ret = "<table><tr>";
			$keys = array_keys($table[0]); //headers
			$cols = count($keys); //number of fields
			for($j = 0; $j < $cols; $j++) //for each field...
				$ret .= "<th>".$keys[$j]."</th>"; //...add headers
			$ret .= "</tr>";
			for($i = 0; $i < $rows; $i++) //for each invoice...
			{
				$ret .= "<tr>";
				for($j = 0; $j < $cols; $j++) //...for each field...
					$ret .= "<td>".$table[$i][$keys[$j]]."</td>"; //...add data in the table
				$ret .= "</tr>";
			}
			$ret .= "</table>";
		}
		
		echo $ret;
		
	}
	
	//adds last column to the table, containing option to set invoice paid/unpaid
	public function addPaidSwitch(&$table, $page)
	{
		$rows = count($table);
		if ($rows > 0)
		{
			for ($i = 0; $i < $rows; $i++)
			{
				if (isset($table[$i]['id']) && isset($table[$i]['invoice_status']))
				{
					$status = 'paid'; //by default we say 'set paid'...
					if ($table[$i]['invoice_status'] == 'paid')
						$status = 'unpaid'; //...but if the invoice is paid we say 'set unpaid'
					array_push($table[$i], "<a href=\"".$_PHP_SELF."?page=".$page."&id=".$table[$i]['id']."&status=".$status."\">Set ".$status."</a>");
				}
				else 
					return false;
			}
			return true;
		}
		return false;
	}
	
	//displays the paginator
	public function printPaginator($page = 1, $last = 1)
	{
		$previous = $page - 1;
		$next = $page + 1;
		if ($previous <= 0) //no negative pages
			$previous = 1;
		if ($next > $last) //no overflow
			$next = $last;
		echo "<a href=\"".$_PHP_SELF."?page=1\"><< </a>";
		echo "<a href=\"".$_PHP_SELF."?page=".$previous."\">< </a>";
		echo $page." / ".$last;
		echo "<a href=\"".$_PHP_SELF."?page=".$next."\"> ></a>";
		echo "<a href=\"".$_PHP_SELF."?page=".$last."\"> >></a>";
	}
	
}