(function($) {
  "use strict";

	// Variables declarations

	var $wrapper = $('.main-wrapper');
	var $pageWrapper = $('.page-wrapper');
	var $slimScrolls = $('.slimscroll');

	// Sidebar

	var Sidemenu = function() {
		this.$menuItem = $('#sidebar-menu a');
	};

	function init() {
		var $this = Sidemenu;
		$('#sidebar-menu a').on('click', function(e) {
			if($(this).parent().hasClass('submenu')) {
				e.preventDefault();
			}
			if(!$(this).hasClass('subdrop')) {
				$('ul', $(this).parents('ul:first')).slideUp(350);
				$('a', $(this).parents('ul:first')).removeClass('subdrop');
				$(this).next('ul').slideDown(350);
				$(this).addClass('subdrop');
			} else if($(this).hasClass('subdrop')) {
				$(this).removeClass('subdrop');
				$(this).next('ul').slideUp(350);
			}
		});
		$('#sidebar-menu ul li.submenu a.active').parents('li:last').children('a:first').addClass('active').trigger('click');
	}

	// Sidebar Initiate
	init();

	// Mobile menu sidebar overlay

	$('body').append('<div class="sidebar-overlay"></div>');
	$(document).on('click', '#mobile_btn', function() {
		$wrapper.toggleClass('slide-nav');
		$('.sidebar-overlay').toggleClass('opened');
		$('html').addClass('menu-opened');
		return false;
	});

	// Sidebar overlay
	$(".sidebar-overlay").on("click", function () {
		$wrapper.removeClass('slide-nav');
		$(".sidebar-overlay").removeClass("opened");
		$('html').removeClass('menu-opened');
	});

	// radio btn hide show
	  $(function() {
		$("input[name='mail_config']").click(function() {
		  if ($("#chkYes").is(":checked")) {
			$("#showemail").show();
		  } else {
			$("#showemail").hide();
		  }
		});
	  });

	// editor
	if ($('#editor').length > 0) {
		ClassicEditor
		.create( document.querySelector( '#editor' ), {
			toolbar: {
                items: [
                    'heading', '|',
                    'fontfamily', 'fontsize', '|',
                    'alignment', '|',
                    'fontColor', 'fontBackgroundColor', '|',
                    'bold', 'italic', 'strikethrough', 'underline', 'subscript', 'superscript', '|',
                    'link', '|',
                    'outdent', 'indent', '|',
                    'bulletedList', 'numberedList', 'todoList', '|',
                    'code', 'codeBlock', '|',
                    'insertTable', '|',
                    'uploadImage', 'blockQuote', '|',
                    'undo', 'redo'
                ],
                shouldNotGroupWhenFull: true
            }
		} )
		.then( editor => {
			window.editor = editor;
		} )
		.catch( err => {
			console.error( err.stack );
		} );
	}
	if ($('.select').length > 0) {
		$('.select').select2({
			minimumResultsForSearch: -1,
			width: '100%'
		});
	}

	$(document).on('click', '#filter_search', function() {
		$('#filter_inputs').slideToggle("slow");
	});

	// Datetimepicker

	if($('.datetimepicker').length > 0 ){
		$('.datetimepicker').datetimepicker({
			format: 'DD-MM-YYYY',
			icons: {
				up: "fas fa-angle-up",
				down: "fas fa-angle-down",
				next: 'fas fa-angle-right',
				previous: 'fas fa-angle-left'
			}
		});
	}

	// Datetimepicker time

	if($('.timepicker').length > 0 ){
		$('.timepicker').datetimepicker({
			format: 'HH:mm A',
			icons: {
				up: "fas fa-angle-up",
				down: "fas fa-angle-down",
				next: 'fas fa-angle-right',
				previous: 'fas fa-angle-left'
			}
		});
	}

	// Time Slot

	if($('#time-slot').length > 0) {
		$('#time-slot').on('click', function() {
			$(".timeslot-sec").show();
			$(".timepicker-sec").hide();
		});
		$('#time-picker').on('click', function() {
			$(".timepicker-sec").show();
			$(".timeslot-sec").hide();
		});
	}

	// MultiStep Form Script

	$(document).ready(function () {
		/*---------------------------------------------------------*/
		$(".next_btn").on('click', function () { // Function Runs On NEXT Button Click
			$(this).closest('fieldset').next().fadeIn('slow');
			$(this).closest('fieldset').css({
				'display': 'none'
			});
			// Adding Class Active To Show Steps Forward;
			$('#progressbar .active').removeClass('active').addClass('activated').next().addClass('active');
			$('#progress-head .active').removeClass('active').next().addClass('active');

		});
		$(".prev_btn").on('click', function () { // Function Runs On NEXT Button Click
			$(this).closest('fieldset').prev().fadeIn('slow');
			$(this).closest('fieldset').css({
				'display': 'none'
			});
			// Adding Class Active To Show Steps Forward;
			$('#progressbar .active').removeClass('active').prev().removeClass('activated').addClass('active');
			$('#progress-head .active').removeClass('active').prev().addClass('active');

		});
	});

	// Add Service Information

    $(".addservice-info").on('click','.trash', function () {
		$(this).closest('.service-cont').remove();
		return false;
    });

    $(".add-extra").on('click', function () {

		var servicecontent = '<div class="row service-cont">' +
			'<div class="col-md-4">' +
				'<div class="form-group">' +
					'<label>Additional Service</label>' +
					'<input type="text" class="form-control" placeholder="Enter Service">' +
				'</div>' +
			'</div>' +
			'<div class="col-md-4">' +
				'<div class="form-group">' +
					'<label>Price</label>' +
					'<input type="text" class="form-control" placeholder="Enter Price">' +
				'</div>' +
			'</div>' +
			'<div class="col-md-4">' +
				'<div class="d-flex">' +
					'<div class="form-group w-100">' +
						'<label>Duration</label>' +
						'<input type="text" class="form-control" placeholder="Enter Service Duration">' +
					'</div>' +
					'<div class="form-group">' +
						'<label>&nbsp;</label>' +
						'<a href="#" class="btn btn-danger-outline trash"><i class="far fa-trash-alt"></i></a>' +
					'</div>' +
				'</div>' +
			'</div>' +
		'</div>';

        $(".addservice-info").append(servicecontent);
        return false;
    });

	// Add Hours

    $(".hours-info").on('click','.trash', function () {
		$(this).closest('.hours-cont').remove();
		return false;
    });

    $(".add-hours").on('click', function () {

		var hourscontent = '<div class="row hours-cont">' +
			'<div class="col-md-4">' +
				'<div class="form-group">' +
					'<label>From</label>' +
					'<div class="form-availability-field">' +
						'<input type="text" class="form-control timepicker" placeholder="Select Time">' +
						'<span class="cus-icon"><i class="fe fe-clock"></i></span>' +
					'</div>' +
				'</div>' +
			'</div>' +
			'<div class="col-md-4">' +
				'<div class="form-group">' +
					'<label>To</label>' +
					'<div class="form-availability-field">' +
						'<input type="text" class="form-control timepicker" placeholder="Select Time">' +
						'<span class="cus-icon"><i class="fe fe-clock"></i></span>' +
					'</div>' +
				'</div>' +
			'</div>' +
			'<div class="col-md-4">' +
				'<div class="d-flex">' +
					'<div class="form-group w-100">' +
							'<label>Slots</label>' +
							'<input type="text" class="form-control" placeholder="Enter Slot">' +
					'</div>' +
					'<div class="form-group">' +
						'<label>&nbsp;</label>' +
						'<a href="#" class="btn btn-danger-outline trash"><i class="far fa-trash-alt"></i></a>' +
					'</div>' +
				'</div>' +
			'</div>' +
		'</div>';

        $(this).parent().find(".hours-info").append(hourscontent);
		$('.timepicker').datetimepicker({
			format: 'HH:mm A',
			icons: {
				up: "fas fa-angle-up",
				down: "fas fa-angle-down",
				next: 'fas fa-angle-right',
				previous: 'fas fa-angle-left'
			}
		});
        return false;
    });

	// Add Timepicker Hours

    $(".hrs-info").on('click','.trash', function () {
		$(this).closest('.hrs-cont').remove();
		return false;
    });

    $(".add-hrs").on('click', function () {

		var hrscontent = '<div class="row hrs-cont">' +
			'<div class="col-md-6">' +
				'<div class="form-group form-info">' +
					'<label class="col-form-label">From</label>' +
					'<div class="form-icon">' +
							'<input type="text" class="form-control timepicker"  placeholder="Select Time">' +
							'<span class="cus-icon"><i class="fe fe-clock"></i></span>' +
						'</div>' +
				'</div>' +
			'</div> ' +
			'<div class="col-md-6">' +
				'<div class="d-flex">' +
					'<div class="form-group form-info w-100">' +
						'<label class="col-form-label">To</label>' +
						'<div class="form-icon">' +
							'<input type="text" class="form-control timepicker"  placeholder="Select Time">' +
							'<span class="cus-icon"><i class="fe fe-clock"></i></span>' +
						'</div>' +
						'</div>' +
						'<div class="form-group">' +
							'<label class="col-form-label">&nbsp;</label>' +
							'<a href="#" class="btn btn-danger-outline trash"><i class="far fa-trash-alt"></i></a>' +
						'</div>' +
					'</div>' +
				'</div>' +
			'</div>';
		'</div>';


        $(this).parent().find(".hrs-info").append(hrscontent);
		$('.timepicker').datetimepicker({
			format: 'HH:mm A',
			icons: {
				up: "fas fa-angle-up",
				down: "fas fa-angle-down",
				next: 'fas fa-angle-right',
				previous: 'fas fa-angle-left'
			}
		});
        return false;
    });

	// Add Timepicker Hours

    $(".day-info").on('click','.trash', function () {
		$(this).closest('.day-cont').remove();
		return false;
    });

    $(".add-day").on('click', function () {

		var daycontent = '<div class="row day-cont">' +
			'<div class="col-md-6">' +
				'<div class="form-group">' +
					'<label class="col-form-label">From</label>' +
					'<div class="form-icon">' +
						'<input type="text" class="form-control timepicker" placeholder="Select Time">' +
						'<span class="cus-icon"><i class="feather-clock"></i></span>' +
					'</div>' +
				'</div>' +
			'</div>' +
			'<div class="col-md-6">' +
				'<div class="d-flex">' +
					'<div class="form-group w-100">' +
						'<label class="col-form-label">To</label>' +
						'<div class="form-icon">' +
							'<input type="text" class="form-control timepicker" placeholder="Select Time">' +
							'<span class="cus-icon"><i class="feather-clock"></i></span>' +
						'</div>' +
					'</div>' +
					'<div class="form-group">' +
						'<label class="col-form-label">&nbsp;</label>' +
						'<a href="#" class="btn btn-danger-outline trash"><i class="far fa-trash-alt"></i></a>' +
					'</div>' +
				'</div>' +
			'</div>' +
		'</div>';

        $(this).parent().parent().find(".day-info").append(daycontent);
		$('.timepicker').datetimepicker({
			format: 'HH:mm A',
			icons: {
				up: "fas fa-angle-up",
				down: "fas fa-angle-down",
				next: 'fas fa-angle-right',
				previous: 'fas fa-angle-left'
			}
		});
        return false;
    });

	// Tooltip


	// Tooltip
	if($('[data-bs-toggle="tooltip"]').length > 0) {
		var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
		var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
			return new bootstrap.Tooltip(tooltipTriggerEl)
		})
	}



    // Owl Carousel

    if ($('.images-carousel').length > 0) {
		$('.images-carousel').owlCarousel({
			loop: true,
			center: true,
			margin: 10,
			responsiveClass: true,
			responsive: {
				0: {
					items: 1
				},
				600: {
					items: 1
				},
				1000: {
					items: 1,
					loop: false,
					margin: 20
				}
			}
		});
    }

	// Sidebar Slimscroll

	if($slimScrolls.length > 0) {
		$slimScrolls.slimScroll({
			height: 'auto',
			width: '100%',
			position: 'right',
			size: '7px',
			color: '#ccc',
			allowPageScroll: false,
			wheelStep: 10,
			touchScrollStep: 100
		});
		var wHeight = $(window).height() - 60;
		$slimScrolls.height(wHeight);
		$('.sidebar .slimScrollDiv').height(wHeight);
		$(window).resize(function() {
			var rHeight = $(window).height() - 60;
			$slimScrolls.height(rHeight);
			$('.sidebar .slimScrollDiv').height(rHeight);
		});
	}

	// Small Sidebar

	$(document).on('click', '#toggle_btn', function() {
		if($('body').hasClass('mini-sidebar')) {
			$('body').removeClass('mini-sidebar');
			$('.subdrop + ul').slideDown();
		} else {
			$('body').addClass('mini-sidebar');
			$('.subdrop + ul').slideUp();
		}
		setTimeout(function(){
			// mA.redraw();
			// mL.redraw();
		}, 300);
		return false;
	});

