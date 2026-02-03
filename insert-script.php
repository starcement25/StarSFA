String sqlQuery1 = "INSERT INTO `customer_master` (`customer_code`, `customer_name`, `route_code`, `emp_code`, `current_balance`, `credit_limit`, `acedns`,`black_list`) " +
			"SELECT 'C/0000001', 'A.B.ELASTO PRODUCTS (P) LTD. (KOL)',   'RT/1', 'E0017', '6213',    '99999999.99', 'Y', 'N'" +
			" UNION " + 
			"SELECT 'C/0000002', 'A.C.ENTERPRISE(JAMURIA)', 			 'RT/2', 'E0010', '41764',   '99999999.99', 'Y', 'N'" +
			" UNION " + 
			"SELECT 'C/0000003', 'A.K.TRANSPORT(DANKUNI)',  			 'RT/3', 'E0044', '38827',   '99999999.99', 'Y', 'N'" +
			" UNION " + 
			"SELECT 'C/0000004', 'A.M TRADING CO(KOLKATA)', 			 'RT/4', 'E0016', '0',       '99999999.99', 'Y', 'N'" +
			" UNION " + 
			"SELECT 'C/0000005', 'A.M. ENTERPRISES(HALDIA)',			 'RT/1', 'E0017', '-174747', '99999999.99', 'Y', 'N'" +
			" UNION " + 
			"SELECT 'C/0000006', 'A.M.ENTERPRISE(JAMURIA)', 			 'RT/2', 'E0010', '0',       '99999999.99', 'Y', 