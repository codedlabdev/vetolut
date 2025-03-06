(function(i,s,o,g,r,a,m){
	i['GoogleAnalyticsObject']=r;
	i[r]=i[r]||function(){
		(i[r].q=i[r].q||[]).push(arguments)},
		i[r].l=1*new Date();a=s.createElement(o),
		m=s.getElementsByTagName(o)[0];
		a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
	})
	
(window,document,'script','../../../www.google-analytics.com/analytics.js','ga');
ga('create','UA-120909275-1','auto');
ga('send','pageview');
$("body").on("contextmenu",function(e){
	return true;
	});

(function($){
	"use strict";
	var $main_nav=$('#main-nav');
	var $toggle=$('.toggle');
	var defaultOptions={
		disableAt:false,customToggle:$toggle,levelSpacing:40,navTitle:'Dactorapp',levelTitles:true,levelTitleAsBack:true,pushContent:'#container',insertClose:2
		};
	var Nav=$main_nav.hcOffcanvasNav(defaultOptions);
	$('.landing-slider').slick({
		dots:true,autoplay:true,nextArrow:false,prewArrow:false,
		});
	$('.top-doctors').slick({
		infinite:false,dots:false,arrows:false,speed:300,autoplay:false,slidesToShow:2.2,slidesToScroll:1,
		});
	$('.available-doctor').slick({
		infinite:false,dots:false,arrows:false,speed:300,autoplay:false,slidesToShow:1.2,slidesToScroll:1,
		});
	$('.recent-doctors').slick({
		infinite:false,dots:false,arrows:false,speed:300,autoplay:false,slidesToShow:2.2,slidesToScroll:1,
		});
	})
(jQuery);