if($('.win-maximize').length > 0) {
		$('.win-maximize').on('click', function(e){
			if (!document.fullscreenElement) {
				document.documentElement.requestFullscreen();
			} else {
				if (document.exitFullscreen) {
					document.exitFullscreen();
				}
			}
		})
	}



	$(document).on('mouseover', function(e) {
		e.stopPropagation();
		if($('body').hasClass('mini-sidebar') && $('#toggle_btn').is(':visible')) {
			var targ = $(e.target).closest('.sidebar').length;
			if(targ) {
				$('body').addClass('expand-menu');
				$('.subdrop + ul').slideDown();
			} else {
				$('body').removeClass('expand-menu');
				$('.subdrop + ul').slideUp();
			}
			return false;
		}

		// $(window).scroll(function() {
		// 	if ($(window).scrollTop() >= 30) {
		// 		$('.header').addClass('fixed-header');
		// 	} else {
		// 		$('.header').removeClass('fixed-header');
		// 	}
		// });

		$(document).on('click', '#loginSubmit', function() {
			$("#adminSignIn").submit();
		});

	});

	// Range slider
	if(document.getElementById("myRange")!=null){
		var slider = document.getElementById("myRange");
		var output = document.getElementById("currencys");
		output.innerHTML = slider.value;

		slider.oninput = function() {
		  output.innerHTML = this.value;
		}
	}
	if(document.getElementById("myRange")!=null){
		document.getElementById("myRange").oninput = function() {
			var value = (this.value-this.min)/(this.max-this.min)*100
			this.style.background = 'linear-gradient(to right, #FF0080 0%, #FF0080 ' + value + '%, #c4c4c4 ' + value + '%, #c4c4c4 100%)'
		  };
		}

	// Logo Hide Btn

	$(document).on("click",".logo-hide-btn",function () {
		$(this).parent().hide();
	});

// Summernote

if($('.summernote').length > 0) {
	$('.summernote').summernote({
		height: 200,                 // set editor height
		minHeight: null,             // set minimum height of editor
		maxHeight: null,             // set maximum height of editor
		focus: false ,
		toolbar: [
			// [groupName, [list of button]]
			['style', ['bold', 'italic', 'underline', 'clear']],
			['font', ['strikethrough', 'superscript', 'subscript']],
			['fontsize', ['fontsize']],
			['color', ['color']],
			['para', ['ul', 'ol', 'paragraph']],
			['height', ['height']]
		]			// set focus to editable area after initializing summernote
	});
}


