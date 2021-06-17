(function($) {

	wp.customize('navigation_width', function(value) {
		value.bind(function(newval) {
		    if (newval === '') {
                $('#site-header, #breadcrumbs').css('max-width', '100%');
                $('#site-header, #breadcrumbs').css('margin', '0');
		    } else {
                $('#site-header, #breadcrumbs').css('max-width', newval);
                $('#site-header, #breadcrumbs').css('margin', '0 auto');
		    }
        });
	});

	wp.customize('hide_site_description', function(value) {
		value.bind(function(newval) {
		    if (newval == 1) {
                $('.site-description').css('display', 'none');
		    } else {
                $('.site-description').css('display', 'block');
		    }
        });
	});

	wp.customize('site_description_below_title', function(value) {
		value.bind(function(newval) {
		    if (newval == 1) {
                $('.header-titles').css('display', 'block');
		    } else {
                $('.header-titles').css('display', 'flex');
		    }
        });
	});

	wp.customize('site_description_text_transform', function(value) {
		value.bind(function(newval) {
		    if (newval === '') {
                $('.site-description').css('text-transform', 'none');
		    } else {
                $('.site-description').css('text-transform', newval);
		    }
        });
	});

	wp.customize('site_description_font_size', function(value) {
		value.bind(function(newval) {
		    if (newval === '') {
                $('.site-description').css('font-size', '2.4rem');
		    } else {
                $('.site-description').css('font-size', (newval / 10) + 'rem');
		    }
        });
	});

	wp.customize('site_description_font_weight', function(value) {
		value.bind(function(newval) {
		    if (newval === '') {
                $('.site-description').css('font-weight', '700');
		    } else {
                $('.site-description').css('font-weight', newval);
		    }
        });
	});

	wp.customize('site_description_color', function(value) {
		value.bind(function(newval) {
		    if (newval === '') {
                $('.site-description').css('color', '#ffffff');
		    } else {
                $('.site-description').css('color', newval);
		    }
        });
	});

	wp.customize('site_title_text_transform', function(value) {
		value.bind(function(newval) {
		    if (newval === '') {
                $('.site-title a').css('text-transform', 'none');
		    } else {
                $('.site-title a').css('text-transform', newval);
		    }
        });
	});

	wp.customize('site_title_font_weight', function(value) {
		value.bind(function(newval) {
		    if (newval === '') {
                $('.site-title a').css('font-weight', '700');
		    } else {
                $('.site-title a').css('font-weight', newval);
		    }
        });
	});

	wp.customize('site_title_color', function(value) {
		value.bind(function(newval) {
		    if (newval === '') {
                $('.site-title a').css('color', '#ffffff');
		    } else {
                $('.site-title a').css('color', newval);
		    }
        });
	});

	wp.customize('nav_text_transform', function(value) {
		value.bind(function(newval) {
		    if (newval === '') {
                $('ul.primary-menu, .modal-menu a').css('text-transform', 'none');
		    } else {
                $('ul.primary-menu, .modal-menu a').css('text-transform', newval);
		    }
        });
	});

	wp.customize('nav_font_size', function(value) {
		value.bind(function(newval) {
		    if (newval === '') {
                $('ul.primary-menu').css('font-size', '2.4rem');
		    } else {
                $('ul.primary-menu').css('font-size', (newval / 10) + 'rem');
		    }
        });
	});

	wp.customize('nav_font_weight', function(value) {
		value.bind(function(newval) {
		    if (newval === '') {
                $('ul.primary-menu, .modal-menu > li > a, .modal-menu > li > .ancestor-wrapper > a').css('font-weight', '700');
		    } else {
                $('ul.primary-menu, .modal-menu > li > a, .modal-menu > li > .ancestor-wrapper > a').css('font-weight', newval);
		    }
        });
	});

	wp.customize('submenu_link_color', function(value) {
		value.bind(function(newval) {
		    if (newval === '') {
                $('.primary-menu ul a').css('color', '#ffffff');
		    } else {
                $('.primary-menu ul a').css('color', newval);
		    }
        });
	});

	wp.customize('hide_expanded_social_menu', function(value) {
		value.bind(function(newval) {
		    if (newval == 1) {
                $('.menu-bottom').css('display', 'none');
		    } else {
                $('.menu-bottom').css('display', 'block');
		    }
        });
	});



	wp.customize('hide_archive_headers', function(value) {
		value.bind(function(newval) {
		    if (newval == 1) {
                $('.archive-header').css('display', 'none');
		    } else {
                $('.archive-header').css('display', 'block');
		    }
        });
	});

	wp.customize('hide_archive_title_prefix', function(value) {
		value.bind(function(newval) {
		    if (newval == 1) {
                $('.archive .archive-title .color-accent').css('display', 'none');
		    } else {
                $('.archive .archive-title .color-accent').css('display', 'inline');
		    }
        });
	});

	wp.customize('archive_title_width', function(value) {
		value.bind(function(newval) {
		    if (newval === '') {
                $('#site-content .archive-header-inner').css('max-width', '100rem');
		    } else {
                $('#site-content .archive-header-inner').css('max-width', newval);
		    }
        });
	});

	wp.customize('archive_title_text_align', function(value) {
		value.bind(function(newval) {
		    if (newval === '') {
                $('h1.archive-title').css('text-align', 'center');
		    } else {
                $('h1.archive-title').css('text-align', newval);
		    }
        });
	});

	wp.customize('archive_title_text_transform', function(value) {
		value.bind(function(newval) {
		    if (newval === '') {
                $('h1.archive-title').css('text-transform', 'none');
		    } else {
                $('h1.archive-title').css('text-transform', newval);
		    }
        });
	});

	wp.customize('archive_title_font_weight', function(value) {
		value.bind(function(newval) {
		    if (newval === '') {
                $('h1.archive-title').css('font-weight', '700');
		    } else {
                $('h1.archive-title').css('font-weight', newval);
		    }
        });
	});

	wp.customize('archive_description_width', function(value) {
		value.bind(function(newval) {
		    if (newval === '') {
                $('#site-content .archive-header-inner .archive-subtitle').css('max-width', '58rem');
		    } else {
                $('#site-content .archive-header-inner .archive-subtitle').css('max-width', newval);
		    }
        });
	});

	wp.customize('archive_description_text_align', function(value) {
		value.bind(function(newval) {
		    if (newval === '') {
                $('div.archive-subtitle').css('text-align', 'center');
		    } else {
                $('div.archive-subtitle').css('text-align', newval);
		    }
        });
	});

	wp.customize('hide_all_headers', function(value) {
		value.bind(function(newval) {
		    if (newval == 1) {
                $('header.entry-header').css('display', 'none');
		    } else {
                $('header.entry-header').css('display', 'block');
		    }
        });
	});

	wp.customize('header_width', function(value) {
		value.bind(function(newval) {
		    if (newval === '') {
                $('#site-content .entry-header-inner, .post-meta-wrapper').css('max-width', '100rem');
		    } else {
                $('#site-content .entry-header-inner, .post-meta-wrapper').css('max-width', newval);
		    }
        });
	});

	wp.customize('hide_all_header_titles', function(value) {
		value.bind(function(newval) {
		    if (newval == 1) {
                $('header.entry-header h1.entry-title, header.entry-header h2.entry-title').css('visibility', 'hidden');
		    } else {
                $('header.entry-header h1.entry-title, header.entry-header h2.entry-title').css('visibility', 'visible');
		    }
        });
	});

	wp.customize('header_title_text_align', function(value) {
		value.bind(function(newval) {
		    if (newval === '') {
                $('h1.entry-title, h2.entry-title').css('text-align', 'center');
		    } else {
                $('h1.entry-title, h2.entry-title').css('text-align', newval);
		    }
        });
	});

	wp.customize('header_title_text_transform', function(value) {
		value.bind(function(newval) {
		    if (newval === '') {
                $('h1.entry-title, h2.entry-title').css('text-transform', 'none');
		    } else {
                $('h1.entry-title, h2.entry-title').css('text-transform', newval);
		    }
        });
	});

	wp.customize('header_title_font_weight', function(value) {
		value.bind(function(newval) {
		    if (newval === '') {
                $('h1.entry-title, h2.entry-title').css('font-weight', '800');
		    } else {
                $('h1.entry-title, h2.entry-title').css('font-weight', newval);
		    }
        });
	});

	wp.customize('hide_excerpt', function(value) {
		value.bind(function(newval) {
		    if (newval == 1) {
                $('.intro-text').css('display', 'none');
		    } else {
                $('.intro-text').css('display', 'block');
		    }
        });
	});

	wp.customize('header_post_meta_align', function(value) {
		value.bind(function(newval) {
		    if (newval === '') {
                $('.post-meta-single-top .post-meta').css('justify-content', 'center');
                $('.post-meta-wrapper').css('max-width', '58rem');
		    } else {
                $('.post-meta-single-top .post-meta').css('justify-content', newval);
                $('.post-meta-wrapper').css('max-width', 'inherit');
		    }
        });
	});

	wp.customize('hide_all_featured_images', function(value) {
		value.bind(function(newval) {
		    if (newval == 1) {
                $('.featured-media').css('display', 'none');
		    } else {
                $('.featured-media').css('display', 'block');
		    }
        });
	});

	wp.customize('hide_pagination', function(value) {
		value.bind(function(newval) {
		    if (newval == 1) {
                $('.pagination-single').css('display', 'none');
		    } else {
                $('.pagination-single').css('display', 'block');
		    }
        });
	});

	wp.customize('footer_nav_text_transform', function(value) {
		value.bind(function(newval) {
		    if (newval === '') {
                $('.footer-menu a').css('text-transform', 'none');
		    } else {
                $('.footer-menu a').css('text-transform', newval);
		    }
        });
	});

	wp.customize('footer_nav_font_weight', function(value) {
		value.bind(function(newval) {
		    if (newval === '') {
                $('.footer-menu').css('font-weight', '500');
		    } else {
                $('.footer-menu').css('font-weight', newval);
		    }
        });
	});

	wp.customize('hide_footer_social_menu', function(value) {
		value.bind(function(newval) {
		    if (newval == 1) {
                $('.footer-social-wrapper, .footer-top:not(.has-footer-menu)').css('display', 'none');
		    } else {
                $('.footer-social-wrapper, .footer-top:not(.has-footer-menu)').css('display', 'block');
		    }
        });
	});

	wp.customize('footer_social_menu_align', function(value) {
		value.bind(function(newval) {
		    if (newval === '') {
                $('.footer-social').css('justify-content', 'center');
		    } else {
                $('.footer-social').css('justify-content', newval);
		    }
        });
	});

	wp.customize('hide_site_footer', function(value) {
		value.bind(function(newval) {
		    if (newval == 1) {
                $('#site-footer').css('display', 'none');
                $('.footer-widgets-outer-wrapper').css('border-bottom', 'none');
		    } else {
                $('#site-footer').css('display', 'block');
                $('.footer-widgets-outer-wrapper').css('border-bottom', '0.1rem solid #dedfdf');
		    }
        });
	});

	wp.customize('remove_copyright', function(value) {
		value.bind(function(newval) {
		    if (newval == 1) {
                $('.footer-copyright').css('display', 'none');
		    } else {
                $('.footer-copyright').css('display', 'block');
		    }
        });
	});

	wp.customize('remove_powered_by_wordpress', function(value) {
		value.bind(function(newval) {
		    if (newval == 1) {
                $('.powered-by-wordpress').css('display', 'none');
		    } else {
                $('.powered-by-wordpress').css('display', 'block');
		    }
        });
	});

	wp.customize('remove_to_the_top', function(value) {
		value.bind(function(newval) {
		    if (newval === '') {
                $('.to-the-top').css('display', 'block');
                $('.footer-credits').css('margin', '0');
		    } else {
                $('.to-the-top').css('display', 'none');
    		    if (newval === 'center') {
                    $('.footer-credits').css('margin', '0 auto');
    		    } else if (newval === 'right') {
                    $('.footer-credits').css('margin', '0 0 0 auto');
    		    }
		    }
        });
	});

	wp.customize('cover_nav_color', function(value) {
		value.bind(function(newval) {
		    if (newval === '') {
                $('.site-description').css('color', '#ffffff');
		    } else {
                $('.site-description').css('color', newval);
		    }
        });
	});

})(jQuery);
