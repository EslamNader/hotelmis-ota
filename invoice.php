<?php
/**
 * @package OTA Hotel Management
 * @copyright e-Novate Pte Ltd 2012-2015
 * @file invoice.php
 * @brief invoice print webpage called by OTA Hotel Management
 * see readme.txt for credits and references
 * @addtogroup CODE_MANAGEMENT
 * @defgroup INVOICE_MANAGEMENT Invoice setup and management page
 * This documentation is for code maintenance, not a user guide.
 * 
 */


//error_reporting(E_ALL & ~E_NOTICE);
include_once(dirname(__FILE__)."/login_check.inc.php");
include_once(dirname(__FILE__)."/queryfunctions.php");
include_once(dirname(__FILE__)."/functions.php");
include_once(dirname(__FILE__)."/dailyfunc.php");
include_once(dirname(__FILE__)."/lang/lang_en.php");

/**< language $lang */
$lang = get_language();
load_language($lang);
/**< logo file $logofile */
$logofile=Get_LogoFile();
access("billing");

/**< string directory $custom_inv */
$custom_inv = dirname(__FILE__)."/custom/invoice.php";

if(file_exists($custom_inv)) {
	require $custom_inv;
	return;
}
/**< bill array $bill */
$bill = array();
/**< book array $book */
$book = array();
/**< guest array $guest */
$guest = array();
/**< bill ID $inv */
$inv = $_GET['inv'];
get_bill($inv, $bill);
if ($bill['book_id'])
	get_booking($bill['book_id'], $book);
else if ($bill['reservation_id'])
	get_reservation($bill['reservation_id'],$book);
if ($book['guestid'])
	get_guest($book['guestid'], $guest);


// Default page size A4 - 210mm x 297mm
//  Header is set to 60mm
//  Footer is set to 27mm
//  body of invoice is set to 210mm
//  Rough rules of thumb
//  font 12px has 59 lines
//  font 12px has 52 lines
//  font 14px has 45 lines

/**< pixel $px */
$px = 14;
/**< line count $lc */
$lc = 43;
/**
 * Print the header for the invoice
 * @param $logofile [in] Logo for header
 * @param $px [in] Pixel size of phone
 * @param $lc [in] Line count
 */
function print_iheader($logofile, $px,$lc) {
	$hotel = "";
	$altname = "";
	$company = "";
	$register = "";
	$ebridgeid = "";
	$tax1 = "";
	$tax2 = "";
	$phone = "";
	$fax = "";
	$IM = "";
	$street = "";
	$city = "";
	$citycode = "";
	$state = "";
	$postcode = "";
	$countrycode = "";
	$country = "";
	$logo = "";
	$latitude = "";
	$longitude = "";
	$lang = "";
	$email = "";
	$web = "";
	$ota = "";
	$chaincode = "";
	Get_HotelSettings($hotel, $altname, $company, $register,
		$ebridgeid, $tax1, $tax2, $phone, $fax, $IM, $street,
		$city, $citycode, $state, $postcode, $countrycode, $country,
		$logo, $latitude, $longitude, $language, $email, $web, $ota, $chaincode);
	
	echo "<tr >\n";
	echo "<td style=\"height:60mm;\" align=right>\n";
	echo "<table width=100%><tr><td width='33%'></td><td width='33%' align=center><h1>".$hotel."</h1></td>";
	echo "<td align=right rowspan=2><img src='".$logofile."' title='".$logofile."' width='70' height='60mm' border='0' /></td></tr>\n";
	echo "</table>";
	echo "</tr>";
	echo "<tr>\n";
	echo "<td style=\"font-size:".$px."px;height:210mm;\"  valign=top >\n";
	echo "<table width='100%' cellspacing=0 >\n";

}

/**
 * Print the footer of the invoice
 * Use the details from the hotel settings to print the address
 * @param $pg [in] Page number
 * @param $px [in] Pixel size 
 * @param $lc [in] Line count
 */