// add Formset
$(document).on("click",".addlinks",function () {
	var experiencecontent = '<div class="form-group links-cont">' +
	'<div class="row align-items-center">' +
	'<div class="col-lg-3 col-12">' +
	'<input type="text" class="form-control" placeholder="Label">' +
	'</div>' +
	'<div class="col-lg-7 col-12">' +
	'<input type="text" class="form-control" placeholder="Link with http:// Or https://">' +
	'</div>' +
	'<div class="col-lg-2 col-12 d-flex justify-content-end">' +
	'<a href="javascript:void(0);" class="btn btn-sm bg-danger-light  delete_review_comment">' +
	'<i class="far fa-trash-alt "></i> ' +
	'</a>' +
	'</div>' +
	'</div>' +
	'</div>' ;

	$(".settings-form").append(experiencecontent);
	return false;
});

$(".settings-form").on('click','.delete_review_comment', function () {
	$(this).closest('.links-cont').remove();
	return false;
});


// add Formset

$(document).on("click",".addnew",function () {
	var experiencecontent = '<div class="form-group links-conts">' +
	'<div class="row align-items-center">' +
	'<div class="col-lg-3 col-12">' +
	'<input type="text" class="form-control" placeholder="Label">' +
	'</div>' +
	'<div class="col-lg-7 col-12">' +
	'<input type="text" class="form-control" placeholder="Link with http:// Or https://">' +
	'</div>' +
	'<div class="col-lg-2 col-12 d-flex justify-content-end">' +
	'<a href="javascript:void(0);" class="btn btn-sm bg-danger-light  delete_review_comment">' +
	'<i class="far fa-trash-alt "></i> ' +
	'</a>' +
	'</div>' +
	'</div>' +
	'</div>' ;

	$(".settingset").append(experiencecontent);
	return false;
});

$(".settingset").on('click','.delete_review_comment', function () {
	$(this).closest('.links-conts').remove();
	return false;
});

$(document).on("click",".addlinknew",function () {
	var experiencecontent = '<div class="form-group links-cont">' +
	'<div class="row align-items-center">' +
	'<div class="col-lg-3 col-12">' +
	'<input type="text" class="form-control" placeholder="Label">' +
	'</div>' +
	'<div class="col-lg-7 col-12">' +
	'<input type="text" class="form-control" placeholder="Link with http:// Or https://">' +
	'</div>' +
	'<div class="col-lg-2 col-12 d-flex justify-content-end">' +
	'<a href="javascript:void(0);" class="btn btn-sm bg-danger-light  delete_review_comment">' +
	'<i class="far fa-trash-alt "></i> ' +
	'</a>' +
	'</div>' +
	'</div>' +
	'</div>' ;

	$(".settings-forms").append(experiencecontent);
	return false;
});

$(".settings-forms").on('click','.delete_review_comment', function () {
	$(this).closest('.links-cont').remove();
	return false;
});


// add social links Formset
$(document).on("click",".addsocail",function () {
	var experiencecontent = '<div class="form-group countset">' +
	'<div class="row align-items-center">' +
	'<div class="col-lg-2 col-12">' +
	'<div class="socail-links-set">' +
	'<ul>' +
	'<li class=" dropdown has-arrow main-drop">' +
	'<a href="javascript:void(0);" class="dropdown-toggle nav-link" data-bs-toggle="dropdown" aria-expanded="false">' +
	'<span class="user-img">' +
	'<i class="fab fa-github me-2"></i>' +
	'</span>' +
	'</a>' +
	'<div class="dropdown-menu">' +
	'<a class="dropdown-item" href="javascript:void(0);"><i class="fab fa-facebook-f me-2"></i>Facebook</a>' +
	'<a class="dropdown-item" href="javascript:void(0);"><i class="fab fa-twitter me-2"></i>twitter</a>' +
	'<a class="dropdown-item" href="javascript:void(0);"><i class="fab fa-youtube me-2"></i> Youtube</a>' +
	'</div>' +
	'</li>' +
	'</ul>' +
	'</div>' +
	'</div>' +
	'<div class="col-lg-8 col-12">' +
	'<input type="text" class="form-control" placeholder="Link with http:// Or https://">' +
	'</div>' +
	'<div class="col-lg-2 col-12 d-flex justify-content-end">' +
	'<a href="javascript:void(0);" class="btn btn-sm bg-danger-light  delete_review_comment">' +
	'<i class="far fa-trash-alt "></i> ' +
	'</a>' +
	'</div>' +
	'</div> ' +
	'</div> ';

	$(".setings").append(experiencecontent);
	return false;
});

$(".setings").on('click','.delete_review_comment', function () {
	$(this).closest('.countset').remove();
	return false;
});


// add Faq

$(document).on("click",".addfaq",function () {
	var experiencecontent = '<div class="row counts-list">' +
	'<div class="col-md-11">' +
	'<div class="cards">' +
	'<div class="form-group">' +
	'<label>Title</label>' +
	'<input type="text" class="form-control" >' +
	'</div>' +
	'<div class="form-group mb-0">' +
	'<label>Content</label>' +
	'<textarea class="form-control"></textarea>' +
	'</div>' +
	'</div>' +
	'</div>' +
	'<div class="col-md-1">' +
	'<a href="javascript:void(0);" class="btn btn-sm bg-danger-light  delete_review_comment">' +
	'<i class="far fa-trash-alt "></i> ' +
	'</a>' +
	'</div>' +
	'</div> ';

	$(".faq").append(experiencecontent);
	return false;
});

$(".faq").on('click','.delete_review_comment', function () {
	$(this).closest('.counts-list').remove();
	return false;
});

$(".settings-form").on('click','.trash', function () {
	$(this).closest('.links-cont').remove();
	return false;
});

$(document).on("click",".add-links",function () {
	var experiencecontent = '<div class="row form-row links-cont">' +
		'<div class="form-group d-flex">' +
			'<button class="btn social-icon"><i class="feather-github"></i></button>' +
			'<input type="text" class="form-control" placeholder="Social Link">' +
			'<div><a href="javascript:void(0);" class="btn trash"><i class="feather-trash-2"></i></a></div>' +
		'</div>' +
	'</div>';

	$(".settings-form").append(experiencecontent);
	return false;
});
// checkbox Select

$('.app-listing .selectbox').on("click", function() {
	$(this).parent().find('#checkboxes').fadeToggle();
	$(this).parent().parent().siblings().find('#checkboxes').fadeOut();
});

$('.invoices-main-form .selectbox').on("click", function() {
	$(this).parent().find('#checkboxes-one').fadeToggle();
	$(this).parent().parent().siblings().find('#checkboxes-one').fadeOut();
});

//checkbox Select

if($('.sortby').length > 0) {
	var show = true;
	var checkbox1 = document.getElementById("checkbox");
	$('.selectboxes').on("click", function() {

		if (show) {
			checkbox1.style.display = "block";
			show = false;
		} else {
			checkbox1.style.display = "none";
			show = true;
		}
	});
}

// Invoices checkbox Show

