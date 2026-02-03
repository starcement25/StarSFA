<?php
include_once 'HTMLTable2JSON.php';
$helper = new HTMLTable2JSON();
$table='<table class="grid" cellspacing="0" width="100%"><caption>Jurisdiction Details for AABCB5576G</caption><tr><td><strong>Surname</strong></td><td>BHARAT SANCHAR NIGAM LIMITED</td></tr><tr><td><strong>Range Code</strong></td><td>32</td><tr></table>
';

/*$table='<table class="grid" cellspacing="0" width="100%"><caption>Jurisdiction Details for AABCB5576G</caption><tr><td><strong>Surname</strong></td><td>BHARAT SANCHAR NIGAM LIMITED</td></tr>
				<tr>
				<td><strong>Middle Name</strong></td>
				<td>
				</td>
			</tr>
				<tr>
				<td><strong>First Name</strong></td>
				<td>
				</td>
			</tr>
		<tr>
				<td><strong>Area Code</strong></td>
				<td>DEL
				</td>
			</tr>
			<tr>
				<td><strong>AO Type</strong></td>
				<td>C
				</td>
			<tr>
			<tr>
				<td><strong>Range Code</strong></td>
				<td>32
				</td>
			<tr>
			<tr>
				<td><strong>AO Number</strong></td>
				<td>1
				</td>
			<tr>
			<tr>
				<td><strong>Jurisdiction</strong></td>
				<td>CIRCLE 4 (1), DELHI
				</td>
			</tr>
			<tr>
				<td><strong>Building Name</strong></td>
				<td>C. R. BUILDING,DELHI
				</td>
			</tr>
			<tr>
				<td><strong>Email ID</strong></td>
				<td>-
				</td>
			</tr>
		
		</table>
';*/


// Standard Usage
$code_output =$helper->tableToJSON('', false, null, null, null, null, null, true, null, null, $table);
echo $code_output;

