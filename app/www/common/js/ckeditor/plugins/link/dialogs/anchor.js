﻿CKEDITOR.dialog.add("anchor",function(e){function n(e,t){return e.createFakeElement(t,"cke_anchor","anchor")}var t=function(e){this._.selectedElement=e;var t=e.data("cke-saved-name");this.setValueOf("info","txtName",t||"")};return{title:e.lang.link.anchor.title,minWidth:300,minHeight:60,onOk:function(){var t=this;var r=t.getValueOf("info","txtName"),i={name:r,"data-cke-saved-name":r};if(t._.selectedElement){if(t._.selectedElement.data("cke-realelement")){var s=n(e,e.document.createElement("a",{attributes:i}));s.replace(t._.selectedElement)}else t._.selectedElement.setAttributes(i)}else{var o=e.getSelection(),u=o&&o.getRanges()[0];if(u.collapsed){if(CKEDITOR.plugins.link.synAnchorSelector)i["class"]="cke_anchor_empty";if(CKEDITOR.plugins.link.emptyAnchorFix){i.contenteditable="false";i["data-cke-editable"]=1}var f=e.document.createElement("a",{attributes:i});if(CKEDITOR.plugins.link.fakeAnchor)f=n(e,f);u.insertNode(f)}else{if(CKEDITOR.env.ie&&CKEDITOR.env.version<9)i["class"]="cke_anchor";var l=new CKEDITOR.style({element:"a",attributes:i});l.type=CKEDITOR.STYLE_INLINE;l.apply(e.document)}}},onHide:function(){delete this._.selectedElement},onShow:function(){var n=this;var r=e.getSelection(),i=r.getSelectedElement(),s;if(i){if(CKEDITOR.plugins.link.fakeAnchor){var o=CKEDITOR.plugins.link.tryRestoreFakeAnchor(e,i);o&&t.call(n,o);n._.selectedElement=i}else if(i.is("a")&&i.hasAttribute("name"))t.call(n,i)}else{s=CKEDITOR.plugins.link.getSelectedLink(e);if(s){t.call(n,s);r.selectElement(s)}}n.getContentElement("info","txtName").focus()},contents:[{id:"info",label:e.lang.link.anchor.title,accessKey:"I",elements:[{type:"text",id:"txtName",label:e.lang.link.anchor.name,required:true,validate:function(){if(!this.getValue()){alert(e.lang.link.anchor.errorName);return false}return true}}]}]}})