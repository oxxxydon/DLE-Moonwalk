/* Chosen v1.8.5 | (c) 2011-2018 by Harvest | MIT License, https://github.com/harvesthq/chosen/blob/master/LICENSE.md */

(function(){var t,e,s,i,n=function(t,e){return function(){return t.apply(e,arguments)}},o=function(t,e){function s(){this.constructor=t}for(var i in e)r.call(e,i)&&(t[i]=e[i]);return s.prototype=e.prototype,t.prototype=new s,t.__super__=e.prototype,t},r={}.hasOwnProperty;(i=function(){function t(){this.options_index=0,this.parsed=[]}return t.prototype.add_node=function(t){return"OPTGROUP"===t.nodeName.toUpperCase()?this.add_group(t):this.add_option(t)},t.prototype.add_group=function(t){var e,s,i,n,o,r;for(e=this.parsed.length,this.parsed.push({array_index:e,group:!0,label:t.label,title:t.title?t.title:void 0,children:0,disabled:t.disabled,classes:t.className}),r=[],s=0,i=(o=t.childNodes).length;s<i;s++)n=o[s],r.push(this.add_option(n,e,t.disabled));return r},t.prototype.add_option=function(t,e,s){if("OPTION"===t.nodeName.toUpperCase())return""!==t.text?(null!=e&&(this.parsed[e].children+=1),this.parsed.push({array_index:this.parsed.length,options_index:this.options_index,value:t.value,text:t.text,html:t.innerHTML,title:t.title?t.title:void 0,selected:t.selected,disabled:!0===s?s:t.disabled,group_array_index:e,group_label:null!=e?this.parsed[e].label:null,classes:t.className,style:t.style.cssText})):this.parsed.push({array_index:this.parsed.length,options_index:this.options_index,empty:!0}),this.options_index+=1},t}()).select_to_array=function(t){var e,s,n,o,r;for(o=new i,s=0,n=(r=t.childNodes).length;s<n;s++)e=r[s],o.add_node(e);return o.parsed},e=function(){function t(e,s){this.form_field=e,this.options=null!=s?s:{},this.label_click_handler=n(this.label_click_handler,this),t.browser_is_supported()&&(this.is_multiple=this.form_field.multiple,this.set_default_text(),this.set_default_values(),this.setup(),this.set_up_html(),this.register_observers(),this.on_ready())}return t.prototype.set_default_values=function(){return this.click_test_action=function(t){return function(e){return t.test_active_click(e)}}(this),this.activate_action=function(t){return function(e){return t.activate_field(e)}}(this),this.active_field=!1,this.mouse_on_container=!1,this.results_showing=!1,this.result_highlighted=null,this.is_rtl=this.options.rtl||/\bchosen-rtl\b/.test(this.form_field.className),this.allow_single_deselect=null!=this.options.allow_single_deselect&&null!=this.form_field.options[0]&&""===this.form_field.options[0].text&&this.options.allow_single_deselect,this.disable_search_threshold=this.options.disable_search_threshold||0,this.disable_search=this.options.disable_search||!1,this.enable_split_word_search=null==this.options.enable_split_word_search||this.options.enable_split_word_search,this.group_search=null==this.options.group_search||this.options.group_search,this.search_contains=this.options.search_contains||!1,this.single_backstroke_delete=null==this.options.single_backstroke_delete||this.options.single_backstroke_delete,this.max_selected_options=this.options.max_selected_options||Infinity,this.inherit_select_classes=this.options.inherit_select_classes||!1,this.display_selected_options=null==this.options.display_selected_options||this.options.display_selected_options,this.display_disabled_options=null==this.options.display_disabled_options||this.options.display_disabled_options,this.include_group_label_in_selected=this.options.include_group_label_in_selected||!1,this.max_shown_results=this.options.max_shown_results||Number.POSITIVE_INFINITY,this.case_sensitive_search=this.options.case_sensitive_search||!1,this.hide_results_on_select=null==this.options.hide_results_on_select||this.options.hide_results_on_select},t.prototype.set_default_text=function(){return this.form_field.getAttribute("data-placeholder")?this.default_text=this.form_field.getAttribute("data-placeholder"):this.is_multiple?this.default_text=this.options.placeholder_text_multiple||this.options.placeholder_text||t.default_multiple_text:this.default_text=this.options.placeholder_text_single||this.options.placeholder_text||t.default_single_text,this.default_text=this.escape_html(this.default_text),this.results_none_found=this.form_field.getAttribute("data-no_results_text")||this.options.no_results_text||t.default_no_result_text},t.prototype.choice_label=function(t){return this.include_group_label_in_selected&&null!=t.group_label?"<b class='group-name'>"+t.group_label+"</b>"+t.html:t.html},t.prototype.mouse_enter=function(){return this.mouse_on_container=!0},t.prototype.mouse_leave=function(){return this.mouse_on_container=!1},t.prototype.input_focus=function(t){if(this.is_multiple){if(!this.active_field)return setTimeout(function(t){return function(){return t.container_mousedown()}}(this),50)}else if(!this.active_field)return this.activate_field()},t.prototype.input_blur=function(t){if(!this.mouse_on_container)return this.active_field=!1,setTimeout(function(t){return function(){return t.blur_test()}}(this),100)},t.prototype.label_click_handler=function(t){return this.is_multiple?this.container_mousedown(t):this.activate_field()},t.prototype.results_option_build=function(t){var e,s,i,n,o,r,h;for(e="",h=0,n=0,o=(r=this.results_data).length;n<o&&(s=r[n],i="",""!==(i=s.group?this.result_add_group(s):this.result_add_option(s))&&(h++,e+=i),(null!=t?t.first:void 0)&&(s.selected&&this.is_multiple?this.choice_build(s):s.selected&&!this.is_multiple&&this.single_set_selected_text(this.choice_label(s))),!(h>=this.max_shown_results));n++);return e},t.prototype.result_add_option=function(t){var e,s;return t.search_match&&this.include_option_in_results(t)?(e=[],t.disabled||t.selected&&this.is_multiple||e.push("active-result"),!t.disabled||t.selected&&this.is_multiple||e.push("disabled-result"),t.selected&&e.push("result-selected"),null!=t.group_array_index&&e.push("group-option"),""!==t.classes&&e.push(t.classes),s=document.createElement("li"),s.className=e.join(" "),t.style&&(s.style.cssText=t.style),s.setAttribute("data-option-array-index",t.array_index),s.innerHTML=t.highlighted_html||t.html,t.title&&(s.title=t.title),this.outerHTML(s)):""},t.prototype.result_add_group=function(t){var e,s;return(t.search_match||t.group_match)&&t.active_options>0?((e=[]).push("group-result"),t.classes&&e.push(t.classes),s=document.createElement("li"),s.className=e.join(" "),s.innerHTML=t.highlighted_html||this.escape_html(t.label),t.title&&(s.title=t.title),this.outerHTML(s)):""},t.prototype.results_update_field=function(){if(this.set_default_text(),this.is_multiple||this.results_reset_cleanup(),this.result_clear_highlight(),this.results_build(),this.results_showing)return this.winnow_results()},t.prototype.reset_single_select_options=function(){var t,e,s,i,n;for(n=[],t=0,e=(s=this.results_data).length;t<e;t++)(i=s[t]).selected?n.push(i.selected=!1):n.push(void 0);return n},t.prototype.results_toggle=function(){return this.results_showing?this.results_hide():this.results_show()},t.prototype.results_search=function(t){return this.results_showing?this.winnow_results():this.results_show()},t.prototype.winnow_results=function(t){var e,s,i,n,o,r,h,l,c,_,a,u,d,f,p;for(this.no_results_clear(),_=0,e=(h=this.get_search_text()).replace(/[-[\]{}()*+?.,\\^$|#\s]/g,"\\$&"),c=this.get_search_regex(e),i=0,n=(l=this.results_data).length;i<n;i++)(o=l[i]).search_match=!1,a=null,u=null,o.highlighted_html="",this.include_option_in_results(o)&&(o.group&&(o.group_match=!1,o.active_options=0),null!=o.group_array_index&&this.results_data[o.group_array_index]&&(0===(a=this.results_data[o.group_array_index]).active_options&&a.search_match&&(_+=1),a.active_options+=1),p=o.group?o.label:o.text,o.group&&!this.group_search||(u=this.search_string_match(p,c),o.search_match=null!=u,o.search_match&&!o.group&&(_+=1),o.search_match?(h.length&&(d=u.index,r=p.slice(0,d),s=p.slice(d,d+h.length),f=p.slice(d+h.length),o.highlighted_html=this.escape_html(r)+"<em>"+this.escape_html(s)+"</em>"+this.escape_html(f)),null!=a&&(a.group_match=!0)):null!=o.group_array_index&&this.results_data[o.group_array_index].search_match&&(o.search_match=!0)));return this.result_clear_highlight(),_<1&&h.length?(this.update_results_content(""),this.no_results(h)):(this.update_results_content(this.results_option_build()),(null!=t?t.skip_highlight:void 0)?void 0:this.winnow_results_set_highlight())},t.prototype.get_search_regex=function(t){var e,s;return s=this.search_contains?t:"(^|\\s|\\b)"+t+"[^\\s]*",this.enable_split_word_search||this.search_contains||(s="^"+s),e=this.case_sensitive_search?"":"i",new RegExp(s,e)},t.prototype.search_string_match=function(t,e){var s;return s=e.exec(t),!this.search_contains&&(null!=s?s[1]:void 0)&&(s.index+=1),s},t.prototype.choices_count=function(){var t,e,s;if(null!=this.selected_option_count)return this.selected_option_count;for(this.selected_option_count=0,t=0,e=(s=this.form_field.options).length;t<e;t++)s[t].selected&&(this.selected_option_count+=1);return this.selected_option_count},t.prototype.choices_click=function(t){if(t.preventDefault(),this.activate_field(),!this.results_showing&&!this.is_disabled)return this.results_show()},t.prototype.keydown_checker=function(t){var e,s;switch(s=null!=(e=t.which)?e:t.keyCode,this.search_field_scale(),8!==s&&this.pending_backstroke&&this.clear_backstroke(),s){case 8:this.backstroke_length=this.get_search_field_value().length;break;case 9:this.results_showing&&!this.is_multiple&&this.result_select(t),this.mouse_on_container=!1;break;case 13:case 27:this.results_showing&&t.preventDefault();break;case 32:this.disable_search&&t.preventDefault();break;case 38:t.preventDefault(),this.keyup_arrow();break;case 40:t.preventDefault(),this.keydown_arrow()}},t.prototype.keyup_checker=function(t){var e,s;switch(s=null!=(e=t.which)?e:t.keyCode,this.search_field_scale(),s){case 8:this.is_multiple&&this.backstroke_length<1&&this.choices_count()>0?this.keydown_backstroke():this.pending_backstroke||(this.result_clear_highlight(),this.results_search());break;case 13:t.preventDefault(),this.results_showing&&this.result_select(t);break;case 27:this.results_showing&&this.results_hide();break;case 9:case 16:case 17:case 18:case 38:case 40:case 91:break;default:this.results_search()}},t.prototype.clipboard_event_checker=function(t){if(!this.is_disabled)return setTimeout(function(t){return function(){return t.results_search()}}(this),50)},t.prototype.container_width=function(){return null!=this.options.width?this.options.width:this.form_field.offsetWidth+"px"},t.prototype.include_option_in_results=function(t){return!(this.is_multiple&&!this.display_selected_options&&t.selected)&&(!(!this.display_disabled_options&&t.disabled)&&!t.empty)},t.prototype.search_results_touchstart=function(t){return this.touch_started=!0,this.search_results_mouseover(t)},t.prototype.search_results_touchmove=function(t){return this.touch_started=!1,this.search_results_mouseout(t)},t.prototype.search_results_touchend=function(t){if(this.touch_started)return this.search_results_mouseup(t)},t.prototype.outerHTML=function(t){var e;return t.outerHTML?t.outerHTML:((e=document.createElement("div")).appendChild(t),e.innerHTML)},t.prototype.get_single_html=function(){return'<a class="chosen-single chosen-default">\n  <input class="chosen-focus-input" type="text" autocomplete="off" />\n  <span>'+this.default_text+'</span>\n  <div><b></b></div>\n</a>\n<div class="chosen-drop">\n  <div class="chosen-search">\n    <input class="chosen-search-input" type="text" autocomplete="off" />\n  </div>\n  <ul class="chosen-results"></ul>\n</div>'},t.prototype.get_multi_html=function(){return'<ul class="chosen-choices">\n  <li class="search-field">\n    <input class="chosen-search-input" type="text" autocomplete="off" value="'+this.default_text+'" />\n  </li>\n</ul>\n<div class="chosen-drop">\n  <ul class="chosen-results"></ul>\n</div>'},t.prototype.get_no_results_html=function(t){return'<li class="no-results">\n  '+this.results_none_found+" <span>"+this.escape_html(t)+"</span>\n</li>"},t.browser_is_supported=function(){return"Microsoft Internet Explorer"===window.navigator.appName?document.documentMode>=8:!(/iP(od|hone)/i.test(window.navigator.userAgent)||/IEMobile/i.test(window.navigator.userAgent)||/Windows Phone/i.test(window.navigator.userAgent)||/BlackBerry/i.test(window.navigator.userAgent)||/BB10/i.test(window.navigator.userAgent)||/Android.*Mobile/i.test(window.navigator.userAgent))},t.default_multiple_text="Select Some Options",t.default_single_text="Select an Option",t.default_no_result_text="No results match",t}(),(t=jQuery).fn.extend({chosen:function(i){return e.browser_is_supported()?this.each(function(e){var n,o;o=(n=t(this)).data("chosen"),"destroy"!==i?o instanceof s||n.data("chosen",new s(this,i)):o instanceof s&&o.destroy()}):this}}),s=function(s){function n(){return n.__super__.constructor.apply(this,arguments)}return o(n,e),n.prototype.setup=function(){return this.form_field_jq=t(this.form_field),this.current_selectedIndex=this.form_field.selectedIndex},n.prototype.set_up_html=function(){var e,s;return(e=["chosen-container"]).push("chosen-container-"+(this.is_multiple?"multi":"single")),this.inherit_select_classes&&this.form_field.className&&e.push(this.form_field.className),this.is_rtl&&e.push("chosen-rtl"),s={"class":e.join(" "),title:this.form_field.title},this.form_field.id.length&&(s.id=this.form_field.id.replace(/[^\w]/g,"_")+"_chosen"),this.container=t("<div />",s),this.container.width(this.container_width()),this.is_multiple?this.container.html(this.get_multi_html()):this.container.html(this.get_single_html()),this.form_field_jq.hide().after(this.container),this.dropdown=this.container.find("div.chosen-drop").first(),this.search_field=this.container.find("input.chosen-search-input"),this.focus_field=this.container.find("input.chosen-focus-input"),this.search_results=this.container.find("ul.chosen-results").first(),this.search_field_scale(),this.search_no_results=this.container.find("li.no-results").first(),this.is_multiple?(this.search_choices=this.container.find("ul.chosen-choices").first(),this.search_container=this.container.find("li.search-field").first()):(this.search_container=this.container.find("div.chosen-search").first(),this.selected_item=this.container.find(".chosen-single").first()),this.results_build(),this.set_tab_index(),this.set_label_behavior()},n.prototype.on_ready=function(){return this.form_field_jq.trigger("chosen:ready",{chosen:this})},n.prototype.register_observers=function(){var t;return this.container.on("touchstart.chosen",function(t){return function(e){t.container_mousedown(e)}}(this)),this.container.on("touchend.chosen",function(t){return function(e){t.container_mouseup(e)}}(this)),this.container.on("mousedown.chosen",function(t){return function(e){t.container_mousedown(e)}}(this)),this.container.on("mouseup.chosen",function(t){return function(e){t.container_mouseup(e)}}(this)),this.container.on("mouseenter.chosen",function(t){return function(e){t.mouse_enter(e)}}(this)),this.container.on("mouseleave.chosen",function(t){return function(e){t.mouse_leave(e)}}(this)),this.search_results.on("mouseup.chosen",function(t){return function(e){t.search_results_mouseup(e)}}(this)),this.search_results.on("mouseover.chosen",function(t){return function(e){t.search_results_mouseover(e)}}(this)),this.search_results.on("mouseout.chosen",function(t){return function(e){t.search_results_mouseout(e)}}(this)),this.search_results.on("mousewheel.chosen DOMMouseScroll.chosen",function(t){return function(e){t.search_results_mousewheel(e)}}(this)),this.search_results.on("touchstart.chosen",function(t){return function(e){t.search_results_touchstart(e)}}(this)),this.search_results.on("touchmove.chosen",function(t){return function(e){t.search_results_touchmove(e)}}(this)),this.search_results.on("touchend.chosen",function(t){return function(e){t.search_results_touchend(e)}}(this)),this.form_field_jq.on("chosen:updated.chosen",function(t){return function(e){t.results_update_field(e)}}(this)),this.form_field_jq.on("chosen:activate.chosen",function(t){return function(e){t.activate_field(e)}}(this)),this.form_field_jq.on("chosen:open.chosen",function(t){return function(e){t.container_mousedown(e)}}(this)),this.form_field_jq.on("chosen:close.chosen",function(t){return function(e){t.close_field(e)}}(this)),this.search_field.on("blur.chosen",function(t){return function(e){t.input_blur(e)}}(this)),this.search_field.on("keyup.chosen",function(t){return function(e){t.keyup_checker(e)}}(this)),this.search_field.on("keydown.chosen",function(t){return function(e){t.keydown_checker(e)}}(this)),this.search_field.on("focus.chosen",function(t){return function(e){t.input_focus(e)}}(this)),this.search_field.on("cut.chosen",function(t){return function(e){t.clipboard_event_checker(e)}}(this)),this.search_field.on("paste.chosen",function(t){return function(e){t.clipboard_event_checker(e)}}(this)),this.is_multiple?this.search_choices.on("click.chosen",function(t){return function(e){t.choices_click(e)}}(this)):(this.container.on("click.chosen",function(t){t.preventDefault()}),this.focus_field.on("blur.chosen",function(t){return function(e){t.input_blur(e)}}(this)),this.focus_field.on("focus.chosen",function(t){return function(e){t.input_focus(e)}}(this)),t=function(t){return function(){return t.search_field.val(t.focus_field.val()),t.focus_field.val("")}}(this),this.focus_field.on("keyup.chosen",function(e){return function(s){t(),e.keyup_checker(s)}}(this)),this.focus_field.on("keydown.chosen",function(e){return function(s){t(),e.keydown_checker(s)}}(this)),this.focus_field.on("cut.chosen",function(e){return function(s){setTimeout(t,0),e.clipboard_event_checker(s)}}(this)),this.focus_field.on("paste.chosen",function(e){return function(s){setTimeout(t,0),e.clipboard_event_checker(s)}}(this)))},n.prototype.destroy=function(){return t(this.container[0].ownerDocument).off("click.chosen",this.click_test_action),this.form_field_label.length>0&&this.form_field_label.off("click.chosen"),this.search_field[0].tabIndex&&(this.form_field_jq[0].tabIndex=this.search_field[0].tabIndex),this.container.remove(),this.form_field_jq.removeData("chosen"),this.form_field_jq.show()},n.prototype.search_field_disabled=function(){return this.is_disabled=this.form_field.disabled||this.form_field_jq.parents("fieldset").is(":disabled"),this.container.toggleClass("chosen-disabled",this.is_disabled),this.search_field[0].disabled=this.is_disabled,this.is_multiple||this.selected_item.off("focus.chosen",this.activate_field),this.is_disabled?this.close_field():this.is_multiple?void 0:this.selected_item.on("focus.chosen",this.activate_field)},n.prototype.container_mousedown=function(e){var s;if(!this.is_disabled)return!e||"mousedown"!==(s=e.type)&&"touchstart"!==s||this.results_showing||e.preventDefault(),null!=e&&t(e.target).hasClass("search-choice-close")?void 0:(this.active_field?this.is_multiple||!e||t(e.target)[0]!==this.selected_item[0]&&!t(e.target).parents("a.chosen-single").length||(e.preventDefault(),this.results_toggle()):(this.is_multiple&&this.search_field.val(""),t(this.container[0].ownerDocument).on("click.chosen",this.click_test_action),this.results_show()),this.activate_field())},n.prototype.container_mouseup=function(t){if("ABBR"===t.target.nodeName&&!this.is_disabled)return this.results_reset(t)},n.prototype.search_results_mousewheel=function(t){var e;if(t.originalEvent&&(e=t.originalEvent.deltaY||-t.originalEvent.wheelDelta||t.originalEvent.detail),null!=e)return t.preventDefault(),"DOMMouseScroll"===t.type&&(e*=40),this.search_results.scrollTop(e+this.search_results.scrollTop())},n.prototype.blur_test=function(t){if(!this.active_field&&this.container.hasClass("chosen-container-active"))return this.close_field()},n.prototype.close_field=function(){return t(this.container[0].ownerDocument).off("click.chosen",this.click_test_action),this.active_field=!1,this.results_hide(),this.container.removeClass("chosen-container-active"),this.clear_backstroke(),this.show_search_field_default(),this.search_field_scale(),this.search_field.blur()},n.prototype.activate_field=function(){if(!this.is_disabled)return this.container.addClass("chosen-container-active"),this.active_field=!0,this.search_field.focus()},n.prototype.test_active_click=function(e){var s;return(s=t(e.target).closest(".chosen-container")).length&&this.container[0]===s[0]?this.active_field=!0:this.close_field()},n.prototype.results_build=function(){return this.parsing=!0,this.selected_option_count=null,this.results_data=i.select_to_array(this.form_field),this.is_multiple?this.search_choices.find("li.search-choice").remove():(this.single_set_selected_text(),this.disable_search||this.form_field.options.length<=this.disable_search_threshold?(this.search_field[0].readOnly=!0,this.focus_field[0].readOnly=!0,this.container.addClass("chosen-container-single-nosearch")):(this.search_field[0].readOnly=!1,this.focus_field[0].readOnly=!1,this.container.removeClass("chosen-container-single-nosearch"))),this.update_results_content(this.results_option_build({first:!0})),this.search_field_disabled(),this.show_search_field_default(),this.search_field_scale(),this.parsing=!1},n.prototype.result_do_highlight=function(t){var e,s,i,n,o;if(t.length){if(this.result_clear_highlight(),this.result_highlight=t,this.result_highlight.addClass("highlighted"),i=parseInt(this.search_results.css("maxHeight"),10),o=this.search_results.scrollTop(),n=i+o,s=this.result_highlight.position().top+this.search_results.scrollTop(),(e=s+this.result_highlight.outerHeight())>=n)return this.search_results.scrollTop(e-i>0?e-i:0);if(s<o)return this.search_results.scrollTop(s)}},n.prototype.result_clear_highlight=function(){return this.result_highlight&&this.result_highlight.removeClass("highlighted"),this.result_highlight=null},n.prototype.results_show=function(){return this.is_multiple&&this.max_selected_options<=this.choices_count()?(this.form_field_jq.trigger("chosen:maxselected",{chosen:this}),!1):(this.container.addClass("chosen-with-drop"),this.results_showing=!0,this.search_field.focus(),this.search_field.val(this.get_search_field_value()),this.winnow_results(),this.form_field_jq.trigger("chosen:showing_dropdown",{chosen:this}))},n.prototype.update_results_content=function(t){return this.search_results.html(t)},n.prototype.results_hide=function(){return this.results_showing&&(this.result_clear_highlight(),setTimeout(function(t){return function(){return t.focus_field.focus()}}(this),0),this.container.removeClass("chosen-with-drop"),this.form_field_jq.trigger("chosen:hiding_dropdown",{chosen:this})),this.results_showing=!1},n.prototype.set_tab_index=function(t){var e,s;if(this.form_field.tabIndex)return s=this.form_field.tabIndex,this.form_field.tabIndex=-1,this.search_field[0].tabIndex=s,null!=(e=this.focus_field[0])?e.tabIndex=s:void 0},n.prototype.set_label_behavior=function(){if(this.form_field_label=this.form_field_jq.parents("label"),!this.form_field_label.length&&this.form_field.id.length&&(this.form_field_label=t("label[for='"+this.form_field.id+"']")),this.form_field_label.length>0)return this.form_field_label.on("click.chosen",this.label_click_handler)},n.prototype.show_search_field_default=function(){return this.is_multiple&&this.choices_count()<1&&!this.active_field?(this.search_field.val(this.default_text),this.search_field.addClass("default")):(this.search_field.val(""),this.search_field.removeClass("default"))},n.prototype.search_results_mouseup=function(e){var s;if((s=t(e.target).hasClass("active-result")?t(e.target):t(e.target).parents(".active-result").first()).length)return this.result_highlight=s,this.result_select(e),this.search_field.focus()},n.prototype.search_results_mouseover=function(e){var s;if(s=t(e.target).hasClass("active-result")?t(e.target):t(e.target).parents(".active-result").first())return this.result_do_highlight(s)},n.prototype.search_results_mouseout=function(e){if(t(e.target).hasClass("active-result")||t(e.target).parents(".active-result").first())return this.result_clear_highlight()},n.prototype.choice_build=function(e){var s,i;return s=t("<li />",{"class":"search-choice"}).html("<span>"+this.choice_label(e)+"</span>"),e.disabled?s.addClass("search-choice-disabled"):((i=t("<a />",{"class":"search-choice-close","data-option-array-index":e.array_index})).on("click.chosen",function(t){return function(e){return t.choice_destroy_link_click(e)}}(this)),s.append(i)),this.search_container.before(s)},n.prototype.choice_destroy_link_click=function(e){if(e.preventDefault(),e.stopPropagation(),!this.is_disabled)return this.choice_destroy(t(e.target))},n.prototype.choice_destroy=function(t){if(this.result_deselect(t[0].getAttribute("data-option-array-index")))return this.active_field?this.search_field.focus():this.show_search_field_default(),this.is_multiple&&this.choices_count()>0&&this.get_search_field_value().length<1&&this.results_hide(),t.parents("li").first().remove(),this.search_field_scale()},n.prototype.results_reset=function(){if(this.reset_single_select_options(),this.form_field.options[0].selected=!0,this.single_set_selected_text(),this.show_search_field_default(),this.results_reset_cleanup(),this.trigger_form_field_change(),this.active_field)return this.results_hide()},n.prototype.results_reset_cleanup=function(){return this.current_selectedIndex=this.form_field.selectedIndex,this.selected_item.find("abbr").remove()},n.prototype.result_select=function(t){var e,s;if(this.result_highlight)return e=this.result_highlight,this.result_clear_highlight(),this.is_multiple&&this.max_selected_options<=this.choices_count()?(this.form_field_jq.trigger("chosen:maxselected",{chosen:this}),!1):(this.is_multiple?e.removeClass("active-result"):this.reset_single_select_options(),e.addClass("result-selected"),s=this.results_data[e[0].getAttribute("data-option-array-index")],s.selected=!0,this.form_field.options[s.options_index].selected=!0,this.selected_option_count=null,this.is_multiple?this.choice_build(s):this.single_set_selected_text(this.choice_label(s)),this.is_multiple&&(!this.hide_results_on_select||t.metaKey||t.ctrlKey)?t.metaKey||t.ctrlKey?this.winnow_results({skip_highlight:!0}):(this.search_field.val(""),this.winnow_results()):(this.results_hide(),this.show_search_field_default()),(this.is_multiple||this.form_field.selectedIndex!==this.current_selectedIndex)&&this.trigger_form_field_change({selected:this.form_field.options[s.options_index].value}),this.current_selectedIndex=this.form_field.selectedIndex,t.preventDefault(),this.search_field_scale())},n.prototype.single_set_selected_text=function(t){return null==t&&(t=this.default_text),t===this.default_text?this.selected_item.addClass("chosen-default"):(this.single_deselect_control_build(),this.selected_item.removeClass("chosen-default")),this.selected_item.find("span").html(t)},n.prototype.result_deselect=function(t){var e;return e=this.results_data[t],!this.form_field.options[e.options_index].disabled&&(e.selected=!1,this.form_field.options[e.options_index].selected=!1,this.selected_option_count=null,this.result_clear_highlight(),this.results_showing&&this.winnow_results(),this.trigger_form_field_change({deselected:this.form_field.options[e.options_index].value}),this.search_field_scale(),!0)},n.prototype.single_deselect_control_build=function(){if(this.allow_single_deselect)return this.selected_item.find("abbr").length||this.selected_item.find("span").first().after('<abbr class="search-choice-close"></abbr>'),this.selected_item.addClass("chosen-single-with-deselect")},n.prototype.get_search_field_value=function(){return this.search_field.val()},n.prototype.get_search_text=function(){return t.trim(this.get_search_field_value())},n.prototype.escape_html=function(e){return t("<div/>").text(e).html()},n.prototype.winnow_results_set_highlight=function(){var t,e;if(e=this.is_multiple?[]:this.search_results.find(".result-selected.active-result"),null!=(t=e.length?e.first():this.search_results.find(".active-result").first()))return this.result_do_highlight(t)},n.prototype.no_results=function(t){var e;return e=this.get_no_results_html(t),this.search_results.append(e),this.form_field_jq.trigger("chosen:no_results",{chosen:this})},n.prototype.no_results_clear=function(){return this.search_results.find(".no-results").remove()},n.prototype.keydown_arrow=function(){var t;return this.results_showing&&this.result_highlight?(t=this.result_highlight.nextAll("li.active-result").first())?this.result_do_highlight(t):void 0:this.results_show()},n.prototype.keyup_arrow=function(){var t;return this.results_showing||this.is_multiple?this.result_highlight?(t=this.result_highlight.prevAll("li.active-result")).length?this.result_do_highlight(t.first()):(this.choices_count()>0&&this.results_hide(),this.result_clear_highlight()):void 0:this.results_show()},n.prototype.keydown_backstroke=function(){var t;return this.pending_backstroke?(this.choice_destroy(this.pending_backstroke.find("a").first()),this.clear_backstroke()):(t=this.search_container.siblings("li.search-choice").last()).length&&!t.hasClass("search-choice-disabled")?(this.pending_backstroke=t,this.single_backstroke_delete?this.keydown_backstroke():this.pending_backstroke.addClass("search-choice-focus")):void 0},n.prototype.clear_backstroke=function(){return this.pending_backstroke&&this.pending_backstroke.removeClass("search-choice-focus"),this.pending_backstroke=null},n.prototype.search_field_scale=function(){var e,s,i,n,o,r,h;if(this.is_multiple){for(o={position:"absolute",left:"-1000px",top:"-1000px",display:"none",whiteSpace:"pre"},s=0,i=(r=["fontSize","fontStyle","fontWeight","fontFamily","lineHeight","textTransform","letterSpacing"]).length;s<i;s++)o[n=r[s]]=this.search_field.css(n);return(e=t("<div />").css(o)).text(this.get_search_field_value()),t("body").append(e),h=e.width()+25,e.remove(),this.container.is(":visible")&&(h=Math.min(this.container.outerWidth()-10,h)),this.search_field.width(h)}},n.prototype.trigger_form_field_change=function(t){return this.form_field_jq.trigger("input",t),this.form_field_jq.trigger("change",t)},n}()}).call(this);

// jQuery toast plugin created by Kamran Ahmed copyright MIT license 2015
if ( typeof Object.create !== 'function' ) {
    Object.create = function( obj ) {
        function F() {}
        F.prototype = obj;
        return new F();
    };
}

(function( $, window, document, undefined ) {

    "use strict";
    
    var Toast = {

        _positionClasses : ['bottom-left', 'bottom-right', 'top-right', 'top-left', 'bottom-center', 'top-center', 'mid-center'],
        _defaultIcons : ['success', 'error', 'info', 'warning'],

        init: function (options, elem) {
            this.prepareOptions(options, $.toast.options);
            this.process();
        },

        prepareOptions: function(options, options_to_extend) {
            var _options = {};
            if ( ( typeof options === 'string' ) || ( options instanceof Array ) ) {
                _options.text = options;
            } else {
                _options = options;
            }
            this.options = $.extend( {}, options_to_extend, _options );
        },

        process: function () {
            this.setup();
            this.addToDom();
            this.position();
            this.bindToast();
            this.animate();
        },

        setup: function () {
            
            var _toastContent = '';
            
            this._toastEl = this._toastEl || $('<div></div>', {
                class : 'jq-toast-single'
            });

            // For the loader on top
            _toastContent += '<span class="jq-toast-loader"></span>';            

            if ( this.options.allowToastClose ) {
                _toastContent += '<span class="close-jq-toast-single">&times;</span>';
            };

            if ( this.options.text instanceof Array ) {

                if ( this.options.heading ) {
                    _toastContent +='<h2 class="jq-toast-heading">' + this.options.heading + '</h2>';
                };

                _toastContent += '<ul class="jq-toast-ul">';
                for (var i = 0; i < this.options.text.length; i++) {
                    _toastContent += '<li class="jq-toast-li" id="jq-toast-item-' + i + '">' + this.options.text[i] + '</li>';
                }
                _toastContent += '</ul>';

            } else {
                if ( this.options.heading ) {
                    _toastContent +='<h2 class="jq-toast-heading">' + this.options.heading + '</h2>';
                };
                _toastContent += this.options.text;
            }

            this._toastEl.html( _toastContent );

            if ( this.options.bgColor !== false ) {
                this._toastEl.css("background-color", this.options.bgColor);
            };

            if ( this.options.textColor !== false ) {
                this._toastEl.css("color", this.options.textColor);
            };

            if ( this.options.textAlign ) {
                this._toastEl.css('text-align', this.options.textAlign);
            }

            if ( this.options.icon !== false ) {
                this._toastEl.addClass('jq-has-icon');

                if ( $.inArray(this.options.icon, this._defaultIcons) !== -1 ) {
                    this._toastEl.addClass('jq-icon-' + this.options.icon);
                };
            };

            if ( this.options.class !== false ){
                this._toastEl.addClass(this.options.class)
            }
        },

        position: function () {
            if ( ( typeof this.options.position === 'string' ) && ( $.inArray( this.options.position, this._positionClasses) !== -1 ) ) {

                if ( this.options.position === 'bottom-center' ) {
                    this._container.css({
                        left: ( $(window).outerWidth() / 2 ) - this._container.outerWidth()/2,
                        bottom: 20
                    });
                } else if ( this.options.position === 'top-center' ) {
                    this._container.css({
                        left: ( $(window).outerWidth() / 2 ) - this._container.outerWidth()/2,
                        top: 20
                    });
                } else if ( this.options.position === 'mid-center' ) {
                    this._container.css({
                        left: ( $(window).outerWidth() / 2 ) - this._container.outerWidth()/2,
                        top: ( $(window).outerHeight() / 2 ) - this._container.outerHeight()/2
                    });
                } else {
                    this._container.addClass( this.options.position );
                }

            } else if ( typeof this.options.position === 'object' ) {
                this._container.css({
                    top : this.options.position.top ? this.options.position.top : 'auto',
                    bottom : this.options.position.bottom ? this.options.position.bottom : 'auto',
                    left : this.options.position.left ? this.options.position.left : 'auto',
                    right : this.options.position.right ? this.options.position.right : 'auto'
                });
            } else {
                this._container.addClass( 'bottom-left' );
            }
        },

        bindToast: function () {

            var that = this;

            this._toastEl.on('afterShown', function () {
                that.processLoader();
            });

            this._toastEl.find('.close-jq-toast-single').on('click', function ( e ) {

                e.preventDefault();

                if( that.options.showHideTransition === 'fade') {
                    that._toastEl.trigger('beforeHide');
                    that._toastEl.fadeOut(function () {
                        that._toastEl.trigger('afterHidden');
                    });
                } else if ( that.options.showHideTransition === 'slide' ) {
                    that._toastEl.trigger('beforeHide');
                    that._toastEl.slideUp(function () {
                        that._toastEl.trigger('afterHidden');
                    });
                } else {
                    that._toastEl.trigger('beforeHide');
                    that._toastEl.hide(function () {
                        that._toastEl.trigger('afterHidden');
                    });
                }
            });

            if ( typeof this.options.beforeShow == 'function' ) {
                this._toastEl.on('beforeShow', function () {
                    that.options.beforeShow(that._toastEl);
                });
            };

            if ( typeof this.options.afterShown == 'function' ) {
                this._toastEl.on('afterShown', function () {
                    that.options.afterShown(that._toastEl);
                });
            };

            if ( typeof this.options.beforeHide == 'function' ) {
                this._toastEl.on('beforeHide', function () {
                    that.options.beforeHide(that._toastEl);
                });
            };

            if ( typeof this.options.afterHidden == 'function' ) {
                this._toastEl.on('afterHidden', function () {
                    that.options.afterHidden(that._toastEl);
                });
            };

            if ( typeof this.options.onClick == 'function' ) {
                this._toastEl.on('click', function () {
                    that.options.onClick(that._toastEl);
                });
            };    
        },

        addToDom: function () {

             var _container = $('.jq-toast-wrap');
             
             if ( _container.length === 0 ) {
                
                _container = $('<div></div>',{
                    class: "jq-toast-wrap",
                    role: "alert",
                    "aria-live": "polite"
                });

                $('body').append( _container );

             } else if ( !this.options.stack || isNaN( parseInt(this.options.stack, 10) ) ) {
                _container.empty();
             }

             _container.find('.jq-toast-single:hidden').remove();

             _container.append( this._toastEl );

            if ( this.options.stack && !isNaN( parseInt( this.options.stack ), 10 ) ) {
                
                var _prevToastCount = _container.find('.jq-toast-single').length,
                    _extToastCount = _prevToastCount - this.options.stack;

                if ( _extToastCount > 0 ) {
                    $('.jq-toast-wrap').find('.jq-toast-single').slice(0, _extToastCount).remove();
                };

            }

            this._container = _container;
        },

        canAutoHide: function () {
            return ( this.options.hideAfter !== false ) && !isNaN( parseInt( this.options.hideAfter, 10 ) );
        },

        processLoader: function () {
            // Show the loader only, if auto-hide is on and loader is demanded
            if (!this.canAutoHide() || this.options.loader === false) {
                return false;
            }

            var loader = this._toastEl.find('.jq-toast-loader');

            // 400 is the default time that jquery uses for fade/slide
            // Divide by 1000 for milliseconds to seconds conversion
            var transitionTime = (this.options.hideAfter - 400) / 1000 + 's';
            var loaderBg = this.options.loaderBg;

            var style = loader.attr('style') || '';
            style = style.substring(0, style.indexOf('-webkit-transition')); // Remove the last transition definition

            style += '-webkit-transition: width ' + transitionTime + ' ease-in; \
                      -o-transition: width ' + transitionTime + ' ease-in; \
                      transition: width ' + transitionTime + ' ease-in; \
                      background-color: ' + loaderBg + ';';


            loader.attr('style', style).addClass('jq-toast-loaded');
        },

        animate: function () {

            var that = this;

            this._toastEl.hide();

            this._toastEl.trigger('beforeShow');

            if ( this.options.showHideTransition.toLowerCase() === 'fade' ) {
                this._toastEl.fadeIn(function ( ){
                    that._toastEl.trigger('afterShown');
                });
            } else if ( this.options.showHideTransition.toLowerCase() === 'slide' ) {
                this._toastEl.slideDown(function ( ){
                    that._toastEl.trigger('afterShown');
                });
            } else {
                this._toastEl.show(function ( ){
                    that._toastEl.trigger('afterShown');
                });
            }

            if (this.canAutoHide()) {

                var that = this;

                window.setTimeout(function(){
                    
                    if ( that.options.showHideTransition.toLowerCase() === 'fade' ) {
                        that._toastEl.trigger('beforeHide');
                        that._toastEl.fadeOut(function () {
                            that._toastEl.trigger('afterHidden');
                        });
                    } else if ( that.options.showHideTransition.toLowerCase() === 'slide' ) {
                        that._toastEl.trigger('beforeHide');
                        that._toastEl.slideUp(function () {
                            that._toastEl.trigger('afterHidden');
                        });
                    } else {
                        that._toastEl.trigger('beforeHide');
                        that._toastEl.hide(function () {
                            that._toastEl.trigger('afterHidden');
                        });
                    }

                }, this.options.hideAfter);
            };
        },

        reset: function ( resetWhat ) {

            if ( resetWhat === 'all' ) {
                $('.jq-toast-wrap').remove();
            } else {
                this._toastEl.remove();
            }

        },

        update: function(options) {
            this.prepareOptions(options, this.options);
            this.setup();
            this.bindToast();
        },
        
        close: function() {
            this._toastEl.find('.close-jq-toast-single').click();
        }
    };
    
    $.toast = function(options) {
        var toast = Object.create(Toast);
        toast.init(options, this);

        return {
            
            reset: function ( what ) {
                toast.reset( what );
            },

            update: function( options ) {
                toast.update( options );
            },
            
            close: function( ) {
            	toast.close( );
            }
        }
    };

    $.toast.options = {
        text: '',
        heading: '',
        showHideTransition: 'fade',
        allowToastClose: true,
        hideAfter: 3000,
        loader: true,
        loaderBg: '#9EC600',
        stack: 5,
        position: 'bottom-left',
        bgColor: false,
        textColor: false,
        textAlign: 'left',
        icon: false,
        beforeShow: function () {},
        afterShown: function () {},
        beforeHide: function () {},
        afterHidden: function () {},
        onClick: function () {}
    };

})( jQuery, window, document );

/*!
 * SweetModal: Sweet, easy and powerful modals and dialogs
 * v1.3.3, 2017-05-27
 * http://github.com/adeptoas/sweet-modal
 *
 * Copyright (c) 2016 Adepto.as AS · Oslo, Norway
 * Dual licensed under the MIT and GPL licenses.
 *
 * See LICENSE-MIT.txt and LICENSE-GPL.txt
 */
!function e(t,n,r){function s(o,u){if(!n[o]){if(!t[o]){var a="function"==typeof require&&require;if(!u&&a)return a(o,!0);if(i)return i(o,!0);var f=new Error("Cannot find module '"+o+"'");throw f.code="MODULE_NOT_FOUND",f}var l=n[o]={exports:{}};t[o][0].call(l.exports,function(e){var n=t[o][1][e];return s(n?n:e)},l,l.exports,e,t,n,r)}return n[o].exports}for(var i="function"==typeof require&&require,o=0;o<r.length;o++)s(r[o]);return s}({1:[function(require,module,exports){var SweetModal,helpers,templates,bind=function(fn,me){return function(){return fn.apply(me,arguments)}};helpers=require("./helpers.coffee"),templates=require("./templates.coffee"),SweetModal=function(){function SweetModal(params){this.params=params,this.close=bind(this.close,this)}return SweetModal.prototype.params={},SweetModal.prototype.$overlay=null,SweetModal.prototype.getParams=function(){return this.params},SweetModal.prototype._constructButtons=function($modal){var $button,$buttons,label,name,obj,ref;if($buttons=$(templates.buttons),"object"==typeof this.params.buttons&&helpers.objectSize(this.params.buttons)>0){ref=this.params.buttons;for(name in ref)obj=ref[name],obj=$.extend({label:void 0,action:function(){},classes:"",class:""},obj),obj.classes.length<1&&(obj.classes=obj.class),label=obj.label||""===obj.label?obj.label:name,$button=$('<a href="javascript:void(0);" class="button '+obj.classes+'">'+label+"</a>"),$button.bind("click",{buttonObject:obj,parentObject:this},function(e){var result;return e.preventDefault(),result=e.data.buttonObject.action(e.data.parentObject),void 0===result||result!==!1?e.data.parentObject.close():void 0}),$buttons.append($button);$modal.append($buttons)}return $buttons},SweetModal.prototype._constructTitle=function($overlay,$modal){var $icon,$modalTabs,$modalTabsUL,$modalTitle,$tpl,icon,key,label,ref,value;if("string"==typeof this.params.title)return""!==this.params.title?$modal.find(".sweet-modal-title h2").html(this.params.title):$modal.find(".sweet-modal-title-wrap").remove();if("object"==typeof this.params.title){$overlay.addClass("tabbed"),$modalTitle=$modal.find(".sweet-modal-title"),$modalTitle.find("h2").remove(),$modalTabs=$(templates.tabs.links),$modalTabsUL=$modalTabs.find("ul"),ref=this.params.title;for(key in ref)value=ref[key],$tpl=$(templates.prepare(templates.tabs.link,{TAB_ID:key})),label=icon=!1,"string"==typeof value?(label=value,icon=!1):(label=value.label||!1,icon=value.icon||!1),icon?($icon=$tpl.find("a .icon").html(icon),value.iconCSS&&$icon.find("img, svg").css(value.iconCSS)):$tpl.find("a .icon").remove(),label||$tpl.find("a label").remove(),$tpl.find("a label").text(label),$modalTabsUL.append($tpl);return $modalTabsUL.find("li:first-child").addClass("active"),$modalTitle.append($modalTabs)}throw"Invalid title type."},SweetModal.prototype._constructContent=function($overlay,$modal){var $modalContent,$tpl,key,m,ref,value;if("string"==typeof this.params.content){if((m=this.params.content.match(/^\S+youtu\.?be\S+(?:v=|\/v\/)(\w+)$/))&&(this.params.content='<iframe width="100%" height="400" src="https://www.youtube.com/embed/'+m[1]+'" frameborder="0" allowfullscreen></iframe>'),""!==this.params.icon)switch($overlay.addClass("sweet-modal-has-icon"),this.params.icon){case $.sweetModal.ICON_SUCCESS:this.params.content=templates.icons.success+this.params.content;break;case $.sweetModal.ICON_ERROR:this.params.content=templates.icons.error+this.params.content;break;case $.sweetModal.ICON_WARNING:this.params.content=templates.icons.warning+this.params.content}$modal.find(".sweet-modal-content").html(this.params.content)}else{$modalContent=$(templates.tabs.content),ref=this.params.content;for(key in ref)value=ref[key],$tpl=$(templates.prepare(templates.tabs.tab,{TAB_ID:key})),$tpl.append(value),$modalContent.append($tpl);$modalContent.find(".sweet-modal-tab:not(:first-child)").hide(),$modal.find(".sweet-modal-content").html($modalContent)}return $modal.addClass(this.params.classes.join(" ")),$overlay.append($modal)},SweetModal.prototype.tojQueryObject=function(){var $modal,$overlay;return this.$overlay?this.$overlay:($overlay=$(templates.overlay).addClass(this.params.theme?this.params.theme.join(" "):$.sweetModal.THEME_LIGHT.join(" ")),$modal=$(templates.modal),this.params.showCloseButton||$modal.find(".sweet-modal-close").remove(),"auto"!==this.params.width&&$modal.css({width:this.params.width,left:"50%",transform:"translateX(-50%)"}),this._constructButtons($modal),this._constructTitle($overlay,$modal),this._constructContent($overlay,$modal),this.$overlay=$overlay,$overlay)},SweetModal.prototype.open=function(){var $icon,$overlay,scope;if(scope=this,$overlay=this.tojQueryObject(),$("body").append(this.$overlay),$overlay.click(function(_this){return function(e){return void 0!==e.target.hasClass&&e.target.hasClass("sweet-modal-clickable")?void 0:_this.params.blocking?_this.bounce():_this.close()}}(this)).delay(100).queue(function(){return $(this).addClass("open"),scope.params.onOpen(scope.tojQueryObject())}),$overlay.find(".sweet-modal-box").click(function(e){return void 0!==e.target.hasClass&&e.target.hasClass("sweet-modal-clickable")?void 0:e.stopPropagation()}),$overlay.find(".sweet-modal-icon").length>0)switch($icon=$overlay.find(".sweet-modal-icon"),this.params.icon){case $.sweetModal.ICON_SUCCESS:$icon.delay(80).queue(function(){return $icon.addClass("animate"),$icon.find(".sweet-modal-tip").addClass("animateSuccessTip"),$icon.find(".sweet-modal-long").addClass("animateSuccessLong")});break;case $.sweetModal.ICON_WARNING:$icon.addClass("pulseWarning"),$icon.find(".sweet-modal-body, .sweet-modal-dot").addClass("pulseWarningIns");break;case $.sweetModal.ICON_ERROR:$icon.delay(240).queue(function(){return $icon.addClass("animateErrorIcon"),$icon.find(".sweet-modal-x-mark").addClass("animateXMark")})}return this.params.timeout&&setTimeout(function(_this){return function(){return _this.close()}}(this),this.params.timeout),this.resize(),this.appendListeners(),this},SweetModal.prototype.bounce=function(){var $overlay;return $overlay=this.tojQueryObject(),$overlay.addClass("bounce"),setTimeout(function(){return $overlay.removeClass("bounce")},300)},SweetModal.prototype.resize=function(){var $modalBox,$overlay,mobileView;return $overlay=this.tojQueryObject(),$modalBox=$overlay.find(".sweet-modal-box"),mobileView=window.matchMedia("screen and (max-width: 914px)").matches,mobileView?$modalBox.removeAttr("style"):($(window).resize(function(){return $modalBox.height()>$(window).height()?$modalBox.css({top:"0",marginTop:"96px"}):$modalBox.css({top:"50%",marginTop:-$modalBox.height()/2-6})}),$(window).trigger("resize")),this},SweetModal.prototype.appendListeners=function(){var $overlay;return $overlay=this.tojQueryObject(),$overlay.find(".sweet-modal-close-link").off("click").click(function(_this){return function(){return _this.close()}}(this)),$overlay.find(".sweet-modal-tabs-links a").off("click").click(function(e){var $innerOverlay,tabHref;return e.preventDefault(),tabHref=$(this).attr("href").replace("#",""),$innerOverlay=$(this).closest(".sweet-modal-overlay"),$innerOverlay.find(".sweet-modal-tabs-links li").removeClass("active").find("a[href='#"+tabHref+"']").closest("li").addClass("active"),$innerOverlay.find(".sweet-modal-tabs-content .sweet-modal-tab").hide().filter("[data-tab="+tabHref+"]").show()}),this},SweetModal.prototype.close=function(){var $overlay,modal;return $overlay=this.tojQueryObject(),$.sweetModal.storage.openModals=function(){var i,len,ref,results;for(ref=$.sweetModal.storage.openModals,results=[],i=0,len=ref.length;len>i;i++)modal=ref[i],modal.getParams()!==this.getParams()&&results.push(modal);return results}.call(this),$overlay.removeClass("open"),this.params.onClose(),setTimeout(function(_this){return function(){return $overlay.remove()}}(this),300),this},SweetModal}(),module.exports=SweetModal},{"./helpers.coffee":2,"./templates.coffee":4}],2:[function(require,module,exports){module.exports={isMobile:function(){return window.matchMedia("screen and (max-width: 420px)").matches},validate:function(params){var isInvalidTabs;if(isInvalidTabs="object"==typeof params.title&&!1||"object"==typeof params.content&&!1,isInvalidTabs&&params.content.length!==params.title.length)throw"Title and Content count did not match.";return!0},objectSize:function(obj){return Object.keys(obj).length}}},{}],3:[function(require,module,exports){!function($){var SweetModal,helpers,templates;return SweetModal=require("./SweetModal.class.coffee"),helpers=require("./helpers.coffee"),templates=require("./templates.coffee"),$.sweetModal=function(props,message){var callbacks,modal,params;return"string"==typeof props&&(props=void 0===message?{content:props}:{title:props,content:message}),(!props.title||props.icon&&props.title)&&(props.type=$.sweetModal.TYPE_ALERT,props.classes=props.classes||["alert"]),params=$.extend({},$.sweetModal.defaultSettings,props),params.content.length<1&&(params.content=params.message),"function"==typeof params.onDisplay&&(params.onOpen=params.onDisplay),callbacks={onOpen:params.onOpen,onClose:params.onClose},params.onOpen=function($overlay){return $.sweetModal.defaultCallbacks.onOpen(),"function"==typeof callbacks.onOpen?callbacks.onOpen($overlay):void 0},params.onClose=function(){return $.sweetModal.defaultCallbacks.onClose(),"function"==typeof callbacks.onClose?callbacks.onClose():void 0},helpers.validate(params),modal=new SweetModal(params),modal.open(),$.sweetModal.storage.openModals.push(modal),modal},$.sweetModal.confirm=function(arg1,arg2,arg3,arg4){var content,errorCallback,successCallback,title;if(title="","string"!=typeof arg1||"function"!=typeof arg2&&void 0!==arg2&&null!==arg2){if("string"!=typeof arg1||"string"!=typeof arg2||"function"!=typeof arg3&&void 0!==arg3&&null!==arg3)throw"Invalid argument configuration.";title=arg1,content=arg2,successCallback=arg3||function(){},errorCallback=arg4||function(){}}else content=arg1,successCallback=arg2||function(){},errorCallback=arg3||function(){};return $.sweetModal({title:title,content:content,buttons:{cancel:{label:$.sweetModal.defaultSettings.confirm.cancel.label,action:errorCallback,classes:$.sweetModal.defaultSettings.confirm.cancel.classes},ok:{label:$.sweetModal.defaultSettings.confirm.yes.label,action:successCallback,classes:$.sweetModal.defaultSettings.confirm.yes.classes}},classes:["alert","confirm"],showCloseButton:!1,blocking:!0})},$.sweetModal.prompt=function(title,placeholder,value,successCallback,errorCallback){var buttons,content;return null==placeholder&&(placeholder=""),null==value&&(value=""),null==successCallback&&(successCallback=null),null==errorCallback&&(errorCallback=null),content=$(templates.prepare(templates.prompt,{TYPE:"text",PLACEHOLDER:placeholder,VALUE:value})),buttons={},successCallback=successCallback||function(){},errorCallback=errorCallback||function(){},$.sweetModal({title:title,content:content.wrap("<div />").parent().html(),buttons:{cancel:{label:$.sweetModal.defaultSettings.confirm.cancel.label,action:errorCallback,classes:$.sweetModal.defaultSettings.confirm.cancel.classes},ok:{label:$.sweetModal.defaultSettings.confirm.ok.label,classes:$.sweetModal.defaultSettings.confirm.ok.classes,action:function(){return successCallback($(".sweet-modal-prompt input").val())}}},classes:["prompt"],showCloseButton:!1,blocking:!0,onOpen:function($overlay){return $overlay.find("input").focus()}})},$.sweetModal.allModalsClosed=function(){return 0===$.sweetModal.storage.openModals.length},$.sweetModal.defaultSettings={title:"",message:"",content:"",icon:"",classes:[],showCloseButton:!0,blocking:!1,timeout:null,theme:$.sweetModal.THEME_LIGHT,type:$.sweetModal.TYPE_MODAL,width:"auto",buttons:{},confirm:{yes:{label:"Yes",classes:"greenB"},ok:{label:"OK",classes:"greenB"},cancel:{label:"Cancel",classes:"redB"}},onOpen:null,onClose:null},$.sweetModal.defaultCallbacks={onOpen:function(){return $("body").css({overflow:"hidden"}),$("#content_wrap").addClass("blurred")},onClose:function(){return $.sweetModal.allModalsClosed()?($("body").css({overflow:"auto"}),$("#content_wrap").removeClass("blurred")):void 0}},$.sweetModal.storage={openModals:[]},"function"!=typeof $.confirm&&($.confirm=$.sweetModal,$.confirm.close=$.sweetModal.closeAll),$.sweetModal.mapNativeFunctions=function(){return window.alert=function(message){return $.sweetModal(message)}},$.sweetModal.THEME_COMPONENTS={LIGHT_OVERLAY:"light-overlay",LIGHT_MODAL:"light-modal",DARK_OVERLAY:"dark-overlay",DARK_MODAL:"dark-modal"},$.sweetModal.THEME_LIGHT=[$.sweetModal.THEME_COMPONENTS.LIGHT_OVERLAY,$.sweetModal.THEME_COMPONENTS.LIGHT_MODAL],$.sweetModal.THEME_DARK=[$.sweetModal.THEME_COMPONENTS.DARK_OVERLAY,$.sweetModal.THEME_COMPONENTS.DARK_MODAL],$.sweetModal.THEME_MIXED=[$.sweetModal.THEME_COMPONENTS.DARK_OVERLAY,$.sweetModal.THEME_COMPONENTS.LIGHT_MODAL],$.sweetModal.TYPE_ALERT="alert",$.sweetModal.TYPE_MODAL="modal",$.sweetModal.ICON_SUCCESS="success",$.sweetModal.ICON_ERROR="error",$.sweetModal.ICON_WARNING="warning"}(jQuery)},{"./SweetModal.class.coffee":1,"./helpers.coffee":2,"./templates.coffee":4}],4:[function(require,module,exports){module.exports={overlay:'<div class="sweet-modal-overlay">\n</div>',modal:'<div class="sweet-modal-box">\n	<div class="sweet-modal-close"><a href="javascript:void(0);" class="sweet-modal-close-link"></a></div>\n	<div class="sweet-modal-title-wrap">\n		<div class="sweet-modal-title"><h2></h2></div>\n	</div>\n	\n	<div class="sweet-modal-content">\n	</div>\n</div>',buttons:'<div class="sweet-modal-buttons"></div>',tabs:{links:'<div class="sweet-modal-tabs-links">\n	<ul>\n	</ul>\n</div>',content:'<div class="sweet-modal-tabs-content">\n</div>',link:'<li>\n	<a href="#modal-{TAB_ID}">\n		<span class="icon"></span>\n		<label></label>\n	</a>\n</li>',tab:'<div class="sweet-modal-tab" data-tab="modal-{TAB_ID}">\n</div>'},icons:{error:'<div class="sweet-modal-icon sweet-modal-error">\n	<span class="sweet-modal-x-mark">\n		<span class="sweet-modal-line sweet-modal-left"></span>\n		<span class="sweet-modal-line sweet-modal-right"></span>\n	</span>\n</div>',warning:'<div class="sweet-modal-icon sweet-modal-warning">\n	<span class="sweet-modal-body"></span>\n	<span class="sweet-modal-dot"></span>\n</div>',info:'<div class="sweet-modal-icon sweet-modal-info"></div>',success:'<div class="sweet-modal-icon sweet-modal-success">\n	<span class="sweet-modal-line sweet-modal-tip"></span>\n	<span class="sweet-modal-line sweet-modal-long"></span>\n	<div class="sweet-modal-placeholder"></div>\n	<div class="sweet-modal-fix"></div>\n</div>'},prompt:'<div class="sweet-modal-prompt">\n	<input type="{TYPE}" placeholder="{PLACEHOLDER}" value="{VALUE}" />\n</div>',prepare:function(tpl,strings){var i,len,lookup,m,matches,replacement;for(matches=tpl.match(/\{([A-Z0-9_\-]+)\}/g)||[],i=0,len=matches.length;len>i;i++)m=matches[i],lookup=m.replace(/\{|\}/g,""),replacement=strings[lookup],void 0===replacement&&(replacement="{"+lookup+"}"),tpl=tpl.replace(new RegExp(m,"g"),replacement);return tpl}}},{}]},{},[3]);