function print_ifooter($pg,$px,$lc) {
	$hotel = "";
	$altname = "";
	$company = "";
	$register = "";
	$ebridgeid = "";
	$tax1 = "";
	$tax2 = "";
	$phone = "";
	$fax = "";
	$IM = "";
	$street = "";
	$city = "";
	$citycode = "";
	$state = "";
	$postcode = "";
	$countrycode = "";
	$country = "";
	$logo = "";
	$latitude = "";
	$longitude = "";
	$lang = "";
	$email = "";
	$web = "";
	$ota = "";
	$chaincode = "";
	Get_HotelSettings($hotel, $altname, $company, $register,
		$ebridgeid, $tax1, $tax2, $phone, $fax, $IM, $street,
		$city, $citycode, $state, $postcode, $countrycode, $country,
		$logo, $latitude, $longitude, $language, $email, $web, $ota, $chaincode);
	
	echo "</table></td>\n";
	echo "</tr>\n";
	echo "<tr><td>";
	echo "Signature:_______________________________      Date: <u>".date('Y/m/d h:i')."</u>";
	echo "</td>\n";
	echo "</tr>\n";
	echo "<tr>\n";
	echo "<td style=\"height:27mm;\">\n";
	echo "<table width=100%><tr><td></td><td align=center>".$hotel."</td><td></td></tr>\n";
	echo "<tr><td></td><td align=center>".$street." ".$city." ".$country." ".$postcode."</td><td></td></tr>\n";
	echo "<tr><td></td><td align=center>T: ".$phone." F: ".$fax." E: ".$email."</td><td align=right>".$pg."</td></tr></table>\n";
	echo "</td>\n";
	echo "</tr>\n";
}

$totalArr=array();
$payedArr=array();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<link href="css/new.css" rel="stylesheet" type="text/css" />
	<link href="css/styles.css" rel="stylesheet" type="text/css" />