$(function() {
	$("input[name='invoice']").click(function() {
		if ($("#chkYes").is(":checked")) {
			$("#show-invoices").show();
		} else {
			$("#show-invoices").hide();
		}
	});
});


	// Invoices Add More

    $(".links-info-one").on('click','.service-trash', function () {
		$(this).closest('.links-cont').remove();
		return false;
    });

    $(document).on("click",".add-links",function () {
		var experiencecontent = '<div class="links-cont">' +
			'<div class="service-amount1">' +
				'<a href="javascript:void(0);" class="service-trash"><i class="fe fe-minus-circle me-1"></i>Service Charge</a> <span>$ 4</span' +
			'</div>' +
		'</div>';

        $(".links-info-one").append(experiencecontent);
        return false;
    });

     $(".links-info-discount").on('click','.service-trash-one', function () {
		$(this).closest('.links-cont-discount').remove();
		return false;
    });

    $(document).on("click",".add-links-one",function () {
		var experiencecontent = '<div class="links-cont-discount">' +
			'<div class="service-amount1">' +
				'<a href="javascript:void(0);" class="service-trash-one"><i class="fe fe-minus-circle me-1"></i>Offer new</a> <span>$ 4 %</span' +
			'</div>' +
		'</div>';

        $(".links-info-discount").append(experiencecontent);
        return false;
    });

    // Invoices Table Add More

    $(".add-table-items").on('click','.remove-btn', function () {
		$(this).closest('.add-row').remove();
		return false;
    });

    $(document).on("click",".add-btn",function () {
		var experiencecontent = '<tr class="add-row">' +
			'<td>' +
				'<input type="text" class="form-control">' +
			'</td>' +
			'<td>' +
				'<input type="text" class="form-control">' +
			'</td>' +
			'<td>' +
				'<input type="text" class="form-control">' +
			'</td>' +
			'<td>' +
				'<input type="text" class="form-control">' +
			'</td>' +
			'<td>' +
				'<input type="text" class="form-control">' +
			'</td>' +
			'<td>' +
				'<input type="text" class="form-control">' +
			'</td>' +
			'<td class="add-remove text-end">' +
				'<a href="javascript:void(0);" class="add-btn me-2"><i class="fas fa-plus-circle"></i></a> ' +
				'<a href="javascript:void(0);" class="copy-btn me-2"><i class="fe fe-copy"></i></a>' +
				'<a href="javascript:void(0);" class="remove-btn"><i class="fe fe-trash-2"></i></a>' +
			'</td>' +
		'</tr>';

        $(".add-table-items").append(experiencecontent);
        return false;
    });

	$(document).on("click",".addnewheader",function () {
		var experiencecontent = '<div class="col-12">' +
		'<div class="form-group-set">' +
		'<div class="row align-items-center">' +
		'<div class="col-lg-3 col-sm-6">' +
		'<input type="text" class="form-control" placeholder="Enter title">' +
		'</div>' +
		'<div class="col-lg-8 col-sm-12">' +
		'<input type="text" class="form-control" placeholder="Enter url">' +
		'</div>' +
		'<div class="col-lg-1 col-sm-12">' +
		'<a href="javascript:void(0);" class="delete-links">' +
		'<i class="far fa-trash-alt "></i>' +
		'</a>' +
		'</div>' +
		'</div>' +
		'</div>' +
		'</div>';

        $(".add-headers").append(experiencecontent);
        return false;
    });

	$(document).on("click",".delete-links",function () {
		$(this).parent().parent().parent().parent().hide();
	});

	// Datatable
	if($('.datatable').length > 0) {
		$('.datatable').DataTable({
			"bFilter": false,
			"sDom": 'fBtlpi',
			'pagingType': 'numbers',
			"ordering": true,
			"language": {
				search: ' ',
				sLengthMenu: '_MENU_',
				searchPlaceholder: "Search...",
				info: "_START_ - _END_ of _TOTAL_ items",
			},
			initComplete: (settings, json)=>{
				$('.dataTables_filter').appendTo('#tableSearch');
				$('.dataTables_filter').appendTo('.search-input');
			},
		});
	}



	// toggle-password
	if($('.toggle-password').length > 0) {
		$(document).on('click', '.toggle-password', function() {
			$(this).toggleClass("fa-eye fa-eye-slash");
			var input = $(".pass-input");
			if (input.attr("type") == "password") {
				input.attr("type", "text");
			} else {
				input.attr("type", "password");
			}
		});
	}
	if($('.toggle-passwords').length > 0) {
		$(document).on('click', '.toggle-passwords', function() {
			$(this).toggleClass("fa-eye fa-eye-slash");
			var input = $(".pass-inputs");
			if (input.attr("type") == "password") {
				input.attr("type", "text");
			} else {
				input.attr("type", "password");
			}
		});
	}
	if($('.toggle-passworda').length > 0) {
		$(document).on('click', '.toggle-passworda', function() {
			$(this).toggleClass("fa-eye fa-eye-slash");
			var input = $(".pass-inputa	");
			if (input.attr("type") == "password") {
				input.attr("type", "text");
			} else {
				input.attr("type", "password");
			}
		});
	}

	// image file upload image
	function readURL(input) {
		if (input.files && input.files[0]) {
			var reader = new FileReader();

			reader.onload = function (e) {
				$('#blah').attr('src', e.target.result);
			}

			reader.readAsDataURL(input.files[0]);
		}
	}
	$("#imgInp").change(function(){
		readURL(this);
	});


	//Custom Country Code Selector

	if($('#phone').length > 0) {
		var input = document.querySelector("#phone");
			window.intlTelInput(input, {
			  utilsScript: "assets/plugins/intltelinput/js/utils.js",
		});
	}
	if($('#phone1').length > 0) {
		var input = document.querySelector("#phone1");
			window.intlTelInput(input, {
			  utilsScript: "assets/plugins/intltelinput/js/utils.js",
		});
	}

	$('.theme-image').on('click', function(){
		$('.theme-image').removeClass('active');
		$(this).addClass('active');
	});
	$('.transfer-lists').on('click', function(){
		$('.transfer-lists').removeClass('active');
		$(this).addClass('active');
	});
	$('.themecolorset').on('click', function(){
		$('.themecolorset').removeClass('active');
		$(this).addClass('active');
	});

	$('.counters').each(function() {
		var $this = $(this),
			countTo = $this.attr('data-count');
		$({ countNum: $this.text()}).animate({
			countNum: countTo
		},
		{
			duration: 3000,
			easing:'linear',
			step: function() {
			$this.text(Math.floor(this.countNum));
			},
			complete: function() {
			$this.text(this.countNum);
			}

		});

	});


	// Gallery slider
	if($('.owl-carousel.gallery-slider').length > 0) {
		$('.owl-carousel.gallery-slider').owlCarousel({
			loop:true,
			margin:24,
			nav:true,
			smartSpeed: 2000,
			navText : ["<i class='fa-solid fa-angle-left'></i>","<i class='fa-solid fa-angle-right'></i>"],
			dots:false,
			navContainer: '.mynav3',
			responsive:{
				0:{
					items:1
				},

				550:{
					items:2
				},
				700:{
					items:2
				},
				1000:{
					items:3
				}
			}
		})
	}

	// Loader
	setTimeout(function () {
		$('#loader');
		setTimeout(function () {
			$("#loader").fadeOut("slow");
			$("#overlayer").delay(2000).fadeOut("slow");
		}, 100);
	}, 500);

	if($('.sidebar .active').length > 0) {
		var container = $('div'),
			scrollTo = $('.sidebar .active');
			container.animate({
				scrollTop: scrollTo.offset().top - container.offset().top + container.scrollTop() - 100
		});
	}

	// Add Timepicker Hours

    $(".social-info").on('click','.trash', function () {
		$(this).closest('.social-cont').remove();
		return false;
    });

    $(".social-add").on('click', function () {

		var socialcontent = '<div class="row align-items-center social-cont">' +
		'<div class="col-lg-5 col-sm-12">' +
			'<div class="form-group">' +
				'<label>Name</label>' +
				'<select class="select">' +
					'<option>Instagram</option>' +
					'<option>facebook</option>' +
				'</select>' +
			'</div>' +
		'</div>' +
		'<div class="col-lg-5 col-sm-12">' +
			'<div class="form-group">' +
				'<label>URL</label>' +
				'<select class="select">' +
					'<option>Ex. www.instagram.com</option>' +
					'<option>Ex. www.facebook.com</option>' +
				'</select>' +
			'</div>' +
		'</div>' +
		'<div class="col-lg-2 col-sm-12">' +
			'<label>&nbsp;</label>' +
			'<div class="form-group d-flex align-items-center">' +
				'<div class="active-switch">' +
					'<label class="switch">' +
						'<input type="checkbox">' +
						'<span class="sliders round"></span>' +
					'</label>' +
				'</div>' +
				'<a href="#" class="del-action ms-2 trash"><i class="fe fe-trash-2"></i></a>' +
			'</div>' +
		'</div>' +
	'</div>';

	$(this).parent().find(".social-info").append(socialcontent);
	$('.select').select2({
		minimumResultsForSearch: -1,
		width: '100%'
	});
	return false;
	});

	$('.bank-box').on('click', function(){
		$('.bank-box').removeClass('active');
		$(this).addClass('active');
	});

	// Social Authentication active

	$('.loc-set').on('click', function(){
		$('.loc-set').removeClass('soc-active');
		$(this).addClass('soc-active');
	});


  // Chat

	var chatAppTarget = $('.chat-window');
	(function() {
		if ($(window).width() > 991)
			chatAppTarget.removeClass('chat-slide');

		$(document).on("click",".chat-window .chat-users-list a.media",function () {
			if ($(window).width() <= 991) {
				chatAppTarget.addClass('chat-slide');
			}
			return false;
		});
		$(document).on("click","#back_user_list",function () {
			if ($(window).width() <= 991) {
				chatAppTarget.removeClass('chat-slide');
			}
			return false;
		});
	})();


	// Remove Gallery
	$(document).on("click",".remove-gallery",function () {
		$(this).parent().parent().hide();
	});

	// Chat sidebar overlay

	if ($(window).width() <= 1199) {
		if($('#task_chat').length > 0) {
			$(document).on('click', '#task_chat', function() {
				$('.sidebar-overlay').toggleClass('opened');
				$('#task_window').addClass('opened');
				return false;
			});
		}
	}

	if ($(window).width() > 767) {
		if($('.theiaStickySidebar').length > 0) {
			$('.theiaStickySidebar').theiaStickySidebar({
			  // Settings
			  additionalMarginTop: 125
			});
		}
	}

	// Feather Icon
	if( $('.feather-icon').length > 0 ){
		feather.replace();
	}

})(jQuery);

