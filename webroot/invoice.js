// JavaScript Document

$(document).ready(function() {

	$('.newTimelink').live('click', function(e) {
		   e.preventDefault();		   
			$('.invoiceTimes').FormModifier({
				actionElem		:		'.newTimelink',
				cloneElem		:		'.invoiceTimes',
				cloneRow		:		true,
				isParent		:		true,
				labelPrefix		:		null,
				labelDiv		:		'',
				child			:		'.timeRow',
				formid			:		'InvoiceAddForm',
				canDeleteLast	:		true,
				appendTo		:		'invoiceTimeLines'
			});
			$('.invoiceTimes').data('FormModifier').appendRow();		
	});
	
	$('.newItemlink').live('click', function(e) {
		   e.preventDefault();		   
			$('.invoiceItems').FormModifier({
				actionElem		:		'.newItemlink',
				cloneElem		:		'.invoiceItems',
				cloneRow		:		true,
				isParent		:		true,
				labelPrefix		:		null,
				labelDiv		:		'',
				child			:		'.itemRow',
				formid			:		'InvoiceAddForm',
				canDeleteLast	:		true,
				appendTo		:		'invoiceItemLines'
			});
			$('.invoiceItems').data('FormModifier').appendRow();		
		});

	$('.deleteTimeLink').live('click', function(e) {
		   e.preventDefault();		   
			$('#' + $(this).attr('id')).FormModifier({
				actionElem		:		'.deleteTimeLink',
				cloneElem		:		'#' + $(this).attr('id'),
				cloneRow		:		true,
				isParent		:		true,
				labelPrefix		:		null,
				labelDiv		:		'',
				child			:		'.timeRow',
				formid			:		'InvoiceAddForm',
				canDeleteLast	:		true,
				appendTo		:		'invoiceTimeLines'
			});
			$('#' + $(this).attr('id')).data('FormModifier').deleteRow();		
	}); 

	$('.deleteItemLink').live('click', function(e) {
		   e.preventDefault();		   
			$('#' + $(this).attr('id')).FormModifier({
				actionElem		:		'.deleteItemLink',
				cloneElem		:		'#' + $(this).attr('id'),
				cloneRow		:		true,
				isParent		:		true,
				labelPrefix		:		null,
				labelDiv		:		'',
				child			:		'.timeRow',
				formid			:		'InvoiceAddForm',
				canDeleteLast	:		true,
				appendTo		:		'invoiceItemLines'
			});
			$('#' + $(this).attr('id')).data('FormModifier').deleteRow();		
	});
	
	// handle the calculations
	$(".invoiceTimeLines").keyup(function() {
		calculateTimeLine();
		calculateTotal();
	});
	$(".invoiceItemLines").keyup(function() {
		calculateItemLine();
		calculateTotal();
	});
	
	$(".lineTotal").each(function() {
		var lineNumber = $(this).attr("id").replace(/[a-zA-Z]/g, '');
		var cost = $("#InvoiceTime" + lineNumber + "Rate").val(); 
		var quantity = $("#InvoiceTime" + lineNumber + "Hours").val();
		if (cost && quantity) {
			var total = parseFloat((cost * quantity).toFixed(2));
			$("#lineTime" + lineNumber + "Total").html(parseFloat(total).toFixed(2));
		};
		calculateTotal();
	});
	
	$(".lineTotal").each(function() {
		var lineNumber = $(this).attr("id").replace(/[a-zA-Z]/g, '');
		var cost = $("#InvoiceItem" + lineNumber + "UnitCost").val(); 
		var quantity = $("#InvoiceItem" + lineNumber + "Quantity").val(); 
		if (cost && quantity) {
			var total = parseFloat((cost * quantity).toFixed(2));
			$("#lineItem" + lineNumber + "Total").html(parseFloat(total).toFixed(2));
		};
		calculateTotal();
	});
});
function calculateTimeLine() {
	var lineNumber = $(":focus").attr("id").replace(/[a-zA-Z]/g, '');
	var cost = $("#InvoiceTime" + lineNumber + "Rate").val();
	var quantity = $("#InvoiceTime" + lineNumber + "Hours").val();
	var total = parseFloat((cost * quantity).toFixed(2));
		
	$("#lineTime" + lineNumber + "Total").html(parseFloat(total).toFixed(2));
}
function calculateItemLine() {
	var lineNumber = $(":focus").attr("id").replace(/[a-zA-Z]/g, '');
	var cost = $("#InvoiceItem" + lineNumber + "UnitCost").val();
	var quantity = $("#InvoiceItem" + lineNumber + "Quantity").val();
	var total = parseFloat((cost * quantity).toFixed(2));
		
	$("#lineItem" + lineNumber + "Total").html(parseFloat(total).toFixed(2));
}
function calculateTotal() {
	var invoiceTotal = 0;
	$(".lineTotal").each(function() {
		invoiceTotal += Number($(this).html());
	});
	$("#InvoiceTotal").val(parseFloat(invoiceTotal).toFixed(2));
}