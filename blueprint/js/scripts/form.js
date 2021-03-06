jQuery(function (){

	if (!Modernizr.input.placeholder) {

		/**
		 * Polyfill for placeholder support
		 * @see https://github.com/jamesallardice/Placeholders.js
		 */
		var Placeholders=function(){function i(a){a.value===a.getAttribute("placeholder")&&(a.className=a.className.replace(/\bplaceholderspolyfill\b/,""),a.value="")}function j(a){""===a.value&&(a.className+=" placeholderspolyfill",a.value=a.getAttribute("placeholder"))}function k(a){a.addEventListener?(a.addEventListener("focus",function(){i(a)},!1),a.addEventListener("blur",function(){j(a)},!1)):a.attachEvent&&(a.attachEvent("onfocus",function(){i(a)}),a.attachEvent("onblur",function(){j(a)}))}function l(){var a=
document.getElementsByTagName("input"),e=document.getElementsByTagName("textarea"),f=a.length,g=f+e.length,d,b,c,h;for(d=0;d<g;d+=1)if(b=d<f?a[d]:e[d-f],h=b.getAttribute("placeholder"),-1===m.indexOf(b.type)&&h&&(c=b.getAttribute("data-currentplaceholder"),h!==c)){if(b.value===c||b.value===h||!b.value)b.value=h,b.className+=" placeholderspolyfill";c||k(b);b.setAttribute("data-currentplaceholder",h)}}function n(a){return function(){var e=a.getElementsByTagName("input"),f=a.getElementsByTagName("textarea"),
g=e.length,d=g+f.length,b,c,h;for(h=0;h<d;h+=1)b=h<g?e[h]:f[h-g],c=b.getAttribute("placeholder"),b.value===c&&(b.value="")}}function o(){var a=document.getElementsByTagName("input"),e=document.getElementsByTagName("textarea"),f=a.length,g=f+e.length,d,b,c;for(d=0;d<g;d+=1)if(b=d<f?a[d]:e[d-f],c=b.getAttribute("placeholder"),-1===m.indexOf(b.type)&&c){b.setAttribute("data-currentplaceholder",c);if(""===b.value||b.value===c)b.className+=" placeholderspolyfill",b.value=c;b.form&&(c=b.form,c.getAttribute("data-placeholdersubmit")||
(c.addEventListener?c.addEventListener("submit",n(c),!1):c.attachEvent&&c.attachEvent("onsubmit",n(c)),c.setAttribute("data-placeholdersubmit","true")));k(b)}}var m="hidden datetime date month week time datetime-local range color checkbox radio file submit image reset button".split(" ");return{init:function(a){var e,f,g,d;"undefined"===typeof document.createElement("input").placeholder&&(e=document.createElement("style"),e.type="text/css",f=document.createTextNode(".placeholderspolyfill { color:#999 !important; }"),
e.styleSheet?e.styleSheet.cssText=f.nodeValue:e.appendChild(f),document.getElementsByTagName("head")[0].appendChild(e),Array.prototype.indexOf||(Array.prototype.indexOf=function(a,c){g=c||0;for(d=this.length;g<d;g+=1)if(this[g]===a)return g;return-1}),o(),a&&setInterval(l,100));return!1},refresh:l}}();

		Placeholders.init(true); //Apply to future and modified elements too

	}
	
});