toastr.options = {
  "closeButton": true,
  "progressBar": true,
  "positionClass": "toast-top-right",
  "timeOut": "3000"
};


$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

var table = $('#datatable').DataTable({
    "aoColumnDefs": [{ "bSortable": true, "aTargets": [ 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12] }],
    "order": [[10, 'asc']],
    "processing": true,
    "serverSide": true,
    "scrollX": true,
    "scrollY": function() {return $(window).height() - 250;},
    "lengthMenu": [[10, 50, 100, 150],[10, 50, 100, 150]],
    "ajax": {
        "url": ajaxURL,
        "type": "POST",
      "data": {
        "warranty_status": function() {return $('#warranty_status').val();},
        "status_filter": function() {return $('#status_filter').val();},
      }
    },
    "aoColumns": [
        {"mData": "action", "bSortable": false},
        {"mData": "warranty_status", "bSortable": false},
        {"mData": "buyer_name"},
        {"mData": "mobile"},
        {"mData": "email"},
        {"mData": "source"},
        {"mData": "product_name"},
        {"mData": "serial_number"},
        {"mData": "purchase_date"},
        {"mData": "expiry_date"},
        {"mData": "address"},
        {"mData": "created_by"},
        {"mData": "modified_by"},
    ]
});


$("#warranty_status, #status_filter").select2();

$("#warranty_status, #status_filter").on('change keyup',function(){
    $('#datatable').DataTable().draw();
});

function changeWarrantyStatus(id, currentStatus) {
    Swal.fire({
        title: "Change Warranty Status",
        input: "select",
        inputOptions: {
            "Pending": "Pending",
            "Active": "Active",
            "Expired": "Expired",
            "Rejected": "Rejected"
        },
        inputValue: currentStatus,
        showCancelButton: true,
        confirmButtonColor: "#526BDF",
        cancelButtonColor: "#EC6767",
        confirmButtonText: "Update Status",
        cancelButtonText: "Cancel",
        inputValidator: (value) => {
            if (!value) {
                return "You need to select a status!";
            }
        }
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                type: 'GET',
                url: changeUserStatusURL,
                data: {
                    id: id,
                    status: result.value
                },
                success: function(resultData) {
                    if (resultData.status === 1) {
                        toastr["success"](resultData.msg);
                        $('#datatable').DataTable().ajax.reload(null, false);
                    } else {
                        toastr["error"](resultData.msg || "Something went wrong.");
                    }
                },
                error: function() {
                    toastr["error"]("Failed to update status.");
                }
            });
        }
    });
}

function userDelete(id) {
    swal.fire({
        title: "Are you sure want to delete this?",
        text: "You won't be able to revert this!",
        showCancelButton: true,
        confirmButtonColor: "#526BDF",
        cancelButtonColor: "#EC6767",
        confirmButtonText: "Yes, delete it!",
        cancelButtonText: "No, cancel it!",
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                type: 'GET',
                url: deleteUser + "?id=" + id,
                success: function(resultData) {
                    if (resultData['status'] == 1) {
                        toastr["success"](resultData['message']);

                        $('#datatable').DataTable().ajax.reload(null, false);
                    }
                }
            });
        }
    });
}

function userRestore(id) {
    swal.fire({
        title: "Are you sure want to restore this?",
        text: "You won't be able to revert this!",
        showCancelButton: true,
        confirmButtonColor: "#526BDF",
        cancelButtonColor: "#EC6767",
        confirmButtonText: "Yes, restore it!",
        cancelButtonText: "No, cancel it!",
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                type: 'GET',
                url: restoreUser + "?id=" + id,
                success: function(resultData) {
                    if (resultData['status'] == 1) {
                        toastr["success"](resultData['message']);

                        $('#datatable').DataTable().ajax.reload(null, false);
                    }
                }
            });
        }
    });
}

var table = $('#datatable_2').DataTable({
    "aoColumnDefs": [{ "bSortable": true, "aTargets": [ 0, 1, 2, 3] }],
    "order": [[0, 'asc']],
    "processing": true,
    "serverSide": true,
    "scrollX": true,
    "scrollY": function() {return $(window).height() - 250;},
    "lengthMenu": [[10, 50, 100, 150],[10, 50, 100, 150]],
    "ajax": {
        "url":userAjaxURL,
        "type": "POST",
        "data": {
            "status_filter": function() {return $('#user_status_filter').val();},
        }
    },
    "aoColumns": [
        {"mData": "action", "bSortable": false},
        {"mData": "username"},
        {"mData": "email"},
        {"mData": "role"},
    ]
});

$("#user_status_filter").select2();

$("#user_status_filter").on('change keyup',function(){
    $('#datatable_2').DataTable().draw();
});


$('#userCreateModal').on('shown.bs.modal', function () {

    $('#user_role').select2({
        dropdownParent: $('#userCreateModal')
    });

    if (!$('#user_id').val()) {
        $('#user_role').html('<option value="Super Admin">Super Admin</option><option value="Sub Admin">Sub Admin</option>');
        $('#user_role').prop('disabled', false);
    }
});

$("#user_status_filter").on('change keyup',function(){
    $('#datatable_2').DataTable().draw();
});

$('#togglePassword').on('click', function() {
    const passwordInput = $('#user_password');
    const icon = $('#togglePasswordIcon');
    const type = passwordInput.attr('type') === 'password' ? 'text' : 'password';
    passwordInput.attr('type', type);
    icon.toggleClass('fa-eye fa-eye-slash');
});

$('#user_password').on('input', function() {
    this.value = this.value.trim();
});
$('#user_email').on('input', function() {
    this.value = this.value.replace(/\s/g, '').toLowerCase();
});
$('#user_name').on('input', function() {
    this.value = this.value.replace(/[^a-z0-9]/gi, '').toLowerCase();
});

