<?php
include(dirname(__FILE__). '/../../../config/config.inc.php');
include(dirname(__FILE__). '/../../../init.php');
include(dirname(__FILE__). '/../payulatam.php');
include(dirname(__FILE__). '/../../../header.php');

$payulatam = new PayuLatam();

if(isset($_REQUEST['signature'])){
	$signature = $_REQUEST['signature'];
} else {
	$signature = $_REQUEST['firma'];
}

if(isset($_REQUEST['merchantId'])){
	$merchantId = $_REQUEST['merchantId'];
} else {
	$merchantId = $_REQUEST['usuario_id'];
}
if(isset($_REQUEST['referenceCode'])){
	$referenceCode = $_REQUEST['referenceCode'];
} else {
	$referenceCode = $_REQUEST['ref_venta'];
}
if(isset($_REQUEST['TX_VALUE'])){
	$value = $_REQUEST['TX_VALUE'];
} else {
	$value = $_REQUEST['valor'];
}
if(isset($_REQUEST['currency'])){
	$currency = $_REQUEST['currency'];
} else {
	$currency = $_REQUEST['moneda'];
}
if(isset($_REQUEST['transactionState'])){
	$transactionState = $_REQUEST['transactionState'];
} else {
	$transactionState = $_REQUEST['estado'];
}

$value = number_format($value, 1, '.', '');

$api_key = Configuration::get('PAYU_LATAM_API_KEY');
$signature_local = $api_key . '~' . $merchantId . '~' . $referenceCode . '~' . $value . '~' . $currency . '~' . $transactionState;
$signature_md5 = md5($signature_local);

if(isset($_REQUEST['polResponseCode'])){
	$polResponseCode = $_REQUEST['polResponseCode'];
} else {
	$polResponseCode = $_REQUEST['codigo_respuesta_pol'];
}

$message = '';
if($transactionState == 6 && $polResponseCode == 5){
	$estadoTx = $payulatam->l('Failed Transaction');
} else if($transactionState == 6 && $polResponseCode == 4){
	$estadoTx = $payulatam->l('Rejected Transaction');
} else if($transactionState == 12 && $polResponseCode == 9994){
	$estadoTx = $payulatam->l('Pending Transaction, Please check if the debit was made in the Bank');
} else if($transactionState == 4 && $polResponseCode == 1){
	$estadoTx = $payulatam->l('Transaction Approved');
	$message = $payulatam->l('¡Thank you for your purchase!');
} else{
	if(isset($_REQUEST['message'])){
		$estadoTx=$_REQUEST['message'];
	} else {
		$estadoTx=$_REQUEST['mensaje'];
	}
}

if(isset($_REQUEST['transactionId'])){
	$transactionId = $_REQUEST['transactionId'];
} else {
	$transactionId = $_REQUEST['transaccion_id'];
}
if(isset($_REQUEST['reference_pol'])){
	$reference_pol = $_REQUEST['reference_pol'];
} else {
	$reference_pol = $_REQUEST['ref_pol'];
}
if(isset($_REQUEST['pseBank'])){
	$pseBank = $_REQUEST['pseBank'];
} else {
	$pseBank = $_REQUEST['banco_pse'];
}
$cus = $_REQUEST['cus'];
if(isset($_REQUEST['description'])){
	$description = $_REQUEST['description'];
} else {
	$description = $_REQUEST['descripcion'];
}
if(isset($_REQUEST['lapPaymentMethod'])){
	$lapPaymentMethod = $_REQUEST['lapPaymentMethod'];
} else {
	$lapPaymentMethod = $_REQUEST['medio_pago_lap'];
}

if (strtoupper($signature) == strtoupper($signature_md5)) {
	Context::getContext()->smarty->assign(
		array(
			'estadoTx' => $estadoTx,
			'transactionId' => $transactionId,
			'reference_pol' => $reference_pol,
			'referenceCode' => $referenceCode,
			'pseBank' => $pseBank,
			'cus' => $cus,
			'value' => $value,
			'currency' => $currency,
			'description' => $description,
			'lapPaymentMethod' => $lapPaymentMethod,
			'message' => $message,
			'valid' => true
		)
	);

/*?>
	<center>
		<table style="width: 42%; margin-top: 100px;">
			<tr align="center">
				<th colspan="2"><?php echo $payulatam->l('Purchase Data');?></th>
			</tr>
			<tr align="left">
				<td><?php echo $payulatam->l('Transaction State');?></td>
				<td><?php echo $estadoTx; ?></td>
			</tr>
			<tr align="left">
				<td><?php echo $payulatam->l('Transaction ID');?></td>
				<td><?php echo $transactionId; ?></td>
			</tr>		
			<tr align="left">
				<td><?php echo $payulatam->l('Purchase Reference');?></td>
				<td><?php echo $reference_pol; ?></td>
			</tr>		
			<tr align="left">
				<td><?php echo $payulatam->l('Transaction Reference');?></td>
				<td><?php echo $referenceCode; ?></td>
			</tr>	
			<?php
				if($pseBank!=null){
			?>
				<tr align="left">
					<td>CUS</td>
					<td><?php echo $cus; ?> </td>
				</tr>
				<tr align="left">
					<td><?php echo $payulatam->l('Bank');?></td>
					<td><?php echo $pseBank; ?> </td>
				</tr>
			<?php
				}
			?>
			<tr align="left">
				<td><?php echo $payulatam->l('Total Value');?></td>
				<td>$<?php echo $value; ?> </td>
			</tr>
			<tr align="left">
				<td><?php echo $payulatam->l('Currency');?></td>
				<td><?php echo $currency; ?></td>
			</tr>
			<tr align="left">
				<td><?php echo $payulatam->l('Description');?></td>
				<td><?php echo $description; ?></td>
			</tr>
			<tr align="left">
				<td><?php echo $payulatam->l('Entity');?></td>
				<td><?php echo $lapPaymentMethod; ?></td>
			</tr>
		</table>
		<p/>
		<h1><?php echo $message ?></h1>
	</center>
<?php*/
} else {
	Context::getContext()->smarty->assign(
		array(
			'valid' => false
		)
	);
}
Context::getContext()->smarty->display(dirname(__FILE__).'/response.tpl');
include(dirname(__FILE__). '/../../../footer.php');
?>