import{d as u,D as k,c as l,o as a,F as b,r as f,l as m,b as n,f as _,E as x,t as g,u as r,w as d,a as e,m as v}from"./app-D6QIOD16.js";import{c}from"./createLucideIcon-BE4enEv4.js";import{_ as M,a as I}from"./Layout.vue_vue_type_script_setup_true_lang-z7qbcIL9.js";import{_ as w}from"./AppLayout.vue_vue_type_script_setup_true_lang-DlVRnanS.js";import"./AppLogoIcon.vue_vue_type_script_setup_true_lang-Co38M-7h.js";import"./index-LjeM1nJa.js";import"./x-DZ_Tfwg_.js";/**
 * @license lucide-vue-next v0.468.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const A=c("MonitorIcon",[["rect",{width:"20",height:"14",x:"2",y:"3",rx:"2",key:"48i651"}],["line",{x1:"8",x2:"16",y1:"21",y2:"21",key:"1svkeh"}],["line",{x1:"12",x2:"12",y1:"17",y2:"21",key:"vw1qmm"}]]);/**
 * @license lucide-vue-next v0.468.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const C=c("MoonIcon",[["path",{d:"M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z",key:"a7tn18"}]]);/**
 * @license lucide-vue-next v0.468.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const $=c("SunIcon",[["circle",{cx:"12",cy:"12",r:"4",key:"4exip2"}],["path",{d:"M12 2v2",key:"tus03m"}],["path",{d:"M12 20v2",key:"1lh1kg"}],["path",{d:"m4.93 4.93 1.41 1.41",key:"149t6j"}],["path",{d:"m17.66 17.66 1.41 1.41",key:"ptbguv"}],["path",{d:"M2 12h2",key:"1t8f8n"}],["path",{d:"M20 12h2",key:"1q8mjw"}],["path",{d:"m6.34 17.66-1.41 1.41",key:"1m8zz5"}],["path",{d:"m19.07 4.93-1.41 1.41",key:"1shlcs"}]]),B=["onClick"],S={class:"ml-1.5 text-sm"},z=u({__name:"AppearanceTabs",props:{class:{default:""}},setup(o){const{appearance:t,updateAppearance:p}=k(),i=[{value:"light",Icon:$,label:"Claro"},{value:"dark",Icon:C,label:"Escuro"},{value:"system",Icon:A,label:"Sistema"}];return(E,j)=>(a(),l("div",{class:m(["inline-flex gap-1 rounded-full bg-brand-card2 p-1",o.class])},[(a(),l(b,null,f(i,({value:s,Icon:h,label:y})=>n("button",{key:s,onClick:q=>r(p)(s),class:m(["flex items-center rounded-md px-3.5 py-1.5 transition-colors",r(t)===s?"bg-brand-gold text-brand-ink shadow-sm":"text-brand-muted-2 hover:bg-brand-line hover:text-brand-cream"])},[(a(),_(x(h),{class:"-ml-1 h-4 w-4"})),n("span",S,g(y),1)],10,B)),64))],2))}}),D={class:"space-y-6"},G=u({__name:"Appearance",setup(o){const t=[{title:"Appearance settings",href:"/settings/appearance"}];return(p,i)=>(a(),_(w,{breadcrumbs:t},{default:d(()=>[e(r(v),{title:"Appearance settings"}),e(M,null,{default:d(()=>[n("div",D,[e(I,{title:"Appearance settings",description:"Update your account's appearance settings"}),e(z)])]),_:1})]),_:1}))}});export{G as default};