$('#userCreationForm').on('submit', function (e) {
    e.preventDefault();

    let valid = true;
    const isEdit = $('#user_id').val() !== '';
    const changePasswordChecked = $('#change_password_checkbox').is(':checked');
    const isCEOEditingSelf = isEdit && $('#user_id').val() == 1; // CEO editing their own profile
    const currentUserId = $('#current_user_id').val() || '1'; // Get current user ID, default to 1 if not set
    const isEditingOwnProfile = isEdit && $('#user_id').val() == currentUserId;

    const fields = [
        { id: '#user_name', error: '#usernameError', msg: 'Please enter a username.' },
        { id: '#user_email', error: '#emailError', msg: 'Please enter a valid email.', validate: val => /^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(val) }
    ];

    // Skip role validation for CEO editing their own profile
    if (!isCEOEditingSelf) {
        fields.push({ id: '#user_role', error: '#roleError', msg: 'Please select a role.' });
    }

    if (!isEdit || changePasswordChecked) {
        fields.push({ id: '#user_password', error: '#passwordError', msg: 'Please enter a password.' });
    }

    fields.forEach(f => {
        const val = $(f.id).val().trim();
        const isValid = f.validate ? f.validate(val) : !!val;
        if (!isValid) {
            $(f.id).addClass('is-invalid');
            $(f.error).text(f.msg);
            valid = false;
        } else {
            $(f.id).removeClass('is-invalid');
            $(f.error).text('');
        }
    });

    if (valid) {
        // Clear previous errors
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').text('');

        // Prepare form data
        let formData = $(this).serializeArray();

        // If CEO is editing their own profile, automatically set role to CEO
        if (isCEOEditingSelf) {
            // Remove any existing user_role from formData
            formData = formData.filter(item => item.name !== 'user_role');
            // Add CEO role
            formData.push({ name: 'user_role', value: 'CEO' });
        }

        if (isEdit && changePasswordChecked) {
            formData.push({ name: 'change_password', value: 1 });
        }

        $.ajax({
            url: userSaveURL,
            type: "POST",
            data: $.param(formData),
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                if (response.status) {
                    // Check if user changed their own password
                    if (isEditingOwnProfile && changePasswordChecked) {
                        toastr["success"]("Password updated successfully! You will be logged out.");

                        // Logout after a short delay
                        setTimeout(function() {
                            window.location.href = '/signout';
                        }, 2000);
                    } else {
                        toastr["success"](isEdit ? "User updated successfully!" : "User created successfully!");
                    }

                    $('#userCreationForm')[0].reset();
                    $('.is-invalid').removeClass('is-invalid');
                    $('.invalid-feedback').text('');
                    $('#userCreateModal').modal('hide');
                }

                $('#datatable_2').DataTable().draw();
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    var errorMap = {
                        user_name: 'usernameError',
                        user_email: 'emailError',
                        user_role: 'roleError',
                        user_password: 'passwordError'
                    };
                    $.each(errors, function(field, messages) {
                        var inputField = $('[name="' + field + '"]');
                        inputField.addClass('is-invalid');
                        var errorDivId = errorMap[field];
                        if (errorDivId) {
                            $('#' + errorDivId).text(messages[0]);
                        }
                    });
                } else {

                    toastr["error"]("Something went wrong. Please try again.");
                }
            }
        });
    }
});

['#user_name', '#user_email', '#user_password'].forEach(id => {
    $(id).on('input', function () {
        $(this).removeClass('is-invalid');
        $(`${id}Error`).text('');
    });
});

$('#user_role').on('change', function () {
    $(this).removeClass('is-invalid');
    $('#roleError').text('');
});

function editUserCredentials(UserId){

    $('#userCreationForm')[0].reset();
    $('#userModalLabel').text('Edit User');

    $.ajax({
        url: getUserDetails,
        type: 'GET',
        data: { id: UserId },
        success: function(response) {
            if (response.status) {

                const user = response.data;
                $('#user_id').val(user.id);
                $('#user_name').val(user.name);
                $('#user_email').val(user.email);
                $('#user_role').val(user.role).trigger('change');
                $('#user_password').val('');

                if (user.id == 1) {
                    $('#user_role').html('<option value="CEO">CEO</option>');
                    $('#user_role').prop('disabled', true);
                } else {
                    $('#user_role').html('<option value="Super Admin">Super Admin</option><option value="Sub Admin">Sub Admin</option>');
                    $('#user_role').prop('disabled', false);
                    $('#user_role').val(user.role).trigger('change');
                }

                // Password change logic for edit
                $('#changePasswordWrapper').show();
                $('#change_password_checkbox').prop('checked', false);
                $('#user_password').prop('disabled', true);
                $('#user_password').val('');
                $('#change_password_checkbox').off('change').on('change', function() {
                    if ($(this).is(':checked')) {
                        $('#user_password').prop('disabled', false);
                    } else {
                        $('#user_password').prop('disabled', true).val('');
                    }
                });

                $('#userModalLabel').text('Edit User');

                $('#userCreateModal').modal('show');
            } else {
                toastr['error']("User not found");
            }
        },
        error: function() {
            toastr['error']("Failed to load user data");
        }
    });
}

function onOpenCreateUserModal() {
    $('#user_id').val('');
    $('#userCreationForm')[0].reset();
    $('#userModalLabel').text('Add New User');
    $('#user_role').html('<option value="Super Admin">Super Admin</option><option value="Sub Admin">Sub Admin</option>');
    $('#user_role').prop('disabled', false);
    // Password change logic for create
    $('#changePasswordWrapper').hide();
    $('#change_password_checkbox').prop('checked', false);
    $('#user_password').prop('disabled', false);
}




// PRODUCT
// PRODUCT
// PRODUCT
// PRODUCT
// PRODUCT
// PRODUCT
// PRODUCT


var table = $('#datatable_1').DataTable({
    "aoColumnDefs": [{ "bSortable": true, "aTargets": [ 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10] }],
    "order": [[9, 'asc']],
    "processing": true,
    "serverSide": true,
    "scrollX": true,
    "scrollY": function() {return $(window).height() - 250;},
    "lengthMenu": [[10, 50, 100, 150],[10, 50, 100, 150]],
    "ajax": {
        "url":productAjax,
        "type": "POST",
        "data": {
            "status_filter": function() {return $('#products_management_status_filter').val();},
        }
    },
    "aoColumns": [
        {"mData": "action", "bSortable": false},
        {"mData": "product_name"},
        {"mData": "offer_price"},
        {"mData": "original_price"},
        {"mData": "rating"},
        {"mData": "review_count"},
        {"mData": "sold_count"},
        {"mData": "created_by"},
        {"mData": "modified_by"},
        {"mData": "created_at"},
        {"mData": "updated_at"},
    ]
});

$("#products_management_status_filter").select2();

$("#products_management_status_filter").on('change keyup',function(){
    $('#datatable_1').DataTable().draw();
});


