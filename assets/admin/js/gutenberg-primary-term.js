!function(e){var t={};function n(r){if(t[r])return t[r].exports;var o=t[r]={i:r,l:!1,exports:{}};return e[r].call(o.exports,o,o.exports,n),o.l=!0,o.exports}n.m=e,n.c=t,n.d=function(e,t,r){n.o(e,t)||Object.defineProperty(e,t,{enumerable:!0,get:r})},n.r=function(e){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},n.t=function(e,t){if(1&t&&(e=n(e)),8&t)return e;if(4&t&&"object"==typeof e&&e&&e.__esModule)return e;var r=Object.create(null);if(n.r(r),Object.defineProperty(r,"default",{enumerable:!0,value:e}),2&t&&"string"!=typeof e)for(var o in e)n.d(r,o,function(t){return e[t]}.bind(null,o));return r},n.n=function(e){var t=e&&e.__esModule?function(){return e.default}:function(){return e};return n.d(t,"a",t),t},n.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},n.p="",n(n.s=83)}({0:function(e,t){e.exports=wp.i18n},1:function(e,t){e.exports=lodash},11:function(e,t){e.exports=wp.url},16:function(e,t){e.exports=wp.apiFetch},2:function(e,t){e.exports=jQuery},3:function(e,t){e.exports=wp.components},4:function(e,t){e.exports=wp.data},5:function(e,t){e.exports=wp.element},52:function(e,t,n){"use strict";var r=n(1),o=n(0),i=n(16),a=n.n(i),c=n(11),s=n(5),u=n(4),l=n(3);function f(e){return(f="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(e){return typeof e}:function(e){return e&&"function"==typeof Symbol&&e.constructor===Symbol&&e!==Symbol.prototype?"symbol":typeof e})(e)}function p(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}function m(e,t){for(var n=0;n<t.length;n++){var r=t[n];r.enumerable=r.enumerable||!1,r.configurable=!0,"value"in r&&(r.writable=!0),Object.defineProperty(e,r.key,r)}}function y(e,t){return(y=Object.setPrototypeOf||function(e,t){return e.__proto__=t,e})(e,t)}function d(e){var t=function(){if("undefined"==typeof Reflect||!Reflect.construct)return!1;if(Reflect.construct.sham)return!1;if("function"==typeof Proxy)return!0;try{return Boolean.prototype.valueOf.call(Reflect.construct(Boolean,[],(function(){}))),!0}catch(e){return!1}}();return function(){var n,r=v(e);if(t){var o=v(this).constructor;n=Reflect.construct(r,arguments,o)}else n=r.apply(this,arguments);return h(this,n)}}function h(e,t){return!t||"object"!==f(t)&&"function"!=typeof t?b(e):t}function b(e){if(void 0===e)throw new ReferenceError("this hasn't been initialised - super() hasn't been called");return e}function v(e){return(v=Object.setPrototypeOf?Object.getPrototypeOf:function(e){return e.__proto__||Object.getPrototypeOf(e)})(e)}var T=function(e){!function(e,t){if("function"!=typeof t&&null!==t)throw new TypeError("Super expression must either be null or a function");e.prototype=Object.create(t&&t.prototype,{constructor:{value:e,writable:!0,configurable:!0}}),t&&y(e,t)}(f,e);var t,n,i,s=d(f);function f(){var e;return p(this,f),(e=s.apply(this,arguments)).onChange=e.onChange.bind(b(e)),e.state={loading:!0,availableTerms:[],selectedTerms:[]},e}return t=f,(n=[{key:"componentDidMount",value:function(){this.fetchTerms()}},{key:"componentWillUnmount",value:function(){Object(r.invoke)(this.fetchRequest,["abort"])}},{key:"componentDidUpdate",value:function(e,t){if(e.selectedTermIds.length<this.props.selectedTermIds.length){var n=Object(r.difference)(this.props.selectedTermIds,e.selectedTermIds)[0];if(!this.termIsAvailable(n))return void this.fetchTerms()}e.selectedTermIds!==this.props.selectedTermIds&&this.updateSelectedTerms(this.state.availableTerms,this.props.selectedTermIds),t.selectedTerms!==this.state.selectedTerms&&this.handleSelectedTermsChange()}},{key:"termIsAvailable",value:function(e){return!!this.state.availableTerms.find((function(t){return t.id===e}))}},{key:"updateSelectedTerms",value:function(e,t){this.setState({selectedTerms:this.filterSelectedTerms(e,t)})}},{key:"handleSelectedTermsChange",value:function(){var e=this.state.selectedTerms,t=this.props.primaryTermID;e.find((function(e){return e.id===t}))||this.onChange(e.length?e[0].id:"")}},{key:"fetchTerms",value:function(){var e=this,t=this.props.taxonomy;t&&(this.fetchRequest=a()({path:Object(c.addQueryArgs)("/wp/v2/".concat(t.rest_base),{per_page:-1,orderby:"count",order:"desc",_fields:"id,name"})}),this.fetchRequest.then((function(t){e.fetchRequest=null,e.setState({loading:!1,availableTerms:t,selectedTerms:e.filterSelectedTerms(t,e.props.selectedTermIds)})}),(function(t){"abort"!==t.statusText&&(e.fetchRequest=null,e.setState({loading:!1}))})))}},{key:"filterSelectedTerms",value:function(e,t){return e.filter((function(e){return t.includes(e.id)}))}},{key:"onChange",value:function(e){Object(u.dispatch)("rank-math").updatePrimaryTermID(e,this.props.taxonomy.slug)}},{key:"shouldComponentUpdate",value:function(e,t){return this.props.selectedTermIds!==e.selectedTermIds||this.props.primaryTermID!==e.primaryTermID||this.state.selectedTerms!==t.selectedTerms}},{key:"render",value:function(){return this.state.selectedTerms.length<2?null:this.state.loading?[wp.element.createElement(l.Spinner,{key:"spinner"}),wp.element.createElement("p",{key:"spinner-text"},"Loading")]:wp.element.createElement(l.SelectControl,{label:Object(o.__)("Select Primary Term","rank-math"),value:this.props.primaryTermID,options:this.state.selectedTerms.map((function(e){return{value:e.id,label:Object(r.unescape)(e.name)}})),onChange:this.onChange})}}])&&m(t.prototype,n),i&&m(t,i),f}(s.Component);t.a=T},6:function(e,t){e.exports=wp.hooks},83:function(e,t,n){"use strict";n.r(t);var r=n(2),o=n.n(r),i=n(6),a=n(5),c=n(3),s=n(4);function u(e){return(u="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(e){return typeof e}:function(e){return e&&"function"==typeof Symbol&&e.constructor===Symbol&&e!==Symbol.prototype?"symbol":typeof e})(e)}function l(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}function f(e,t){for(var n=0;n<t.length;n++){var r=t[n];r.enumerable=r.enumerable||!1,r.configurable=!0,"value"in r&&(r.writable=!0),Object.defineProperty(e,r.key,r)}}function p(e,t){return(p=Object.setPrototypeOf||function(e,t){return e.__proto__=t,e})(e,t)}function m(e){var t=function(){if("undefined"==typeof Reflect||!Reflect.construct)return!1;if(Reflect.construct.sham)return!1;if("function"==typeof Proxy)return!0;try{return Boolean.prototype.valueOf.call(Reflect.construct(Boolean,[],(function(){}))),!0}catch(e){return!1}}();return function(){var n,r=d(e);if(t){var o=d(this).constructor;n=Reflect.construct(r,arguments,o)}else n=r.apply(this,arguments);return y(this,n)}}function y(e,t){return!t||"object"!==u(t)&&"function"!=typeof t?function(e){if(void 0===e)throw new ReferenceError("this hasn't been initialised - super() hasn't been called");return e}(e):t}function d(e){return(d=Object.setPrototypeOf?Object.getPrototypeOf:function(e){return e.__proto__||Object.getPrototypeOf(e)})(e)}var h=function(e){!function(e,t){if("function"!=typeof t&&null!==t)throw new TypeError("Super expression must either be null or a function");e.prototype=Object.create(t&&t.prototype,{constructor:{value:e,writable:!0,configurable:!0}}),t&&p(e,t)}(a,e);var t,n,r,i=m(a);function a(){return l(this,a),i.apply(this,arguments)}return t=a,(n=[{key:"handleSelectedTermsChange",value:function(){var e=this.state.selectedTerms,t=parseInt(this.props.primaryTermID);e.find((function(e){return e.id===t}))||this.onChange(e.length?e[0].id:"")}},{key:"onChange",value:function(e){e=parseInt(e),rankMath.assessor.serpData.primaryTerm=e,o()("#rank_math_primary_"+this.props.taxonomy.slug).val(e),Object(s.dispatch)("core/editor").editPost({meta:{refreshMe:"refreshUI"}})}}])&&f(t.prototype,n),r&&f(t,r),a}(n(52).a),b=Object(s.withSelect)((function(e,t){var n=t.slug,r=e("core/editor").getEditedPostAttribute,i=r("meta"),a=e("core").getTaxonomy(n);return{taxonomy:a,meta:i,selectedTermIds:a?r(a.rest_base):[],primaryTermID:o()("#rank_math_primary_"+a.slug).val()}}))(h),v=function(e){var t=e.TermComponent;return function(e){return!1!==rankMath.assessor.primaryTaxonomy&&e.slug===rankMath.assessor.primaryTaxonomy.name}(e)?wp.element.createElement(a.Fragment,null,wp.element.createElement(t,e),wp.element.createElement(c.PanelRow,{className:"rank-math-primary-term-picker"},wp.element.createElement(b,e))):wp.element.createElement(t,e)};function T(){return(T=Object.assign||function(e){for(var t=1;t<arguments.length;t++){var n=arguments[t];for(var r in n)Object.prototype.hasOwnProperty.call(n,r)&&(e[r]=n[r])}return e}).apply(this,arguments)}var g=function e(){!function(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}(this,e),Object(i.addFilter)("editor.PostTaxonomyType","rank-math",(function(e){return function(t){return wp.element.createElement(v,T({TermComponent:e},t))}}))};o()(document).ready((function(){window.rankMathEditor=new g}))}});