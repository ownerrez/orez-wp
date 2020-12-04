jQuery(".ownerrez-photo-carousel").each(function() {
	var div = jQuery(this);
	div.lightSlider({
		gallery: true,
		item: 1,
		loop: true,
		auto: true,
		pause: 5000,
		thumbItem: 6,
		slideMargin: 0,
		galleryMargin: 0,
		thumbMargin: 0,
		enableDrag: false,
		mode: 'fade',
		onSliderLoad: function (el) {
			el.lightGallery({
				selector: '.ownerrez-photo-carousel .lslide',
				preload: 4
			});
		},
		prevHtml: '<span class="ownerrez-photo-carousel-prev lg-prev lg-icon"></span>',
		nextHtml: '<span class="ownerrez-photo-carousel-next lg-next lg-icon"></span>'
	}).removeClass("loading-slider");
});