<title><?php echo $_L['MAIN_Title'];?></title>
</head>
<body>
<table style="width:210mm;" border=0 class="fixed">
<?php 
	$pg = 1;
	// Print the invoice header
	print_iheader($logofile,$px,$lc);
	// Print the customer details, name, dates etc
	// Line 1 
	echo "<tr><td> ".$_L['INV_guestname']."</td>";
	echo "<td>".$book['guestname']."</td>";
	echo "<td></td>";
	echo "<td></td>";
	echo "<td></td>";
	echo "<td>".$_L['INV_room']."</td>";
	echo "<td>".get_roomno($book['roomid'])."</td>";
	echo "</tr>";
	// Line 2
	echo "<tr><td>".$_L['INV_address']."</td>";
	echo "<td></td>";
	echo "<td></td>";
	echo "<td></td>";
	echo "<td></td>";
	echo "<td>".$_L['INV_arrival']."</td>";
	echo "<td>".$book['checkindate'];
	echo "</td></tr>";
	// Line 3
	echo "<tr><td colspan=3 rowspan=3>".$guest['address']."</td>";
	echo "<td></td>";
	echo "<td></td>";
	echo "<td>".$_L['INV_depart']."</td>";
	echo "<td>".$book['checkoutdate'];
	echo "</td></tr>";
	// Line 4
	echo "<tr>";
	echo "<td></td>";
	echo "<td></td>";
	echo "<td>".$_L['INV_billno']."</td>";
	echo "<td>".$bill['billno'];
	echo "</td></tr>";
	// Line 5
	echo "<tr>";
	echo "<td></td>";
	echo "<td></td>";
	echo "<td>".$_L['INV_billdate']."</td>";
	echo "<td>".$bill['date_billed']."</td>";
	echo "</tr>";
	echo "<tr><td colspan=5>&nbsp;</td></tr>";
	echo "<tr style=\"border-width:2px; border-color:#000\ border-style:solid\"><td><b>".$_L['INV_date']."</b></td><td><b>".$_L['INV_description']."</b></td>";
	echo "<td><b>".$_L['INV_amount']."</b></td><td><b>".$_L['INV_service']."</b></td><td><b>".$_L['INV_tax']."</b></td><td><b>".$_L['INV_qty'] ."</b></td><td><b>".$_L['INV_balance']."</b></td></tr>\n";
	$i=6; // 7 lines
	// Loop over the invoice items
	$j = 0;
	$total = 0;
	while ($j < $bill['transcount']) {
		// skip voided transaction items
		if($bill['trans'][$j]['status'] == STATUS_VOID) {
			$j++;
			continue;
		}
		// end of lines print the footer
		if($i <> 0 && $i % $lc == 0) {
			print_ifooter($pg,$px,$lc);
			$pg++;
			$i = 0;
			continue;
		}
		if($i == 0) {
			print_iheader($logofile,$px,$lc);
		}
		echo "<tr style=\"border-width:2px; border-color:#000\ border-style:solid\"> <td>". $bill['trans'][$j]['trans_date']."</td>";
		echo "<td>". get_itemname($bill['trans'][$j]['item_id'])."</td>";
		echo "<td>". sprintf("%02.2f",$bill['trans'][$j]['amount'])."</td>";
		echo "<td>". sprintf("%02.2f",$bill['trans'][$j]['svc'])."</td>";
		echo "<td>". sprintf("%02.2f",$bill['trans'][$j]['tax'])."</td>";
		echo "<td>". sprintf("%02d",$bill['trans'][$j]['quantity'])."</td>";
		echo "<td>". sprintf("%02.2f",$bill['trans'][$j]['grossamount'])." ".$bill['trans'][$j]['currency']."</td>";
		echo "</tr>";
		$total += $bill['trans'][$j]['grossamount'];		
		
		if(array_key_exists($bill['trans'][$j]['currency'], $totalArr)){
			$totalArr[$bill['trans'][$j]['currency']] += $bill['trans'][$j]['grossamount'];
		}else{
			$totalArr[$bill['trans'][$j]['currency']]=$bill['trans'][$j]['grossamount'];
		}				
								
		$i++; // Next line
		$j++; // Next receipt item
	}
	
	// print the receipts header
	if(($lc - ($i % $lc)) <= 2) {
		$i = 0;
		print_ifooter($pg,$px,$lc);
		print_iheader($logofile,$px,$lc);
		$pg++;
	}
	echo "<tr><td colspan=5> &nbsp; </td></tr>";
	echo "<tr style=\"border-width:2px; border-color:#000\ border-style:solid\"><td colspan=5 align=left><b> Receipts </b></td></tr>";
	// Loop over receipts
	$j = 0;
	/** @cond */
	while ($j < $bill['rcptcount']) {
		// skip voided transaction items
		if($bill['rcpts'][$j]['status'] == STATUS_VOID) {
			$j++;
			continue;
		}
		$payedArr[$bill['rcpts'][$j]['srcCurrency']] += $bill['rcpts'][$j]['amount'];
		// end of lines print the footer
		if($i <> 0 && $i % $lc == 0) {
			print_ifooter($pg,$px,$lc);
			$pg++;
			$i = 0;
			continue;
		}
		if($i == 0) {
			print_iheader($logofile,$px,$lc);
		}
		echo "<tr style=\"border-width:2px; border-color:#000\ border-style:solid\"> <td>". $bill['rcpts'][$j]['rcpt_date']."</td>";
		echo "<td>". get_foptext($bill['rcpts'][$j]['fop']);
		echo "</td>";
		echo "<td></td>";
		echo "<td></td>";
		echo "<td></td>";
		echo "<td></td>";
		echo "<td>". sprintf("%02.2f",$bill['rcpts'][$j]['amount'])." ".$bill['rcpts'][$j]['srcCurrency']."</td>";
		echo "</tr>";
		$total -= $bill['rcpts'][$j]['amount'];
		$i++;  // Next line
		$j++;  // Next receipt item
	}
	/** @endcond */
	// print the receipts header
	/** @cond */
	if(($lc - ($i % $lc)) <= 2) {
		$i = 0;
		print_ifooter($pg,$px,$lc);
		print_iheader($logofile,$px,$lc);
		$pg++;
	}
	/** @endcond */
	echo "<tr><td colspan='7'></td></tr><tr style=\"border-width:2px; border-color:#000\ border-style:solid\"> <td colspan=6 align=right><b>";
	echo $_L['INV_balance']."</b> </td><td>";
	//echo sprintf("%02.2f",$total);
	//calculating balance due for each currency code	
	$dueArr=array();
	$balidx=0;	
	foreach($totalArr as $idx => $val){
		$balanceDue=0;
		if(array_key_exists($idx, $payedArr)){
			$temp = $val - $payedArr[$idx];
			if($temp!=0){
				$dueArr[$idx]=$temp;
			}
		}else{
			$dueArr[$idx] = $val;
		}
	}						
	//printing balance due for each currency code
	if(count($dueArr)>0){
		foreach($dueArr as $idx => $balanceDue){
			if($balanceDue>0){
				//echo sprintf("%02.2f",$balanceDue)." ".$idx."<br/>";
				if($balidx==0){
					echo sprintf("%02.2f",$balanceDue)." ".$idx."</td></tr>"; 
				}else{
					echo "<tr><td colspan=6></td><td>".sprintf("%02.2f",$balanceDue)." ".$idx."</td></tr>"; 
				}
				$balidx++;
			}
		}
	}else{
		echo "0.00</td></tr>";
	}	
	if($i <> 0 ) print_ifooter($pg,$px,$lc);
	
?>
</td>
</tr>
</table>

</body>
</html>

<?php
/**
 */
?>