(self.webpackChunkvimeography_pro_admin=self.webpackChunkvimeography_pro_admin||[]).push([[137],{3137:(e,t,a)=>{"use strict";a.r(t),a.d(t,{default:()=>m});var l=a(7271),n=function(e){var t=e.children;return l.createElement("div",{className:"vm-mb-4"},t)},r=function(e){var t=e.children;return l.createElement("div",{className:"vm-font-semibold vm-text-gray-700 vm-block vm-mb-1"},t)};const m=function(e){var t=e.galleryCtx,a=function(e){t.dispatch({type:"EDIT_GALLERY_STATE",payload:e})};return l.createElement(l.Fragment,null,l.createElement("div",{className:"vm-p-4"},l.createElement(n,null,l.createElement(r,null,"Sort videos by"),l.createElement("select",{value:t.state.sort,onChange:function(e){return a({sort:e.target.value})}},l.createElement("option",{value:"date"},"date added"),l.createElement("option",{value:"likes"},"number of likes"),l.createElement("option",{value:"comments"},"number of comments"),l.createElement("option",{value:"plays"},"number of plays"),l.createElement("option",{value:"alphabetical"},"alphabetically"),l.createElement("option",{value:"duration"},"duration"),l.createElement("option",{value:"default"},"use my sort from Vimeo"))),"default"!==t.state.sort&&l.createElement(n,null,l.createElement("label",{className:"vm-flex vm-items-start"},l.createElement("input",{type:"checkbox",checked:"asc"===t.state.direction,className:"vm-transform vm-translate-y-1",onChange:function(e){return a({direction:e.target.checked?"asc":"desc"})}}),l.createElement("div",null,l.createElement(r,null,"Reverse sort order")))),l.createElement(n,null,l.createElement(r,null,"Videos per page"),l.createElement("input",{type:"number",value:t.state.videos_per_page,onChange:function(e){return a({videos_per_page:parseInt(e.target.value)})},className:"vm-mb-2"}),l.createElement("p",{className:"vm-text-xs vm-text-gray-400"},"Sets the number of videos that should show up on each gallery page. Max is 100 videos per page.")),l.createElement(n,null,l.createElement("label",{className:"vm-flex vm-items-start"},l.createElement("input",{type:"checkbox",checked:t.state.enable_search,className:"vm-transform vm-translate-y-1",onChange:function(e){return a({enable_search:e.target.checked})}}),l.createElement("div",null,l.createElement(r,null,"Enable searching"),l.createElement("p",{className:"vm-text-xs vm-text-gray-400"},"When checked, allows your viewers to search this gallery for a specific video by its title or description.")))),l.createElement(n,null,l.createElement("label",{className:"vm-flex vm-items-start"},l.createElement("input",{type:"checkbox",checked:t.state.enable_playlist,className:"vm-transform vm-translate-y-1",onChange:function(e){return a({enable_playlist:e.target.checked})}}),l.createElement("div",null,l.createElement(r,null,"Enable auto-advance"),l.createElement("p",{className:"vm-text-xs vm-text-gray-400"},"When checked, viewers will automatically be shown the next queued video in your gallery as soon as they complete a video.")))),l.createElement(n,null,l.createElement("label",{className:"vm-flex vm-items-start"},l.createElement("input",{type:"checkbox",checked:t.state.allow_downloads,className:"vm-transform vm-translate-y-1",onChange:function(e){return a({allow_downloads:e.target.checked})}}),l.createElement("div",null,l.createElement(r,null,"Show download links"),l.createElement("p",{className:"vm-text-xs vm-text-gray-400 vm-mb-2"},"You can only allow downloads if the videos are coming from your own Vimeo account and you are a Vimeo Pro member."),l.createElement("p",{className:"vm-text-xs vm-text-gray-400"},"Download links expire after 3 hours, so make sure to set your video refresh settings to every hour to prevent serving expired download links to your viewers."))))))}}}]);
//# sourceMappingURL=137.chunk.86d1d89da181e4d57609.js.map