$(function() {

    // Initialize variant section state on page load
    if ($('#productCreationForm #uc_has_variant').is(':checked')) {
        $('#productCreationForm #variant-section').show();
        // Show existing variants if editing
        if (window.isEdit) {
            if ($('#productCreationForm #uc_variant_1_name').val()) {
                $('#productCreationForm #variant-1').show();
                window.variantCount = 1;
            }
            if ($('#productCreationForm #uc_variant_2_name').val()) {
                $('#productCreationForm #variant-2').show();
                window.variantCount = 2;
            }
            if ($('#productCreationForm #uc_variant_3_name').val()) {
                $('#productCreationForm #variant-3').show();
                $('#productCreationForm #add-variant-btn').hide();
                window.variantCount = 3;
            }
        }
    } else {
        $('#productCreationForm #variant-section').hide();
    }

    $('#productCreationForm').on('change', '#uc_has_variant', function() {
        if ($(this).is(':checked')) {
            $('#productCreationForm #variant-section').show();
            $('#productCreationForm #variant-1').show();
            window.variantCount = 1;
        } else {
            $('#productCreationForm #variant-section').hide();
            $('#productCreationForm #variant-section input').val('').removeClass('is-invalid');
            $('#productCreationForm #variant-section .invalid-feedback').text('');
            window.variantCount = 1;
            $('#productCreationForm #variant-2, #productCreationForm #variant-3').hide();
            $('#productCreationForm #add-variant-btn').show();
        }
    });

    window.variantCount = 1;
    $('#productCreationForm').on('click', '#add-variant-btn', function() {
        if (window.variantCount === 1) {
            $('#productCreationForm #variant-2').show();
            window.variantCount++;
        } else if (window.variantCount === 2) {
            $('#productCreationForm #variant-3').show();
            $(this).hide();
            window.variantCount++;
        }
    });

    $('#productCreationForm form').on('submit', function(e) {

        e.preventDefault();
        let isValid = true;
        var $form = $(this);

        $form.find('.form-control').removeClass('is-invalid');
        $form.find('.invalid-feedback').text('');

        const requiredFields = [
            {id: '#uc_product_name', msg: 'Product name is required.'},
            {id: '#uc_offer_price', msg: 'Offer price is required.'},
            {id: '#uc_original_price', msg: 'Original price is required.'},
            {id: '#uc_features', msg: 'Features are required.'},
            {id: '#uc_description', msg: 'Description is required.'},
            {id: '#uc_rating', msg: 'Rating is required.'},
            {id: '#uc_review_count', msg: 'Review count is required.'},
            {id: '#uc_sold_count', msg: 'Sold count is required.'}
        ];

        requiredFields.forEach(function(field) {
            var $input = $form.find(field.id);
            if ($input.val().trim() === '') {
                $input.addClass('is-invalid');
                $input.next('.invalid-feedback').text(field.msg);
                isValid = false;
            }
        });


        var $defaultImg = $form.find('#default_img');
        if (!window.isEdit || !window.hasDefaultImg) {
            if (!$defaultImg.val()) {
                $defaultImg.addClass('is-invalid');
                $defaultImg.next('.invalid-feedback').text('Default product image is required.');
                isValid = false;
            }
        } else if ($defaultImg.val()) {
            // Validate image dimensions and format
            var file = $defaultImg[0].files[0];
            if (file) {
                var allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
                var maxSize = 5 * 1024 * 1024; // 5MB

                if (!allowedTypes.includes(file.type)) {
                    $defaultImg.addClass('is-invalid');
                    $defaultImg.next('.invalid-feedback').text('Please upload image in JPG, PNG or GIF format.');
                    $defaultImg.val('');
                    $defaultImg.replaceWith($defaultImg.clone(true));
                    toastr['error']('Please upload image in JPG, PNG or GIF format for default product image.');
                    isValid = false;
                } else if (file.size > maxSize) {
                    $defaultImg.addClass('is-invalid');
                    $defaultImg.next('.invalid-feedback').text('Image size should be less than 5MB.');
                    $defaultImg.val('');
                    $defaultImg.replaceWith($defaultImg.clone(true));
                    toastr['error']('Image size should be less than 5MB for default product image.');
                    isValid = false;
                } else {
                    // Check image dimensions
                    var img = new Image();
                    var canvas = document.createElement('canvas');
                    var ctx = canvas.getContext('2d');

                    img.onload = function() {
                        var width = this.width;
                        var height = this.height;

                        if (width !== 620 || height !== 500) {
                            $defaultImg.addClass('is-invalid');
                            $defaultImg.next('.invalid-feedback').text('Image dimensions must be exactly 620px × 500px.');
                            $defaultImg.val('');
                            $defaultImg.replaceWith($defaultImg.clone(true));
                            toastr['error']('Default product image must be exactly 620px × 500px. Current size: ' + width + 'px × ' + height + 'px');
                            isValid = false;
                        } else {
                            $defaultImg.removeClass('is-invalid');
                            $defaultImg.next('.invalid-feedback').text('');
                        }
                    };

                    img.onerror = function() {
                        $defaultImg.addClass('is-invalid');
                        $defaultImg.next('.invalid-feedback').text('Invalid image file.');
                        $defaultImg.val('');
                        $defaultImg.replaceWith($defaultImg.clone(true));
                        toastr['error']('Invalid image file for default product image.');
                        isValid = false;
                    };

                    img.src = URL.createObjectURL(file);
                }
            }
        }


        if (!window.isEdit || !window.hasAdditionalImg) {
            let hasAdditionalImage = false;
            for (let i = 1; i <= 6; i++) {
                if ($form.find('#uc_img_' + i).val()) {
                    hasAdditionalImage = true;
                    break;
                }
            }
            if (!hasAdditionalImage) {
                let $firstImg = $form.find('#uc_img_1');
                $firstImg.addClass('is-invalid');
                $firstImg.next('.invalid-feedback').text('At least one additional product image is required.');
                isValid = false;
            }
        }

        if ($('#productCreationForm #uc_has_variant').is(':checked')) {
            $form.find('#variant-section > div:visible').each(function() {
                $(this).find('input').each(function() {
                    if ($(this).val().trim() === '') {
                        $(this).addClass('is-invalid');
                        $(this).next('.invalid-feedback').text('This field is required.');
                        isValid = false;
                    }
                });
            });
        }



        if (!isValid) {
            return false;
        }

        var formData = new FormData(this);
        $.ajax({
            url: productStore,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('input[name="_token"]').val()
            },
            success: function(response) {
                if (response.success) {

                    toastr['success']("Product saved successfully!");
                    $form[0].reset();
                    $('#productCreationForm #variant-section').hide();
                    window.variantCount = 1;
                    $('#productCreationForm #variant-2, #productCreationForm #variant-3').hide();
                    $('#productCreationForm #add-variant-btn').show();

                    setTimeout(function() {
                        window.location.href = window.productIndexUrl;
                    }, 1200);
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    $.each(errors, function(field, messages) {
                        var fieldId = '#uc_' + field.replace(/_/g, '_');
                        var $input = $(fieldId);
                        if ($input.length) {
                            $input.addClass('is-invalid');
                            $input.next('.invalid-feedback').text(messages[0]);
                        }
                    });
                } else {

                    toastr['error']("An error occurred while saving the product.");
                }
            }
        });
    });

    $('#productCreationForm').on('input', '.form-control', function() {
        var $input = $(this);
        if ($input.val().trim() !== '') {
            $input.removeClass('is-invalid');
            $input.next('.invalid-feedback').text('');
        }
    });

    // Real-time validation for default image
    $('#productCreationForm').on('change', '#default_img', function() {
        var $input = $(this);
        var file = this.files[0];

        if (file) {
            var allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
            var maxSize = 5 * 1024 * 1024; // 5MB

            if (!allowedTypes.includes(file.type)) {
                $input.addClass('is-invalid');
                $input.next('.invalid-feedback').text('Please upload image in JPG, PNG or GIF format.');
                $input.val('');
                $input.replaceWith($input.clone(true));
                toastr['error']('Please upload image in JPG, PNG or GIF format for default product image.');
                return;
            }

            if (file.size > maxSize) {
                $input.addClass('is-invalid');
                $input.next('.invalid-feedback').text('Image size should be less than 5MB.');
                $input.val('');
                $input.replaceWith($input.clone(true));
                toastr['error']('Image size should be less than 5MB for default product image.');
                return;
            }

            // Check image dimensions
            var img = new Image();
            img.onload = function() {
                var width = this.width;
                var height = this.height;

                if (width !== 620 || height !== 500) {
                    $input.addClass('is-invalid');
                    $input.next('.invalid-feedback').text('Image dimensions must be exactly 620px × 500px.');
                    $input.val('');
                    $input.replaceWith($input.clone(true));
                    toastr['error']('Default product image must be exactly 620px × 500px. Current size: ' + width + 'px × ' + height + 'px');
                } else {
                    $input.removeClass('is-invalid');
                    $input.next('.invalid-feedback').text('');
                    toastr['success']('Default product image uploaded successfully!');
                }
            };

            img.onerror = function() {
                $input.addClass('is-invalid');
                $input.next('.invalid-feedback').text('Invalid image file.');
                $input.val('');
                $input.replaceWith($input.clone(true));
                toastr['error']('Invalid image file for default product image.');
            };

            img.src = URL.createObjectURL(file);
        }
    });

    $('#productCreationForm').on('click', '.clear-img-btn', function() {
        var inputSelector = $(this).data('img');
        var $input = $(inputSelector);
        $input.val('');
        $input.removeClass('is-invalid');
        $input.next('.invalid-feedback').text('');

        // Clear the file input display
        if ($input.attr('type') === 'file') {
            $input.replaceWith($input.clone(true));
        }
    });

});

