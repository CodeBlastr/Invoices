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
	calculateInvoice(); // used on the edit page to calculate upon load 
	
	// handle the form iteractivity 
	$('#InvoiceAddForm, #InvoiceEditForm').on('change', '.combobox', function() {
		var that = this;
		if($(this).val() == '+') {
			var id = $(this).attr('id');
			var name = $(this).attr('name');
			var clas = $(this).attr('class');
			$(this).replaceWith('<input name="' + name + '" id="' + id + '" type="text" class="' + clas + '" />');
		} else {
			// get the invoice item template and fill the sibling inputs
			$.ajax({
				url: '/invoices/invoice_items/view/' + $(this).val() + '.json',
			}).done(function( data ) {
				if (data) {
					var description = data.invoiceItem.InvoiceItem.description;
					var unitCost = data.invoiceItem.InvoiceItem.unit_cost;
					var quantity = 1;
					$(that).closest('tr').find('input[name*="description"]').first().val(description);
					$(that).closest('tr').find('input[name*="unit_cost"]').first().val(unitCost);
					$(that).closest('tr').find('input[name*="quantity"]').first().val(quantity);
					clone(that); // this is a call to the form.utility plugin
					calculateInvoice(); 
				}
			});
		}
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