(self.webpackChunkvimeography_pro_admin=self.webpackChunkvimeography_pro_admin||[]).push([[374],{5360:(n,A,e)=>{"use strict";e.d(A,{Z:()=>i});var t=e(4015),o=e.n(t),r=e(3645),a=e.n(r)()(o());a.push([n.id,"/*! modern-normalize v1.0.0 | MIT License | https://github.com/sindresorhus/modern-normalize */\n\n/*\nDocument\n========\n*/\n\n/**\nUse a better box model (opinionated).\n*/\n\n*,\n*::before,\n*::after {\n  box-sizing: border-box;\n}\n\n/**\nUse a more readable tab size (opinionated).\n*/\n\n:root {\n  -moz-tab-size: 4;\n  tab-size: 4;\n}\n\n/**\n1. Correct the line height in all browsers.\n2. Prevent adjustments of font size after orientation changes in iOS.\n*/\n\nhtml {\n  line-height: 1.15; /* 1 */\n  -webkit-text-size-adjust: 100%; /* 2 */\n}\n\n/*\nSections\n========\n*/\n\n/**\nRemove the margin in all browsers.\n*/\n\nbody {\n  margin: 0;\n}\n\n/**\nImprove consistency of default fonts in all browsers. (https://github.com/sindresorhus/modern-normalize/issues/3)\n*/\n\nbody {\n  font-family:\n\t\tsystem-ui,\n\t\t-apple-system, /* Firefox supports this but not yet `system-ui` */\n\t\t'Segoe UI',\n\t\tRoboto,\n\t\tHelvetica,\n\t\tArial,\n\t\tsans-serif,\n\t\t'Apple Color Emoji',\n\t\t'Segoe UI Emoji';\n}\n\n/*\nGrouping content\n================\n*/\n\n/**\n1. Add the correct height in Firefox.\n2. Correct the inheritance of border color in Firefox. (https://bugzilla.mozilla.org/show_bug.cgi?id=190655)\n*/\n\nhr {\n  height: 0; /* 1 */\n  color: inherit; /* 2 */\n}\n\n/*\nText-level semantics\n====================\n*/\n\n/**\nAdd the correct text decoration in Chrome, Edge, and Safari.\n*/\n\nabbr[title] {\n  -webkit-text-decoration: underline dotted;\n          text-decoration: underline dotted;\n}\n\n/**\nAdd the correct font weight in Edge and Safari.\n*/\n\nb,\nstrong {\n  font-weight: bolder;\n}\n\n/**\n1. Improve consistency of default fonts in all browsers. (https://github.com/sindresorhus/modern-normalize/issues/3)\n2. Correct the odd 'em' font sizing in all browsers.\n*/\n\ncode,\nkbd,\nsamp,\npre {\n  font-family:\n\t\tui-monospace,\n\t\tSFMono-Regular,\n\t\tConsolas,\n\t\t'Liberation Mono',\n\t\tMenlo,\n\t\tmonospace; /* 1 */\n  font-size: 1em; /* 2 */\n}\n\n/**\nAdd the correct font size in all browsers.\n*/\n\nsmall {\n  font-size: 80%;\n}\n\n/**\nPrevent 'sub' and 'sup' elements from affecting the line height in all browsers.\n*/\n\nsub,\nsup {\n  font-size: 75%;\n  line-height: 0;\n  position: relative;\n  vertical-align: baseline;\n}\n\nsub {\n  bottom: -0.25em;\n}\n\nsup {\n  top: -0.5em;\n}\n\n/*\nTabular data\n============\n*/\n\n/**\n1. Remove text indentation from table contents in Chrome and Safari. (https://bugs.chromium.org/p/chromium/issues/detail?id=999088, https://bugs.webkit.org/show_bug.cgi?id=201297)\n2. Correct table border color inheritance in all Chrome and Safari. (https://bugs.chromium.org/p/chromium/issues/detail?id=935729, https://bugs.webkit.org/show_bug.cgi?id=195016)\n*/\n\ntable {\n  text-indent: 0; /* 1 */\n  border-color: inherit; /* 2 */\n}\n\n/*\nForms\n=====\n*/\n\n/**\n1. Change the font styles in all browsers.\n2. Remove the margin in Firefox and Safari.\n*/\n\nbutton,\ninput,\noptgroup,\nselect,\ntextarea {\n  font-family: inherit; /* 1 */\n  font-size: 100%; /* 1 */\n  line-height: 1.15; /* 1 */\n  margin: 0; /* 2 */\n}\n\n/**\nRemove the inheritance of text transform in Edge and Firefox.\n1. Remove the inheritance of text transform in Firefox.\n*/\n\nbutton,\nselect { /* 1 */\n  text-transform: none;\n}\n\n/**\nCorrect the inability to style clickable types in iOS and Safari.\n*/\n\nbutton,\n[type='button'],\n[type='submit'] {\n  -webkit-appearance: button;\n}\n\n/**\nRemove the inner border and padding in Firefox.\n*/\n\n/**\nRestore the focus styles unset by the previous rule.\n*/\n\n/**\nRemove the additional ':invalid' styles in Firefox.\nSee: https://github.com/mozilla/gecko-dev/blob/2f9eacd9d3d995c937b4251a5557d95d494c9be1/layout/style/res/forms.css#L728-L737\n*/\n\n/**\nRemove the padding so developers are not caught out when they zero out 'fieldset' elements in all browsers.\n*/\n\nlegend {\n  padding: 0;\n}\n\n/**\nAdd the correct vertical alignment in Chrome and Firefox.\n*/\n\nprogress {\n  vertical-align: baseline;\n}\n\n/**\nCorrect the cursor style of increment and decrement buttons in Safari.\n*/\n\n/**\n1. Correct the odd appearance in Chrome and Safari.\n2. Correct the outline style in Safari.\n*/\n\n[type='search'] {\n  -webkit-appearance: textfield; /* 1 */\n  outline-offset: -2px; /* 2 */\n}\n\n/**\nRemove the inner padding in Chrome and Safari on macOS.\n*/\n\n/**\n1. Correct the inability to style clickable types in iOS and Safari.\n2. Change font properties to 'inherit' in Safari.\n*/\n\n/*\nInteractive\n===========\n*/\n\n/*\nAdd the correct display in Chrome and Safari.\n*/\n\nsummary {\n  display: list-item;\n}\n\n/**\n * Manually forked from SUIT CSS Base: https://github.com/suitcss/base\n * A thin layer on top of normalize.css that provides a starting point more\n * suitable for web applications.\n */\n\n/**\n * Removes the default spacing and border for appropriate elements.\n */\n\nblockquote,\ndl,\ndd,\nh1,\nh2,\nh3,\nh4,\nh5,\nh6,\nhr,\nfigure,\np,\npre {\n  margin: 0;\n}\n\nbutton {\n  background-color: transparent;\n  background-image: none;\n}\n\n/**\n * Work around a Firefox/IE bug where the transparent `button` background\n * results in a loss of the default `button` focus styles.\n */\n\nbutton:focus {\n  outline: 1px dotted;\n  outline: 5px auto -webkit-focus-ring-color;\n}\n\nfieldset {\n  margin: 0;\n  padding: 0;\n}\n\nol,\nul {\n  list-style: none;\n  margin: 0;\n  padding: 0;\n}\n\n/**\n * Tailwind custom reset styles\n */\n\n/**\n * 1. Use the user's configured `sans` font-family (with Tailwind's default\n *    sans-serif font stack as a fallback) as a sane default.\n * 2. Use Tailwind's default \"normal\" line-height so the user isn't forced\n *    to override it to ensure consistency even when using the default theme.\n */\n\nhtml {\n  font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, \"Segoe UI\", Roboto, \"Helvetica Neue\", Arial, \"Noto Sans\", sans-serif, \"Apple Color Emoji\", \"Segoe UI Emoji\", \"Segoe UI Symbol\", \"Noto Color Emoji\"; /* 1 */\n  line-height: 1.5; /* 2 */\n}\n\n/**\n * Inherit font-family and line-height from `html` so users can set them as\n * a class directly on the `html` element.\n */\n\nbody {\n  font-family: inherit;\n  line-height: inherit;\n}\n\n/**\n * 1. Prevent padding and border from affecting element width.\n *\n *    We used to set this in the html element and inherit from\n *    the parent element for everything else. This caused issues\n *    in shadow-dom-enhanced elements like <details> where the content\n *    is wrapped by a div with box-sizing set to `content-box`.\n *\n *    https://github.com/mozdevs/cssremedy/issues/4\n *\n *\n * 2. Allow adding a border to an element by just adding a border-width.\n *\n *    By default, the way the browser specifies that an element should have no\n *    border is by setting it's border-style to `none` in the user-agent\n *    stylesheet.\n *\n *    In order to easily add borders to elements by just setting the `border-width`\n *    property, we change the default border-style for all elements to `solid`, and\n *    use border-width to hide them instead. This way our `border` utilities only\n *    need to set the `border-width` property instead of the entire `border`\n *    shorthand, making our border utilities much more straightforward to compose.\n *\n *    https://github.com/tailwindcss/tailwindcss/pull/116\n */\n\n*,\n::before,\n::after {\n  box-sizing: border-box; /* 1 */\n  border-width: 0; /* 2 */\n  border-style: solid; /* 2 */\n  border-color: #e5e7eb; /* 2 */\n}\n\n/*\n * Ensure horizontal rules are visible by default\n */\n\nhr {\n  border-top-width: 1px;\n}\n\n/**\n * Undo the `border-style: none` reset that Normalize applies to images so that\n * our `border-{width}` utilities have the expected effect.\n *\n * The Normalize reset is unnecessary for us since we default the border-width\n * to 0 on all elements.\n *\n * https://github.com/tailwindcss/tailwindcss/issues/362\n */\n\nimg {\n  border-style: solid;\n}\n\ntextarea {\n  resize: vertical;\n}\n\ninput::placeholder,\ntextarea::placeholder {\n  color: #9ca3af;\n}\n\nbutton {\n  cursor: pointer;\n}\n\ntable {\n  border-collapse: collapse;\n}\n\nh1,\nh2,\nh3,\nh4,\nh5,\nh6 {\n  font-size: inherit;\n  font-weight: inherit;\n}\n\n/**\n * Reset links to optimize for opt-in styling instead of\n * opt-out.\n */\n\na {\n  color: inherit;\n  text-decoration: inherit;\n}\n\n/**\n * Reset form element properties that are easy to forget to\n * style explicitly so you don't inadvertently introduce\n * styles that deviate from your design system. These styles\n * supplement a partial reset that is already applied by\n * normalize.css.\n */\n\nbutton,\ninput,\noptgroup,\nselect,\ntextarea {\n  padding: 0;\n  line-height: inherit;\n  color: inherit;\n}\n\n/**\n * Use the configured 'mono' font family for elements that\n * are expected to be rendered with a monospace font, falling\n * back to the system monospace stack if there is no configured\n * 'mono' font family.\n */\n\npre,\ncode,\nkbd,\nsamp {\n  font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, \"Liberation Mono\", \"Courier New\", monospace;\n}\n\n/**\n * Make replaced elements `display: block` by default as that's\n * the behavior you want almost all of the time. Inspired by\n * CSS Remedy, with `svg` added as well.\n *\n * https://github.com/mozdevs/cssremedy/issues/14\n */\n\nimg,\nsvg,\nvideo,\ncanvas,\naudio,\niframe,\nembed,\nobject {\n  display: block;\n  vertical-align: middle;\n}\n\n/**\n * Constrain images and videos to the parent width and preserve\n * their instrinsic aspect ratio.\n *\n * https://github.com/mozdevs/cssremedy/issues/14\n */\n\nimg,\nvideo {\n  max-width: 100%;\n  height: auto;\n}\n\n.vm-bg-white {\n  --tw-bg-opacity: 1;\n  background-color: rgba(255, 255, 255, var(--tw-bg-opacity));\n}\n\n.vm-bg-blue-500 {\n  --tw-bg-opacity: 1;\n  background-color: rgba(59, 130, 246, var(--tw-bg-opacity));\n}\n\n.hover\\:vm-bg-blue-600:hover {\n  --tw-bg-opacity: 1;\n  background-color: rgba(37, 99, 235, var(--tw-bg-opacity));\n}\n\n.vm-rounded {\n  border-radius: 0.25rem;\n}\n\n.vm-border {\n  border-width: 1px;\n}\n\n.vm-border-b {\n  border-bottom-width: 1px;\n}\n\n.vm-cursor-move {\n  cursor: move;\n}\n\n.vm-block {\n  display: block;\n}\n\n.vm-flex {\n  display: flex;\n}\n\n.vm-grid {\n  display: grid;\n}\n\n.vm-flex-col {\n  flex-direction: column;\n}\n\n.vm-items-start {\n  align-items: flex-start;\n}\n\n.vm-items-center {\n  align-items: center;\n}\n\n.vm-justify-center {\n  justify-content: center;\n}\n\n.vm-flex-1 {\n  flex: 1 1 0%;\n}\n\n.vm-font-mono {\n  font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, \"Liberation Mono\", \"Courier New\", monospace;\n}\n\n.vm-font-semibold {\n  font-weight: 600;\n}\n\n.vm-h-5 {\n  height: 1.25rem;\n}\n\n.vm-text-xs {\n  font-size: 0.75rem;\n  line-height: 1rem;\n}\n\n.vm-text-sm {\n  font-size: 0.875rem;\n  line-height: 1.25rem;\n}\n\n.vm-text-base {\n  font-size: 1rem;\n  line-height: 1.5rem;\n}\n\n.vm-text-lg {\n  font-size: 1.125rem;\n  line-height: 1.75rem;\n}\n\n.vm-text-xl {\n  font-size: 1.25rem;\n  line-height: 1.75rem;\n}\n\n.vm-text-2xl {\n  font-size: 1.5rem;\n  line-height: 2rem;\n}\n\n.vm-my-4 {\n  margin-top: 1rem;\n  margin-bottom: 1rem;\n}\n\n.vm-my-10 {\n  margin-top: 2.5rem;\n  margin-bottom: 2.5rem;\n}\n\n.vm-mb-1 {\n  margin-bottom: 0.25rem;\n}\n\n.vm-mr-2 {\n  margin-right: 0.5rem;\n}\n\n.vm-mb-2 {\n  margin-bottom: 0.5rem;\n}\n\n.vm-mb-4 {\n  margin-bottom: 1rem;\n}\n\n.vm-mb-5 {\n  margin-bottom: 1.25rem;\n}\n\n.vm-mb-10 {\n  margin-bottom: 2.5rem;\n}\n\n.vm-max-w-md {\n  max-width: 28rem;\n}\n\n.vm-p-4 {\n  padding: 1rem;\n}\n\n.vm-py-2 {\n  padding-top: 0.5rem;\n  padding-bottom: 0.5rem;\n}\n\n.vm-px-3 {\n  padding-left: 0.75rem;\n  padding-right: 0.75rem;\n}\n\n* {\n  --tw-shadow: 0 0 #0000;\n}\n\n.vm-shadow-xl {\n  --tw-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);\n  box-shadow: var(--tw-ring-offset-shadow, 0 0 #0000), var(--tw-ring-shadow, 0 0 #0000), var(--tw-shadow);\n}\n\n* {\n  --tw-ring-inset: var(--tw-empty,/*!*/ /*!*/);\n  --tw-ring-offset-width: 0px;\n  --tw-ring-offset-color: #fff;\n  --tw-ring-color: rgba(59, 130, 246, 0.5);\n  --tw-ring-offset-shadow: 0 0 #0000;\n  --tw-ring-shadow: 0 0 #0000;\n}\n\n.vm-text-right {\n  text-align: right;\n}\n\n.vm-text-white {\n  --tw-text-opacity: 1;\n  color: rgba(255, 255, 255, var(--tw-text-opacity));\n}\n\n.vm-text-gray-400 {\n  --tw-text-opacity: 1;\n  color: rgba(156, 163, 175, var(--tw-text-opacity));\n}\n\n.vm-text-gray-600 {\n  --tw-text-opacity: 1;\n  color: rgba(75, 85, 99, var(--tw-text-opacity));\n}\n\n.vm-text-gray-700 {\n  --tw-text-opacity: 1;\n  color: rgba(55, 65, 81, var(--tw-text-opacity));\n}\n\n.vm-text-blue-500 {\n  --tw-text-opacity: 1;\n  color: rgba(59, 130, 246, var(--tw-text-opacity));\n}\n\n.hover\\:vm-text-white:hover {\n  --tw-text-opacity: 1;\n  color: rgba(255, 255, 255, var(--tw-text-opacity));\n}\n\n.vm-no-underline {\n  text-decoration: none;\n}\n\n.vm-w-5 {\n  width: 1.25rem;\n}\n\n.vm-w-72 {\n  width: 18rem;\n}\n\n.vm-w-full {\n  width: 100%;\n}\n\n.vm-z-10 {\n  z-index: 10;\n}\n\n.vm-grid-cols-2 {\n  grid-template-columns: repeat(2, minmax(0, 1fr));\n}\n\n.vm-transform {\n  --tw-translate-x: 0;\n  --tw-translate-y: 0;\n  --tw-rotate: 0;\n  --tw-skew-x: 0;\n  --tw-skew-y: 0;\n  --tw-scale-x: 1;\n  --tw-scale-y: 1;\n  transform: translateX(var(--tw-translate-x)) translateY(var(--tw-translate-y)) rotate(var(--tw-rotate)) skewX(var(--tw-skew-x)) skewY(var(--tw-skew-y)) scaleX(var(--tw-scale-x)) scaleY(var(--tw-scale-y));\n}\n\n.vm-translate-y-1 {\n  --tw-translate-y: 0.25rem;\n}\n\n@keyframes vm-spin {\n  to {\n    transform: rotate(360deg);\n  }\n}\n\n@keyframes vm-ping {\n  75%, 100% {\n    transform: scale(2);\n    opacity: 0;\n  }\n}\n\n@keyframes vm-pulse {\n  50% {\n    opacity: .5;\n  }\n}\n\n@keyframes vm-bounce {\n  0%, 100% {\n    transform: translateY(-25%);\n    animation-timing-function: cubic-bezier(0.8,0,1,1);\n  }\n\n  50% {\n    transform: none;\n    animation-timing-function: cubic-bezier(0,0,0.2,1);\n  }\n}\n\n@media (min-width: 640px) {\n}\n\n@media (min-width: 768px) {\n}\n\n@media (min-width: 1024px) {\n}\n\n@media (min-width: 1280px) {\n}\n\n@media (min-width: 1536px) {\n}\n","",{version:3,sources:["webpack://./node_modules/tailwindcss/tailwind.css","<no source>"],names:[],mappings:"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",sourcesContent:["@tailwind base;\n\n@tailwind components;\n\n@tailwind utilities;\n",null],sourceRoot:""}]);const i=a},3645:n=>{"use strict";n.exports=function(n){var A=[];return A.toString=function(){return this.map((function(A){var e=n(A);return A[2]?"@media ".concat(A[2]," {").concat(e,"}"):e})).join("")},A.i=function(n,e,t){"string"==typeof n&&(n=[[null,n,""]]);var o={};if(t)for(var r=0;r<this.length;r++){var a=this[r][0];null!=a&&(o[a]=!0)}for(var i=0;i<n.length;i++){var s=[].concat(n[i]);t&&o[s[0]]||(e&&(s[2]?s[2]="".concat(e," and ").concat(s[2]):s[2]=e),A.push(s))}},A}},4015:n=>{"use strict";function A(n,A){(null==A||A>n.length)&&(A=n.length);for(var e=0,t=new Array(A);e<A;e++)t[e]=n[e];return t}n.exports=function(n){var e,t,o=(t=4,function(n){if(Array.isArray(n))return n}(e=n)||function(n,A){if("undefined"!=typeof Symbol&&Symbol.iterator in Object(n)){var e=[],t=!0,o=!1,r=void 0;try{for(var a,i=n[Symbol.iterator]();!(t=(a=i.next()).done)&&(e.push(a.value),!A||e.length!==A);t=!0);}catch(n){o=!0,r=n}finally{try{t||null==i.return||i.return()}finally{if(o)throw r}}return e}}(e,t)||function(n,e){if(n){if("string"==typeof n)return A(n,e);var t=Object.prototype.toString.call(n).slice(8,-1);return"Object"===t&&n.constructor&&(t=n.constructor.name),"Map"===t||"Set"===t?Array.from(n):"Arguments"===t||/^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(t)?A(n,e):void 0}}(e,t)||function(){throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.")}()),r=o[1],a=o[3];if("function"==typeof btoa){var i=btoa(unescape(encodeURIComponent(JSON.stringify(a)))),s="sourceMappingURL=data:application/json;charset=utf-8;base64,".concat(i),c="/*# ".concat(s," */"),l=a.sources.map((function(n){return"/*# sourceURL=".concat(a.sourceRoot||"").concat(n," */")}));return[r].concat(l).concat([c]).join("\n")}return[r].join("\n")}},3379:(n,A,e)=>{"use strict";var t,o=function(){var n={};return function(A){if(void 0===n[A]){var e=document.querySelector(A);if(window.HTMLIFrameElement&&e instanceof window.HTMLIFrameElement)try{e=e.contentDocument.head}catch(n){e=null}n[A]=e}return n[A]}}(),r=[];function a(n){for(var A=-1,e=0;e<r.length;e++)if(r[e].identifier===n){A=e;break}return A}function i(n,A){for(var e={},t=[],o=0;o<n.length;o++){var i=n[o],s=A.base?i[0]+A.base:i[0],c=e[s]||0,l="".concat(s," ").concat(c);e[s]=c+1;var m=a(l),d={css:i[1],media:i[2],sourceMap:i[3]};-1!==m?(r[m].references++,r[m].updater(d)):r.push({identifier:l,updater:f(d,A),references:1}),t.push(l)}return t}function s(n){var A=document.createElement("style"),t=n.attributes||{};if(void 0===t.nonce){var r=e.nc;r&&(t.nonce=r)}if(Object.keys(t).forEach((function(n){A.setAttribute(n,t[n])})),"function"==typeof n.insert)n.insert(A);else{var a=o(n.insert||"head");if(!a)throw new Error("Couldn't find a style target. This probably means that the value for the 'insert' parameter is invalid.");a.appendChild(A)}return A}var c,l=(c=[],function(n,A){return c[n]=A,c.filter(Boolean).join("\n")});function m(n,A,e,t){var o=e?"":t.media?"@media ".concat(t.media," {").concat(t.css,"}"):t.css;if(n.styleSheet)n.styleSheet.cssText=l(A,o);else{var r=document.createTextNode(o),a=n.childNodes;a[A]&&n.removeChild(a[A]),a.length?n.insertBefore(r,a[A]):n.appendChild(r)}}function d(n,A,e){var t=e.css,o=e.media,r=e.sourceMap;if(o?n.setAttribute("media",o):n.removeAttribute("media"),r&&"undefined"!=typeof btoa&&(t+="\n/*# sourceMappingURL=data:application/json;base64,".concat(btoa(unescape(encodeURIComponent(JSON.stringify(r))))," */")),n.styleSheet)n.styleSheet.cssText=t;else{for(;n.firstChild;)n.removeChild(n.firstChild);n.appendChild(document.createTextNode(t))}}var u=null,h=0;function f(n,A){var e,t,o;if(A.singleton){var r=h++;e=u||(u=s(A)),t=m.bind(null,e,r,!1),o=m.bind(null,e,r,!0)}else e=s(A),t=d.bind(null,e,A),o=function(){!function(n){if(null===n.parentNode)return!1;n.parentNode.removeChild(n)}(e)};return t(n),function(A){if(A){if(A.css===n.css&&A.media===n.media&&A.sourceMap===n.sourceMap)return;t(n=A)}else o()}}n.exports=function(n,A){(A=A||{}).singleton||"boolean"==typeof A.singleton||(A.singleton=(void 0===t&&(t=Boolean(window&&document&&document.all&&!window.atob)),t));var e=i(n=n||[],A);return function(n){if(n=n||[],"[object Array]"===Object.prototype.toString.call(n)){for(var t=0;t<e.length;t++){var o=a(e[t]);r[o].references--}for(var s=i(n,A),c=0;c<e.length;c++){var l=a(e[c]);0===r[l].references&&(r[l].updater(),r.splice(l,1))}e=s}}}},3374:(n,A,e)=>{"use strict";e.r(A),e.d(A,{default:()=>c});var t=e(3379),o=e.n(t),r=e(5360);o()(r.Z,{insert:"head",singleton:!1}),r.Z.locals;var a=e(7271),i=function(n){var A=n.children;return a.createElement("div",{className:"vm-mb-4"},A)},s=function(n){var A=n.children;return a.createElement("div",{className:"vm-font-semibold vm-text-gray-700 vm-block vm-mb-1"},A)};const c={ProSettings:function(n){var A=n.galleryCtx,e=function(n){A.dispatch({type:"EDIT_GALLERY_STATE",payload:n})};return a.createElement(a.Fragment,null,a.createElement("div",{className:"vm-p-4"},a.createElement(i,null,a.createElement(s,null,"Sort videos by"),a.createElement("select",{value:A.state.sort,onChange:function(n){return e({sort:n.target.value})}},a.createElement("option",{value:"date"},"date added"),a.createElement("option",{value:"likes"},"number of likes"),a.createElement("option",{value:"comments"},"number of comments"),a.createElement("option",{value:"plays"},"number of plays"),a.createElement("option",{value:"alphabetical"},"alphabetically"),a.createElement("option",{value:"duration"},"duration"),a.createElement("option",{value:"default"},"use my sort from Vimeo"))),"default"!==A.state.sort&&a.createElement(i,null,a.createElement("label",{className:"vm-flex vm-items-start"},a.createElement("input",{type:"checkbox",checked:"asc"===A.state.direction,className:"vm-transform vm-translate-y-1",onChange:function(n){return e({direction:n.target.checked?"asc":"desc"})}}),a.createElement("div",null,a.createElement(s,null,"Reverse sort order")))),a.createElement(i,null,a.createElement(s,null,"Videos per page"),a.createElement("input",{type:"number",value:A.state.videos_per_page,onChange:function(n){return e({videos_per_page:parseInt(n.target.value)})},className:"vm-mb-2"}),a.createElement("p",{className:"vm-text-xs vm-text-gray-400"},"Sets the number of videos that should show up on each gallery page. Max is 100 videos per page.")),a.createElement(i,null,a.createElement("label",{className:"vm-flex vm-items-start"},a.createElement("input",{type:"checkbox",checked:A.state.enable_search,className:"vm-transform vm-translate-y-1",onChange:function(n){return e({enable_search:n.target.checked})}}),a.createElement("div",null,a.createElement(s,null,"Enable searching"),a.createElement("p",{className:"vm-text-xs vm-text-gray-400"},"When checked, allows your viewers to search this gallery for a specific video by its title or description.")))),a.createElement(i,null,a.createElement("label",{className:"vm-flex vm-items-start"},a.createElement("input",{type:"checkbox",checked:A.state.enable_playlist,className:"vm-transform vm-translate-y-1",onChange:function(n){return e({enable_playlist:n.target.checked})}}),a.createElement("div",null,a.createElement(s,null,"Enable auto-advance"),a.createElement("p",{className:"vm-text-xs vm-text-gray-400"},"When checked, viewers will automatically be shown the next queued video in your gallery as soon as they complete a video.")))),a.createElement(i,null,a.createElement("label",{className:"vm-flex vm-items-start"},a.createElement("input",{type:"checkbox",checked:A.state.allow_downloads,className:"vm-transform vm-translate-y-1",onChange:function(n){return e({allow_downloads:n.target.checked})}}),a.createElement("div",null,a.createElement(s,null,"Show download links"),a.createElement("p",{className:"vm-text-xs vm-text-gray-400 vm-mb-2"},"You can only allow downloads if the videos are coming from your own Vimeo account and you are a Vimeo Pro member."),a.createElement("p",{className:"vm-text-xs vm-text-gray-400"},"Download links expire after 3 hours, so make sure to set your video refresh settings to every hour to prevent serving expired download links to your viewers."))))))}}}}]);
//# sourceMappingURL=374.chunk.fe6fa82acf8c015b77b3.js.map