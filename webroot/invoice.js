// JavaScript Document

$(document).ready(function() {
	// reusable
	$(".reusableSelect").change(function() {
	  var idNumber = $(this).attr('id');
	  idNumber = idNumber.replace(eval("/InvoiceItem|Name/ig"), '');
	  $("#InvoiceItem"+idNumber+"Description").val( $("#reusableDesc_"+idNumber).html() );
	  $("#InvoiceItem"+idNumber+"UnitCost").val( $("#reusableUnit_"+idNumber).html() );
	  $("#InvoiceItem"+idNumber+"Quantity").val( '1' );
	  if ( !$(this).val() ) {
		$("#InvoiceItem"+idNumber+"Description").val('');
		$("#InvoiceItem"+idNumber+"UnitCost").val('');
		$("#InvoiceItem"+idNumber+"Quantity").val('');
		$("#lineItem"+idNumber+"Total").val('0.00');
	  }
	});
	
	// handle the calculations
	$('table').on('keyup', 'input', function(e) {
		calculateInvoice();
	});
});


function calculateInvoice() {
	$('.calc-row').each(function() {
		var rate = $('.calc-rate', this).val();
		var multiplier = $('.calc-multiplier', this).val();
		var total = parseFloat(Math.round((rate * multiplier) * 100) / 100).toFixed(2);
		$('.calc-line-total', this).val(total);
	});
	calculateTotal();
}

function calculateTotal() {
	var invoiceTotal = 0;
	$('.calc-line-total').each(function() {
		invoiceTotal += Number($(this).val());
	});
	$('.calc-total').val(parseFloat(invoiceTotal).toFixed(2));
}