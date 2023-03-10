<?php

	$inData = getRequestInfo();
	
	$searchResults = "";
	$searchCount = 0;
	$searchID = "";
	$conn = new mysqli("localhost", "TheBeast", "WeLoveCOP4331", "COP4331");
	if ($conn->connect_error) 
	{
		returnWithError( $conn->connect_error );
	} 
	else
	{
		$stmt = $conn->prepare("select ID,Name,Phone,Email from Contacts where Name like ? and UserID=?");
		$contactName = "%" . $inData["search"] . "%";
		$stmt->bind_param("ss", $contactName, $inData["userId"]);
		$stmt->execute();
		
		$result = $stmt->get_result();
		
		while($row = $result->fetch_assoc())
		{
			if( $searchCount > 0 )
			{
				$searchResults .= ",";
				$searchID .= ",";
			}
			$searchCount++;
			$searchResults .= '"' . $row["Name"] .' '. $row["Phone"] .' '. $row["Email"]. '"';
			$searchID .= '"' . $row["ID"] . '"';
		}
		
		if( $searchCount == 0 )
		{
			returnWithError( "No Records Found" );
		}
		else
		{
			returnWithInfo( $searchResults, $searchID );
		}
		
		$stmt->close();
		$conn->close();
	}

	function getRequestInfo()
	{
		return json_decode(file_get_contents('php://input'), true);
	}

	function sendResultInfoAsJson( $obj )
	{
		header('Content-type: application/json');
		echo $obj;
	}
	
	function returnWithError( $err )
	{
		$retValue = '{"id":0,"firstName":"","lastName":"","error":"' . $err . '"}';
		sendResultInfoAsJson( $retValue );
	}
	
	function returnWithInfo( $searchResults, $searchID )
	{
		$retValue = '{"results":[' . $searchResults . '],"id":[' . $searchID . '],"error":""}';
		sendResultInfoAsJson( $retValue );
	}
	
?>