function productDelete(id) {
    swal.fire({
        title: "Are you sure want to delete this?",
        text: "You won't be able to revert this!",
        showCancelButton: true,
        confirmButtonColor: "#526BDF",
        cancelButtonColor: "#EC6767",
        confirmButtonText: "Yes, delete it!",
        cancelButtonText: "No, cancel it!",
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                type: 'GET',
                url: productDeleteURL + "?id=" + id,
                success: function(resultData) {

                    if (resultData['status'] == 1) {
                        toastr["success"](resultData['message']);

                        $('#datatable_1').DataTable().draw();
                    }
                }
            });
        }
    });
}

function productRestore(id) {
    swal.fire({
        title: "Are you sure want to restore this?",
        text: "You won't be able to revert this!",
        showCancelButton: true,
        confirmButtonColor: "#526BDF",
        cancelButtonColor: "#EC6767",
        confirmButtonText: "Yes, restore it!",
        cancelButtonText: "No, cancel it!",
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                type: 'GET',
                url: productRestoreURL + "?id=" + id,
                success: function(resultData) {
                    if (resultData['status'] == 1) {
                        toastr["success"](resultData['message']);
                        $('#datatable_1').DataTable().draw();
                    }
                }
            });
        }
    });
}


// PRODUCT NAME MANAGEMENT
// PRODUCT NAME MANAGEMENT
// PRODUCT NAME MANAGEMENT
// PRODUCT NAME MANAGEMENT
// PRODUCT NAME MANAGEMENT
// PRODUCT NAME MANAGEMENT
// PRODUCT NAME MANAGEMENT

var table = $('#datatable_3').DataTable({
    "aoColumnDefs": [{ "bSortable": true, "aTargets": [ 0, 1, 2, 3] }],
    "order": [[2, 'asc']],
    "processing": true,
    "serverSide": true,
    "scrollX": true,
    "scrollY": function() {return $(window).height() - 250;},
    "lengthMenu": [[10, 50, 100, 150],[10, 50, 100, 150]],
    "ajax": {
        "url":productNameAjaxURL,
        "type": "POST",
        "data": {
            "status_filter": function() {return $('#products_name_status_filter').val();},
        }
    },
    "aoColumns": [
        {"mData": "action", "bSortable": false},
        {"mData": "name"},
        {"mData": "created_by"},
        {"mData": "modified_by"},
    ]
});

$("#products_name_status_filter").select2();

$("#products_name_status_filter").on('change keyup',function(){
    $('#datatable_3').DataTable().draw();
});


$(function() {

    $('#productNameForm').on('submit', function(e) {
        e.preventDefault();

        var $form = $(this);
        var $input = $form.find('#product_name');
        var value = $input.val().trim();
        var isValid = true;

        // Reset previous error
        $input.removeClass('is-invalid');
        $input.next('.invalid-feedback').text('');

        // Validate
        if (value === '') {
            $input.addClass('is-invalid');
            $input.next('.invalid-feedback').text('Product name is required.');
            isValid = false;
        }

        if (!isValid) {
            return false;
        }

        var formData = $form.serialize();
        $('#productNameSaveBtn').prop('disabled', true);

        $.ajax({
            url: productNameSaveURL,
            type: 'POST',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('input[name="_token"]').val()
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message || 'Product name saved!');
                    $form[0].reset();
                } else {
                    toastr.error(response.message || 'Failed to save product name.');
                }
                $('#productNameSaveBtn').prop('disabled', false);
                $("#product_name_id").val('');
                $("#productNameSaveBtn").html("Insert");
    
            },
            error: function(xhr) {
                toastr.error('An error occurred.');
                $('#productNameSaveBtn').prop('disabled', false);
            }
        });
    });

    // Remove error on input
    $('#product_name').on('input', function() {
        if ($(this).val().trim() !== '') {
            $(this).removeClass('is-invalid');
            $(this).next('.invalid-feedback').text('');
        }
    });

});

function productNameEditView(id) {
    $('#productNameForm').trigger("reset").removeClass('was-validated');
    $('#product_name_id').val('');

    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });

    $.ajax({
        type: 'GET',
        url: getProductNameDetailURL + "?id=" + id,
        success: function(resultData) {
            if (resultData['status'] == 1) {
                $("#product_name_id").val(resultData['data']['id']);
                $("#product_name").val(resultData['data']['name']);
            }
            $("#productNameSaveBtn").html("Update");
        }
    });
}

function productNameDelete(id) {
    swal.fire({
        title: "Are you sure want to delete this?",
        text: "You won't be able to revert this!",
        showCancelButton: true,
        confirmButtonColor: "#526BDF",
        cancelButtonColor: "#EC6767",
        confirmButtonText: "Yes, delete it!",
        cancelButtonText: "No, cancel it!",
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                type: 'GET',
                url: deleteProductNameURL + "?id=" + id,
                success: function(resultData) {

                    if (resultData['status'] == 1) {
                        toastr["success"](resultData['message']);

                        $('#datatable_3').DataTable().draw();
                    }
                }
            });
        }
    });
}

function productNameRestore(id) {
    swal.fire({
        title: "Are you sure want to restore this?",
        text: "You won't be able to revert this!",
        showCancelButton: true,
        confirmButtonColor: "#526BDF",
        cancelButtonColor: "#EC6767",
        confirmButtonText: "Yes, restore it!",
        cancelButtonText: "No, cancel it!",
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                type: 'GET',
                url: restoreProductNameURL + "?id=" + id,
                success: function(resultData) {
                    if (resultData['status'] == 1) {
                        toastr["success"](resultData['message']);
                        $('#datatable_3').DataTable().draw();
                    }
                }
            });
        }
    });
}


$(function() {


    $('#formGeneralSetting').on('submit', function(e) {
        e.preventDefault();

        var $form = $(this);
        var $submitBtn = $form.find('button[type="submit"]');
        var formData = new FormData(this);

        $submitBtn.prop('disabled', true).text('Saving...');

        $.ajax({
            url: generalSettingSave,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('input[name="_token"]').val()
            },
            success: function(response) {
                if (response.success) {
                    toastr['success']("Settings saved successfully!");
                } else {
                    toastr['error'](response.message || "An error occurred while saving settings.");
                }
            },
            error: function(xhr) {
                if (xhr.status === 422 && xhr.responseJSON.errors) {
                    $.each(xhr.responseJSON.errors, function(field, messages) {
                        toastr['error'](messages[0]);
                    });
                } else {
                    toastr['error']("An error occurred while saving settings.");
                }
            },
            complete: function() {
                $submitBtn.prop('disabled', false).text('Save Settings');
            }
        });
    });


});
