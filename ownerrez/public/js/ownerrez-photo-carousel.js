jQuery(".ownerrez-photo-carousel").each(function() {
	var loadingPager = jQuery(this).nextAll("ul.loading-pager");

	// XXX: the height has to be applied after load (vs. in the regular CSS) or else we'll end up with height:0 slider :-/
	// XXX: we have to capture the load event (if the image isn't cached) as well as checking for complete after the slider loads (if it is cached)
	var isImageLoaded = false;
	var isSliderLoaded = false;
	var carousel = this;

	// height fix for non-cached
	jQuery("li img", carousel).on("load", function ()
	{
		if (isSliderLoaded && !isImageLoaded)
			jQuery("li", carousel).css("height", "100%");

		isImageLoaded = true;
	});

	jQuery(this).lightSlider({
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
			loadingPager.remove();
			lightGallery(el.get(0), {
				licenseKey: "5CD6B56C-064145F1-A29A5203-5655C26F",
				selector: '.ownerrez-photo-carousel .lslide',
				preload: 4,
				plugins: [lgVideo,lgThumbnail],
				loadYouTubeThumbnail: false,
				gotoNextSlideOnVideoEnd: false,
				youTubePlayerParams: {
					modestbranding: 1,
					iv_load_policy: 3
				}
			});
			// height fix for cached
			if (isImageLoaded || jQuery("li img", carousel).filter(function (index) { return this.complete; }).length > 0)
				jQuery("li", carousel).css("height", "100%");

			isSliderLoaded = true;
		},
		prevHtml: '<span class="ownerrez-photo-carousel-prev lg-prev lg-icon"></span>',
		nextHtml: '<span class="ownerrez-photo-carousel-next lg-next lg-icon"></span>'
	}).removeClass("loading-slider");
});