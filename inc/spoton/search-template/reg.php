<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title> 
<script language="javascript">
function load()
{
	document.forms["myformsubmit"].submit();
}
</script>
</head>
<body onLoad="load()">
<form action='https://apidev.marketleader.com/appserver.svc/rest' method='POST' id='myformsubmit'>
<!--production URL"https://api.marketleader.com/appserver.svc/rest -->


<div><input type="hidden" name='loginid' value='SpotOnAPITest'></div>

    <div><input type="hidden" name='password' value='Pinpoint123'></div>    

    <div><input type="hidden" name='usertype' value='Partner'></div>

    <div><input type="hidden" name='type' value='Partner'></div>

    <div><input type="hidden" name='operation' value='LeadInsert'></div>



    <div><input type="hidden" name='partnerkey' value='DvH8N2aGzNxrGfNeZdX4rwYuQYNfDRaGqJF86+TrzTSzj/hQENJmV/tae7l7Pi5X'></div>     

<div style="visibility:hidden">
    <div><span style="width:120px;">Lead:</span>

        <textarea  id="test" rows="20" cols="30" name="lead" value="">
        {
         <?php echo '"Acres":'.$_POST['acres'];?>,
        <?php echo '"Address":'. $_POST['address'];?>,
        <?php echo '"AddressType":'.'"'.$_POST['addresstype'].'"';?>,
        <?php echo '"AssignToAgentID":'.'"'.$_POST['assign'].'"';?>,
       <?php echo '"AssignToType":'.$_POST['assigntype'];?>,
        <?php echo '"Baths":'.$_POST['baths'];?>,
        <?php echo '"Beds":'.$_POST['beds'];?>,
        <?php echo '"BuyerAgencyAgreement":'.$_POST['buyer'];?>,
        <?php echo '"Cellphone":'.$_POST['cellphone'];?>,
        <?php echo '"City":'.'"'.$_POST['city'].'"';?>,
        <?php echo '"Comments":'.'"'.$_POST['Comments'].'"';?>,
        <?php echo '"Country":'.'"'.$_POST['country'].'"';?>,
        <?php echo '"CreditRating":'.$_POST['credit'];?>,
        <?php echo '"EmailAddress":'.'"'.$_POST['email'].'"';?>,
        <?php echo '"Features":'.$_POST['features'];?>,
        <?php echo '"FirstName":'.'"'.$_POST['firstname'].'"';?>,
        <?php echo '"Garage":'.$_POST['garage'];?>,
        <?php echo '"HasOwnAgent":'.$_POST['ownagent'];?>,
        <?php echo '"HomePhone":'.$_POST['homephone'];?>,
        <?php echo '"KwoId":'.$_POST['kwoid'];?>,
        <?php echo '"LastName":'.'"'.$_POST['lastname'].'"';?>,
        <?php echo '"LeadPrequalified":'.$_POST['leading'];?>,
        <?php echo '"LookingInAreaNames":'.$_POST['areanames'];?>,
        <?php echo '"MaxPrice":'.$_POST['maxprice'];?>,
        <?php echo '"MinPrice":'.$_POST['minprice'];?>,
        <?php echo '"Password":'.'"'.$_POST['password'].'"';?>,
        <?php echo '"PostalCode":'.$_POST['postalcode'];?>,
        <?php echo '"PreferredContactMethod":'.$_POST['preferedcontact'];?>,
        <?php echo '"PreferredContactTime":'.$_POST['preferedtime'];?>,
        <?php echo '"PropertyType":'.$_POST['property'];?>,
        <?php echo '"ProspectType":'.'"'.$_POST['prospect'].'"';?>,
        <?php echo '"ReferringURL":'.$_POST['referingurl'];?>,
		<?php echo '"SecondaryFirstName":'.$_POST['secondaryfirst'];?>,
        <?php echo '"SecondaryLastName":'.$_POST['secondarylast'];?>,
        <?php echo '"SourceCode":'.'"'.$_POST['sourcecode'].'"';?>,
        <?php echo '"SquareFeet":'.$_POST['squarefeet'];?>,
        <?php echo '"State_Province":'.'"'.$_POST['stateprovince'].'"';?>,
        <?php echo '"WantsImmediateContact":'.$_POST['contact1'];?>,
        <?php echo '"WantsLenderContact":'.$_POST['contact2'];?>,
        <?php echo '"Phone Number":'.$_POST['phone'];?>,
        <?php echo '"WorkingWithAgentName":'.$_POST['agentname'];?>,
        <?php echo '"YearBuilt":'.$_POST['yearbuilt'];?>
        }
        </textarea></div>
        <br />
        <br />
</div>

</form>
</body>
</html>

