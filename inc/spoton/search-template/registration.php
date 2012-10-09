<?php 
/**
* A custom page template for the Registration Form.
*/
get_header(); 

?>
<?php   $url = plugins_url()."/spotonconnect-IDX-Plugin/inc/spoton/search-template/reg.php"; ?>


<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<style type="text/css">

#left-column
{
position:relative;
float:left;
}

</style>
</head>

<body>
<form action="<?php  echo $url; ?>" method="post">
    <br/>
<br/>
    <br/>

<br/>
<div class="left-column">
<label for="first"><b>First Name:</b></label>
<input style="margin-left:70px" size="40" type="text" name="firstname" value=""/>
<br/>
<br/>
<label for="last"><b>Last Name:</b></label>
<input class="style" style="margin-left:72px" size="40" type="text" name="lastname" value="" />
<br/>
<br/>
<label for="email"><b>Email Address:</b></label>
<input  class="style" style="margin-left:45px" size="40" type="text" name="email" value="" maxlength="150"/>
<br/>
<br/>
<label for="phone"><b>Phone Number:</b></label>
<input class="style" style="margin-left:40px" size="40" type="text" name="phone" value=""/>
<br/>
<br/>
<button name="submit" style="margin-left:230px" >submit</button></div>

<input style="margin-left:120px" type="hidden" name="acres" value="null" size="30"/>
<input style="margin-left:39px;" type="hidden" name="assign" value="10790:70" size="30"/>
<input style="margin-left:64px" type="hidden" name="assigntype" value="null" size="30"/>
<input type="hidden" name="address" value="null"/>

<select style="visibility:hidden" name="addresstype">
<option>Mailing</option>
<option>Business</option>
<option>Vacation</option>
<option>Investment</option></select>

<select style="margin-left:127px;visibility:hidden" name="baths">
<option>0</option>
<option>1</option>
<option>2</option>
<option>3</option>
<option>4</option>
<option>5</option></select>
<p>
<select style="margin-left:132px;visibility:hidden" name="beds">
<option>0</option>
<option>1</option>
<option>2</option>
<option>3</option>
<option>4</option>
<option>5</option></select></p>

<p>
<input style="margin-left:4px" type="hidden" name="buyer" value="0" size="30"/></p>
 
<p>
<input style="margin-left:100px" type="hidden" name="cellphone" value="null" size="30"/></p>

<p>
<input style="margin-left:140px" type="hidden" name="city" value="null" size="30"/></p>

<p>
<input type="hidden" style="margin-left:98px" name="Comments" rows="6" value="null" size="30"/></p>

<p>
<input style="margin-left:113px" type="hidden" name="country" value="null" size="30"/></p>

<p>
<input style="margin-left:80px" type="hidden" name="credit" value="null" size="30"/></p>

<p>
<input style="margin-left:110px" type="hidden" name="features" value="null" size="30"/></p>

<p>
<input style="margin-left:120px" type="hidden" name="garage" value="null" size="30"/></p>

<p>
  <input style="margin-left:75px" type="hidden" name="ownagent" value="0" size="30"/>
</p>

<p>
<input style="margin-left:90px" type="hidden" name="homephone" value="null" size="30"/></p>

<p>
<input style="margin-left:128px" type="hidden" name="kwoid" value="null" size="30"/></p>
  
<p>
<input style="margin-left:60px" type="hidden" name="leading" value="null" size="30"/></p>

<p>
<input style="margin-left:27px" type="hidden" name="areanames" value="null" size="30"/></p>

<p>
<select style="margin-left:110px;visibility:hidden" name="maxprice">
<option>100000</option>
<option>75000</option>
<option>50000</option>
<option>25000</option>
<option>10000</option>
<option>5000</option></select></p>

<p>
<select style="margin-left:114px;visibility:hidden" name="minprice">
<option>100000</option>
<option>75000</option>
<option>50000</option>
<option>25000</option>
<option>10000</option>
<option>5000</option></select></p>

<p>
<input type="hidden" name="password" value="null" style="margin-left:110px" size="30"/></p>

<p>
<input type="hidden" name="postalcode" value="null" style="margin-left:100px" size="30"/></p>
 
<p>
<input style="margin-left:10px" size="30" type="hidden" name="preferedcontact" value="null" /></p>
  
<p>
<input type="hidden" name="preferedtime" value="null" style="margin-left:30px" size="30"/></p>
  
<p>
<input type="hidden" name="property" value="null" style="margin-left:87px" size="30"/></p>

<p>
<select style="visibility:hidden" name="prospect">
<option>Sell</option>
<option>Buy</option>
<option>Investment</option>
</select></p>
  
<p>
<input type="hidden" name="referingurl" value="null" style="margin-left:85px" size="30"/></p>

<p>
<input type="hidden" name="secondaryfirst" value="null" style="margin-left:44px" size="30"/></p>
  
<p>
<input type="hidden" name="secondarylast" value="null" style="margin-left:44px"size="30"/></p>

<p>
<input type="hidden" name="sourcecode" value="api" style="margin-left:103px" size="30"/></p>

<p>
<input type="hidden" name="squarefeet" value="null" style="margin-left:108px" size="30"/></p>

<p>
<input type="hidden" name="stateprovince" value="WA" style="margin-left:86px" size="30"/></p>

<p>
<input type="hidden" name="contact1" value="0" style="margin-left:23px" size="30" ></p>

<p>
<input type="hidden" name="contact2" value="0" style="margin-left:46px" size="30"/></p>

<p>
<input type="hidden" name="agentname" value="null" style="margin-left:20px" size="30"/></p>

<p>
<input type="hidden" name="yearbuilt" value="null" style="margin-left:127px" size="30"/></p>

</form>
</body>
</html>



<?php get_footer(); ?>