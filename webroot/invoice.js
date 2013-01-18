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
	
	$('.newReuseItemlink').live('click', function(e) {
		   e.preventDefault();		   
			$('.reusableItems').FormModifier({
				actionElem		:		'.newReuseItemlink',
				cloneElem		:		'.reusableItems',
				cloneRow		:		true,
				isParent		:		true,
				labelPrefix		:		null,
				labelDiv		:		'',
				child			:		'.itemRow',
				formid			:		'InvoiceAddForm',
				canDeleteLast	:		false,
				appendTo		:		'reusableItemLine'
			});
			$('.reusableItems').data('FormModifier').appendRow();		
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

	$('.deleteReuseItemLink').live('click', function(e) {
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
				appendTo		:		'reusableItemLine'
			});
			$('#' + $(this).attr('id')).data('FormModifier').deleteRow();		
	});
	
	// reusable
	$(".reusableSelect").change(function() {
	  var idNumber = $(this).attr('id');
	  idNumber = idNumber.replace(eval("/InvoiceItem|Name/ig"), '');
	  $("#InvoiceItem"+idNumber+"Description").val( $("#reusableDesc_"+idNumber).html() );
	  $("#InvoiceItem"+idNumber+"UnitCost").val( $("#reusableUnit_"+idNumber).html() );
	});
	
	// handle the calculations
	$(".invoiceTimeLines").bind("keyup change", function(e) {
		calculateTimeLine();
		calculateTotal();
	});
	$(".invoiceItemLines").bind("keyup change", function(e) {
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